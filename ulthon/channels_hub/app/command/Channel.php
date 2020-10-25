<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

use think\facade\Config;

use app\common\MainWorkers;
use app\common\Worker;

use app\model\Channel as AppChannel;
use app\model\HttpChannel as AppHttpChannel;
use Channel\Client;

use think\facade\Log;

/**
 * Channel 命令行类
 */
class Channel extends Command
{
  protected $config = [];

  public function configure()
  {
    $this->setName('channel')
      ->addArgument('action', Argument::OPTIONAL, "start|stop|restart|reload|status|connections", 'start')
      ->addOption('host', 'H', Option::VALUE_OPTIONAL, 'the host of workerman server.', null)
      ->addOption('port', 'p', Option::VALUE_OPTIONAL, 'the port of workerman server.', null)
      ->addOption('daemon', 'd', Option::VALUE_NONE, 'Run the workerman server in daemon mode.')
      ->setDescription('Workerman HTTP Server for ThinkPHP');
  }

  public function execute(Input $input, Output $output)
  {
    $action = $input->getArgument('action');

    if (DIRECTORY_SEPARATOR !== '\\') {
      if (!in_array($action, ['start', 'stop', 'reload', 'restart', 'status', 'connections'])) {
        $output->writeln("<error>Invalid argument action:{$action}, Expected start|stop|restart|reload|status|connections .</error>");
        return false;
      }

      global $argv;
      array_shift($argv);
      array_shift($argv);
      array_unshift($argv, 'think', $action);
    } elseif ('start' != $action) {
      $output->writeln("<error>Not Support action:{$action} on Windows.</error>");
      return false;
    }

    if ('start' == $action) {
      $output->writeln('Starting Workerman http server...');
    }

    //主进程

    //加载基本的进程
    (new MainWorkers($this->input->hasOption('daemon')))->build();




    Worker::runAll();
  }

  protected function getHost(string $default = '0.0.0.0')
  {
    if ($this->input->hasOption('host')) {
      $host = $this->input->getOption('host');
    } else {
      $host = !empty($this->config['host']) ? $this->config['host'] : $default;
    }

    return $host;
  }

  protected function getPort(string $default = '2346')
  {
    if ($this->input->hasOption('port')) {
      $port = $this->input->getOption('port');
    } else {
      $port = !empty($this->config['port']) ? $this->config['port'] : $default;
    }

    return $port;
  }

}
