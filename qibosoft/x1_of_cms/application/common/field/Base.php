<?php
namespace app\common\field;

use think\Db;

/**
 * 自定义字段
 */
class Base
{
    protected static $pagetype = 'form';   //页面类型,表单页或内容页 form show
    
    /**
     * 自定义模板元素
     * @param string $type 表单类型
     * @param array $field 某个字段的配置参数
     * @param array $info 信息内容
     * @param string $pagetype 参数主要是show 或 list 哪个页面使用,表单页用不到,主要是针对显示的时候,用在列表页或者是内容页 , 内容页会完全转义,列表页的话,可能只转义部分,或者干脆不转义
     * @return mixed
     */
    protected static function get_item($type='',$field=[],$info=[],$pagetype='show'){
        if($type==''){
            return ;
        }
        $file = __DIR__ . '/'  . $type . '/' . static::$pagetype . '.php';
        $string = '';
        $name = $field['name'];
        if(is_file($file)){
            $string = include($file);
        }else{
            return ;
        }
        $static = config('view_replace_str.__STATIC__');
        $string = str_replace(['__STATIC__'], [$static], $string);
        return $string;
    }
    
    /**
     * 取得某个字段转义后的HTML代码
     * @param array $field 具体某个字段的配置参数
     * @param array $info 信息内容 这里使用&是方便修改其值
     * @param string $pagetype 参数主要是show 或 list 哪个页面使用,主要是针对显示的时候,用在列表页或者是内容页 , 内容页会完全转义,列表页的话,可能只转义部分,或者干脆不转义
     * @return string[]|unknown[]|mixed[]
     */
    public static function format_field($field=[],&$info=[],$pagetype='list'){
        
        $name = $field['name'];
        $f_value = $info[$name];
        if($info[$name]===''||$info[$name]===null){
            return '';
        }
        if ( ($show = static::get_item($field['type'],$field,$info)) !='' ) {    //个性定义的表单模板,优先级最高
            
        }elseif(in_array($field['type'],['images','files','image','file','jcrop','images2'])){
            
            $show = static::format_url($field,$info);
            
        }elseif ($field['type'] == 'ueditor') {
            
            $show = fun('ueditor@show',$f_value,$pagetype);
            
        }elseif ($field['type'] == 'textarea') {    // 多行文本框
            
            $show = str_replace([' ',"\r\n"], ['&nbsp;','<br>'], $f_value);
            
        }elseif ($field['type'] == 'select' || $field['type'] == 'radio' || $field['type'] == 'usergroup3') {      // 下拉框 或 单选按钮 及用户组单选
            
            $info["_$name"] = $f_value;         //为了保留原始值
            
            $field['type'] == 'usergroup3' && $field['options'] = 'app\common\model\Group@getTitleList'; //用户组单选
            
            if( preg_match('/^[a-z]+(\\\[\w]+)+@[\w]+/',$field['options']) || preg_match('/^([\w]+)@([\w]+),([\w]+)/i',$field['options']) ){
                //$show = $f_value;   //对于动态生成的数组,原型输出,不执行类,不读数据库,避免效率降低
                static $options_array = [];     //避免反复执行
                $array = $options_array[md5($field['options'])];
                if ($f_value && empty($array)) {
                    $array = $options_array[md5($field['options'])] = static::options_2array($field['options']);
                }
                $show = $array?$array[$f_value]:'';				
            }else{
                $detail = is_array($field['options']) ? $field['options'] : str_array($field['options']);
                $show = $detail[$f_value];
            }
            
        }elseif ($field['type'] == 'checkbox'||$field['type'] == 'usergroup2') {    //复选框 及用户组多选
            
            $info["_$name"] = $f_value;         //为了保留原始值
            $field['type'] == 'usergroup2' && $field['options'] = 'app\common\model\Group@getTitleList'; //用户组多选
            
            $detail = [];
            if( preg_match('/^[a-z]+(\\\[\w]+)+@[\w]+/',$field['options']) || preg_match('/^([\w]+)@([\w]+),([\w]+)/i',$field['options']) ){
                static $options_array = [];     //避免反复执行
                $detail = $options_array[md5($field['options'])];
                if ( $f_value && empty($detail) ) {
                    $detail = $options_array[md5($field['options'])] = static::options_2array($field['options']);
                }                
            }else{
                $detail = is_array($field['options']) ? $field['options'] : str_array($field['options']);
            }
            
            $array = [];            
            foreach(explode(',',$f_value) AS $v){
                if($v===''){
                    continue ;
                }
                $array[] = "<span class='{$name}_val val_box'>{$detail[$v]}</span>";
            }
            $show = implode("<span class='{$name}_exp explode_box'> 、</span>",$array);
            
        }elseif($field['type']=='array' && !in_array($name, ['type1','type2','type3'])){    //商城的三个参数特殊点,这里就不处理了
            
            $array = json_decode($info[$name],true);
            foreach($array AS $value){
                $show .= "<div class='array_field $name'>$value</div>";
            }
            
        }elseif($field['type']=='date'){
            
            $show = format_time($info[$name],'Y-m-d');
            
        }elseif($field['type']=='datetime'){
            
            $show = format_time($info[$name],'Y-m-d H:i');
            
        }elseif($field['type']=='callback'){    //回调函数
            
            $show = $field['fun']($info[$name],$info);
            
        }else{  //直接输出
            
            $show = $info[$name];
            
            if($field['type']=='text' && $field['unit']){   //单位
                $show .='<span class="unit"> '.$field['unit'].'</span>';
            }
        }
        
        return $show;
    }
    
    /**
     * 对字段包含有附件的路径补全转义
     * @param array $field 某个字估的配置信息
     * @param array $info 内容原始数据
     * @return void|string|unknown|void[]|string[]|array[]
     */
    public static function format_url($field=[],$info=[]){
        $name = $field['name'];
        $f_value = $info[$name];
        
        if($field['type'] == 'images'||$field['type'] == 'files'){
            
            $detail = explode(',',$f_value);
            $value = [];
            foreach($detail AS $va){
                if($field['type'] == 'images'){
                    $va && $value[]['picurl'] = tempdir($va);
                }else{
                    $va && $value[]['url'] = tempdir($va);
                }
            }
            $f_value = $value;
            
        }elseif($field['type'] == 'image'||$field['type'] == 'file'||$field['type'] == 'jcrop'){
            
            $f_value && $f_value = tempdir($f_value);
            
        }elseif($field['type'] == 'images2'){
            
            $value = json_decode($f_value,true);
            foreach($value AS $k=>$vs){
                $vs['picurl'] = tempdir($vs['picurl']);
                $value[$k] = $vs;
            }
            $f_value = $value;
        }elseif($field['type'] == 'files2'){
            
            $value = json_decode($f_value,true);
            foreach($value AS $k=>$vs){
                $vs['url'] = tempdir($vs['url']);
                $value[$k] = $vs;
            }
            $f_value = $value;
        }
        return $f_value;
    }
    
    
    /**
     * 把单选\多选\下拉框架的参数转义为可选项数组
     * @param string $str 可以是类 app\bbs\model\Sort@getTitleList
     * @return void|string|array|unknown[]
     */
    protected static function options_2array($str=''){
        if($str==''){
            return ;
        }
        if(preg_match('/^[a-z]+(\\\[\w]+)+@[\w]+/',$str)){  //类似这种格式 app\xx\xx@action
            list($class_name,$action,$params) = explode('@',$str);
            if(class_exists($class_name)&&method_exists($class_name, $action)){
                $obj = new $class_name;
                if ($params!='') {
                    $_params = json_decode($params,true)?:fun('label@where',$params);
                }else{
                    $_params = [];
                }
                //$_params = $params ? json_decode($params,true) : [] ;
                $array = call_user_func_array([$obj, $action], isset($_params[0])?$_params:[$_params]);
            }
        }elseif(preg_match('/^([\w]+)@([\w]+),([\w]+)/i',$str)){        //类似这种格式 cms_mysort@id,name@uid
            list($table_name,$fields,$params) = explode('@',$str);
            preg_match('/^qb_/i',$table_name) && $table_name = str_replace('qb_', '', $table_name);
            if($params=='uid'){ //特殊属性uid指定用户
                $map = [
                        'uid'=>intval(login_user('uid'))
                ];
            }elseif ($params!='') {
                $map = json_decode($params,true)?:fun('label@where',$params);
            }
            is_array($map) || $map = [];
            $array = Db::name($table_name)->where($map)->column($fields);
        }else{
            $array = str_array($str,"\n");
        }
        return $array;
    }
    
}
