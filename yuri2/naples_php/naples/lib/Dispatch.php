<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/11/30
 * Time: 10:18
 */

namespace naples\lib;


use naples\AutoLoad;
use naples\lib\base\Controller;
use naples\lib\base\Service;

/**
 * 调度类，从url请求到执行业务
 */
class Dispatch extends Service
{
    private $error=''; //错误信息
    private $doc=''; //注释保存

    /**
     * 开始由路由调度到控制器，返回控制器方法运行结果
     */
    function  start(){
        tick('分析res');$this->getResInfo();tick('分析res');
        tick('合并配置');$this->mergeConfig($this->config('moduleName'));tick('合并配置');
        tick('检查条件');$this->checkNeeds();tick('检查条件');
        tick('控制器主流程');$this->activeController();tick('控制器主流程');
    }

    /** 从路由分析并保存res信息 */
    private function getResInfo(){
        $resInfo=Factory::getRoute()->getResInfo();
        $this->config('controllerName',$resInfo['controllerName']);\Yuri2::arrPublic('p.controller',$resInfo['controllerName']);
        $this->config('actionName',$resInfo['actionName']);\Yuri2::arrPublic('p.action',$resInfo['actionName']);
        $this->config('moduleName',$resInfo['moduleName']);\Yuri2::arrPublic('p.module',$resInfo['moduleName']);
        $this->config('dirNamespace',str_replace('/','\\',$resInfo['moduleName']));
        $this->config('controllerClass','\naples\app\\'.$this->config('dirNamespace').'\controller'.'\\'.$this->config('controllerName'));
        $this->config('urlParam',$resInfo['urlParam']);
    }

    /**
     * 合并配置项
     * @param $moduleName string 模块名
     */
    private function mergeConfig($moduleName){
        $configPath=PATH_NAPLES.'/app/'.$moduleName.'/config.php';
        if (is_file($configPath)){
            $arr=require $configPath;
            $objConfig=Factory::getConfig();
            $arrOld=$objConfig->configs;
            $objConfig->configs=array_merge($arrOld,$arr);
        }
    }

    /**
     * 检查需求（条件）,发生错误会直接跳出
     */
    private function checkNeeds(){
        tick('常规条件验证');
        $checkRel=true; //注释check标志
        //检查控制器对应文件是否存在
        $isExistClass=AutoLoad::tryFindClass($this->config('controllerClass'));
        if (!$isExistClass){
            $errorMsg=config('debug')?'找不到控制器:'.$this->config('controllerClass'):'您所访问的页面不存在';
            header('HTTP/1.0 404 Not Found');
            error($errorMsg);
        }
        $classRef=new \ReflectionClass($this->config('controllerClass'));
        $this->config('classRef',$classRef);
        //检查action是否存在
        $publicMethods=$classRef->getMethods(\ReflectionMethod::IS_PUBLIC);
        $hasPublicMethod=false;
        foreach ($publicMethods as $publicMethod){
            if ($publicMethod->getName()==$this->config('actionName')){
                $hasPublicMethod=true;
                //此处检查是否是_开头的action，_开头默认不允许http访问
                if (preg_match('/^_/',$this->config('actionName'))){
                    $hasPublicMethod=false;
                }
            }
        }
        if (!$hasPublicMethod){
            $errorMsg=config('debug')?'不能执行方法:'.$this->config('controllerClass').'::'.$this->config('actionName'):'您所访问的页面不存在';
            header('HTTP/1.0 404 Not Found');
            error($errorMsg);
        }
        tick('常规条件验证');
        if (config('doc_check')){
            tick('注释条件验证');
            $methodRef=$classRef->getMethod($this->config('actionName'));
            $docStr=$methodRef->getDocComment();
            $this->doc=$docStr;
            $objDocParser=Factory::getDocParser([],true);
            $this->config('docLines',$objDocParser->getLines($docStr));
            $docResults=$objDocParser->parse($docStr); //无法缓存
            foreach ($docResults as $docResult){
                if ($docResult!==true){
                    $this->error.="<P>$docResult</P>\r\n";
                    $checkRel=false;
                }
            }
            tick('注释条件验证');
        }
        $this->config('checkRel',$checkRel);
    }

    /** 启动控制器 */
    private function activeController(){
        if ($this->config('checkRel')){
            tick('动作执行预处理');
            $class=$this->config('controllerClass');
            $action=$this->config('actionName');
            $objController=new $class();
            $this->beforeAction($objController);
            $urlParams=$this->bindUrlParams($this->config('urlParam'));
            $methodRef=$this->config('classRef')->getMethod($action);
            tick('动作执行预处理');
            tick('动作执行流程');$rel=\Yuri2::invokeMethod($methodRef,$urlParams,$objController,true);tick('动作执行流程');
            tick('动作执行后处理');
            $objController->sendActionResult([$rel,$objController->config('docLines')]);
            $this->afterAction($objController);
            if ($rel===FLAG_NOT_SET){
                $bindError=config('debug')?'参数绑定错误'.dump($urlParams,false):'无法显示，页面参数错误';
                header('HTTP/1.1 403 Forbidden');
                error($bindError);
            }
            tick('动作执行后处理');
        }else{
            //check不通过
            if (config('debug') and $this->error){
                $errMsg= "注释规则不通过<h4>$this->error</h4>";
                header('HTTP/1.1 403 Forbidden');
                error($errMsg);
            }
            else{
                header('HTTP/1.1 403 Forbidden');
                error('非法访问');
            };
        }
    }

    /**
     * 外界获取info的接口
     * @return array
     */
    public function getInfo(){
        return [
            'res分析'=>$this->config(),
            'action注释'=>$this->doc,
            'action_check_error'=>$this->error,
        ];
    }

    /** 处理路由绑定参数 */
    private function bindUrlParams($params){
        $rel=[];
        if (config('bind_follow_order')){
            return $params;
        }else{
            $len=count($params);
            if ($len==0){return $rel;}
            if ($len%2!=0){
                $params[]='';
                $len++;
            }
            //两个一组取出
            for ($i=0;$i<$len;$i+=2){
                $key=$params[$i];
                $value=$params[$i+1];
                $rel[$key]=$value;
            }
            return $rel;
        }
    }

    /** 在action执行前运行的代码 为控制器对象初始化 */
    private function beforeAction($objController){
        /** @var Controller $objController */
        $objController->moduleName=$this->config('moduleName');
        $objController->controllerName=$this->config('controllerName');
        $objController->actionName=$this->config('actionName');
        $docLines=Factory::getDocParser()->linesToArray($this->config('docLines'));
        $objController->config('docLines',$docLines);
        $objController->beforeAction();
    }

    /**
     * 在action执行后运行的代码 调用控制器的after方法
     * @param $objController Controller
     */
    private function afterAction($objController){
        $objController->afterAction();
    }

}