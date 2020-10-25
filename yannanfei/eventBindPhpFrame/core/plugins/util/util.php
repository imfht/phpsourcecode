<?php
/**
 * Created by PhpStorm.
 * User: Happy
 * Date: 2016/5/18 0018
 * Time: 14:37
 */
//通用函数插件
class utilPlugin extends  Plugin{

    //-------------公共函数
    //发送请求
    /* 发送get请求
* @param string $url 请求地址
* @param array $post_data post键值对数据
* @return string
*/
    function http($url, $post_data)
    {
        $postdata = http_build_query($post_data);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type: text/html; charset=utf-8',
                'content' => $postdata,
                'timeout' => 15 * 60 // 超时时间（单位:s）
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        return $result;
    }
    function httpPost($url, $post_data){
            $post_data = http_build_query($post_data);
            $options = array(
                'http' => array(
                    'method' => 'POST',
                    'header' => 'Content-type:application/x-www-form-urlencoded',
                    'content' => $post_data,
                    'timeout' => 15 * 60 // 超时时间（单位:s）
                )
            );
            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            return $result;
    }

    //官方自带示例的get方法
    function httpGet($url,$data=array()) {
        if($data){
            $url=$url.'?'.http_build_query($data);
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);
        // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
        // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false) 普通post请求，至少1秒的时间
    public  function  post($url,$data=array()){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//跳过ssl验证
        $response = curl_exec($ch);
        if ($response  === FALSE) {//因为请求的回复可能是空字符串，curl在请求出错的情况下回返回FALSE值，所以我们必须使用===，而不是==。
            write( "cURL 具体出错信息: " . curl_error($ch));
        }
        curl_close($ch);
        return $response;
    }

    //快速的post请求，本地异步的话基本是50ms左右
    //http://blog.csdn.net/hzbigdog/article/details/10009043 ,设置超时时间
    public  function  fast_post($url,$post_data){
            $ch=curl_init($url);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0); //强制协议为1.0
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Expect: ")); //头部要送出'Expect: '
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); //强制使用IPV4协议解析域名
             curl_setopt($ch, CURLOPT_TIMEOUT,2);   //只需要设置一个秒的数量就可以
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
           $result= curl_exec($ch);
            curl_close($ch);
           return $result;
    }

    //设置缓存
    public  function  set_cache($key, $value,$path=''){
        //优先按照设置路径中的来
        if($path){
            $save_path=$path;
        }
        //设置路径
       else if($this->config['cache_path']){
            $save_path=$this->config['cache_path'];
        }
        else{
            $save_path=$this->config['plugin_path'].'/cache';
        }
        mk_dir($save_path);
        $filename =$save_path . '/' . $key . '.html';
        if (is_array($value)) {
            $value = serialize($value);
        }
        //存入文件
        file_put_contents($filename, $value);
    }

     //取缓存
    public  function  get_cache($key,$path='',$expire = 0)
    {
        //
        if(!$expire){//如果没有失效时间，使用配置默认
         $expire=$this->config['expire'];
        }

        //优先按照设置路径中的来
        if($path){
            $save_path=$path;
        }
        //设置路径
        else if($this->config['cache_path']){
            $save_path=$this->config['cache_path'];
        }
        else{
            $save_path=$this->config['plugin_path'].'/cache';
        }
        //
        $filename =$save_path . '/' . $key . '.html';
        if(!file_exists($filename)){
              return false;
          }
        $state=@stat($filename);
        if($state){
            $time=$state['mtime']; //修改时间
            if($expire!=0&&time()-$time>$expire){
             return false;//缓存失效的情况
            }
            else{
                $content=file_get_contents($filename);
                if (!$content || strlen($content) < 2) {
                    return $content;
                }
                if (substr($content, 0, 2) == 'a:') {//数组需要反序列化
                    return unserialize($content);
                } else {
                    return $content;
                }
            }
        }
        else{
            return false;//文件不存在
        }
    }
     //生成指定长度的随机字符串，可用于签名
   public  function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
    //不转义json_encode 兼容高版本php
   public  function json_encode_no_zh($arr) {
        $str = str_replace ( "\\/", "/", json_encode ( $arr ) );
        $search = "#\\\u([0-9a-f]{4})#i";

        if (strpos ( strtoupper(PHP_OS), 'WIN' ) === false) {
            $callback=array($this,'json_encode_callback_lin');
        } else {
            $callback=array($this,'json_encode_callback_win');
        }
        return preg_replace_callback( $search, $callback, $str );
    }

  private  function  json_encode_callback_lin($param){
      $str=$param[1];
      return iconv('UCS-2BE', 'UTF-8', pack('H4', $str));//LINUX
  }
  private  function  json_encode_callback_win($param){
      $str=$param[1];
    return iconv('UCS-2', 'UTF-8', pack('H4', $str));//WINDOWS
  }

  function redirect($url){
        header('Location:'.$url);
    }
  //判断网络文件是否存在，例如图片文件
   function  http_file_exist($url){
       if( @fopen( $url, 'r' ) )//如果文件存在
       {
           return true;
       }
       return false;
   }
    //缩放图片，资源分为网络资源和本地资源，网络资源下载到本地，$save_path为保存路径,如果保存路径为空则直接输出
    public  function  resize_image($source,$max_width,$max_height,$save_path=null){
        /*
         *  * 网络资源返回
         * var_dump(getimagesize(@'D:\net\meirong2\mobile\8.PNG'));die;
 * array (size=7)
  0 => int 640
  1 => int 640
  2 => int 2
  3 => string 'width="640" height="640"' (length=24)
  'bits' => int 8
  'channels' => int 3
  'mime' => string 'image/jpeg' (length=10)
本地资源返回
array (size=6)
  0 => int 94
  1 => int 96
  2 => int 3
  3 => string 'width="94" height="96"' (length=22)
  'bits' => int 8
  'mime' => string 'image/png' (length=9)
 * */
            $remote=false;
            $tmp_path='';
        //判断网络文件或者实体文件的存在性
        if(substr($source,0,4)=='http'){
          if(!$this->http_file_exist($source)){
              return false; //文件不存在
          }
            //存储为本地临时图片获取资源后在删除图片即可
          $img_resource=file_get_contents($source);
            $new_source=BASE_PATH.'/cache/';
            mk_dir($new_source);
            $new_source.=md5($source);
          file_put_contents($new_source,$img_resource);
            $tmp_path= $source=$new_source;
            $remote=true;//原始资源是远程图片，以便后期删除临时
        }
        if(file_exists($source)){
            $size_src = getimagesize($source);
            $pic_width = $size_src['0'];
            $pic_height = $size_src['1'];
            $pic_type=$size_src['mime'];
            switch($pic_type){
                case 'image/png':
                    $function = 'imagepng';
                    $source = imagecreatefrompng($source);
                    break;
                case 'image/jpeg':
                    $source = imagecreatefromjpeg($source);
                    $function = 'imagejpeg';
                    break;
                case 'image/gif':
                    $function = 'imagegif';
                    $source = imagecreatefromgif($source);
                    break;
                default:
                    $source = imagecreatefromjpeg($source);
                    $function = 'imagejpeg';
                    break;

            }
            $new_width = $pic_width;
            $new_height = $pic_height;
            if ($max_width) { //只按照宽度缩放  //第二个参数是宽度
                $new_width = $max_width;
                $new_height = $pic_height * ($new_width / $pic_width);
            } else if ($max_height) {//只按照高度缩放 //mode后第二个参数是高度
                $new_height = $max_height;
                $new_width = $pic_width * ($new_height / $pic_height);
            } else { //按照最大的边缩放
                $max =$pic_width/2; //按照一半进行缩放
                //按照最大边自由缩放
                if ($pic_width > $pic_height) {
                    $new_width = $max;
                    $new_height = $pic_height * ($max / $pic_width);
                } else {
                    $new_height = $max;
                    $new_width = $pic_width * ($max / $pic_height);
                }
            }

            if (function_exists("imagecopyresampled"))//function_exists("imagecopyresampled") ,真彩图，速度较慢
            {
                $newim = imagecreatetruecolor($new_width, $new_height);
                imagecopyresampled($newim, $source, 0, 0, 0, 0, $new_width, $new_height, $pic_width, $pic_height);
            } else {
                $newim = imagecreate($new_width, $new_height);
                imagecopyresized($newim, $source, 0, 0, 0, 0, $new_width, $new_height, $pic_width, $pic_height);
            }

            $function($newim, $save_path);//
            imagedestroy($newim);
            if($remote&&file_exists($tmp_path)){
                unlink($tmp_path);
            }
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * @desc arraySort php二维数组排序 按照指定的key 对数组进行排序
     * @param array $arr 将要排序的数组
     * @param string $keys 指定排序的key
     * @param string $type 排序类型 asc | desc
     * @return array
     */
    function arraySort($arr, $keys, $type = 'asc') {
        $keys_value = $new_array = array();
        foreach ($arr as $k => $v){
            $keys_value[$k] = $v[$keys];
        }
        $type == 'asc' ? asort($keys_value) : arsort($keys_value);
        reset($keys_value);
        foreach ($keys_value as $k => $v) {
            $new_array[$k] = $arr[$k];
        }
        return $new_array;
    }
    //检测数组中是否有重复项
    function check_repeat($nary){
        //检测重名
        sort($nary);
        $count=count($nary)-1;
        for($i=0;$i<$count;$i++){
            if($nary[$i]==$nary[$i+1]){
                return $nary[$i];
            }
        }
        return false;
    }

    //根据ip获取地址 返回type=1 完整 2精简
    function  get_ip_address($ip='',$return_type=1){
        if(!$ip){
            return false;
        }
            if($ip){
                $url="http://ip.taobao.com/service/getIpInfo.php?ip=".$ip;
                $ip=json_decode(file_get_contents($url),true);
                if($ip['code']=='1'){
                    return false;
                }
                $data =$ip['data'];
                if($return_type==2){
                    $data=$data['region'].$data['city'].$data['isp'];
                }

            }else{
                $url = "http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json";
                $ip=json_decode(file_get_contents($url),true);
                $data = $ip;
            }
            return $data;
    }

    function  gbk_to_utf8($data=''){
        return  iconv("UTF-8", "GB2312//IGNORE", $data);

    }
    function  utf8_to_gbk($data=''){
        return  iconv( "GB2312//IGNORE","UTF-8", $data);
    }

    function utf8_to_unicode($utf8_str) {
        $unicode = 0;
        $unicode = (ord($utf8_str[0]) & 0x1F) << 12;
        $unicode |= (ord($utf8_str[1]) & 0x3F) << 6;
        $unicode |= (ord($utf8_str[2]) & 0x3F);
        return dechex($unicode);
    }

    /**
     * Unicode字符转换成utf8字符
     * @param  [type] $unicode_str Unicode字符
     * @return [type]              Utf-8字符
     */
    function unicode_to_utf8($unicode_str) {
        $utf8_str = '';
        $code = intval(hexdec($unicode_str));
        //这里注意转换出来的code一定得是整形，这样才会正确的按位操作
        $ord_1 = decbin(0xe0 | ($code >> 12));
        $ord_2 = decbin(0x80 | (($code >> 6) & 0x3f));
        $ord_3 = decbin(0x80 | ($code & 0x3f));
        $utf8_str = chr(bindec($ord_1)) . chr(bindec($ord_2)) . chr(bindec($ord_3));
        return $utf8_str;
    }

    function  unicode_to_gbk($unicode_str){
        $str = str_replace ( "\\/", "/", $unicode_str);
        $search = "#\\\u([0-9a-f]{4})#i";

        if (strpos ( strtoupper(PHP_OS), 'WIN' ) === false) {
            $callback=array($this,'json_encode_callback_lin');
        } else {
            $callback=array($this,'json_encode_callback_win');
        }
        return preg_replace_callback( $search, $callback, $str );
    }
    //反序列化session字符串
    function  unserial_session($session_str=''){

        if(!$session_str){return array();}
        $str=$session_str;
        $arr1=explode('|',$str);
        $arr_len=count($arr1);
        $last_key='';
        $resolve_session=array();
        foreach($arr1 as $key=> $value){
            if($key==0){//第一个只有键
                $last_key=$value;
            }
            else if($key==$arr_len-1){ //最后一个只有值
                $resolve_session[$last_key]=unserialize($value);
            }
            else{
                if(substr($value,0,1)=='a'){ //数组
                    $pos=strrpos($value,'}');
                }
                else{
                    $pos=strrpos($value,';');
                }

                $val=substr($value,0,$pos+1);
                $resolve_session[$last_key]=unserialize($val);
                $last_key=substr($value,$pos+1);
            }
        }
        return $resolve_session;
    }
    //二维数组排序功能
    //$array 要排序的数组
    //$row  排序依据列
    //$type 排序类型[asc or desc]
    //return 排好序的数组
    //相同键值的时候有问题
    function array_sort($array,$row,$type){
        if(!is_array($array)){return array();}

        $sort = array();
        foreach ($array as $value) {
            $sort[] = $value[$row];
        }
        if($type=='asc'){
            $type=SORT_ASC;
        }
        else{
            $type=SORT_DESC;
        }
        array_multisort($sort, $type, $array);

         /*
        $array_temp = array();
        foreach($array as $v){
            $array_temp[$v[$row]] = $v;
        }
        if($type == 'asc'){
            ksort($array_temp);
        }elseif($type='desc'){
            krsort($array_temp);
        }else{
        }
        return $array_temp;
        */
        return $array;
     }
    //获取星期
    function  get_week($stamp,$type=1){
        //获取数字型星期几
        $number_wk=date("w",$stamp);
        $weekArr=array("日","一","二","三","四","五","六");
        //自定义星期数组
        if($type==1){
          $prefix='星期';
        }
        else if($type==2){
         $prefix='周';
        }
        else{
            $prefix='';
        }

        //获取数字对应的星期
       return $prefix. $weekArr[$number_wk];
    }

}