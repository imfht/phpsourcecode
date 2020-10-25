<?php
/**
 * Created by PhpStorm.
 * User: Happy
 * Date: 2016/5/17 0017
 * Time: 17:53
 */
//基础控制类
class Control{
    //统一service函数接口
    function  send_data($data=array()){
        exit(json_encode($data));
    }

    //将模板中../resource 全部替换成./resource
    protected  function  replace_resource_path($name){
        $view_path=BASE_PATH.'/views/'.$name.'.html';
        $source=file_get_contents($view_path);
      return  str_replace('../resource','./resource',$source);
    // return preg_replace('~../resource~','./resource',$source);
    }


    //替换和插入layout文件  ,新添加layout设置功能
    protected function  display_layout($main, $layout = 'layout',$layout_setting=array())
    {
        //禁用缓存
        /*
        header('Cache-Control:no-cache,must-revalidate');
        header('Pragma:no-cache');
        header("Expires:0");
        */

        //设置layout_setting中主界面的问题
        $layout_setting['server_name']=c('server_name');
        $layout_setting['meirong_name']=c('meirong_name');
        $layout_setting['meirong_type']=c('meirong_type');
        $layout_setting['meirong_hash']=APP_NAME;
        $layout_setting['stamp']=time();
        $layout_setting['debug']=c('debug');

        echo  $this->fetch_layout($main,$layout,$layout_setting);
    }

    protected  function  fetch_layout($main, $layout = 'layout',$layout_setting=array()){

        //要把子页面的css提取出来放到主页面</head>之前；
        /*
        $pattern_css = '~(<link.+?href=".+?>)~i';//替换css
        $pattern_js = '~(<script.+?src=".+?</script>)~i';//替换js

        $part_js = '~<script.+?src=".+?/part.js"></script>~';
        preg_match_all($pattern_css, $main, $match_css);
        preg_match_all($pattern_js, $main, $match_js);
        $css_str = '';
        $js_str = '';
        foreach ((array)$match_css[0] as $value) {
            $css_str .= $value;
        }
        foreach ((array)$match_js[0] as $value) {
            $js_str .= $value;
        }
        //匹配js块代码
        $js_code_pattern='~<script>(.|\n)*?</script>~i';
        preg_match($js_code_pattern,$main,$matches); //匹配页面脚本

        if($matches){
            $main=preg_replace($js_code_pattern,'',$main);
            $js_str.=$matches[0];
        }
        $main = trim(preg_replace(array($pattern_css, $pattern_js), '', $main)); //原来的位置置空


        //id为page_content才是主页面
        $pattern = '~(<div.+?class="placeholder.+?>[^</div>]*</div>)~';
        $layout = file_get_contents($layout_file);

        $content = preg_replace($pattern, $main, $layout, 1);//只替换一次
        $content = preg_replace($part_js, '', $content); //原来的位置置空,partjs是前端设计用的
        $content = str_replace('</head>', $css_str . '</head>', $content);
        //preg_match('~<script.+?sea.init.js.+?</script>~', $content, $mat);

        // $content = preg_replace('~<script.+?sea.init.js.+?</script>~', $js_str . '\\0', $content, 1);
        $content = preg_replace('~</body>~', $js_str . '\\0', $content, 1);
        */
        //为了支持多模板需要做处理
        $te_t=c('template');
        if($te_t&&$te_t!='views'){
            $layout_file = BASE_PATH.'/views/template/'.$te_t.'/'.$layout.'.html';
        }
        else{
            $layout_file = BASE_PATH . '/views/'.$layout.'.html';
        }
        if(file_exists($layout_file)){
            $content=file_get_contents($layout_file);
        }
        else{
            throw new Exception('layout file not exits:'.$layout_file);
        }
        $layout_setting['main']=$main;
        $content=BlitzPhp::template($content,$layout_setting);//layout模板赋值
        return $content;
    }
    //显示提示信息  自动退出
    protected  function  show_message($title,$jump_url='index.php',$detail='',$count_down=5,$exit=true){

        if(file_exists(BASE_PATH.'/views/show_message.html')){
               $content=file_get_contents(BASE_PATH.'/views/show_message.html');
        }
        else{  //使用系统默认的跳转样式
             $content=file_get_contents(CORE_PATH.'/tpl/show_message.tpl');
        }
        $main= BlitzPhp::template($content,array(
            'title'=>$title,
            'detail'=>$detail,
            'jmp_url'=>$jump_url,
            'count_down'=>$count_down
        ));
        $this->display_layout($main);
        if($exit){
            exit();
        }
    }
}

//获取一个control,新增支持跨组control,比如mobile使用使用pc的commonContrl
function control($load_control){
    if(strpos($load_control,'/')){//包含区分组件的情况,不包括开始就是/的情况
        $arr=explode('/',$load_control);
        $app=$arr[0];
        $control=$arr[1];
        $file_name = ROOT_PATH .'/'.$app.'/control/' . $control . '.php';
    }
    else{
        $control=$load_control;
        $file_name = BASE_PATH . '/control/' .$control.'.php';
        $load_control=basename(BASE_PATH).'/'.$control;//为了保证键值一致性，添加app前缀。否则可能声明两个对象
    }

    static $_cache = array();
    if (isset($_cache[$load_control])) {
        return $_cache[$load_control];
    }

    if (!$control) //如果为空或null直接返回model对象
    {
        return new Control();
    }

    $class_name = $control . 'Control';

    if (class_exists(@$class_name, false)) { //由于测试是indexControl已经加载过但不是这个方法加载过的，所以没在静态缓存中
        return $_cache[$load_control] = new $class_name();
    }
    include($file_name); //动态引入文件
    if (!class_exists($class_name)) {
        $error = 'Control Error:  Class ' . $class_name . ' is not exists!';
        throw new Exception($error);
    } else {
        return $_cache[$load_control] = new $class_name();
    }
}

//根据，模板快速创建一个control文件,并默认创建index, show——control名称的 函数
function create_control($control){
  $tpl=  file_get_contents(CORE_PATH.'/tpl/createcontrol.tpl');
   $tpl= str_replace('{{control}}',$control,$tpl);
   $target=BASE_PATH.'/control/'.$control.'.php';
    if(file_exists($target)){
        throw new Exception("control $control 文件已经存在，是否要覆盖源文件？");
    }
    file_put_contents($target,$tpl);
    echo  'create control '.$control.' success!';
}
//include文件而不实例化
function  include_control($load_control){
    if(strpos($load_control,'/')){//包含区分组件的情况,不包括开始就是/的情况
        $arr=explode('/',$load_control);
        $app=$arr[0];
        $control=$arr[1];
        $file_name = ROOT_PATH .'/'.$app.'/control/' . $control . '.php';
    }
    else{
        $control=$load_control;
        $file_name = BASE_PATH . '/control/' .$control.'.php';
       // $load_control=basename(BASE_PATH).'/'.$control;//为了保证键值一致性，添加app前缀。否则可能声明两个对象
    }
    $class_name = $control . 'Control';
    if (class_exists(@$class_name, false)) { //由于测试是indexControl已经加载过但不是这个方法加载过的，所以没在静态缓存中
     return true;
    }
    else{
        include($file_name); //动态引入文件
    }
}


