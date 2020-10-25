<?php
/**
 * Created by PhpStorm.
 * User: Happy
 * Date: 2016/5/22 0022
 * Time: 21:04
 * 新增支持 $_key获取当前数组键 $_value获取当前数组值 即循环的value
 * 支持elseif
 * 2016-7-25 新增支持函数 {{php::count(val)}}
 * 2016-7-25 新增数组形式例如{{IF $_value['class2']}}    $_value代表当前循环变量，找不到不会再去搜索全局变量
 *
 *
 */
//php实现blitz模板效果
class  BlitzPhp{
    private  $Variable=array();//变量
    private  $Tmpl='';//模板文件内容
    private  $file_path='';
    private  $foreach_count=0;//循环深度，因为要处理内部变量
    private  $deep_count=0;//套嵌深度，每一个{}都是一层嵌套 包括if和foreach
    private static   $last_compile_content='';

    public  function  __construct($path=''){
        $this->file_path=$path;
        if($path){
            $this->Tmpl=file_get_contents($path);
        }
    }
    private   function   TN($str, $left='{{', $right='}}'){
        /*
        $pattern = '/'.$left.'([^'.$left.$right.']*)'.$right.'/e';
        return preg_replace($pattern, "\$this->select('\\1');", $str);
        */
        //使用新版本php 不支持pre_replace
        $pattern = '/'.$left.'([^'.$left.$right.']*)'.$right.'/';
        return preg_replace_callback($pattern,array($this, 'select'),$str);
    }
    //实现Blitz模板的引擎效果

    //此函数使用的是空格拆分切记{{}}不要有多余的空格，比如使用函数
    private  function  select($param){//{{}}之间的内容
        /*array (
  0 => '{{url}}',
  1 => 'url',
   )
 * */
        $param=$param[1];
        $tag = stripslashes(trim($param)); //对待单引号addslashes()加个\，stripslashes()去个\

        if (empty($tag))
        {
            return '';
        }
        //如果是变量的处理
        //$str = preg_replace( "/".$left."(\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_$\x7f-\xff\[\]\'\']*)".$right."/", "<?php echo \\1;? >", $str );
        //看是否符合函数的模式，如果符合使用函数处理
        preg_match('/php::(.+?)\((.+?)\)/',$param,$matches);

        if($matches){//如果匹配的是函数
            //获取参数
            // $parameter=explode(',',$matches[2]);//逗号分隔参数
            return $this->handle_function($matches);
        }
        $p=explode(' ', $tag);
        //如果trim不包含空格就是变量
        if(count($p)==1&&strtolower($p[0])!='else'){ //变量
            return  $this->handle_val($tag); //处理变量
        }
        $tag_sel = array_shift($p); //获取空格分割后第一个数组元素关键字

        $param=implode('',$p);
        switch (strtolower($tag_sel)) {
            case 'if':
                return $this->handle_if($param);
                break;
            case  'begin':
                return $this->handle_foreach($param);
                break;
            case  'end':
                return $this->handle_end($param);
                break;
            case 'else':
                return '<?php } else { ?>';
                break;
            case 'elseif':
                return $this->handle_if($param,true);//第二个参数代表处理的是elseif
            default:
                throw new Exception("unrecognized tag:".$tag_sel); //识别的标签
        }
    }
    //处理变量，处理套嵌变量，处理函数
    private   function  handle_val($param){

        $param=$this->_handle_param($param);
        $str='<?php echo '.$param.';?>';
        return $str ;
    }
    //内部调用第一个是参数比如if（）括号内的参数  $label代表标签例如if foreach等
    private  function  _handle_param($param,$need_isset=true){
        //嵌套的情况特殊处理
        $param=trim($param);
        if(substr($param,0,1)!='$')
        {
            $param='$'.$param;
        }
        if(substr($param,1,5)=='PRE::'){ //原始赋值形式
            $arr=explode('::',$param);
            $param='$'.$arr[1];
        }
        elseif($this->foreach_count>0){ //如果当前嵌套大于0，说明if在循环内，直接转换变量

            //如果是输出循环$key的情况 获取索引的情况
            if($param=='$_key'||$param=='$_num'){
                $count= $this->foreach_count; //当前的循环计数
                //有可能是嵌套的循环，所以内外层循环应该区分
                //  $key=
                $param='$key'.$count;
            }
            else if(preg_match('~\$_key[0-9]+~',$param)){//自己匹配键值的情况,通常有多层数组，获取上层数组的键方法
                $arr=explode('$_key',$param); //example id="td3_{{$_key1}}_{{$_key}}"
                $param='$key'.$arr[1];
            }
           else if(strstr($param,'$_global.')){  //如果是要找全局的参数配置顶层,数组嵌套中使用
               $arr=explode('$_global.',$param);
               $param='$'.$arr[1]; //要添加$符号以代表变量
            }
            else if($param=='$_value'){ //输出键值的情况
                $count= $this->foreach_count; //当前的循环计数
                $param='$value'.$count;  //当前嵌套的值
            }
            else if(strstr($param,'$_value[')){//$_value['123']这种情况
                $arr=explode('$_value',$param);
                $count= $this->foreach_count; //当前的循环计数
                $param='$value'.$count.$arr[1];
            }
            else{  //输出数组键值的情况
                $param2=substr($param,1);
                $parent_label='$value'.$this->foreach_count;
                //$parent_key=$this->parent_key;
                //如果是数组，处理方式不同
                if(preg_match('/(.+?)(\[.+?\])/',$param2,$match)){ //如果参数传递的是{{$aa['bb']}}的形式
                    $param3=$parent_label.'[\''.$match[1].'\']'.$match[2]; //构造数组赋值的形式

                }else{  //如果参数传递的是{{$aa}}的形式
                    $param3=$parent_label.'[\''.$param2.'\']'; //
                }
               if($need_isset){
                   //有个全局替换的过程
                   $param='isset('.$param3.')?'.$param3.':""';//如果数组不存在就用空代替
               }
                else{
                    $param=$param3;
                }

            }
        }
        return $param;
    }
    //处理函数，参数是匹配的正则
    private  function   handle_function($matches){

        $func=$matches[1];
        $param=$matches[2];

        $p_arr=explode(',',$param);

        foreach($p_arr as $key=>$value){

            //如果是带数组的情况，处理数组前边的就可以了
            //如果首字母是数字或者包含单双引号，说明是常量不用处理
            $first=substr($value,0,1);

            if(is_numeric($first)||$first=="'"||$first=='"'){
                $p_arr[$key]= $value;
            }
            else{
                $p_arr[$key]= $this->_handle_param($value);
            }
        }
        $str= '<?php echo '.$func.'('.implode(',',$p_arr).');?>'; //粘连成新的转换好变量的函数

        return $str;
    }

    //处理if标签
    private  function  handle_if($param,$elseif=false){
        $operator='';
        //if的情况特殊处理 因为有可能$param参数包含>=  <=  ==这样的判断条件
        $arr=array();
        if(strstr($param,'==')){ $arr=explode('==',$param,2);$operator='==';}
        else if(strstr($param,'!=')){ $arr=explode('!=',$param,2);$operator='!=';}
        else if(strstr($param,'>=')){ $arr=explode('>=',$param,2);$operator='>=';}
        else if(strstr($param,'<=')){ $arr=explode('<=',$param,2);$operator='<=';}
        else if(strstr($param,'<')){ $arr=explode('<',$param,2);$operator='<';}
        else if(strstr($param,'>')){ $arr=explode('>',$param,2);$operator='>';}

        if($arr){
            $param=$arr[0];
        }
        $param=$this->_handle_param($param,false);
        $this->deep_count+=1;
        if($operator){
            if($elseif){
                $str='<?php } elseif(('.$param.')'.$operator.$arr[1].') { ?>';
            } else{
                $str='<?php if(('.$param.')'.$operator.$arr[1].') { ?>';
            }
        }
        else{
            if($elseif){
                $str='<?php }elseif('.$param.') { ?>';
            }
            else{
                $str='<?php if('.$param.') { ?>';
            }
        }
        return $str;
    }

    //处理结束标签
    private  function  handle_end($param){

        $param=trim($param);
        if($param&&strtoupper($param)!='IF'){

            $this->foreach_count--;//循环深度减少1
        }
        $this->deep_count-=1;//套嵌深度减少1
        return '<?php } ?>';
    }

    //处理foreach标签
    private  function  handle_foreach($param){
        //<?php foreach(\\1 as $key=>$value) {  ? >
        $param=trim($param);
        if(substr($param,0,1)!='$')
        {
            $param='$'.$param;
        }
        $str='';
     //是全局变量的情况
        if(strstr($param,'$_global.')){  //如果是要找全局的参数配置顶层,数组嵌套中使用
            $arr=explode('$_global.',$param);
            $param='$'.$arr[1]; //要添加$符号以代表变量
        }
        elseif(substr($param,1,5)=='PRE::'){ //原始赋值形式
            $arr=explode('::',$param);
            $param='$'.$arr[1];
        }
        else if($this->foreach_count>0){ //如果当前嵌套大于0，说明foreach在循环内，直接转换变量
            //添加全局指定
            $param2=substr($param,1);
            $parent_label='$value'.$this->foreach_count;

            //$parent_key=$this->parent_key;
            $str.='<?php '.$param.'='.$parent_label.'[\''.$param2.'\'];?>'; //构造数组赋值的形式
        }


        $this->deep_count+=1;
        $this->foreach_count+=1;
        $count= $this->foreach_count; //当前的循环计数
        //有可能是嵌套的循环，所以内外层循环应该区分
        $key='$key'.$count;
        $value='$value'.$count;
        $str.= '<?php foreach('."$param as $key =>$value".') {  ?>';

        return $str;
    }

    //编译和执行模板文件代码
    public  function  compile($content='',$setting=array()){
        $set=$this->Variable;
        if($setting){$set=array_merge($set,$setting);}

        $php_code=$this->middle_compile($content);
      //  file_put_contents('a.php',$php_code);
        //中间编译
        extract($set);//数组转成变量方便模板使用
        ob_start();
        eval('?>'.$php_code);
        $content= ob_get_contents();
        ob_end_clean();

        return $content;
    }
    //指编译和返回中间编辑结果，不执行
    public   function  middle_compile($content=''){

        $con=$this->Tmpl;
        if($content){$con=$content;}
        return  self::$last_compile_content= $this->TN($con);
    }


    //include
    public    function  Binclude($path,$setting=array()){
        if(!file_exists($path)){
            throw new Exception('file ['.$path.'] not exist ！！');
        }else{
            $content=file_get_contents($path);
            return $this->compile($content,$setting);
        }
    }
    //加载内容
    public  function  load($content){
        $this->Tmpl=$content;
        return $this;
    }

    public  function  parse($setting=array()){

        return $this->compile('',$setting);
    }
    //加载内容
    public   function  display($setting=array())
    {
        echo $this->parse($setting);
    }

    //获取最后一次编译的中间结果 ,php代码
    public static  function  get_last_compile(){
        return self::$last_compile_content;
    }
    //设置值
    public  function  set($val,$value=''){
        if(is_array($val)){
            $this->Variable=array_merge($this->Variable,$val);
        }
        else{
            $this->Variable[$val]=$value;
        }
    }
    //根据模板和变量获取最终的值
    public  static  function  template($content='',$setting=array()){
              $t=new BlitzPhp();
      return  $t->compile($content,$setting);
    }
}