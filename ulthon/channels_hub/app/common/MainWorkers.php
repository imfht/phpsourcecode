<?php

namespace app\common;

use think\facade\App;
use Channel\Client;
use app\common\Worker;
use app\helper\Html;
use app\model\Channel;
use app\model\HttpChannel;
use Channel\Server;
use Exception;
use PDO;
use think\facade\Log;
use think\facade\Config;
use Workerman\Connection\AsyncTcpConnection;
use Workerman\Connection\TcpConnection;
use Workerman\Lib\Timer;

class MainWorkers extends Worker
{


  protected $config = [];

  public function __construct($daemon = false)
  {
    $this->setConfig('daemon', $daemon);
  }

  public function build()
  {
    $this->buildProgressChannelServer();
    $this->buildWebAdminWorker();
    $this->buildHttpChannelServer();
    $this->buildPortChannelTplServer();
  }

  public function setConfig($name, $value)
  {
    $this->config[$name] = $value;
  }

  //web管理面板
  public function buildWebAdminWorker()
  {
    //web管理面板

    $port = config('channel.web_admin_port');
    $worker = new HttpServer('0.0.0.0', $port);


    if (empty($this->config['pidFile'])) {
      $this->config['pidFile'] = App::getRootPath() . 'runtime/worker.pid';
    }

    // 避免pid混乱
    $this->config['pidFile'] .= '_' . $port;

    // 设置应用根目录
    $worker->setRootPath(App::getRootPath());

    // 应用设置
    if (!empty($this->config['app_init'])) {
      $worker->appInit($this->config['app_init']);
      unset($this->config['app_init']);
    }

    $worker->setStaticOption('daemonize', $this->config['daemon']);

    // 开启HTTPS访问
    if (!empty($this->config['ssl'])) {
      $this->config['transport'] = 'ssl';
      unset($this->config['ssl']);
    }

    // 设置网站目录
    if (empty($this->config['root'])) {
      $this->config['root'] = App::getRootPath() . 'public';
    }

    $worker->setRoot($this->config['root']);
    unset($this->config['root']);

    // 全局静态属性设置
    foreach ($this->config as $name => $val) {
      if (in_array($name, ['stdoutFile', 'daemonize', 'pidFile', 'logFile'])) {
        $worker->setStaticOption($name, $val);
        unset($this->config[$name]);
      }
    }

    // 设置服务器参数
    $worker->option($this->config);
  }

  //进程间通信服务
  public function buildProgressChannelServer()
  {


    $channel_server = new ChannelServer('0.0.0.0', Config::get('channel.channel_server_port'));
  }


  //服务端Http监听服务
  public function buildHttpChannelServer()
  {
    $worker = new Worker('tcp://0.0.0.0:' . config('channel.channel_http_port'));
    $worker->count = 4;
    $worker->name = 'HttpChannelServer';
    $worker->onWorkerStart = function (Worker $worker) {
      Log::debug('HttpChannelServer start,listen to ' . $worker->getSocketName());
      Client::connect('127.0.0.1', Config::get('channel.channel_server_port'));


      Client::on('new_http_channel', function (HttpChannel $model_channel) use ($worker) {

        $worker::$modelHttpChannel[md5($model_channel->getData('domain'))] = $model_channel;

        Log::debug('收到事件域名监听:' . $model_channel->id . '(' . $model_channel->getAttr('domain') . ' ' . $model_channel->local_target_ip . ':' . $model_channel->local_target_port . ')');
      });

      $model_list_http_channel = HttpChannel::where('status', 0)->select();

      foreach ($model_list_http_channel as $model_http_channel) {
        $worker::$modelHttpChannel[md5($model_http_channel->getData('domain'))] = $model_http_channel;
      }

      Client::on('new_inside_client', function ($data) use ($worker) {
        $inside_client_key = $data['inside_client_key'];
        $inside_client_pid = $data['inside_client_pid'];

        $worker::$insideClient[$inside_client_key] = [];

        Log::debug("收到内网客户端上线,client_key:{$inside_client_key},pid:{$inside_client_pid}");

        $worker::$insideClient[$inside_client_key][$inside_client_pid] = time();
      });
      Client::on('update_inside_client', function ($data) use ($worker) {
        $inside_client_key = $data['inside_client_key'];
        $inside_client_pid = $data['inside_client_pid'];

        if (!isset($worker::$insideClient[$inside_client_key])) {
          $worker::$insideClient[$inside_client_key] = [];
        }

        // Log::debug("内网客户端心跳,client_key:{$inside_client_key},pid:{$inside_client_pid}");

        $worker::$insideClient[$inside_client_key][$inside_client_pid] = time();
      });



      Client::on('inside_close_connection', function ($data) use ($worker) {
        $connection_id = $data['connection_id'];
        if (isset($worker::$outsideConnections[$connection_id])) {
          $worker::$outsideConnections[$connection_id]->close();
          unset($worker::$outsideConnections[$connection_id]);
        } else {
        }
      });
    };

    $worker->onConnect = function (TcpConnection $connection) {
      // 收到一个连接,先给标记上id

      $connection_id = uniqid();
      $connection->uid = $connection_id;
      $connection->host = '';

      Log::debug('收到新的外网连接,标记ID:' . $connection_id);
    };

    $worker->onClose = function (TcpConnection $connection) {
      Log::debug('外网连接关闭了,connection_id:' . $connection->uid);
      // 连接关闭了
      Client::publish('outside_close_connection', [
        'connection_id' => $connection->uid,
        // 'inside_client_key' => $connection->insideClientKey,
        // 'inside_client_pid' => $connection->insideClientPid,
      ]);
    };

    $worker->onMessage = function (TcpConnection $connection, $data) use ($worker) {

      Log::debug('收到外网消息');


      $data_splice = str_split($data, 10240);

      $data_splice_count = count($data_splice);

      if (empty($connection->host)) {

        list($http_header) = \explode("\r\n\r\n", $data, 1);

        $header_content = \explode("\r\n", $http_header);
        unset($header_content[0]);
        $host = '';
        foreach ($header_content as $header_item_content) {

          list($key, $value)       = \explode(':', $header_item_content, 2);
          $key                     = \str_replace('-', '_', strtoupper($key));
          $value                   = \trim($value);

          if ($key == 'HOST') {
            $host = $value;
            break;
          }
        }

        if (empty($host)) {
          $connection->close(Html::message('访问错误,没有在header中检测到HOST'));
        }
        $connection->host = $host;
      }

      $host = $connection->host;

      if (isset($worker::$modelHttpChannel[md5($host)])) {
        $model_http_channel = $worker::$modelHttpChannel[md5($host)];

        $inside_client_key = $model_http_channel->client->key;
        if (!isset($worker::$outsideConnections[$connection->uid])) {

          if (empty($worker::$insideClient[$inside_client_key])) {
            // 内网客户端未上线
            $connection->close(Html::message('内网客户端未上线'));
            return false;
          }
          $inside_client_pid = array_rand($worker::$insideClient[$inside_client_key]);
          Log::debug($worker::$insideClient[$inside_client_key][$inside_client_pid]);
          if (time() - $worker::$insideClient[$inside_client_key][$inside_client_pid] > 10) {
            $connection->close(Html::message('内网客户端已下线'));
          }
          // 收到外网连接,但是还没有通知内网客户端建立连接
          $worker::$outsideConnections[$connection->uid] = $connection;
          // 给相应的内网客户端的一个进程发送建立连接命令

          $connection->insideClientKey = $inside_client_key;
          $connection->insideClientPid = $inside_client_pid;

          // 通知内网相应进程建立连接
          Client::publish('outside_new_connection_' . $inside_client_key . '_' . $inside_client_pid, [
            'connection_id' => $connection->uid,
            'inside_client_key' => $inside_client_key,
            'inside_client_pid' => $inside_client_pid,
            'local_target_ip' => $model_http_channel->local_target_ip,
            'local_target_port' => $model_http_channel->local_target_port,
          ]);

          Client::on('inside_new_message_' . $inside_client_key . '_' . $inside_client_pid, function ($message) use ($worker) {

            $connection_id = $message['connection_id'];
            $message_id = $message['message_id'];
            $data = $message['data'];
            $data_splice_index = $message['data_splice_index'];
            $data_splice_count = $message['data_splice_count'];

            // 判断外网连接是否还存在

            if (isset($worker::$outsideConnections[$connection_id])) {

              $connection = $worker::$outsideConnections[$connection_id];

              if (!isset($worker::$responseMessage[$message_id])) {
                $worker::$responseMessage[$message_id] = [];
              }

              $worker::$responseMessage[$message_id][$data_splice_index] = $data;

              if (count($worker::$responseMessage[$message_id]) == $data_splice_count) {
                ksort($worker::$responseMessage[$message_id]);
                $original_data = '';
                foreach ($worker::$responseMessage[$message_id] as $data_splice) {
                  $original_data .= $data_splice;
                }
                unset($worker::$responseMessage[$message_id]);
                $connection->send($original_data);
              }
            } else {
            }
          });
        }


        $message_id = uniqid();
        foreach ($data_splice as $data_index => $data_item) {

          $message = [
            'connection_id' => $connection->uid,
            'inside_client_key' => $connection->insideClientKey,
            'inside_client_pid' => $connection->insideClientPid,
            'message_id' => $message_id,
            'data' => $data_item,
            'data_splice_index' => $data_index,
            'data_splice_count' => $data_splice_count
          ];

          Log::debug('发送到客户端中心,message_id:' . $message_id . ',数据分片:' . $data_index . ',分片总数:' . $data_splice_count . ',工作进程:' . $connection->insideClientPid);
          Client::publish('outside_new_message_' . $connection->insideClientKey . '_' . $connection->insideClientPid, $message);
        }
      } else {
        $connection->close('不支持的域名');
      }
    };
  }

  public function buildPortChannelTplServer()
  {


    $model_list_channel = Channel::select();

    foreach ($model_list_channel as $model_channel) {
      Log::debug($model_channel->listen_address);
      $channel_worker = new Worker($model_channel->listen_address);

      $channel_worker->onWorkerStart = function () use ($channel_worker,$model_channel) {

        Log::debug('端口监听上线:'.$model_channel->listen_address);
        Client::connect('127.0.0.1', Config::get('channel.channel_server_port'));

        Client::on('new_inside_client', function ($data) use ($channel_worker) {
          $inside_client_key = $data['inside_client_key'];
          $inside_client_pid = $data['inside_client_pid'];

          $channel_worker::$insideClient[$inside_client_key] = [];

          Log::debug("收到内网客户端上线,client_key:{$inside_client_key},pid:{$inside_client_pid}");

          $channel_worker::$insideClient[$inside_client_key][$inside_client_pid] = time();
        });
        Client::on('update_inside_client', function ($data) use ($channel_worker) {
          $inside_client_key = $data['inside_client_key'];
          $inside_client_pid = $data['inside_client_pid'];

          if (!isset($channel_worker::$insideClient[$inside_client_key])) {
            $channel_worker::$insideClient[$inside_client_key] = [];
          }

          // Log::debug("内网客户端心跳,client_key:{$inside_client_key},pid:{$inside_client_pid}");

          $channel_worker::$insideClient[$inside_client_key][$inside_client_pid] = time();
        });

        Client::on('inside_close_connection', function ($data) use ($channel_worker) {
          $connection_id = $data['connection_id'];
          if (isset($channel_worker::$outsideConnections[$connection_id])) {
            $channel_worker::$outsideConnections[$connection_id]->close();
            unset($channel_worker::$outsideConnections[$connection_id]);
          } else {
          }
        });
      };

      $channel_worker->onConnect = function (TcpConnection $connection) use ($model_channel, $channel_worker) {

        // 检测客户端是否在线
        $inside_client_key = $model_channel->client->key;

        if (empty($channel_worker::$insideClient[$inside_client_key])) {
          // 内网客户端未上线
          Log::debug('内网客户端未上线');
          $connection->close();
          return false;
        }
        $inside_client_pid = array_rand($channel_worker::$insideClient[$inside_client_key]);
        Log::debug($channel_worker::$insideClient[$inside_client_key][$inside_client_pid]);
        if (time() - $channel_worker::$insideClient[$inside_client_key][$inside_client_pid] > 10) {
          Log::debug('内网客户端已下线');
          $connection->close();
        }

        $connection_id = uniqid();
        $connection->uid = $connection_id;
        $connection->insideClientKey = $inside_client_key;
        $connection->insideClientPid = $inside_client_pid;

        Log::debug('收到新的外网连接,标记ID:' . $connection_id.',端口:'.$model_channel->server_port);

        // 通知内网相应进程建立连接
        Client::publish('outside_new_connection_' . $inside_client_key . '_' . $inside_client_pid, [
          'connection_id' => $connection->uid,
          'inside_client_key' => $inside_client_key,
          'inside_client_pid' => $inside_client_pid,
          'local_target_ip' => $model_channel->local_target_ip,
          'local_target_port' => $model_channel->local_target_port,
        ]);

        Client::on('inside_new_message_' . $inside_client_key . '_' . $inside_client_pid, function ($message) use ($channel_worker) {
          $connection_id = $message['connection_id'];
          $message_id = $message['message_id'];
          $data = $message['data'];
          $data_splice_index = $message['data_splice_index'];
          $data_splice_count = $message['data_splice_count'];
          Log::debug("收到内网客户端信息,message_id:$message_id,分片号码:$data_splice_index,分片总数:$data_splice_count");

          // 判断外网连接是否还存在

          if (isset($channel_worker::$outsideConnections[$connection_id])) {

            $connection = $channel_worker::$outsideConnections[$connection_id];

            if (!isset($channel_worker::$responseMessage[$message_id])) {
              $channel_worker::$responseMessage[$message_id] = [];
            }

            $channel_worker::$responseMessage[$message_id][$data_splice_index] = $data;

            if (count($channel_worker::$responseMessage[$message_id]) == $data_splice_count) {
              ksort($channel_worker::$responseMessage[$message_id]);
              $original_data = '';
              foreach ($channel_worker::$responseMessage[$message_id] as $data_splice) {
                $original_data .= $data_splice;
              }
              unset($channel_worker::$responseMessage[$message_id]);
              Log::debug('向外网客户发送数据');
              $connection->send($original_data);
            }
          } else {
            Log::debug('外网连接已掉线');
          }
        });

        $channel_worker::$outsideConnections[$connection_id] = $connection;
      };

      $channel_worker->onMessage = function ($connection, $data) {


        $data_splice = str_split($data, 10240);

        $data_splice_count = count($data_splice);

        $message_id = uniqid();
        foreach ($data_splice as $data_index => $data_item) {

          $message = [
            'connection_id' => $connection->uid,
            'inside_client_key' => $connection->insideClientKey,
            'inside_client_pid' => $connection->insideClientPid,
            'message_id' => $message_id,
            'data' => $data_item,
            'data_splice_index' => $data_index,
            'data_splice_count' => $data_splice_count
          ];

          Log::debug('发送到客户端中心,message_id:' . $message_id . ',数据分片:' . $data_index . ',分片总数:' . $data_splice_count . ',工作进程:' . $connection->insideClientPid);
          Client::publish('outside_new_message_' . $connection->insideClientKey . '_' . $connection->insideClientPid, $message);
        }
      };

      $channel_worker->onClose = function ($connection) {
        Log::debug('外网连接关闭了,connection_id:' . $connection->uid);
        // 连接关闭了
        Client::publish('outside_close_connection', [
          'connection_id' => $connection->uid,
          // 'inside_client_key' => $connection->insideClientKey,
          // 'inside_client_pid' => $connection->insideClientPid,
        ]);
      };
    }
  }
}
