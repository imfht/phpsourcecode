<?php
/**
 * 自定制函数类
 *
 * 用于全局的函数定义，数据初始化
 *
 * @author		黑冰(001.black.ice@gmail.com)
 * @copyright	(c) 2012
 * @version		$Id$
 * @package		com.tools
 * @since		v1.1
 */

class MyFunction{

    /**
     * 用于提示信息 跳转
     *
     * @param
     * string	$strMes		(提示信息)
     * string	$url		(跳转到那一页)
     * string	$strTarGet	(target)
     *
     * @return void
     */
    static function funAlert($strMes, $url='', $strTarGet=''){
        if(!empty($strMes)){
            echo "<script>alert('$strMes');</script>";
        }else{
            echo "<script>alert('非法操作');</script>";
        }

        //跳转
        if(!empty($url) && !empty($strTarGet)){
            echo "<script>window.open('$url', '$strTarGet');</script>";
        }else if(!empty($url) && $url!='-1'){
            echo "<script>location.href='$url';</script>";
        }else if(!empty($url) && $url=='-1'){
            echo "<script>history.go(-1);</script>";
        }
    }


    /**
     * 修饰内容配色
     * @param	string	$title			要加修饰的字符串
     * @param	int		$bedeck			修饰代码
     * @param	string	$label			修饰符标签
     * @return  string
     */
    static function StringBedeck($title, $bedeck,$label='font') {
        switch($bedeck){
            case 1:
                $title = '<'.$label.' style=font-weight:bold>'.$title.'</'.$label.'>';
                break;
            case 2:
                $title = '<'.$label.' style=color:red>'.$title.'</'.$label.'>';
                break;
            case 3:
                $title = '<'.$label.' style=color:green>'.$title.'</'.$label.'>';
                break;
            case 4:
                $title = '<'.$label.' style=color:blue>'.$title.'</'.$label.'>';
                break;
            case 5:
                $title = '<'.$label.' style=color:#FFAE00>'.$title.'</'.$label.'>';
                break;
            case 6:
                $title = '<'.$label.' style=font-weight:bold;color:red>'.$title.'</'.$label.'>';
                break;
            case 7:
                $title = '<'.$label.' style=font-weight:bold;color:green>'.$title.'</'.$label.'>';
                break;
            case 8:
                $title = '<'.$label.' style=font-weight:bold;color:blue>'.$title.'</'.$label.'>';
                break;
            case 9:
                $title = '<'.$label.' style=font-weight:bold;color:#FFAE00>'.$title.'</'.$label.'>';
                break;
        }
        return $title;
    }

    /**
     * 取得全局静态数组值, 只在需要的时候获取
     * @author	嬴益虎(whoneed@yeah.net)
     * @param	string	$strKey		需要操作的数据key
     * @param	int		$intT		需要返回指定的值,否则返回所有 默认为-1,返回所有
     * @time	2011-10-11
     * @return	array or string
     */
    static function funGetData($strKey='', $intT = -1){

        $arrTemp = array();

        // 定义常用标题样式
        if($strKey == 'bedeck'){
            $arrTemp = array(
                0	=> '无修饰',
                1	=> '[加粗]',
                2	=> '[标红]',
                3	=> '[标绿]',
                4	=> '[标蓝]',
                5	=> '[标橙]',
                6	=> '[红粗]',
                7	=> '[绿粗]',
                8	=> '[蓝粗]',
                9	=> '[橙粗]'
            );
        }else if($strKey == 'status'){
            $arrTemp = array(
                0	=> '未审核通过',
                1	=> '审核通过',
            );
        }else if($strKey == 'yes_or_no'){
            $arrTemp = array(
                0	=> '否',
                1	=> '是',
            );
        }else if($strKey == 'sign_status'){
            $arrTemp = array(
                0	=> '非签约',
                1	=> '签约',
            );
        }else if($strKey == 'drop_status'){
            $arrTemp = array(
                0	=> '未下架',
                1	=> '已下架',
            );
        }else if($strKey == 'stars_status'){
            $arrTemp = array(
                0	=> '无星级',
                1	=> '1星',
                2	=> '2星',
                3	=> '3星',
                4	=> '4星',
                5	=> '5星',
            );
        }else if($strKey == 'software_type'){
            $arrTemp = array(
                1	=> '游戏',
                2	=> '应用',
            );
        }else if($strKey == 'mobile_platform'){
            $arrTemp = array(
                1	=> 'andorid',
                2	=> 'iphone',
                3   => 'winphone',
            );
        }else if($strKey == 'app_type'){
            $arrTemp = array(
                1	=> '休闲游戏',
                2	=> '动作格斗',
                3   => '角色冒险',
                4	=> '塔防策略',
                5	=> '赛车竞速',
                6	=> '棋牌益智',
                7	=> '飞行射击',
                8	=> '体育竞赛',
                9	=> '网游',
                10	=> '其他',
            );
        }

        // 返回
        if(empty($arrTemp)){
            MyFunction::funAlert('静态数组出错!');
            return array();
        }else{
            if($intT > -1){
                return $arrTemp[$intT];
            }else{
                return $arrTemp;
            }
        }
    }

    /**
     * 分页函数
     * @author	嬴益虎(whoneed@yeah.net)
     * @param	int	$intCount		总条数
     * @param	int	$intPage		当前页码
     * @param	int	$intPageSize	每页显示条数
     * @time	2011-10-12
     * @return	&
     */
    static function funGetPage($intCountRow = 0, $intCurrentPage = 1, $intPageSize = 20){

        // 默认字符串链接字符串
        $strPage = "<span class='pages'>&nbsp;&nbsp;共%intTCount%个</span>&nbsp;&nbsp;页次%intTCurrentPage%/%intTCountPage%&nbsp;&nbsp;&nbsp;&nbsp;	[%strTFirst%] | [%strTPrePage%] | [%strTNextPage%] | [%strTLastPage%]&nbsp;&nbsp;&nbsp;&nbsp;</span>";

        $intTCount			= 0;
        $intTCurrentPage	= 0;
        $intTCountPage		= 0;
        $strTFirst			= "<a>首页</a>";
        $strTLastPage		= "<a>尾页</a>";
        $strTPrePage		= "<a>上一页</a>";
        $strTNextPage		= "<a>下一页</a>";

        if($intCountRow){
            $intTCount			= $intCountRow;		//总条数
            $intTCurrentPage	= $intCurrentPage;	//当前页数
            $intTCountPage		= ceil($intCountRow / $intPageSize);	//总页数

            // 如果当前页大于总页数,默认置为第一页
            // 有两种情况：1.手工设置的页数太大 2.上一页搜索的页数比当前搜索的页码大
            if($intTCurrentPage > $intTCountPage) $intTCurrentPage = 1;

            // 当前url的其他GET参数
            $strCUrl = '';
            if($_GET){
                foreach($_GET as $k=>$v){
                    if($k != 'page')
                        $strCUrl .= "&{$k}={$v}";
                }
            }

            //首页
            if($intTCurrentPage > 1 && $intCurrentPage <= $intTCountPage){
                $strTFirst = "<a href='?page=1{$strCUrl}'>首页</a>";
            }

            //尾页
            if($intTCurrentPage < $intTCountPage && $intCurrentPage < $intTCountPage){
                $strTLastPage = "<a href='?page={$intTCountPage}{$strCUrl}'>尾页</a>";
            }

            //上一页
            $intPage = $intTCurrentPage - 1;
            if($intPage >= 1 && $intCurrentPage <= $intTCountPage){
                $strTPrePage = "<a href='?page={$intPage}{$strCUrl}'>上一页</a>";
            }

            //下一页
            $intPage = $intTCurrentPage + 1;
            if($intPage <= $intTCountPage && $intCurrentPage < $intTCountPage){
                $intPage = $intTCurrentPage + 1;
                $strTNextPage = "<a href='?page={$intPage}{$strCUrl}'>下一页</a>";
            }
        }

        // 替换
        $strPage = str_replace('%intTCount%', $intTCount, $strPage);	//总条数
        $strPage = str_replace('%intTCurrentPage%', $intTCurrentPage, $strPage);	//当前页数
        $strPage = str_replace('%intTCountPage%', $intTCountPage, $strPage);	//总页数
        $strPage = str_replace('%strTFirst%', $strTFirst, $strPage);	//首页
        $strPage = str_replace('%strTLastPage%', $strTLastPage, $strPage);	//尾页
        $strPage = str_replace('%strTPrePage%', $strTPrePage, $strPage);	//上一页
        $strPage = str_replace('%strTNextPage%', $strTNextPage, $strPage);	//下一页

        return $strPage;
    }

    /**
     * 支持utf8中文字符截取
     * @param	string $text		待处理字符串
     * @param	int $start			从第几位截断
     * @param	int $sublen			截断几个字符
     * @param	string $code		字符串编码
     * @param	string $ellipsis	附加省略字符
     * @return	string
     */
    static function csubstr($string, $start = 0,$sublen=12, $ellipsis='',$code = 'UTF-8'){
        if($code == 'UTF-8'){
            $tmpstr = '';
            $i = $start;
            $n = 0;
            $str_length = strlen($string);//字符串的字节数
            while (($n+0.5<$sublen) and ($i<$str_length)){
                $temp_str=substr($string,$i,1);
                $ascnum=Ord($temp_str);	//得到字符串中第$i位字符的ascii码
                if ($ascnum>=224){		//如果ASCII位高与224，
                    $tmpstr .= substr($string,$i,3); //根据UTF-8编码规范，将3个连续的字符计为单个字符
                    $i=$i+3;            //实际Byte计为3
                    $n++;				//字串长度计1
                }elseif ($ascnum>=192){ //如果ASCII位高与192，
                    $tmpstr .= substr($string,$i,3); //根据UTF-8编码规范，将2个连续的字符计为单个字符
                    $i=$i+3;            //实际Byte计为2
                    $n++;				//字串长度计1
                }else{					//其他情况下，包括小写字母和半角标点符号，
                    $tmpstr .= substr($string,$i,1);
                    $i=$i+1;			//实际的Byte数计1个
                    $n=$n+0.5;			//小写字母和半角标点等与半个高位字符宽...
                }
            }
            if(strlen($tmpstr)<$str_length ){
                $tmpstr .= $ellipsis;//超过长度时在尾处加上省略号
            }
            return $tmpstr;
        }else{
            $strlen = strlen($string);
            if($sublen != 0) $sublen = $sublen*2;
            else $sublen = $strlen;
            $tmpstr = '';
            for($i=0; $i<$strlen; $i++){
                if($i>=$start && $i<($start+$sublen)){
                    if(ord(substr($string, $i, 1))>129) $tmpstr.= substr($string, $i, 2);
                    else $tmpstr.= substr($string, $i, 1);
                }
                if(ord(substr($string, $i, 1))>129) $i++;
            }
            if(strlen($tmpstr)<$strlen ) $tmpstr.= $ellipsis;
            return $tmpstr;
        }
    }

    /**
     *
     * 加密字符串，常用于加密密码
     *
     * @param
     * string	$password	//待加密的字符串
     * boolean	$isAdmin	//加混淆码
     *
     * @return string
     */
    public static function funHashPassword($password, $isAdmin=false){

        // 管理员验证，加混淆码
        if($isAdmin){
            return md5(md5($password).'nicai&*(>!');
        }else{
            return md5(md5($password).'!@#coolsoft#@!');
        }
    }

    /**
     * 获取IP
     * @time	2011-10-12
     * @return	string
     */
    static function funGetIP(){

        $ip = '';

        switch(true){
            case isset($_SERVER["HTTP_X_FORWARDED_FOR"]):
                $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
                break;
            case isset($_SERVER["HTTP_CLIENT_IP"]):
                $ip = $_SERVER["HTTP_CLIENT_IP"];
                break;
            default:
                $ip = $_SERVER["REMOTE_ADDR"] ? $_SERVER["REMOTE_ADDR"] : '127.0.0.1';
        }
        if(strpos($ip, ', ') > 0){
            $ips = explode(', ', $ip);
            $ip = $ips[0];
        }

        return $ip;
    }

    /**
     * 得到用户的IP
     */
    public static function getUserHostAddress()
    {
        switch(true)
        {
            case ($ip = getenv('HTTP_X_REAL_IP')):
                break;
            case ($ip=getenv("HTTP_X_FORWARDED_FOR")):
                break;
            case ($ip=getenv("HTTP_CLIENT_IP")):
                break;
            default:
                $ip=getenv("REMOTE_ADDR") ? getenv("REMOTE_ADDR") : '127.0.0.1';
        }
        if (strpos($ip, ', ') > 0)
        {
            $ips = explode(', ', $ip);
            $ip = $ips[0];
        }
        return $ip;
    }

    public static function get_url($url,$ispost = FALSE,$post_data=null,$header=FALSE,$headerData='')
    {
        //启动一个CURL会话
        $ch = curl_init();

        //发送时携带header信息
        if($header){
            curl_setopt($ch,CURLOPT_HEADER,0);
            curl_setopt($ch,CURLOPT_HTTPHEADER,$headerData);
        }

        // 要访问的地址
        curl_setopt($ch, CURLOPT_URL, $url);

        // 对认证证书来源的检查，0表示阻止对证书的合法性的检查。
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        // 从证书中检查SSL加密算法是否存在
//        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);

        //模拟用户使用的浏览器，在HTTP请求中包含一个”user-agent”头的字符串。
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)");

        //发送一个常规的POST请求，类型为：application/x-www-form-urlencoded，就像表单提交的一样。
        if ($ispost)
        {

            curl_setopt($ch, CURLOPT_POST, 1);
            //要传送的所有数据，如果要传送一个文件，需要一个@开头的文件名
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        }



        //连接关闭以后，存放cookie信息的文件名称
        #curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);

        // 包含cookie信息的文件名称，这个cookie文件可以是Netscape格式或者HTTP风格的header信息。
        #curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);

        // 设置curl允许执行的最长秒数
        //curl_setopt($ch, CURLOPT_TIMEOUT, 6);

        // 获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

        // 执行操作
        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch,CURLINFO_HTTP_CODE);

        // 关闭CURL会话
        curl_close($ch);
        return array('code'=>$httpcode,'content'=>$result);
    }

    // 取得/设置 断点数据
    public static function funSync($ope_key = '', $ope_value = '')
    {
        $strR = '';

        if($ope_key){
            // 获取数据
            $cdb = new CDbCriteria();
            $cdb->condition = 'ope_key=:ope_key';
            $cdb->params	= array(':ope_key' => $ope_key);
            $cdb->limit		= 1;

            $objDB = Whoneed_sync::model()->find($cdb);
            if($objDB){
                $strR = trim($objDB->ope_value);
            }

            // 设置数据
            if($ope_value){
                if(!$objDB){
                    $objDB = new Whoneed_sync();
                    $objDB->ope_key		= $ope_key;
                }

                $objDB->ope_value	= $ope_value;
                $objDB->save();
            }
        }

        return $strR;
    }

    // 计算给定时间之间的差距
    // 如：几个小时，几分
    public static function funRangeTimeZh($beginTime, $endTime = ''){
        // init
        $strR = '';
        if(!$beginTime) return $strR;
        if(!$endTime) $endTime = time();

        // 计算
        $strR = $endTime - $beginTime;
        if($strR){
            $strT = '';

            // hour
            $hour = (int)($strR / 3600);
            if($hour){
                $strT .= $hour.'小时';
            }

            // minute
            $minute = (int)(($strR - $hour * 3600) / 60);
            if($minute){
                $strT .= $minute.'分钟';
            }

            // second
            if($strT == ''){
                $strT = $strR.'秒';
            }

            $strR = $strT.'前';
        }

        return $strR;
    }



    //表单输入防止注入
    static function inNoInjection($input_str){
        if(is_string($input_str)){
            $input_str = htmlspecialchars(trim($input_str));
            if(get_magic_quotes_gpc()){
                $input_str = $input_str;
            }else{
                $input_str = addslashes($input_str);
            }
        }elseif(is_array($input_str)){
            $input_str = array_map('inNoInjection',$input_str);
        }

        return $input_str;

    }

    //弹窗返回
    static function alert_back($str='',$href=''){
        if($str && $href){
            echo "<script>alert('$str');window.location.href='$href';</script>";
        }elseif($str && !$href){
            echo "<script>alert('$str');history.back();</script>";
        }elseif(!$str && $href){
            echo "<script>window.location.href='$href';</script>";
        }else{
            echo "<script>history.back();</script>";
        }
    }


    //身份证邮箱***显示
    static public function setPhoneEmail($str,$startcount,$endcount,$count){
        if( ! empty($str) && mb_strlen($str,'utf8')>($startcount+$endcount)){
            $startStr = mb_substr($str,0,$startcount);
            $endStr = mb_substr($str,$endcount);
            $tem = '';
            for($i=0;$i<$count;$i++){
                $tem .='*';
            }
            return $startStr.$tem.$endStr;
        }else{
            return $str;
        }

    }
    //目录新建
    public static  function createDir($dir){
        if(!is_dir($dir)){
            $arr = explode('/',$dir);            //分割目录，分割成单独的
            $dir_arr = array();
            foreach($arr as $value){
                if($value == '.' || $value == '..'|| $value == '')continue;        //剔除其中的一些没有用的字符
                array_push($dir_arr,$value);                                                    // 分割出的单独目录分别进栈


                mkdir(implode('/',$dir_arr));   //循环创建目录，首先创建123 然后再创建456 这样就符合了mkdir()的规则，
            }
        }else{
            echo 'Directory exists';
        }
    }

    //添加样式
    public static  function addClass($id,$className){
        $str = <<<STR
        <script>
        (function(){
            var element = document.getElementById('$id');
            if(element.className == ""){
                element.className = '$className';
            }else{
                element.className += " "+'$className';
            }
        })();
        </script>
STR;
        echo $str;

    }


    /*
     * $exceptionKey        异常key，每个异常的标识
     * $fileName                   异常文件名
     * $exceptionDot              出错点，默认异常所在行数
     * $exceptionContent               异常内容
     * $exceptionTime                              异常时间(data_time Y-m-d H:i:s)格式
     *
     */
    public static function saveException($exceptionKey,$exceptionContent,$fileName='',$exceptionDot='',$exceptionTime=''){

        $arrBackTrace = debug_backtrace();
        $exception_model = new Pdl_exception_log();
        $exception_model->exception_key = $exceptionKey;
        $exception_model->content =  $exceptionContent;
        $exception_model->file_name = $fileName?$fileName:$arrBackTrace[0]['file'];
        $exception_model->exception_dot = $exceptionDot?$exceptionDot:$arrBackTrace[0]['line'];;
        $exception_model->exception_time = $exceptionTime?$exceptionTime:date('Y-m-d H:i:s');

        return $exception_model->save() ? TRUE : FALSE;
    }

    //check    distribute
    //检查某渠道下是否已经分发游戏包
    public static function checkDistribute($channelId,$subId){
        $channelDistribute = Pdc_channel_distribute::model();
        if($objFind = $channelDistribute->find("channel_id='{$channelId}' and sub_id='{$subId}'")){
            $package_model = Pdc_package::model();
            $packageId = $objFind->package_id;
            return $package_model->find("id='{$packageId}'");
        }else{
            return FALSE;
        }
    }

     //某段时间渠道费用
    public static function getTimeCost($startTime,$endTime,$channelId){
        $sql = "select sum(reg_cost) reg_cost_sum,sum(active_cost) active_cost_sum from pdc_channel_cost_slave where record_date between '{$startTime}' and '{$endTime}' and channel_id='{$channelId}'";
        $arr = Page::funGetIntroBySql($sql,TRUE,Yii::app()->db_data_centre);
        return $arr;
    }
    //某段时间内的注册数、激活数(过滤掉channel=1下的测试子渠道)
    public static function getTimeReg($startTime,$endTime,$channelId){
        $sql = "select
                        sum(reg_users) as reg_sum,
                        sum(new_run_nums) as new_run_nums,
                        channel_id
                from
                        pds_channel_daily
                where
                         record_date between '{$startTime}'
                         and '{$endTime}'
                         and channel_id = '{$channelId}'
                         and ((channel_id=1 and sub_id !=0) or (channel_id != 1))
                group by
                        channel_id
                ";
        $arrChannel = Page::funGetIntroBySql($sql,TRUE,Yii::app()->db_data_statistics);
        return $arrChannel;
    }
    //pdl设置，获取sync数据
    public static  function getSetSync($ope_key,$ope_value=''){
        $strR = '';

        if($ope_key){
            // 获取数据
            $cdb = new CDbCriteria();
            $cdb->condition = 'ope_key=:ope_key';
            $cdb->params	= array(':ope_key' => $ope_key);
            $cdb->limit		= 1;

            $objDB = Pdl_sync::model()->find($cdb);
            if($objDB){
                $strR = trim($objDB->ope_value);
            }

            // 设置数据
            if($ope_value){
                if(!$objDB){
                    $objDB = new Pdl_sync();
                    $objDB->ope_key		= $ope_key;
                    $objDB->ope_value = 0;
                }

                $objDB->ope_value	= $ope_value;
                $objDB->save();
            }
        }

        return $strR;
    }

    static function rand($number = 16)
    {
        return substr(md5(uniqid(rand(), true)), mt_rand(0,15), $number);
    }

    //get SSDB
    public static function getSSDB(){
        $host = Yii::app()->params['SSDB']['host'];
        $port = Yii::app()->params['SSDB']['port'];

        try{
            $ssdb = new SimpleSSDB($host, $port);
        }catch(SSDBException $e){
            die(__LINE__ . ' ' . $e->getMessage());
        }
        return $ssdb;
    }
    
    //pdc_type是否存在
    public static function pdcTypeExist($appid,$serverid,$device){
        $obj = Pdc_type::model()->find("appid='{$appid}' AND channel_id='{$serverid}' AND device='{$device}'");
        if($obj){
            $id = $obj->id;
        }else{
            $objType = new Pdc_type();
            $objType->appid = $appid;
            $objType->channel_id = $serverid;
            $objType->device = $device;
            $objType->save();
            $id = $objType->attributes['id'];
        }
        return $id;
    }

    //获取pdcTypeObj
    public static function getPdcTypeObj($appid,$channel_id,$sub_id,$device){
        static $ObjTypeArr = array();
        $md5_key = md5($appid.'_'.$channel_id.'_'.$sub_id.'_'.$device);//下划线是为了避免重复:1_11,11_1;
        if( !isset( $ObjTypeArr[$md5_key]) ){
            $objType = Pdc_type::model()->find("appid='{$appid}' AND channel_id='{$channel_id}' AND device='{$device}' AND sub_id='{$sub_id}'");
            if(!$objType){
                $objType = new Pdc_type();
                $objType->appid = $appid;
                $objType->channel_id = $channel_id;
                $objType->sub_id = $sub_id;
                $objType->device = $device;
                $objType->save();
            }
            $ObjTypeArr[$md5_key] = $objType;
        }
        return $ObjTypeArr[$md5_key];
    }

    //暂时保留,后面删除;
    public static function getAppidByAppKey($appkey){
        return Pdc_app::model()->find("appkey = '{$appkey}'");
    }

    public static function getAppObjByAppKey($appkey){
        static $ObjAppArr = array();
        if( !isset( $ObjAppArr[$appkey]) ){
            $objApp = Pdc_app::model()->find("appkey = '{$appkey}'");
            if($objApp){
                $ObjAppArr[$appkey] = $objApp;
            }else
                return false;
        }
        return $ObjAppArr[$appkey];
    }

    //计算时间段天数(包括同一天，同一天相差记为1天)
    static  function countDays($startDay,$endDay){
        $startDay = strtotime($startDay);
        $endDay = strtotime($endDay);
        if(date('Y-m-d',$startDay) == date('Y-m-d',$endDay)){
            return 1;
        }
        if($endDay > $startDay){
            return ceil(($endDay-$startDay)/(24*60*60))+1;
        }else{
            return 0;
        }
    }

    //返回两个时间段之间的所有日期（包括首尾两天）
    static function getDays($startDay,$endDay){
        $dayCount = self::countDays($startDay,$endDay);
        $arrDay = array();
        for($i=0;$i<$dayCount;$i++){
            array_push($arrDay,date('Y-m-d',strtotime($startDay)+24*60*60*$i));
        }
        return $arrDay;
    }

    //serverid更换兼容函数 channelInfo('channel_id'=>$channel_id,'sub_id'=>$sub_id)
    static function getChannelByServerid($serverid){
        $channelInfo = false;
        if( preg_match('/^\d+$/',$serverid) ){
            $channelInfo = self::getChannelInfo($serverid);
        }else if( preg_match('/^[^_]+_[^_]+_([\d]+)(?:_([\d]+))?/',$serverid,$result) ){
            $channelInfo['channel_id'] = $result[1];
            $channelInfo['sub_id']     = isset($result[2]) ? $result[2] : 0;
        }
        //去掉测试数据
        if($channelInfo && $channelInfo['channel_id']<10 && $channelInfo['sub_id']<10){
            return false;
        }
        //验证channelInfo真实性
        if($channelInfo){
            $objChannel = Pdcc_sub_channel::model()->find("channel_id = {$channelInfo['channel_id']} and sub_id = {$channelInfo['sub_id']}");
            if(!$objChannel && $channelInfo['sub_id']==0){
                $objChannel = Pdcc_channel::model()->find("id = {$channelInfo['channel_id']}");
            }
            if(!$objChannel) return false;
        }
        return $channelInfo;
    }
    
    /*兼容函数,同上
     *根据channel_id获取array($channel_id,$sub_id);
     */
    static function getChannelInfo($channel_id){
        $channel_id = (int)$channel_id;
        $channelInfo = false;
        $objChannel = Pdc_channel::model()->find("id = {$channel_id}");
        if($objChannel){
            if($objChannel->fid==0){
                $channelInfo['channel_id'] = $objChannel->id;
                $channelInfo['sub_id']     = 0;
            }else{
                $channelInfo['channel_id'] = $objChannel->fid;
                $channelInfo['sub_id']     = $objChannel->id;
            }
        }
        return $channelInfo;
    }

    //check phone number
    public static function checkPhoneNumber($phone){
        if($phone){
            $phonePattern = '/^(13[0-9]{9})|(15[0-9][0-9]{8})$/';
            if(preg_match($phonePattern,$phone))
                return TRUE;
            else
                return FALSE;
        }
        return FALSE;
    }

    //check email
    public static function checkEmail($email){
        if($email){
            if(filter_var($email,FILTER_VALIDATE_EMAIL))
                return TRUE;
            else
                return FALSE;
        }
        return FALSE;
    }

    //check is phone or pc
    public static function checkWap(){
        // 先检查是否为wap代理，准确度高
        if(stristr($_SERVER['HTTP_VIA'],"wap")){
            return true;
        }
        // 检查浏览器是否接受 WML.
        elseif(strpos(strtoupper($_SERVER['HTTP_ACCEPT']),"VND.WAP.WML") > 0){
            return true;
        }
        //检查USER_AGENT
        elseif(preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])){
            return true;
        }
        else{
            return false;
        }
    }

    //返回投放标识，例如szjj_hdjq_10_1
    public static function getChannelMark($channelId='',$isFaterChannel=TRUE){
        if( ! $channelId){
            exit('not found channel_id');
        }
        $subChannel = 0;
        if( ! $isFaterChannel){
            //子渠道，查找父渠道
            $subChannel = $channelId;
            $channelId = Pdcc_sub_channel::model()->find("sub_id='{$channelId}'")->channel_id;
        }

        $productId = Pdcc_channel::model()->find("id='{$channelId}'")->product_id;
        $product_ab = Pdcc_product::model()->find("id='{$productId}'")->app_ab;
        $companyId = Pdcc_product::model()->find("id='{$productId}'")->company_id;
        $company_ab = Pdcc_company::model()->find("id='{$companyId}'")->company_ab;

        return $company_ab.'_'.$product_ab.'_'.$channelId.'_'.$subChannel;
    }

    //==================== 差异化合并多个数组
    // array_merge 会把相同的key做更新，如果合并的是多级数组，不能做到差异化合并
    public static function array_merge_diff()
    {
        $arrR   = array();
        $intArg = func_num_args();

        // 参数必须大于2
        if($intArg >= 2)
        {
            $arrR = func_get_arg(0);
            for($i = 1; $i < $intArg; $i++)
            {
                $arrT = func_get_arg($i);
                if($arrT && is_array($arrT))
                {
                    self::do_array_merge_diff($arrR, $arrT);
                }
            }
        }

        return $arrR;
    }

    public static function do_array_merge_diff(&$to, $from)
    {
        foreach($from as $k => $v)
        {
            if(is_array($v))
            {
                self::do_array_merge_diff($to[$k], $v);
            }else{
                $to[$k] = $v;
            }
        }
    }
}
