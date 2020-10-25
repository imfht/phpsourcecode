<?php
/**
 * Created by PhpStorm.
 * User: xzl
 * Date: 14/10/24
 * Time: 下午12:54
 */

namespace Huasuan\Route;


class Generator {
    private $_file = '../app/routes/routes.php';
    private $_content = '';
    private $_controller = '../app/controllers/';
    public function __construct(){
        $this -> getContent();
    }
    //生成路由组
    public function setGroup($data = array()){
        $data['type'] = 'group';
        if(!isset($data['name'])) exit('NOT FOUNT NAME!!!');
        $this -> setRoute($data);
    }
    //路由修改
    public function updateGroup($old,$data = array()){
        $type = 'group';
        if(!isset($data['name'])) exit('NOT FOUNT NAME!!!');

        $reg = '/\/\/@group '. $old .'\s.*function\(.*\){/Uis';
        /*读取模板*/
        $tpl = $this -> getTpl($type);
        $tpl = str_replace(array(':NAME',':BEFORE',':AFTER',':DOMAIN'),
            array($data['name'],$data['before'],$data['after'],$data['domain']),$tpl);
        preg_match($reg,$tpl,$data);
        $group_title = $data[0];
        $this -> _content = preg_replace($reg,$group_title,$this -> _content);
        file_put_contents($this -> _file,$this -> _content);

    }
    //移除路由
    public function removeGroup($name){
        $type = 'group';
        if(!isset($name)) exit('NOT FOUNT NAME!!!');
        $name = str_replace('.',"\\.",$name);
        $reg = '/\/\/@group '. $name .'\s.*function\(.*\){([\s.]*)}\);\s.*\/\/@endgroup '.$name.'/Uis';
        preg_match($reg,$this -> _content,$data);
        if(isset($data[1])){
            $con = str_replace("\n    ","\n",$data[1]);
            $this -> _content = preg_replace($reg,$con,$this -> _content);
            file_put_contents($this -> _file,$this -> _content);
        }
    }
    //生成controller路由
    public function setController($data = array()){
        $data['type'] = 'route_controller';
        if(!isset($data['name'])) exit('NOT FOUNT NAME!!!');
        if(!isset($data['action'])) exit('NOT FOUNT ACTION!!!');
        if(!isset($data['uri'])) exit('NOT FOUNT URI!!!');
        if(!isset($data['method'])) exit('NOT FOUNT METHOD!!!');
        $this -> setRoute($data);
    }
    //生成closure路由
    public function setClosure($data = array()){
        $data['type'] = 'route_closure';
        if(!isset($data['name'])) exit('NOT FOUNT NAME!!!');
        if(!isset($data['uri'])) exit('NOT FOUNT URI!!!');
        if(!isset($data['method'])) exit('NOT FOUNT METHOD!!!');
        $this -> setRoute($data);
    }
    //生成controller类
    public function createController($controller_name){
        $controller_name = ucfirst($controller_name);
        $file = $this -> _controller . $controller_name.'.php';
        if(file_exists($file)){
            return false;
        }
        $php = "<?php\n/**\n* Created by Huasuan\\Route.\n* User: admin\n* Date: ".date('Y-m-d H:i:s')."\n* \n*/\n\nclass {$controller_name} extends BaseController {\n\n}";
        file_put_contents($file,$php);
    }
    //生成action方法
    public function createAction($controller_name,$action){
        $controller_name = ucfirst($controller_name);
        $file = $this -> _controller . $controller_name.'.php';
        if(!file_exists($file)){
            $this -> createController($controller_name);
        }
        $con = file_get_contents($file);
        $this -> backUp($con,$file);
        $reg = '/function.*'.$action.'.*\(.*\)/U';
        if(preg_match($reg,$con)){
            return false;
        }
        $php = "\n\tpublic function {$action}(){\n\n\t}";
        $pos = strrpos($con,'}');
        $php = substr($con,0,$pos) . $php . "\n}";
        file_put_contents($file,$php);
    }

    //修改action方法
    public function updateAction($controller_name,$old_action,$action){
        $controller_name = ucfirst($controller_name);
        $file = $this -> _controller . $controller_name.'.php';
        if(!file_exists($file)){
            $this -> createController($controller_name);
        }

        $con = file_get_contents($file);
        $this -> backUp($con,$file);
        $reg = '/function.*'.$old_action.'.*\((.*)\)/U';

        $con = preg_replace($reg,"function {$action}($1)",$con);
        file_put_contents($file,$con);
    }
    //移除action方法
    public function removeAction(){

    }
    /*
     * 生成路由,亦可修改路由
     */
    function setRoute($data = array()){
        !isset($data['type']) && $data['type'] = 'route_closure';
        !isset($data['method']) && $data['method'] = 'get';
        !isset($data['before']) && $data['before'] = '';
        !isset($data['after']) && $data['after'] = '';
        !isset($data['domain']) && $data['domain'] = '';
        !isset($data['action']) && $data['action'] = '';
        !isset($data['uri']) && $data['uri'] = '';

        $name = $data['name'];
        $type = $data['type'];
        /*读取模板*/
        $tpl = $this -> getTpl($type);
        $tpl = str_replace(array(':NAME',':URI',':BEFORE',':ACTION',':AFTER',':DOMAIN',':METHOD'),
            array($data['name'],$data['uri'],$data['before'],$data['action'],$data['after'],$data['domain'],$data['method']),$tpl);
        if($this -> check($name,$type)){
            if($type == 'group'){

                $reg = '/\/\/@group '. $name .'\s.*function\(.*\){/Uis';
                preg_match($reg,$tpl,$data);
                $group_title = $data[0];

                $reg = '/\/\/@group '. $name .'\s.*function\(.*\){/Uis';
                preg_match($reg,$this -> _content,$data);
                $rep = $data[0];

                $this -> _content = str_replace($rep,$group_title,$this -> _content);
            }else{
                $reg = '/\/\/@'.$type.' '.$name.'\s.*\/\/@end'.$type.' '.$name.'/Uis';
                $this -> _content = preg_replace($reg,$tpl,$this -> _content);
            }
        }else{
            $this -> _content .= "\n".$tpl."\n";
        }
        file_put_contents($this -> _file,$this -> _content);
    }
    /*
     * 路由分组
     * 参数：$gname 组路由名，$cname 子路由名
     */
    function moveRoute($gname,$cname,$type){
        $group = $this -> getRoute($gname,'group');
        if($this -> checkGroup($cname,$type,$group)) return false;
        $route = $this -> getRoute($cname,$type);
        $gname = str_replace('.',"\\.",$gname);
        $reg = '/\/\/@group '. $gname .'\s.*function\(.*\){/Uis';
        preg_match($reg,$group,$data);
        if(!isset($data[0])) exit($group.'<br />NOT FOUND GROUP!!!'.$reg);
        $group_title = $data[0];
        $group = str_replace($group_title,'',$group);
        $content = $group_title . "\n    ".str_replace("\n","\n    ",$route) . $group;

        $this -> removeRoute($cname,$type);
        $reg = '/\/\/@group '.$gname.'\s.*\/\/@endgroup '.$gname.'/Uis';
        $this -> _content = preg_replace($reg,$content,$this -> _content);

        file_put_contents($this -> _file,$this -> _content);
    }

    /*
     *  检测路由是否已经定义
     **/
    function checkGroup($name,$type,$group){
        $name = str_replace('.',"\\.",$name);
        $reg = '/\/\/@'.$type.' '.$name.'(.*)\/\/@end'.$type.' '.$name.'/Uis';
        //echo $reg;exit;
        //$reg = '/\@/Uis';
        $this -> getContent();

        if(preg_match($reg,$group)){
            return true;
        }
        return false;
    }
    /*
    *  移除子路由
    */
    function removeRoute($name,$type){
        $name = str_replace('.',"\\.",$name);
        $reg = '/\/\/@'.$type.' '.$name.'\s.*\/\/@end'.$type.' '.$name.'/Uis';
        $this -> _content = preg_replace($reg,'',$this -> _content);
        file_put_contents($this -> _file,$this -> _content);
    }
    /*
     *获取路由
     */
    function getRoute($gname,$type){
        $gname = str_replace('.',"\\.",$gname);
        $reg = '/\/\/@'.$type.' '.$gname.'\s.*\/\/@end'.$type.' '.$gname.'/Uis';
        preg_match($reg,$this -> _content,$group);
        if(!isset($group[0])) exit($this -> _content.'<br />NOT FOUND ROUTE!!!'.$reg);
        return $group[0];
    }
    /*
     *  检测路由是否已经定义
     **/
    function check($name,$type){
        $name = str_replace('.',"\\.",$name);
        $reg = '/\/\/@'.$type.' '.$name.'(.*)\/\/@end'.$type.' '.$name.'/Uis';
        //echo $reg;exit;
        //$reg = '/\@/Uis';
        $this -> getContent();

        if(preg_match($reg,$this -> _content)){
            return true;
        }
        return false;
    }
    private function getContent(){
        if(empty($this -> _content)){
            $this -> _content = file_get_contents($this -> _file);
            $this -> backUp($this -> _content,$this -> _file);
        }
    }
    private function backUp($content,$file){
        $path = app_path('storage') . '/routes/bak/' . date('Y-m-d-H:i:s').pathinfo($file)['basename'];
        file_put_contents($path,$content);
    }
    private function getTpl($name){
        $file = '../app/routes/tpl/'.$name.'.php';
        if(file_exists($file)){
            return file_get_contents($file);
        }else{
            exit('TPL NOT FOUND!!!');
        }
    }
} 