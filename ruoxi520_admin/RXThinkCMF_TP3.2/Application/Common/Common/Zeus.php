<?php 
// +----------------------------------------------------------------------
// | RXThink框架 [ RXThink ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2019 南京RXThink工作室
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

/**
 * 系统公共函数类
 * 
 * @author 牧羊人
 * @date 2017-01-01
 */
class Zeus {
    
    /**
     * 验证手机号是否正确
     * 
     * @author 牧羊人
     * @date 2018-07-11
     */
    static function isValidMobile($mobile) {
        return preg_match('/^1[3456789]{1}\d{9}$/', $mobile) ? true : false;
    }
    
    /**
     * 验证邮编是否正确
     * 
     * @author 牧羊人
     * @date 2018-07-11
     */
    static function isValidZipCode($code) {
        return preg_match('/^[1-9][0-9]{5}$/', $code) ? true : false;
    }
    
    /**
     * 验证邮箱是否正确
     * 
     * @author 牧羊人
     * @date 2018-07-11
     */
    static function isValidEmail($email) {
        $regex = "/\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/";
        return preg_match($regex, $email) ? true : false;
    }
    
    /**
     * 检查身份证号是否合法
     * 
     * @author 牧羊人
     * @date 2018-07-11
     */
    static function isValidIdNo($idNo)  {
        $idNo = strtoupper($idNo);
        $regx = "/(^\d{15}$)|(^\d{17}([0-9]|X)$)/";
        $arr_split = array();
        if (!preg_match($regx, $idNo)) {
            return FALSE;
        }
        //检查15位
        if (15==strlen($idNo)) {
            $regx = "/^(\d{6})+(\d{2})+(\d{2})+(\d{2})+(\d{3})$/";
            @preg_match($regx, $idNo, $arr_split);
            $dtm_birth = "19".$arr_split[2] . '/' . $arr_split[3]. '/' .$arr_split[4];
            if (!strtotime($dtm_birth)) {
                return FALSE;
            } else {
                return TRUE;
            }
        } else {
            //检查18位
            $regx = "/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/";
            @preg_match($regx, $idNo, $arr_split);
            $dtm_birth = $arr_split[2] . '/' . $arr_split[3]. '/' .$arr_split[4];
            //检查生日日期是否正确
            if (!strtotime($dtm_birth)) {
                return FALSE;
            } else {
                //检验18位身份证的校验码是否正确。
                //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
                $arr_int = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
                $arr_ch = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
                $sign = 0;
                for ($i=0; $i<17; $i++) {
                    $b = (int) $idNo{$i};
                    $w = $arr_int[$i];
                    $sign += $b * $w;
                }
                $n = $sign % 11;
                $val_num = $arr_ch[$n];
                if ($val_num != substr($idNo,17, 1)) {
                    return FALSE;
                } else {
                    return TRUE;
                }
            }
        }
    }
    
    /**
     * 验证密码（6~12位字母和数字组成）
     * 
     * @author 牧羊人
     * @date 2018-07-11
     */
    static function isValidPassword($password,$isLength=2) {
        if (strlen($password)>12 || strlen($password)<6) {
            return false;
        }
        if( 2 ==$isLength ){
            if(preg_match("/^\d*$/",$password))
            {
                return false;//全数字
            }
            if(preg_match("/^[a-z]*$/i",$password))
            {
                return false;//全字母
            }
            if(!preg_match("/^[a-z\d]*$/i",$password))
            {
                return false;//特殊字符;
            }
        }
        return true;
    }
    
    /**
     * 生成随机字符
     * 
     * @author 牧羊人
     * @date 2018-07-11
     */
    static function getRandCode($num=12) {
        $codeSeeds = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeSeeds .= "abcdefghijklmnopqrstuvwxyz";
        $codeSeeds .= "0123456789_";
        $len = strlen($codeSeeds);
        $code = "";
        for ($i=0; $i<$num; $i++) {
            $rand = rand(0, $len-1);
            $code .= $codeSeeds[$rand];
        }
        return $code;
    }
    
    /**
     * 生成指定位数的邀请码
     *
     * @author 牧羊人
     * @date 2018-08-30
     * @param unknown $id
     * @return string
     */
    static function getInviteCode($start=0, $length=8) {
        return substr(md5(microtime(true).Zeus::getRandCode(10).rand(1,100000)) ,$start, $length);
    }
    
    /**
     * 获取指定随机纯数字
     * 
     * @author 牧羊人
     * @date 2018-08-31
     */
    static function getRandomNum($len=3){
        $min = pow(10 , ($len - 1));
        $max = pow(10, $len) - 1;
        return mt_rand($min, $max);
    }
    
    /**
     * 订单编号生成规则
     *
     * @author 牧羊人
     * @date 2018-09-29
     */
    static function getOrderNum($prefix="") {
        $micro = substr(microtime(), 2, 3);
        return $prefix . date("YmdHis").$micro.rand(100000,999999);
    }
    
    /**
     * 去除内容A标签
     * 
     * @author 牧羊人
     * @date 2018-07-11
     */
    static function trimA($content){
        $content = preg_replace("/<a[^>]*>(.*)<\/a>/isU",'${1}',$content);
        return $content;
    }
    
    /**
     * 价格转化成分
     * 
     * @author 牧羊人
     * @date 2018-07-11
     */
    static function formatToCent($money) {
        return (string) ($money*100);
    }
    
    /**
     * 价格转化成元
     * 
     * @author 牧羊人
     * @date 2018-07-11
     */
    static function formatToYuan($money, $default="") {
        if ($money>0) {
            return number_format($money/100, 2, ".", "");
        }
        return "0.00";
    }
    
    /**
     * 银行卡格式转换
     * 
     * @author 牧羊人
     * @date 2018-07-11
     */
    static function formatBankCardNo($bankCardNo,$isCover = true){
        if($isCover){
            //截取银行卡号前4位
            $prefix = substr($bankCardNo,0,4);
            //截取银行卡号后4位
            $suffix = substr($bankCardNo,-4,4);
            	
            $formatCardNo = $prefix." **** **** **** ".$suffix;
        }else{
            $arr = str_split($bankCardNo,4);//4的意思就是每4个为一组
            $formatCardNo = implode(' ',$arr);
        }
        return $formatCardNo;
    }
    
    /**
     * 分页初始化
     * 
     * @author 牧羊人
     * @date 2018-07-11
     */
    static function initPage(&$page, &$perpage, &$limit) {
        $page = (int) $_REQUEST['page'];
        $perpage = (int) $_REQUEST['perpage'];
        $page = $page ? $page : 1;
        $perpage = $perpage ? $perpage : 10;
        $startIndex = ($page-1)*$perpage;
        $limit = "{$startIndex}, {$perpage}";
    }
    
    /**
     * 去除HTML标签、图像等 仅保留文本
     * 
     * @author 牧羊人
     * @date 2018-07-11
     */
    static public function strip_html_tags2($str,$isSub=2){
        $str = htmlspecialchars_decode($str);//把一些预定义的 HTML 实体转换为字符
        $str = str_replace("&nbsp;","",$str);//将空格替换成空
        $str = strip_tags($str);//函数剥去字符串中的 HTML、XML 以及 PHP 的标签,获取纯文本内容
        $str = str_replace(array("\n", "\r\n", "\r"), ' ', $str);
        $preg = "/<script[\s\S]*?<\/script>/i";
        $str = preg_replace($preg,"",$str,-1);//剥离JS代码
        if($isSub==2) {
            //返回字符串中的前100字符串长度的字符
            $str = mb_substr($str, 0, 100,"utf-8");
        }
        return $str;
    }
    
    /**
     * 去除指定的HTML标签
     * 
     * @author 牧羊人
     * @date 2018-07-11
     * 示例：echo strip_html_tags(array('a','img'),$str)
     */
    static function strip_html_tags($tags,$str){
        $html=array();
        foreach ($tags as $tag) {
            $html[]='/(<'.$tag.'.*?>[\s|\S]*?<\/'.$tag.'>)/';
        }
        $result = preg_replace($html,'',$str);
        return $result;
    }
    
    /**
     * 生成MD5加密的密码
     * 
     * @author 牧羊人
     * @date 2018-07-11
     */
    static function getPassWord($password){
        return md5(md5($password));
    }
    
    /**
     * 根据出生日期计算年龄
     * 
     * @author 牧羊人
     * @date 2018-07-11
     */
    static function getAgeFromBirthday($birthday){
        //格式化出生时间年月日
        $byear = date('Y',$birthday);
        $bmonth = date('m',$birthday);
        $bday = date('d',$birthday);
    
        //格式化当前时间年月日
        $tyear = date('Y');
        $tmonth = date('m');
        $tday = date('d');
    
        //开始计算年龄
        $age = $tyear-$byear;
        if($bmonth>$tmonth || $bmonth==$tmonth && $bday>$tday){
            $age--;
        }
        return $age;
    }
    
    /**
     * 发送短信
     * 
     * @author 牧羊人
     * @date 2018-07-11
     */
    static function sendSms($mobile, $content) {
        return Zeus::getSereviceResponse(array(
            'app'=>"sms",
            'act'=>"send",
            'group_id'=>1,
            'sign'=>"【云多普】",
            'mobile'=>$mobile,
            'content'=>$content
        ));
    }
    
    /**
     * 请求服务并返回结果
     * 
     * @author 牧羊人
     * @date 2018-07-11
     */
    static function getSereviceResponse($data=array(), $url = "http://service.nuoshibang.cn") {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        if ($data) {
            $crypt = getCryptDesObject();
            $data = $crypt->encrypt(json_encode($data));
            $data = http_build_query(array('APIDATA'=>$data), null, "&");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $responseText = curl_exec($ch);
        //log::jsonInfo($responseText);
        $responseText = $crypt->decrypt($responseText, API_KEY);
        //log::jsonInfo($responseText);
        $info = json_decode($responseText, 1);
        if (!$info) {
            $info = array();
            $info['code']= "90001";
            $info['msg'] = "网络异常:".curl_error($ch);
            //log::jsonInfo($info);
        }
    
        return $info;
    }
    
    /**
     * 从内容中获取图片
     * 
     * @author 牧羊人
     * @date 2018-07-17
     * @param unknown $content
     * @param string $title
     * @param string $path
     * @return boolean|number 失败返回false 成功则返回替换图片数
     */
    static function saveImageByContent(&$content, $title=false, $path='article') {
        preg_match_all("/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i", str_ireplace("\\","",$content), $match);
        if(!$match[1]) {
            return false;
        }
        $i=0;
        foreach ($match[1] as $id => $oldImage) {
            $saveImage = Zeus::saveImage($oldImage, $path);
            if($saveImage) {
                $content = str_replace($oldImage, "[IMG_URL]" .$saveImage, $content);
                $i++;
            }
        }
        if( (strpos($content, 'alt=\"\"') !== false) && $title ) {
            $content = str_replace('alt=\"\"', 'alt=\"'.$title.'\"', $content);
        }
        return $i;
    }
    
    /**
     * 保存 上传临时图片或者外链图片
     * 
     * @author 牧羊人
     * @date 2018-07-16
     */
    static function saveImage($image, $itemDir="/") {
        if (!$image) return false;
        $itemDir = trim($itemDir, "/");
        $ext = pathinfo($image, PATHINFO_EXTENSION);
        //是否是本站图片
        if (strpos($image, IMG_URL) !== false) {
            //是否是临时目录下
            if (strpos($image, 'temp/') === false) {
                return str_replace(IMG_URL, "", $image);
            }
            $newPath = Zeus::createImagePath($itemDir, $ext);
            $oldPath = str_replace(IMG_URL, IMG_PATH , $image);
            if (!file_exists($oldPath)) return false;
            rename($oldPath, IMG_PATH.$newPath);
            return  $newPath;
        }
        //保存远程图片
        $newPath = Zeus::saveFileByUrl($image,$itemDir);
        return $newPath;
    }
    
    /**
     * 生成媒体文件路径
     * 
     * @author 牧羊人
     * @date 2018-07-16
     * @param string $prefix 目录前缀
     * @param string $ext
     * @param string $root
     * @return string
     */
    static function createImagePath($prefix="", $ext="", $root=IMG_PATH) {
        $dateDir = date("/Y/m/d/H");
        if ($dateDir) {
            $dateDir = ($prefix ? "/" : '') .$prefix.$dateDir;
        }
        if (!$ext) $ext = "jpg";
        $absImgPath = $root.$dateDir;
        if (!is_dir($absImgPath)) mkdir($absImgPath, 0777, true);
        $filename = substr(md5(time().rand(0,999999)),8, 16).rand(100,999).".{$ext}";
        $filePath = $dateDir."/".$filename;
        return $filePath;
    }
    
    /**
     * 保存外链文件
     * 
     * @author 牧羊人
     * @date 2018-07-16
     * @param unknown $url 需要保存的外链地址
     * @param string $dir 最终保存的目录名
     * @return 成功返回绝对路径  失败则返回false
     */
    static function saveFileByUrl($url, $dir=false) {
        $content = file_get_contents($url);
        if(!$content) {  //不存在或没抓到
            return false;
        }
        if ($content {0} . $content {1} == "\xff\xd8") {
            $ext = 'jpg';
        } else if ($content {0} . $content {1} . $content {2} == "\x47\x49\x46") {
            $ext = 'gif';
        } else if ($content {0} . $content {1} . $content {2} == "\x89\x50\x4e") {
            $ext = 'png';
        }else{// 不是有效的图片
            return false;
        }
        $savePath = Zeus::createImagePath($dir, $ext);
        return file_put_contents(IMG_PATH . $savePath, $content) ? $savePath : false ;
         
    }
    
    /**
     * 创建验证码文件
     *
     * @author 牧羊人
     * @date 2018-08-30
     * @param unknown $mobile
     * @param unknown $vcode
     * @param number $disbaledTime
     * @return boolean
     */
    static function createSMSCode($mobile, &$vcode, $disbaledTime=60) {
        $dir = TEMP_PATH."sms/";
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        $flagFile = $dir.$mobile;
        if (file_exists($flagFile)) {
            $fileTime = filemtime($flagFile);
            $durTime = time()-$fileTime;
            if ($durTime<$disbaledTime) {
                $content = trim(file_get_contents($flagFile));
                $info = json_decode($content,1);
                $vcode = $info['sms']['code'];
                return false;
            }
        } else {
            @mkdir($dir);
        }
        $vcode = rand(111111,999999);
        $info['sms']['code'] = $vcode;
        $info['sms']['mobile'] = $mobile;
        $info['sms']['error'] = 0;
        $content = json_encode($info);
        file_put_contents($flagFile, $content);
        return true;
    }
    
    /**
     * 校验验证码
     *
     * @author 牧羊人
     * @date 2018-08-30
     * @param unknown $mobile
     * @param unknown $vcode
     * @param string $destory
     * @return Ambigous <number, multitype:string unknown >
     */
    static function checkSMSCode($mobile, $vcode, $destory=true) {
        if (!$mobile) {
            return message("请输入手机号", false);
        }
        if (!$vcode) {
            return message("请输入验证码", false);
        }
        $dir = TEMP_PATH."sms/";
        $flagFile = $dir.$mobile;
        if (!file_exists($flagFile)) {
            return  message("验证码错误", false);
        }
        $restTime = time()-filemtime($flagFile);
        if ($restTime>600) {
            return  message("验证码错误", false);
        }
        $content = file_get_contents($flagFile);
        $info = json_decode($content, true);
        if (!$info) {
            return message("验证码错误", false);
        }
        if ($info['sms']['error']>3) {
            return message("验证码已失效，请重新获取", false);
        }
        do {
            if ($vcode!=$info['sms']['code']) {
                $info['sms']['error']++;
                $result = message("您输入的验证码有误请重新输入", false);
                break;
            }
            if ($mobile!=$info['sms']['mobile']) {
                $info['sms']['error']++;
                $result = message("验证码错误2", false);
                break;
            }
            if ($vcode==$info['sms']['code']) {
                if ($destory) {
                    $info['sms'] = array();
                }
                $result = message("验证码正确",true);
            }
        } while (0);
        $content = json_encode($info);
        file_put_contents($flagFile, $content);
        return $result;
    }
   
}

?>