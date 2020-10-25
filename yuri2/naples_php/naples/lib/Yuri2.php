<?php
/**
 * 独立函数静态类
 * 不会依赖外部
 * 带有缓存（可靠）
 * @author yuri2
 */
class Yuri2{

    /** 内部缓存 begin---------------------------------------------------------------------------------------------------------------------- */
    private static $cache=[]; //静态类缓存，将大大提高函数执行效率。例如多次判断当前IP，其实只要判断一次，然后把IP缓存至此。

    /**
     * 是否缓存了结果
     * @access private
     * @param $key string
     * @return bool
     * @author yuri2
     */
    private static function isCached($key){
       if (isset(self::$cache[$key]))
           return true;
       else
           return false;
    }

    /**
     * 使用缓存
     * @access private
     * @param $key string
     * @param $value mixed
     * @return mixed
     * @author yuri2
     */
    private static function useCache($key, $value=FLAG_NOT_SET){
        if ($value!==FLAG_NOT_SET){
            self::$cache[$key]=$value;
            return true;
        }else{
            return self::$cache[$key];
        }
    }
    /** 内部缓存 end---------------------------------------------------------------------------------------------------------------------- */



    /** 常用函数---------------------------------------------------------------------------------------------------------------------- */

    /**
     * 加锁写入
     * @param $path string
     * @param $mode int
     * @param $data string
     * @return bool
     */
    public static function writeData( $path , $mode , $data ){
        $retries = 0;
        $max_retries = 1000;
        do {
            if ( $retries > 0)
            {
                usleep(rand(1, 10000));
            }
            $retries += 1;
        } while (!$fp = fopen ( $path , $mode ) and $retries <= $max_retries );
        do {
            if ( $retries > 0)
            {
                usleep(rand(1, 10000));
            }
            $retries += 1;
        } while (! flock ( $fp , LOCK_EX) and $retries <= $max_retries );
        if ( $retries == $max_retries )
        {
            return false;
        }
        fwrite( $fp , "$data" );
        flock ( $fp , LOCK_UN);
        fclose( $fp );
        return true;
    }

    /**
     * 父子ID类数据转换为树形数据
     * @param $data array
     * @param $id_name string
     * @param $pid_name string
     * @param $children_name string
     * @param $root_id string
     * @param $ergodicFunction Closure|bool
     * @return array|bool
     */
    public static function dataToTree($data,$id_name='id',$pid_name='pid',$children_name='children',$root_id='0',$ergodicFunction=false){
        $data_assoc=[];
        $max=50;
        $nodes=[];
        foreach ($data as $item){
            if (isset($item[$id_name])){
                $data_assoc[$item[$id_name]]=$item;
            }
        }
        if (!isset($data_assoc[$root_id])){
            return false; //找不到根节点
        }
        $tree=[];
        self::arrGetSet($tree,$root_id,$data_assoc[$root_id]); //添加根节点
        $nodes[$root_id]=[$root_id]; //记录根节点的路径
        while (count($data_assoc)>0){
            foreach ($data_assoc as $k=>$v){
                if (isset($nodes[$k])){continue;}
                else{
                    if (in_array($v[$pid_name],array_keys($nodes))){
                        //如果父节点已经被收录
                        $path_arr=$nodes[$v[$pid_name]];
                        array_push($path_arr,$k);
                        $nodes[$k]=$path_arr;
                        $path=implode('.'.$children_name.'.',$path_arr);
                        self::arrGetSet($tree,$path,$data_assoc[$k]); //添加子节点
                        unset($data_assoc[$k]); //从列表移除
                    }
                }
            }
            $max--;
            if ($max<0){
                break;
            }
        }
        if ($ergodicFunction){
            self::ergodicTree($tree,$ergodicFunction,$id_name,$pid_name,$children_name);
        }
        return $tree;
    }

    /**
     * 遍历树形结构
     * @param $data array
     * @param $ergodicFunction Closure
     * @param $id_name string
     * @param $pid_name string
     * @param $children_name string
     * @param $parent array|bool 父节点的引用
     */
    public static function ergodicTree(&$data,$ergodicFunction,$id_name='id',$pid_name='pid',$children_name='children',&$parent=false){
        foreach ($data as $k =>&$v){
            $ergodicFunction($v,$parent); //对当前结点操作
            if (isset($v[$children_name])){
                self::ergodicTree($v[$children_name],$ergodicFunction,$id_name,$pid_name,$children_name,$v);
            }
        }
    }

    /**
     * 发送子框架刷新父窗口的js代码
     */
    public static function refreshFather(){
        echo '<script>window.parent.window.location.reload();</script>';
    }

    /**
     * 判断变量是否可打印
     * @param $var mixed
     * @return mixed
     */
    static function isEchoAble($var){
        if (is_string($var) or is_numeric($var) or is_bool($var) or is_null($var)){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * 更聪明的echo 将输出最适合的格式
     * @param $var mixed
     * @param $filters string
     * @return void
     */
    static function smarterEcho($var, $filters = '|')
    {
        $filters=self::explodeWithoutNull($filters,'|');
        if (self::isEchoAble($var)){
            if ($var===true){
                echo 'true';
            }elseif ($var===false){
                echo 'false';
            }elseif (is_null($var)){
                echo '';
            }else{

                if (in_array('date',$filters)){
                    $var=date('Y/m/d H:i:s',$var);
                }
                if (in_array('bool',$filters)){
                    $var = (($var === 1 or $var === '1' or $var == 'true' or $var == 'yes' or $var == '是') ? '是' : '否');
                }
                if (in_array('percent',$filters)){
                    $var= sprintf("%01.2f", $var*100).'%';
                }
                if (in_array('e',$filters)){
                    $var=htmlspecialchars($var);
                }
                echo $var;
            }
        }elseif (is_array($var)){
            if (in_array('json',$filters)){
                $var= json_encode($var);
            }elseif (in_array('xml',$filters)){
                $var= Yuri2::arrayToXml($var);
            }else{
                $var=var_export($var,true);
            }
            if (in_array('e',$filters)){
                $var=htmlspecialchars($var);
            }
            echo $var;
        }
        else{
            self::dump($var);
        }
    }

    /**
     * 驼峰法转下划线法
     * Convert a namespace to the standard PEAR underscore format.
     *
     * Then convert a class name in CapWords to a table name in
     * lowercase_with_underscores.
     *
     * Finally strip doubled up underscores
     *
     * For example, CarTyre would be converted to car_tyre. And
     * CarTyre would be car_tyre.
     *
     * @param  string $CamelCase
     * @return string
     */
    static function Camel_to_UnderScore($CamelCase){
        return strtolower(preg_replace(
            array('/(?<=[a-z])([A-Z])/', '/__/'),
            array('_$1', '_'),
            $CamelCase
        ));
    }

    /**
     * 下划线法转驼峰法
     * @param $str string
     * @param $ucfirst bool 是否大写开头
     * @return string
     */
    static function UnderScore_to_Camel ( $str , $ucfirst = true)
    {
        $str = ucwords(str_replace('_', ' ', $str));
        $str = str_replace(' ','',lcfirst($str));
        return $ucfirst ? ucfirst($str) : $str;
    }

    /**
     * 字符串为正则做转义
     * @param $str string
     * @return string
     */
    static function strForPreg($str){
        $rules='*.?+$^[](){}|\/';
        $len=strlen($str);
        $rel='';
        for ($i=0;$i<$len;$i++){
            $char=$str{$i};
            if (strstr($rules,$char)){
                $rel.='\\'.$char;
            }else{
                $rel.=$char;
            }
        }
        return $rel;
    }

    /** 
     * 字符串加上php标签
     * @param $str string
     * @return string
     */
    static function addPhpTag($str){
        return "<?php $str ?>";
    }
    
    /**
     * 获取协议头
     * @return string
     * @author yuri2
     */
    public static function getHttpType(){
        if(self::isCached('getHttpType')){return self::useCache('getHttpType');}
        $rel=  ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https' : 'http';
        self::useCache('getHttpType',$rel);
        return $rel;
    }

    /**
     * 获取域名
     * @return string host
     */
    public static function getHost(){
        if(self::isCached('getHost')){return self::useCache('getHost');}
        if (isset($_SERVER['HTTP_HOST'])){
            $host=$_SERVER['HTTP_HOST'];
            $host=preg_replace('/:\d+$/','',$host);
        }else{
            $host=$_SERVER['SERVER_NAME'];
        }
        self::useCache('getHost',$host);
        return $host;
    }

    /**
     * 根据当前域名判断是否是本地环境
     */
    public static function isLocal(){
        if(self::isCached('isLocal')){return self::useCache('isLocal');}
        $host=self::getHost();
        $rel=($host=='localhost' or $host=='127.0.0.1')?true:false;
        self::useCache('isLocal',$rel);
        return $rel;
    }

    /**
     * 浏览器友好的变量输出
     * @param mixed         $var 变量
     * @param boolean       $echo 是否输出 默认为true 如果为false 则返回输出字符串
     * @param string        $label 标签 默认为空
     * @param integer       $flags htmlspecialchars flags
     * @return void|string
     * @author tp5
     */
    public static function dump($var, $echo = true, $label = null, $flags = ENT_SUBSTITUTE)
    {
        $label = (null === $label) ? '' : rtrim($label) . ':';
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
        if (PHP_SAPI == 'cli') {
            $output = PHP_EOL . $label . $output . PHP_EOL;
        } else {
            if (!extension_loaded('xdebug')) {
                $output = htmlspecialchars($output, $flags);
            }
            $output = '<pre>' . $label . $output . '</pre>';
        }
        if ($echo) {
            echo ($output);
            return null;
        } else {
            return $output;
        }
    }

    /**
     * 反复调用此函数，返回值依次为 1 , 2, 3...
     * @return int
     * @author yuri2
     */
    public static function getIntNoOnce(){
        if (isset(self::$cache['getIntNoOnce'])){
            self::$cache['getIntNoOnce']++;
            return self::$cache['getIntNoOnce'];
        }else{
            self::$cache['getIntNoOnce']=1;
            return 1;
        }
    }

    /**
     * 抛出一个带信息的异常
     * @param $message string 异常信息
     * @throws \Exception
     * @author yuri2
     */
    public static function throwException($message){
        throw new \Exception($message);
    }

    /**
     * 判断是否为ajax请求
     * @return bool
     * @author yuri2
     */
    public static function isAjax (){
        if (self::isCached('isAjax')){return self::useCache('isAjax');}//直接返回缓存值
        if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"])=="xmlhttprequest"){
            // ajax 请求的处理方式
            self::useCache('isAjax',true); //保存结果到缓存
            return true;
        }else{
            // 正常请求的处理方式
            self::useCache('isAjax',false); //保存结果到缓存
            return false;
        }

    }

    /**
     * 判断是否为post请求
     * @return bool
     * @author yuri2
     */
    public static function isPost (){
        if (self::isCached('isPost')){return self::useCache('isPost');}//直接返回缓存值
        if (isset($_SERVER['REQUEST_METHOD']) and $_SERVER['REQUEST_METHOD']=='POST') {
            self::useCache('isPost',true); //保存结果到缓存
            return true;
        }else{
            self::useCache('isPost',false); //保存结果到缓存
            return false;
        }
    }

    /**
     * 判断是否为get请求
     * @return bool
     * @author yuri2
     */
    public static function isGet (){
        if (self::isCached('isGet')){return self::useCache('isGet');}//直接返回缓存值
        if (isset($_SERVER['REQUEST_METHOD']) and $_SERVER['REQUEST_METHOD']=='GET') {
            self::useCache('isGet',true); //保存结果到缓存
            return true;
        }else{
            self::useCache('isGet',false); //保存结果到缓存
            return false;
        }
    }

    /**
     * 判断是否为pjax请求
     * @return bool
     * @author yuri2
     */
    public static function isPjax (){
        if (self::isCached('isPjax')){return self::useCache('isPjax');}//直接返回缓存值
        $result = !is_null($_SERVER['HTTP_X_PJAX']) ? true : false;
        self::useCache('isPjax',$result); //保存结果到缓存
        return $result;
    }

    /**
     * 判断是否是索引数组
     * @param $array array
     * @return bool
     */
    public static function isAssoc($array) {
        if(is_array($array)) {
            $keys = array_keys($array);
            return $keys != array_keys($keys);
        }
        return false;
    }

    /**
     * 调用反射执行类的方法 支持参数绑定 （依赖bindParams）
     * @access public
     * @param \ReflectionMethod |\ReflectionFunction $method 方法
     * @param array        $vars   变量
     * @param object        $obj   方法执行对象
     * @param bool        $flag   设为true，则不会抛出异常，返回FLAG_NOT_SET
     * @return mixed
     * @author yuri2
     */
    public static function invokeMethod($method, $vars = [],$obj=null,$flag=false)
    {

        $args = self::bindParams($method, $vars,$flag);
        if ($args===FLAG_NOT_SET and $flag){return FLAG_NOT_SET;}
        $refType=get_class($method);
        if ($refType=='ReflectionMethod'){
            return $method->invokeArgs(!empty($obj) ? $obj : null, $args);
        }elseif ($refType=='ReflectionFunction'){
            return $method->invokeArgs($args);
        }else{
            return null;
        }
    }

    /**
     * 绑定参数 （被invokeMethod调用）
     * @access public
     * @param \ReflectionMethod|\ReflectionFunction $reflect 反射类
     * @param array             $vars    变量
     * @param bool        $flag   设为true，则不会抛出异常，返回FLAG_NOT_SET
     * @throws \Exception
     * @return array
     * @author yuri2
     */
    public static function bindParams($reflect, $vars = [],$flag=false)
    {

        $args = [];
        // 判断数组类型 数字数组时按顺序绑定参数
        reset($vars);
        $type = key($vars) === 0 ? 1 : 0;
        if ($reflect->getNumberOfParameters() > 0) {
            $params = $reflect->getParameters();
            foreach ($params as $param) {
                $name  = $param->getName();

                if (1 == $type && !empty($vars)) {
                    $args[] = array_shift($vars);
                } elseif (0 == $type && isset($vars[$name])) {
                    $args[] = $vars[$name];
                } elseif ($param->isDefaultValueAvailable()) {
                    $args[] = $param->getDefaultValue();
                } else {
                    if ($flag){return FLAG_NOT_SET;}
                    throw new \Exception('method param miss:' . $name);
                }
            }
        }
        return $args;
    }

    /**
     * 创建多级目录
     * @param $path string 目标路径
     * @param $mode int 权限
     * @return bool 是否成功
     * @author yuri2
     * */
    public static function createDir($path,$mode=0775){
        $path=self::autoSysCoding($path);
        if (is_dir($path)){  //判断目录存在否，存在不创建
            return true;
        }else{ //不存在则创建
            $re=@mkdir($path,$mode,true); //第三个参数为true即可以创建多极目录
            if ($re){ return true;}
            else{return false;}
        }
    }

    /**
     * 查看这个路径是几级的(其实就是计数分隔符/或者\)
     * @param $path string 目标路径
     * @return int 层级数目
     * @author yuri2
     */
    public static function dirLevel($path){
        $s1=substr_count($path,'/');
        $s2=substr_count($path,'\\');
        return $s1+$s2;
    }

    /**
     * 分割字符串为数组，过滤空值
     * @param $str string 目标字符串
     * @param $delimiter string 过滤值 默认为空
     * @return array 返回非空元素数组
     */
    public static function explodeWithoutNull($str, $delimiter=' '){
        $arr=explode($delimiter,$str);
        $rel=[];
        foreach ($arr as $v){
            if ($v!=''){
                $rel[]=$v;
            }
        }
        return $rel;
    }

    /**
     * 删除目录及之下的文件
     * @param $dir string 目标目录
     * @param $flag string flag 别管他
     * @return bool 是否成功
     * @author yuri2
     */
    public static function delDir($dir,$flag='first',$remain_self=false) {
        //先删除目录下的文件：
        $dir=self::autoSysCoding($dir);
        if (!is_dir($dir)){return true;}
        $dh=opendir($dir);$rel=true;
        while ($file=readdir($dh)) {
            if($file!="." && $file!="..") {
                $fullPath=$dir.self::autoSysCoding("/").$file;
                if(!is_dir($fullPath)) {
                    unlink($fullPath);
                } else {
                    $rel=self::delDir($fullPath,'not first');
                }
            }
        }
        closedir($dh);
        //删除当前文件夹?
        if($flag=='not first' or $flag===true){
            if(rmdir($dir)) {
                return true;
            } else {
                return false;
            }
        }
        if ($flag=='first'){
            if (!$remain_self)
                return rmdir($dir);
            else
                return true;
        }else{
            return false;
        }
    }

    /**
     * 遍历文件夹
     * @param $dir string
     * @param $callable callable
     * @return array
     */
    public static function ergodicDir($dir,$callable){
        $dir=self::autoSysCoding($dir);
        $rel=[];
        $dh=opendir($dir);
        while ($file=readdir($dh)) {
            if($file!="." && $file!="..") {
               $rel[]= $callable($file);
            }
        }
        closedir($dh);
        return $rel;
    }

    /**
     * 递归遍历目录及之下的文件
     * @param $dir string 目标目录
     * @param $funcFile callable
     * @param $funcDir callable|string
     * @author yuri2
     */
    public static function ergodicDirRecursion($dir,$funcFile,$funcDir='') {
        $dir=self::autoSysCoding($dir);
        $dh=opendir($dir);
        while ($file=readdir($dh)) {
            if($file!="." && $file!="..") {
                $fullPath=$dir.self::autoSysCoding("/").$file;
                if(is_dir($fullPath)) {
                    $relDir=$funcDir?$funcDir($fullPath):true;
                    if ($relDir and is_dir($fullPath)){
                        //递归
                        self::ergodicDirRecursion($fullPath,$funcFile,$funcDir);
                    }
                } else {
                    $relFile=$funcFile($fullPath);
                }
            }
        }
        closedir($dh);
    }

    /**
     * 正则匹配删除指定后缀
     * @param $str string 目标字符串
     * @param $suffix string 要删除的后缀
     * @return string 结果
     * @author yuri2
     */
    public static function removeSuffix($str,$suffix){
        $suffix=str_replace('.','\.',$suffix);
        $reg="/$suffix$/is";
        $rel=  preg_replace($reg, "", $str);
        return $rel;
    }

    /**
     * 在网页右下角打印一个耗费时间提示框（单位 ms）
     * @param $time int 耗时
     * @author yuri2
     */
    public static function timeAttention($time){
        $time=round($time*1000,2);
        $color='orange';
        if ($time<10){$color='green';}
        if ($time>20){$color='red';}
        $time.=' ms';
        echo' <naplesSpendTime style="display: block;position: fixed;bottom: 5px;right: 5px;width:6em;height:16px;font-size: 13px;color: '.$color.';border-radius: 4px;background-color: rgb(40,40,40);text-align: center;line-height: 16px;border:1px solid '.$color.'">
            '.$time.'
        </naplesSpendTime>';
    }

    /**
     * 快速 获取/设置 全局数组变量的值 如 $v=arrPublic('get.user.id') ; $rel=arrPublic('get.user.id','2333')
     * @param $target string 目标字符串
     * @param $value mixed 要设置的值
     * @return mixed 结果(不存在会返回空)
     * @author yuri2
     */
    public static function arrPublic($target,$value=FLAG_NOT_SET){
        $arrPath=self::explodeWithoutNull($target,'.');
        $target=array_shift($arrPath);
        switch ($target){
            case 'get':
                $arrMain=&$_GET;
                break;
            case 'post':
                $arrMain=&$_POST;
                break;
            case 'request':
                $arrMain=&$_REQUEST;
                break;
            case 'server':
                $arrMain=&$_SERVER;
                break;
            case 'p':
                if (!isset($_SERVER['naples_p'])){$_SERVER['naples_p']=[];}
                $arrMain=&$_SERVER['naples_p'];
                break;
            case 'cookie':
                $arrMain=&$_COOKIE;
                break;
            case 'global':
                if(!isset($_SERVER['global'])){
                    $_SERVER['global']=[];
                }
                $arrMain=&$_SERVER['global'];
                break;
            case 'session':
                if(!isset($_SESSION)){
                    session_start();
                }
                $arrMain=&$_SESSION;
                break;
            default:
                return null;
        }
        return self::arrGetSet($arrMain,$arrPath,$value,null);

    }

    /**
     * 快速 获取/设置 数组变量的值 如 $v=arrGetSet($arr1,'user.id') ; $rel=arrGetSet($arr1,'user.id','2333')
     * @param $hd array 目标数组
     * @param $path string or array 寻找的路径
     * @param $value1 mixed 要设置的值
     * @param $value2 mixed 额外参数(高级用法：arrGetSet($arr1,'user.id','+=',1)          )
     * @return mixed 结果
     * @author yuri2
     */
    public static function arrGetSet(&$hd, $path='', $value1=FLAG_NOT_SET, $value2=null){
        $arrPath=is_array($path)?$path:self::explodeWithoutNull($path,'.');
        if ($value1===FLAG_NOT_SET){
            foreach ($arrPath as $item) {
                $hd=&$hd[$item];
            }
            return $hd;
        }else{
            foreach ($arrPath as $item) {
                $hd=&$hd[$item];
            }
            if ($value2){
                switch ($value1){
                    case '+=':
                        $hd+=$value2;
                        break;
                    case '-=':
                        $hd-=$value2;
                        break;
                    case '*=':
                        $hd*=$value2;
                        break;
                    case '/=':
                        $hd/=$value2;
                        break;
                    case '%=':
                        $hd%=$value2;
                        break;
                    case '.=':
                        $hd.=$value2;
                        break;
                    case '=+':
                        $hd=$value2+$hd;
                        break;
                    case '=-':
                        $hd=$value2-$hd;
                        break;
                    case '=*':
                        $hd=$value2*$hd;
                        break;
                    case '=/':
                        $hd=$value2/$hd;
                        break;
                    case '=%':
                        $hd=$value2%$hd;
                        break;
                    case '=.':
                        $hd=$value2.$hd;
                        break;
                    default:
                        return false;
                }
            }
            else{
                $hd=$value1;
            }
            return true;
        }
    }

    /**
     * 拷贝目录到一个新目录
     * @param $src string 源目录
     * @param $dst string 目标目录
     * @param $mode string 递归标记，不用管
     * @return bool 是否成功
     * @author yuri2
     */
    public static function recurseCopy($src, $dst, $mode='first') {
        $src=self::autoSysCoding($src);
        $dst=self::autoSysCoding($dst);
        $s=self::autoSysCoding('/');
        if ($mode=='first'){
            //先创建一个目录
            $dir=basename($src);
            if (!file_exists($dir)){
                if(!@mkdir($dst.$s.$dir)){return false;}
            }
            $dst=$dst.$s.$dir;
        }
        $dir = opendir($src);
        @mkdir($dst);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != $s ) && ( $file != '..' )) {
                if ( is_dir($src . $s . $file) ) {
                    if(!self::recurseCopy($src . $s . $file,$dst . $s . $file,'not_first')){return false;}
                }
                else {
                    copy($src . $s . $file,$dst . $s . $file);
                }
            }
        }
        closedir($dir);
        return true;
    }

    /**
     * 立即重定向，还可以在后面添加需要get出去的数组，将自动按照url格式生成get
     * @param $url string
     * @param $params array 需要发送get的关联数组
     * @param $code int 需要发送的状态码
     * @author yuri2
     */
    public static function redirect($url,$params=[],$code=302){
        if ($params){
            $query = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
            $url .= (strpos($url, '?') ? '&' : '?') . $query;
        }
        http_response_code($code);
        header("Location: $url");
        exit();
    }

    /**
     * echo 一个js alert()
     * @param $msg string 警告内容
     * @author yuri2
     */
    public static function jsAlert($msg=''){
        echo "<script>alert('$msg')</script>";
    }

    /**
     * 获取IP
     * @return string IP
     * @author yuri2
     */
    public static function getIp(){
        if (self::isCached('getIp')){return self::useCache('getIp');}//直接返回缓存值
        $user_IP = (isset($_SERVER["HTTP_VIA"]) and $_SERVER["HTTP_VIA"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];
        $user_IP = ($user_IP)?$user_IP : $_SERVER["REMOTE_ADDR"];
        self::useCache('getIp',$user_IP);
        return $user_IP;
    }

    /**
     * 获取扩展名，不含.符号
     * @param $fileName string 一个合法的文件名
     * @return string 扩展名
     * @author yuri2
     */
    public static function getExtension($fileName){
        $fileName=self::autoEncoding($fileName);
        $arr=self::explodeWithoutNull($fileName,'/');
        $fileName=array_pop($arr);
        $arr=self::explodeWithoutNull($fileName,'\\');
        $fileName=array_pop($arr);
        if ( substr_count($fileName, '.')==0){
            return '';
        }
        $arr=self::explodeWithoutNull($fileName,'.');
        $ext=array_pop($arr);
        return $ext;
    }

    /**
     * GET 请求
     * @param string $url
     * @return string content
     */
    public static  function http_get($url){
        $oCurl = curl_init();
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if(intval($aStatus["http_code"])==200){
            return $sContent;
        }else{
            return false;
        }
    }

    /**
     * POST 请求
     * @param string $url
     * @param array $param
     * @param boolean $post_file 是否文件上传
     * @return string content
     */
    public static  function http_post($url,$param=[],$post_file=false){
        $oCurl = curl_init();
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        if (is_string($param) || $post_file) {
            $strPOST = $param;
        } else {
            $aPOST = array();
            foreach($param as $key=>$val){
                $aPOST[] = $key."=".urlencode($val);
            }
            $strPOST =  join("&", $aPOST);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($oCurl, CURLOPT_POST,true);
        curl_setopt($oCurl, CURLOPT_SAFE_UPLOAD, false);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if(intval($aStatus["http_code"])==200){
            return $sContent;
        }else{
            return false;
        }
    }

    /**
     * https 发起post多发请求
     * 2016/5/25, by CleverCode, Create
     * @param array $nodes url和参数信息。$nodes = array
     *                                              (
     *                                                 [0] = > array
     *                                                   (
     *                                                       'url' => 'http://www.baidu.com',
     *                                                       'data' => '{"a":1,"b":2}'
     *                                                   ),
     *                                                 [1] = > array
     *                                                   (
     *                                                       'url' => 'http://www.baidu.com',
     *                                                   )
     *                                                 ....
     *                                              )
     * @param int $timeOut 超时设置
     * @return array
     */
    public static function httpMulti($nodes,$timeOut = 5)
    {/*{{{*/
        try
        {
            if (false == is_array($nodes))
            {
                return array();
            }

            $mh = curl_multi_init();
            $curlArray = array();
            foreach($nodes as $key => $info)
            {
                if(false == is_array($info))
                {
                    continue;
                }
                if(false == isset($info['url']))
                {
                    continue;
                }

                $ch = curl_init();
                // 设置url
                $url = $info['url'];
                curl_setopt($ch, CURLOPT_URL, $url);

                $data = isset($info['data']) ? $info['data'] :null;
                if(false == empty($data))
                {
                    curl_setopt($ch, CURLOPT_POST, 1);
                    // array
                    if (is_array($data) && count($data) > 0)
                    {
                        curl_setopt($ch, CURLOPT_POST, count($data));
                    }
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                }

                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                // 如果成功只将结果返回，不自动输出返回的内容
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                // user-agent
                curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:22.0) Gecko/20100101 Firefox/22.0");
                // 超时
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeOut);

                $curlArray[$key] = $ch;
                curl_multi_add_handle($mh, $curlArray[$key]);
            }

            $running = NULL;
            do {
                usleep(10000);
                curl_multi_exec($mh,$running);
            } while($running > 0);

            $res = array();
            foreach($nodes as $key => $info)
            {
                $res[$key] = curl_multi_getcontent($curlArray[$key]);
            }
            foreach($nodes as $key => $info){
                curl_multi_remove_handle($mh, $curlArray[$key]);
            }
            curl_multi_close($mh);
            return $res;
        }
        catch ( Exception $e )
        {
            return array();
        }

        return array();
    }/*}}}*/

    /**
     * 数组转XML
     * @param $arr
     * @return string
     * @author yuri2
    */
    public static function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }

    /**
     * XML转数组
     * @param $xml string xml
     * @return array
     * @author yuri2
     */
    public static function xmlToArray($xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $values;
    }

    /**
     * 生成唯一的id 唯一性相当不错，几年内是不会重复的
     * @return string 唯一ID
     * @author yuri2
     */
    public static function uniqueID(){
        //TODO 是否要保证字母开头？
        return md5(uniqid(md5(microtime(true)),true));
    }

    /**
     * 模拟添加数据到Request数组
     * @param $key string 键名
     * @param $value mixed 键值
     * @param $mode string get/post/both 自动有优先级处理
     * @author yuri2
     */
    public static function addRequest($key,$value,$mode='get'){
        switch ($mode){
            case 'get':
                $_GET[$key]=$value;
                if (!isset($_REQUEST[$key]))
                    $_REQUEST[$key]=$value;
                break;
            case 'post':
                $_POST[$key]=$value;
                $_REQUEST[$key]=$value;
                break;
            case 'both':
                $_POST[$key]=$value;
                $_GET[$key]=$value;
                $_REQUEST[$key]=$value;
                break;
            default:
                break;
        }
    }


    //-------------------------------------------------------------------------------------

    const ENC_KEY='naples';//默认key

    /**
     * authCode 加密/解密
     * @param $string string 明文 或 密文
     * @param $operation string D表示解密,其它表示加密
     * @param $key string 密匙
     * @param $expiry int 密文有效期
     * @return string
     * @author yuri2
     */
    public static function authCode($string, $operation = 'D', $key = '', $expiry = 0) {
        // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
        $ckey_length = 4;
        // 密匙
        $key = md5($key ? $key : 'naples');
        // 密匙a会参与加解密
        $keya = md5(substr($key, 0, 16));
        // 密匙b会用来做数据完整性验证
        $keyb = md5(substr($key, 16, 16));
        // 密匙c用于变化生成的密文
        $keyc = $ckey_length ? ($operation == 'D' ? substr($string, 0, $ckey_length):
            substr(md5(microtime()), -$ckey_length)) : '';
        // 参与运算的密匙
        $cryptkey = $keya.md5($keya.$keyc);
        $key_length = strlen($cryptkey);
        // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，解密时会通过这个密匙验证数据完整性
        // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
        $string = $operation == 'D' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
        $string_length = strlen($string);
        $result = '';
        $box = range(0, 255);
        $rndkey = array();
        // 产生密匙簿
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }
        // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
        for($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        // 核心加解密部分
        for($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            // 从密匙簿得出密匙进行异或，再转成字符
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if($operation == 'D') {
            // substr($result, 0, 10) == 0 验证数据有效性
            // substr($result, 0, 10) - time() > 0 验证数据有效性
            // substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16) 验证数据完整性
            // 验证数据有效性，请看未加密明文的格式
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) &&
                substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
            // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
            return $keyc.str_replace('=', '', base64_encode($result));
        }
    }

    /**
     * 加密（依赖authCode）
     * @param $string  string 需要加密的文字
     * @param $key string 密钥
     * @param $expiry int 有效期
     * @return  string 密文
     * @author yuri2
     */
    public static function encrypt($string,$key=self::ENC_KEY,$expiry=99999999){
        if ($key!=self::ENC_KEY){$key.=self::ENC_KEY;}
        return self::authCode($string,'E',$key,$expiry);
    }

    /**
     * 解密（依赖authCode）
     * @param $string  string 需要解密的文字
     * @param $key string 密钥
     * @return  string 明文
     * @author yuri2
     */
    public static function decrypt($string,$key=self::ENC_KEY){
        if ($key!=self::ENC_KEY){$key.=self::ENC_KEY;}
        return self::authCode($string,'D',$key);
    }

    //-------------------------------------------------------------------------------------

    /**
     * 获得操作系统名
     * @return string 操作系统名
     * @author yuri2
     */
    public static function getOS(){
        if (self::isCached('getOS')){return self::useCache('getOs');}
        $os='';
        $Agent=isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'';
        if (preg_match('/win/',$Agent)&&strpos($Agent, '95')){
            $os='Windows 95';
        }elseif(preg_match('/win 9x/i',$Agent)&&strpos($Agent, '4.90')){
            $os='Windows ME';
        }elseif(preg_match('/win/i',$Agent)&&preg_match('/98/',$Agent)){
            $os='Windows 98';
        }elseif(preg_match('/win/i',$Agent)&&preg_match('/nt 5.0/i',$Agent)){
            $os='Windows 2000';
        }elseif(preg_match('/win/i',$Agent)&&preg_match('/nt 6.0/i',$Agent)){
            $os='Windows Vista';
        }elseif(preg_match('/win/i',$Agent)&&preg_match('/nt 6.1/i',$Agent)){
            $os='Windows 7';
        }elseif(preg_match('/win/i',$Agent)&&preg_match('/nt 5.1/i',$Agent)){
            $os='Windows XP';
        }elseif(preg_match('/win/i',$Agent)&&preg_match('/nt/i',$Agent)){
            $os='Windows NT';
        }elseif(preg_match('/win/i',$Agent)&&preg_match('/32/',$Agent)){
            $os='Windows 32';
        }elseif(preg_match('/linux/i',$Agent)){
            $os='Linux';
        }elseif(preg_match('/unix/i',$Agent)){
            $os='Unix';
        }else if(preg_match('/sun/i',$Agent)&&preg_match('/os/i',$Agent)){
            $os='SunOS';
        }elseif(preg_match('/ibm/i',$Agent)&&preg_match('/os/i',$Agent)){
            $os='IBM OS/2';
        }elseif(preg_match('/Mac/i',$Agent)&&preg_match('/PC/i',$Agent)){
            $os='Macintosh';
        }elseif(preg_match('/PowerPC/i',$Agent)){
            $os='PowerPC';
        }elseif(preg_match('/AIX/i',$Agent)){
            $os='AIX';
        }elseif(preg_match('/HPUX/i',$Agent)){
            $os='HPUX';
        }elseif(preg_match('/NetBSD/i',$Agent)){
            $os='NetBSD';
        }elseif(preg_match('/BSD/i',$Agent)){
            $os='BSD';
        }elseif(preg_match('/OSF1/i',$Agent)){
            $os='OSF1';
        }elseif(preg_match('/IRIX/i',$Agent)){
            $os='IRIX';
        }elseif(preg_match('/FreeBSD/i',$Agent)){
            $os='FreeBSD';
        }elseif($os==''){
            $os='Unknown';
        }
        self::useCache('getOs',$os);
        return $os;
    }

    /**
     * 就替换一次字符串
     * @param $needle string 被替换值
     * @param $replace string 替换值
     * @param $haystack string 主体
     * @return string 结果
     * @author yuri2
     */
    public static function strReplaceOnce($needle, $replace, $haystack) {
        // Looks for the first occurence of $needle in $haystack
        // and replaces it with $replace.
        $pos = strpos($haystack, $needle);
        if ($pos === false) {
            return $haystack;
        }
        return substr_replace($haystack, $replace, $pos, strlen($needle));
    }

    /**
     * 获取当前操作系统的默认编码格式
     * 依赖于getOS()
     * @return string 编码 如 utf-8
     * @author yuri2
     */
    public static function getOSCoding(){
        if (self::isCached('getOSCoding')){return self::useCache('getOSCoding');}
        $os=self::getOS();
        $preg='/windows/i';
        if (preg_match($preg,$os)){
            $rel= 'gbk';
        }else{
            $rel= 'utf-8';
        }
        self::useCache('getOSCoding',$rel);
        return $rel;
    }

    /**
     * 自动把字符串变成操作系统默认编码
     * 依赖于getOS() getOSCoding() autoEncoding()
     * @param $str string 处理目标
     * @return string 结果
     * @author yuri2
     */
    public static function autoSysCoding($str){
        $osCoding=self::getOSCoding();
        return self::autoEncoding($str,$osCoding);
    }

    /**
     * [数学]返回一个全排列
     * @param $arr array 排列元素
     * @param $rel array 结果数组（引用）
     * @param $str string 辅助参数 保持默认
     */
    public static function fullArray($arr,&$rel, $str=''){ // $str 为保存由 i 组成的一个排列情况
        $cnt = count($arr);
        if($cnt == 1){
            $item= $str . $arr[0];
            $rel[]=$item;
        }  else {
            for ($i = 0; $i < count($arr); $i++) {
                $tmp = $arr[0];
                $arr[0] = $arr[$i];
                $arr[$i] = $tmp;
                self::fullArray(array_slice($arr, 1),$rel, $str . $arr[0]);
            }
        }

    }

    /** 以下内容由小伙伴贡献------------------------------------------------------------------------------------------------- */

    /**
     * 自动检测当前字符串编码，转换为指定编码
     * @param $data string 目标
     * @param $to string 转换为什么编码
     * @return string 结果
     * @author love_fc
     */
    public static function autoEncoding($data, $to = 'utf-8')
    {
        if ($data == null) {
            return false;
        }
        $encode_arr = ["ASCII","UTF-8","GB2312","GBK","BIG5"];
        if (function_exists('mb_detect_encoding') && function_exists('mb_convert_encoding')) {
            $encoded = mb_detect_encoding($data, $encode_arr);
            if (function_exists('iconv')) {
                return iconv($encoded, $to, $data);
            }
            return mb_convert_encoding($data, $to, $encoded);
        }
        return false;
    }

    /**
     * 导出数据为excel表格
     * @param $data array   一个二维数组,结构如同从数据库查出来的数组
     * @param $title array  excel的第一行标题,一个数组,如果为空则没有标题
     * @param $filename string 下载的文件名
     * EXCEL($arr,array('id','账户','密码','昵称'),'文件名!');
     * @author love_fc
     */
    public static function exportExcel($data = array(), $title = array(), $filename = 'report')
    {
        header("Content-type:application/octet-stream");
        header("Accept-Ranges:bytes");
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=" . $filename . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        //导出xls 开始
        if (!empty($title)) {
            foreach ($title as $k => $v) {
                $title[$k] = iconv("UTF-8", "GB2312", $v);
            }
            $title = implode("\t", $title);
            echo "$title\n";
        }
        if (!empty($data)) {
            foreach ($data as $key => $val) {
                foreach ($val as $ck => $cv) {
                    $data[$key][$ck] = iconv("UTF-8", "GB2312", $cv);
                }
                $data[$key] = implode("\t", $data[$key]);

            }
            echo implode("\n", $data);
        }
        exit;
    }

    /**
     * 获得内存用量
     * @param $num int 精度
     * @return string MB为单位的内存用量
     * @author love_fc
     */
    public static function memoryUsage($num=2) {
        $memory     = ( ! function_exists('memory_get_usage')) ? '0' : round(memory_get_usage()/1024/1024, $num).'MB';
        return $memory;
    }

    /**
     * 获得一个随机字符串
     * @param $len int 长度
     * @param $originalCode string 取值范围
     * @return string 结果
     * @author love_fc
     */
    public static function strRand($len, $originalCode = '0123456789zxcvbnmasdfghjklqwertyuiop')
    {
        $countdistrub = strlen($originalCode);
        $_dscode      = "";
        for ($j = 0; $j < $len; $j++) {
            $dscode = $originalCode[rand(0, $countdistrub - 1)];
            $_dscode .= $dscode;
        }
        return $_dscode;
    }

    /**
     * 获得指定qq的头像链接
     * @param $qq int QQ
     * @return string 结果
     * @author love_fc
     */
    public static function getQQHead($qq)
    {
        $url = 'http://q.qlogo.cn/headimg_dl?bs=qq&dst_uin=' . $qq . '&fid=blog&spec=100';
        return $url;
    }

    /**
     * 根据新浪api查ip地址
     * @param $ip string ip
     * @return array 结果
     * @author love_fc
     *
     * public 'ret' => int 1
     * public 'start' => int -1
     * public 'end' => int -1
     * public 'country' => string '中国' (length=6)
     * public 'province' => string '北京' (length=6)
     * public 'city' => string '北京' (length=6)
     * public 'district' => string '' (length=0)
     * public 'isp' => string '' (length=0)
     * public 'type' => string '' (length=0)
     * public 'desc' => string '' (length=0)
     */
    public static function iPQuery($ip)
    {
        $url = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=' . $ip;
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $output = curl_exec($ch);
            curl_close($ch);
            if ($output == false)
                return false;
            return json_decode($output);
        }else{
            return false;
        }

    }

    /**
     * 去除字符串中多余的空格
     * @param $str string 字符串
     * @return string 结果
     * @author love_fc
     */
    public static function strRemoveBlank($str)
    {
        $before = array(
            " ",
            "　",
            "\t",
            "\n",
            "\r",
            "&nbsp;"
        );
        $after  = array(
            "",
            "",
            "",
            "",
            "",
            ""
        );
        return str_replace($before, $after, $str);
    }

    /**
     * 字符串截取，支持中文和其他编码
     * @static
     * @access public
     * @param string $str 需要转换的字符串
     * @param int $start 开始位置
     * @param string $length 截取长度
     * @param string $charset 编码格式
     * @return string
     * @author love_fc
     */
    public static function subStr($str, $start = 0, $length, $charset = "utf-8")
    {
        if (function_exists("mb_substr"))
            $slice = mb_substr($str, $start, $length, $charset);
        elseif (function_exists('iconv_substr')) {
            $slice = iconv_substr($str, $start, $length, $charset);
            if (false === $slice) {
                $slice = '';
            }
        } else {
            $re['utf-8']  = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re[$charset], $str, $match);
            $slice = join("", array_slice($match[0], $start, $length));
        }
        return $slice;
    }

    /**
     * 简单判断浏览器类型(不太准？)
     * @return  string 浏览器名
     * @author love_fc
     */
    public static function getUA()
    {
        if (self::isCached('getUA')){return self::useCache('getUA');}
        $ac = $_SERVER['HTTP_USER_AGENT'];
        
        if (strpos($ac, "QQBrowser") !== false) {
            $rel= 'QQ浏览器';
        }
        elseif (strpos($ac, "baidubrowser") !== false) {
            $rel= '百度浏览器';
        }
        elseif (strpos($ac, "Opera") !== false) {
            $rel= '欧朋浏览器';
        }
        elseif (strpos($ac, "UCWEB") !== false) {
            $rel= 'UC浏览器';
        }
        elseif (strpos($ac, "Windows") !== false) {
            $rel= 'Windows';
        }
        elseif (strpos($ac, "Android") !== false) {
            $rel= 'Android';
        }
        elseif (strpos($ac, "iPhone") !== false) {
            $rel= 'iPhone';
        }
        elseif (strpos($ac, "iPad") !== false) {
            $rel= 'iPad';
        }
        elseif (strpos($ac, "Nokia") !== false) {
            $rel= 'Nokia';
        }
        elseif (strpos($ac, "Chrome") !== false) {
            $rel= 'Chrome';
        }
        elseif (strpos($ac, "Safari") !== false) {
            $rel= 'Safari';
        }
        else {
            $rel= false;
        }
        self::useCache('getUA',$rel);
        return $rel;
    }

    /**
     * 简单判断浏览器是否是IE 5,6,7,8
     * @return  bool
     * @author yuri2
     */
    public static function isOldIE()
    {
        if (self::isCached('isOldIE')) {
            return self::useCache('isOldIE');
        }
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($agent, "MSIE 5") or strpos($agent, "MSIE 6") or strpos($agent, "MSIE 7") or strpos($agent, "MSIE 8")) {
            $rel = true;
        } else {
            $rel = false;
        }
        self::useCache('isOldIE', $rel);
        return $rel;

    }

    /**
     *  将byte数字换成1024合适的单位
     * @param $size int 大小(byte)
     * @param $dec int 精度
     * @return string 结果
     * @author love_fc
     */
    public static function byteSize($size, $dec = 2)
    {
        $a   = array(
            "B",
            "KB",
            "MB",
            "GB",
            "TB",
            "PB",
            "EB",
            "ZB",
            "YB"
        );
        $pos = 0;
        while ($size >= 1024) {
            $size /= 1024;
            $pos++;
        }
        return round($size, $dec) . " " . $a[$pos];
    }

    /**
     * 下载文件
     * @param $file_path string 文件路径
     * @return bool 文件是否存在
     * @author love_fc
     */
    public static function download($file_path)
    {
        //用以解决中文不能显示出来的问题
        $file_path = self::autoSysCoding($file_path);
        $file_name = basename($file_path);
        //首先要判断给定的文件存在与否
        if (!file_exists($file_path)) {
            return false;
        }
        $fp        = fopen($file_path, "r");
        $file_size = filesize($file_path);
        //下载文件需要用到的头
        header("Content-type: application/octet-stream");
        header("Accept-Ranges: bytes");
        header("Accept-Length:" . $file_size);
        header("Content-Disposition: attachment; filename=" . $file_name);
        $buffer     = 1024;
        $file_count = 0;
        //向浏览器返回数据
        while (!feof($fp) && $file_count < $file_size) {
            $file_con = fread($fp, $buffer);
            $file_count += $buffer;
            echo $file_con;
        }
        fclose($fp);
        return true;
    }

    /**
     * 批量修改权限
     * @param $path string 路径
     * @param $filemode int 权限 默认0775
     * @return bool 结果
     * @author love_fc
     */
    public static function chmodAll($path, $filemode = 0755)
    {
        $path = self::autoSysCoding($path);
        if (!is_dir($path))
            return chmod($path, $filemode);
        $dh = opendir($path);
        while (($file = readdir($dh)) !== false) {
            if ($file != '.' && $file != '..') {
                $fullpath = $path . self::autoSysCoding('/') . $file;
                if (is_link($fullpath))
                    return FALSE;
                elseif (!is_dir($fullpath) && !chmod($fullpath, $filemode))
                    return FALSE;
                elseif (!self::chmodAll($fullpath, $filemode))
                    return FALSE;
            }
        }
        closedir($dh);
        if (chmod($path, $filemode))
            return TRUE;
        else
            return FALSE;
    }

    /**
     * 离线下载
     * @param $url string 下载地址
     * @param $file string 保存路径
     * @param $second int 超时时间
     * @return bool 是否成功
     * @author love_fc
     */
    public static function offlineDownload($url, $file, $second = 720)
    {
        $file=self::autoSysCoding($file);
        $dir=dirname($file);
        self::createDir($dir);
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, $second);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            $content = curl_exec($ch);
            curl_close($ch);
            if ($content) {
                if (file_put_contents($file, $content))
                    return true;
            }
        }
        else {
            $timeout = array(
                'http' => array(
                    'timeout' => $second
                )
            );
            $ctx     = stream_context_create($timeout);
            $content = file_get_contents($url, false, $ctx);
            if ($content) {
                if (file_put_contents($file, $content))
                    return true;
            }
        }
        return false;
    }

    /**
     * 检测是否是手机端
     * @return bool 是否
     * @author love_fc
     */
    public static function isMobile()
    {
        if (self::isCached('isMobile')){return self::useCache('isMobile');}

        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
            self::useCache('isMobile',true);
            return true;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        elseif (isset($_SERVER['HTTP_VIA'])) {
            // 找不到为flase,否则为true
            $rel=stristr($_SERVER['HTTP_VIA'], "wap") ;
            self::useCache('isMobile',$rel);
        }
        // 脑残法，判断手机发送的客户端标志,兼容性有待提高
        elseif (isset($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array(
                'nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile'
            );
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                self::useCache('isMobile',true);
                return true;
            }
        }
        // 协议法，因为有可能不准确，放到最后判断
        elseif (isset($_SERVER['HTTP_ACCEPT'])) {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                self::useCache('isMobile',true);
                return true;
            }
        }
        self::useCache('isMobile',false);
        return false;
    }

}
