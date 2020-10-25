<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/11/30
 * Time: 0:08
 */

namespace naples\lib\base;
use naples\lib\Factory;
use naples\lib\naplesTpl\NaplesTpl;
use naples\lib\TplExtend;

/** 控制器基类 */
class Controller extends Service
{
    protected $data=[];//页面变量数组
    /** @var  NaplesTpl $view */
    private $view;//视图对象
    public $moduleName;
    public $controllerName;
    public $actionName;
    /** @var  TplExtend $tplExObj */
    protected $tplExObj;
    
    /**
     * 为模板页面分配变量
     * @param $key string|array
     * @param $value mixed
     */
    protected function assign($key,$value=null){
        if (is_array($key)){
            foreach ($key as $k=>$v){
                $this->data[$k]=$v;
            }
        }else{
            $this->data[$key]=$value;
        }
    }

    /**
     * @param $results 0：action返回值 1：@注释数组
     */
    public function sendActionResult($results){
        $result=$results[0];
        $docs=$results[1];
        //string,xml,json
        if ($result!==null){
            if (!empty($docs['format'])){
                switch ($docs['format']){
                    case 'string':
                        if (is_array($result)){
                            dump($result);
                        }else{
                            echo $result;
                        }
                        break;
                    case 'xml':
                        if (is_array($result)){
                            echo \Yuri2::arrayToXml($result);
                        }else{
                            echo $result;
                        }
                        break;
                    case 'json':
                        if (is_array($result)){
                            echo json_encode($result);
                        }else{
                            echo $result;
                        }
                        break;
                    default:
                        echo $result;
                        break;
                }
            }
            elseif(config('ajax_format') and is_array($result)){
                switch (config('ajax_format')){
                    case 'json':
                        echo json_encode($result);
                        break;
                    case 'xml':
                        echo \Yuri2::arrayToXml($result);
                        break;
                    default:
                        echo json_encode($result);
                        break;
                }
            }
            else{
                echo $result;
            }
        }
    }

    /** 将在action启动前被调用 */
    public function beforeAction(){}

    /** 将在action启动后被调用 */
    public function afterAction(){}

    /**
     * 获得模板文件的路径（相对于app目录下）
     * @param $res string res a/b/c
     * @return string
     */
    protected function getViewFilePath($res=''){
        $controllerName=$this->controllerName;
        $actionName=$this->actionName;
        $moduleName=$this->moduleName;
        if (!empty($res)){
            $arrRes=\Yuri2::explodeWithoutNull($res,'/');
            $num=count($arrRes);
            switch ($num){
                case 1:
                    $actionName=$arrRes[0];
                    break;
                case 2:
                    $controllerName=$arrRes[0];
                    $actionName=$arrRes[1];
                    break;
                case 3:
                    $moduleName=$arrRes[0];
                    $controllerName=$arrRes[1];
                    $actionName=$arrRes[2];
                    break;
                default:
                    break;
            }
        }
        $rel1='/'.$moduleName.'/view/'.$controllerName.'/'.$actionName;
        $rel2=PATH_NAPLES.'/app'.$rel1.'.'.config('tpl_suffix');
        $rel3=PATH_NAPLES.'/app'.$rel1.'.php';
        $rel=[$rel1,$rel2,$rel3];
        return $rel;
    }

    /**
     * 渲染模板
     * @param string $res
     * @return bool
     */
    protected function render($res=''){
        tick('模板渲染');
        $this->view=Factory::getView();
        //添加tplExtend
        $tpl=$res?$this->getViewFilePath($res):$this->getViewFilePath();
        if (!is_file($tpl[1])){
            if (!is_file($tpl[2])){
                $msg=$this->config('debug')?'找不到模板文件':'找不到模板文件:<br/><p>'.$tpl[1].'</p><p>'.$tpl[2].'</p>';
                header('HTTP/1.1 404 Not Found');
                error($msg);
                return false;
            }
            else{
                $this->tplExObj=Factory::getTplExtend();//页面辅助对象
                $this->data['N']=$this->tplExObj;
                foreach ($this->data as $k=>$v){
                    $$k=$v;
                }
              require $tpl[2]; //引用普通模板php文件
            }
        }else{
            $controller=$this;
            $this->view->callResToFile(function ($res) use ($controller){
                $infos=$controller->getViewFilePath($res);
                return $infos;
            });
            $this->view->render($res,$this->data);
        }
        tick('模板渲染');
    }

    /**
     * @param $key string
     * @return mixed
     */
    protected function getDocLines($key=FLAG_NOT_SET){
        $rel= $this->config('docLines');
        if ($key==FLAG_NOT_SET){
            if (empty($rel)){
                return [];
            }else{
                return $rel;
            }
        }else{
            if (isset($rel[$key])){
                return $rel[$key];
            }else{
                return null;
            }
        }
    }

    /**
     * 校验验证码
     * @param $code string
     * @return bool
     */
    protected function checkCaptcha($code){
        if (strtolower(session('sysNaples.captchaCode'))==strtolower($code)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 校验表单令牌
     * @return bool
     */
    protected function checkToken(){
        $token=request('naples_sys_auto_token');
        //获取前缀
        $prefix=preg_match('/^([\w-]+)_\w+$/',$token,$matches);
        if ($prefix){
            $prefix=$matches[1];
            $serverToken= session('sysNaples.form_tokens.'.$prefix);
            if ($token==$serverToken){
                session('sysNaples.form_tokens.'.$prefix,'used');
                if (isset($_REQUEST['naples_sys_auto_token'])){unset($_REQUEST['naples_sys_auto_token']);}
                if (isset($_GET['naples_sys_auto_token'])){unset($_GET['naples_sys_auto_token']);}
                if (isset($_POST['naples_sys_auto_token'])){unset($_POST['naples_sys_auto_token']);}
                return true;
            }
        }
        return false;
    }
    
}