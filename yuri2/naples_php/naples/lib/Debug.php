<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/11/29
 * Time: 10:31
 */

namespace naples\lib;


use naples\AutoLoad;
use naples\lib\base\Service;

/**
 * 调试工具类
 */
class Debug extends Service
{
    private $ticks=[];//秒表记录
    private $dbs=[];//db记录
    private $debugs=[];//跟踪调试变量数组
    private $cacheHit=[[],[]];

    /**
     * 按一下秒表
     * @param $title string 标题，提示语
     */
    public function tick($title){
        if (!Factory::getConfig()->config('debug')){return;}
        $mtime=explode(' ',microtime());
        $time=$mtime[1]+$mtime[0];
        $this->ticks[]=[
            'time'=>$time,
            'title'=>$title,
        ];
    }

    /**
     * 返回秒表记录数组
     * @return array
     */
    public function getTicks(){
        return $this->ticks;
    }

    /**
     * 返回到目前的总耗时
     * @return float
     * */
    public function getTakingUntil(){
        $len=count($this->ticks);
        if ($len==0){return 0;}
        $taking=$this->ticks[$len-1]['time']-$this->ticks[0]['time'];
        return $taking;
    }

    /** 显示trace按钮 */
    public function displayTrace(){
        if (!config('debug') or !config('show_debug_btn')){return;}
        $timeSpend=round($this->getTakingUntil()*1000);
        $reportUrl=url('SysNaples/Admin/report/'.ID);
        $div=<<<EOT
<a id="naples-trace-btn" title='打开详细报告' href='$reportUrl' target='_blank' style="
    border-radius: 12px 0 0 0;
    cursor: pointer;
    font-size:13px;
    background-color: #667e69;
    color: aliceblue;
    position: fixed;
    bottom: 0;
    right: 0;
    text-align: center;
    width: auto;
    padding:5px;
    height: auto;
    line-height: 20px;
    margin:0;
    text-decoration: none;
    ">
$timeSpend ms</a>
EOT;
        echo $div;

    }

    /** 生成简报trace */
    private function traceShort(){
        $ip=\Yuri2::getIp();
        $memUsage=\Yuri2::memoryUsage();
        $url=\Yuri2::getHttpType().'://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        $arr=[
            '访问者IP'=>$ip,
            '主机'=>$_SERVER['HTTP_HOST'],
            'URL'=>$url,
            '内存用量'=>$memUsage,
            '请求方式'=>[
                '是否 get ?'=>\Yuri2::isGet(),
                '是否 post ?'=>\Yuri2::isPost(),
                '是否 ajax ?'=>\Yuri2::isAjax(),
            ],
            'request数组'=>$_REQUEST,
        ];
        return $arr;
    }

    /** 生成请求trace */
    private function traceRequest(){
        return ['get'=>$_GET,'post'=>$_POST,'cookie'=>$_COOKIE,'session'=>$_SESSION];
    }

    /** 生成耗时trace */
    private function traceTaking(){
        $rel=[];
        $stack=[];
        $order=[];

        foreach ($this->ticks as $tick){
            $title=$tick['title'];
            $time=$tick['time'];
            if (!isset($order[$title])){
                $order[$title]=$time;
            }
            if (empty($stack[$title])){
                //不在栈里
                $stack[$title]=$time;
            }else{
                //已经有一个在栈里
                $timeSpend=$time-$stack[$title];
                $rel[$title]=str_repeat('-------- ', count($stack)).round($timeSpend*1000.0,3).' ms';
                unset($stack[$title]);
            }
            foreach ($order as $key=>$val){
                if (isset($rel[$key])){
                    $order[$key]=$rel[$key];
                }
            }
            foreach ($stack as $key=>$val){
                $order[$key]='<b style="color: red;"> -------- timeout</b>';
            }
        }
        return $order;
    }

    /** 生成文件加载trace */
    private function traceFileLoad(){
        return AutoLoad::getLogFileLoaded();
    }

    /** 生成服务器trace */
    private function traceServer(){
        return (['配置列表'=>config(),'服务器参数'=>$_SERVER]);
    }

    /** 生成运行trace */
    private function traceRun(){
        $arr=Factory::getDispatch()->getInfo();
        $arr['cache命中']=$this->cacheHit[0];
        $arr['cache未命中']=$this->cacheHit[1];
        return ($arr);
    }

    /** 生成模板引擎trace */
    private function traceTpl(){
        if (is_callable([Factory::getView(),'getTrace'])){
            $arr=Factory::getView()->getTrace();
            return ($arr);
        }else{
            return [];
        }

    }

    /**
     * 生成自定义调试trace
     * @param $key string 插入调试标题
     * @param $value mixed 待trace调试变量
     * @return string
     */
    public function traceDebug($key=FLAG_NOT_SET,$value=FLAG_NOT_SET){
        if ($key===FLAG_NOT_SET){
            return $this->debugs;
        }else{
            $this->debugs[$key]=$value;
            return true;
        }
    }

    /** 记录数据库语句trace */
    public function traceDb($value=null){
        if ($value===null){
            return ($this->dbs);
        }else{
            $this->dbs[]=$value;
            return true;
        }
    }

    /** 统计cache命中率 */
    public function cacheHitOrNot($hit=false,$key){
        if ($hit){
            $this->cacheHit[0][]=$key;
        }else{
            $this->cacheHit[1][]=$key;
        }
    }

    /** 保存浏览信息用于后期查看 */
    public function saveDebug(){
        if (!config('debug')){return ;}
        $short=$this->traceShort();
        $server=$this->traceServer();
        $request=$this->traceRequest();
        $taking=$this->traceTaking();
        $tpl=$this->traceTpl();
        $fileLoaded=$this->traceFileLoad();
        $debug=$this->traceDebug();
        $run=$this->traceRun();
        $db=$this->traceDb();
        $errors=ErrorCatch::getErrors();
        $infos=[
            '简报'=>$short,
            '环境'=>$server,
            '请求'=>$request,
            '耗时'=>$taking,
            '模板'=>$tpl,
            '加载'=>$fileLoaded,
            '调试'=>$debug,
            '运行'=>$run,
            '数据'=>$db,
            '错误'=>$errors,
        ];
        $saveContent=serialize($infos);
        $dir=PATH_RUNTIME.'/reports';
        \Yuri2::createDir($dir);
        //在此清理久远的记录
        \Yuri2::ergodicDir($dir,function ($file) use($dir){
            $mtime=filemtime($dir.'/'.$file);
            if ($mtime+3600<TIMESTAMP){
                //删除一小时前的记录
                unlink($dir.'/'.$file);
            }
        });
        //清理完毕
        file_put_contents($dir.'/'.ID.'.rep',$saveContent);
    }

}


