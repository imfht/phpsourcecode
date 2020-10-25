<?php 
namespace  App\Lib\Api;
use Mattbrown\Laracurl\Laracurl;
use Mattbrown\Laracurl\Response;
use Exception;
use Log;

/**
 * @author hubagen
 * @modify yukai 2016.1.14
 *
 * @todo 解决不同客户端域名不同的情况
 * @todo 引入专门的
 * debug handler
 * reponse handler
 * curl handler
 * log handler
 * uri handler
 * err handler
 * 则升级为3.0
 *
 *
 */
class BaseApi extends Laracurl
{
    private $errMsg = '操作成功';
    private $status = 1;

    private $debug;

    protected $system = '';
    protected $path   = '';
    protected $dir    = '/api/';


    public function __construct($setting = [])
    {
        $this->debug = empty($setting['debug'])?0:$setting['debug'];
        $path        = empty($setting['path'])?'':$setting['path'];
        $this->setDefaultPath($path);
    }


    public function easyget($url , $param = [])
    {
        return $this->exec($url, $param, 'get');
    }

    public function easypost($url , $param = [])
    {
        return $this->exec($url, $param, 'post');
    }

    private function exec($url, $param = [], $type = 'get')
    {
        // exit($url);
        $partner_id = \Session::get('partner_id','');
        if($partner_id){
            $param['partner_id'] = $partner_id;
        }
        try{
            $method = strtolower($type);
            $url = $this->makeUrl($url, $param, $method);
//             echo "<a href='http://{$url}' target='_blank'>{$url}</a><br/>"; //die;

//            echo $url;

            \Log::info($url);

            if ($type == 'get') {
                $res = $this->get($url);
                //var_dump($res);echo '<hr>';var_dump($url);exit;
            }else{
                $res = $this->post($url, $param);
            }
        }catch(Exception $e){
            //捕获超时等
            //
            if ($this->debug) {
                $trace = $this->makeTrace(get_defined_vars());
                $this->setErr($trace);
            }else{
                 //Log::info($e->getMessage());
            }
            $res = false;
        }

        $this->debug($res);

        return $this->response($res);
    }

    /**
     * 根据get/post生成请求链接http://out.fanfan.com/api/order/update?id=619&cash=10&sure_user=%E5%BC%A0%E4%B8%89&sure_id=5&sure_tel=18684789995&opera_user=admin&opear_tel=18684789995&opera_id=1' target='_blank'>url</a>
     */
    public function makeUrl($url, $param, $type)
    {
        if ($type == 'get') {
            if ( count($param) != 0 ) {
                $url = $this->buildUrl($url , $param);
            }
        }else{
            $url = $url;
        }
        return $url;
    }

    /**
     * 生成trace 信息
     */
    private function makeTrace($data)
    {
        extract($data);
        return "\r\nMessage:".$e->getMessage()."\r\nFile:".$e->getFile()."\r\nLine:".$e->getLine()."\r\nType:".$type."\r\nUrl:".$url."\r\nParam:".json_encode($param)."\r\n";
    }

    /**
     * 处理响应
     */
    private function response($res)
    {
        if ($res === false) {
            //请求失败
            $response = false;
        }elseif($res instanceof Response){
            if ($res->statusCode == 200) {

                $info = json_decode($res->body, true);
                // print_r($info);
                if (empty($info)) {
                    // Log::info($res->body);
                    //json 解码失败
                    $response = false;
                }else{
                    if ($info['status'] != 1) {
                        if(isset($info['message'])){
                            $this->setErr($info['message'],$info['status']);
                        }else{
                            $this->setErr($info);
                        }
                        //请求成功 返回错误提示
                        $response = false;
                    }else{
                        if (isset($info['result'])) {
                            $response = $info['result'];
                        }else{
                            $response = true;
                        }
                    }
                }
            }else{
                $this->setErr($res->statusText);
                //请求错误 如500
                $response = false;
            }
        }
        return $response;
    }

    public function setErr($msg,$status = -1)
    {
        if (!$this->debug) {
            // Log::info($msg);
        }
        $this->errMsg = $msg;
        $this->status = $status;
    }

    public function getErr()
    {
        return $this->errMsg;
    }
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * 输出调试信息
     */
    private function debug($res)
    {
        if (!$this->debug ) {
            return false;
        }

        if (!empty($res)) {
            //接口正常返回
            var_dump($res->info['url']);
            echo($res->body);
        }else{
            (var_dump($this->getErr()));
        }
    }

    /**
     * 根据不同域名生成不同地址
     */
    protected function apiUrl($uri)
    {
        //if (getRunEnv() == 0) {
        //     $path = $this->setDefaultPath();
        //     return $path.$uri;
        // }else{
        return $this->path.$uri;
        //}
    }

    public function setDefaultPath($path = '')
    {
        $http_host = $_SERVER['HTTP_HOST'];
        // 去除端口影响
        strpos($http_host, ':') == false || $http_host = strstr($http_host, ':', TRUE);

        $path = str_replace(array('www.','phone.','manage.'), $this->system.'.', $http_host).$this->dir;
        $path = str_replace(['weiweiyongche', 'wanliweiwei'], 'buketech', $path);
        
        $this->path = $path;
        return $path;
    }

}
