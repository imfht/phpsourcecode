<?php
namespace app\common\fun;

use app\common\model\Shorturl AS ShorturlModel;

class Wxapp{
    
    /**
     * 生成公众号二维码
     * @param number $id 数字或字母都可以的
     * @return string|void|string|unknown
     */
    public static function mp_code($id=0){
        if( config('webdb.weixin_appid')=='' || config('webdb.weixin_appsecret')==''){
            return '系统没有配置公众号';
        }
        $path = config('upload_path') . '/mp_code/';
        $randstr = md5($id);
        $img_path  = $path.$randstr.'.png';
        if ( is_file($img_path) && (time()-filemtime($img_path)<3600*24) ) {
            return tempdir("uploads/mp_code/{$randstr}.png");
        }
        if (!is_dir($path)) {
            mkdir($path);
        }
        $access_token = wx_getAccessToken(true,false);
        if (empty($access_token)) {
            return 'access_token不存在!';
        }
        
        $str = http_curl('https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token,[
                'action_name'=>'QR_LIMIT_STR_SCENE',
                'action_info'=>[
                       'scene'=>[
                               'scene_str'=>$id,
                       ] 
                ],
        ],'json');
        $res = json_decode($str,true);
        $tick = $res['ticket'];
        if (empty($access_token)) {
            return 'ticket不存在!';
        }
        $code = http_Curl("https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=$tick");        
        if (strlen($code)>500) {
            write_file($img_path, $code);
            return tempdir("uploads/mp_code/{$randstr}.png");
        }else{
            return $code;
        }
    }

    /**
     * 强制用户关注公众号,并且绑定该微信登录
     * @param string $url 关注后提示跳转的地址
     * @param number $uid 当前用户UID
     * @return unknown
     */
    public static function bind($url='',$uid=0){
        $uid = $uid ?: login_user('uid');
        $code = 'bind'.$uid;
        cache($code,$url?:$uid,300);
        $img = self::mp_code($code);
        return $img;        
    }
    
    
    /**
     * 圈子微信二维码小程序入口
     * @param int $id
     * $id 取值 最大32个可见字符，只支持数字，大小写英文以及部分特殊字符：!#$&'()*+,/:;=?@-._~，其它字符请自行编码为合法字符（因不支持%，中文无法使用 urlencode 处理，请使用其他编码方式）
     * 加前缀处理的方法是 qun/Error.php/_initialize 务必用_下画线做分隔符,比如 bbs_123
     */
    public static function qun_code($id=0,$logo=''){
        if( config('webdb.wxapp_appid')=='' || config('webdb.wxapp_appsecret')==''){
            return 'http://x1.php168.com/public/static/qibo/nowxapp.jpg';
        }
        $path = config('upload_path') . '/qun_code/';
        $img_path  = $path.$id.'.png';
        if ( is_file($img_path) && (time()-filemtime($img_path)<3600*24) ) {
            return tempdir("uploads/qun_code/{$id}.png");
        }
        if (!is_dir($path)) {
            mkdir($path);
        }
        $access_token = wx_getAccessToken(true,true);
        if (empty($access_token)) {
            return 'access_token不存在!';
        }
        $code = http_curl('https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token='.$access_token,[
                'scene'=>$id,
                'page'=>'pages/hy/web/index',
                'width'=>'430',
        ],'json');
        if (strlen($code)>500) {
            write_file($img_path, $code);
            if ($logo!='') {
                self::make_logo($img_path,$logo);
            }
            return tempdir("uploads/qun_code/{$id}.png");
        }else{
            return $code;
        }
    }
    
    
    /**
     * 通用小程序二维码入口
     * @param string $url 要生成小程序二维码的URL网址
     * @param int $uid 当前用户UID
     * 小程序的关键字 取值 最大32个可见字符，只支持数字，大小写英文以及部分特殊字符：!#$&'()*+,/:;=?@-._~，其它字符请自行编码为合法字符（因不支持%，中文无法使用 urlencode 处理，请使用其他编码方式）
     * 加前缀处理的方法是 qun/Error.php/_initialize 务必用_下画线做分隔符,比如 bbs_123
     */
    public static function wxapp_codeimg($url='',$uid=0,$logo=''){
        if( config('webdb.wxapp_appid')=='' || config('webdb.wxapp_appsecret')==''){
            if (!is_file(PUBLIC_PATH."static/images/nowxapp.jpg")&&is_writable(PUBLIC_PATH."static/images/")) {                
                file_put_contents(PUBLIC_PATH."static/images/nowxapp.jpg",http_curl('http://x1.php168.com/public/static/qibo/nowxapp.jpg'));                
            }
            if(is_file(PUBLIC_PATH."static/images/nowxapp.jpg")){
                return PUBLIC_URL.'/static/qibo/nowxapp.jpg';
            }else{
                return 'http://x1.php168.com/public/static/qibo/nowxapp.jpg';
            }            
        }
        if ($uid===0) {
            $uid = login_user('uid');
        }
        $url = get_url($url);   //补全http
        $id = ShorturlModel::getId($url,2,$uid);
        $path = config('upload_path') . '/wxapp_codeimg/';
        $img_path  = $path.$id.'.png';
        if ( is_file($img_path) && (time()-filemtime($img_path)<3600*24) ) {
            return tempdir("uploads/wxapp_codeimg/{$id}.png");
        }
        if (!is_dir($path)) {
            mkdir($path);
        }
        $access_token = wx_getAccessToken(true,true);
        if (empty($access_token)) {
            return 'access_token不存在!';
        }
        $code = http_curl('https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token='.$access_token,[
            'scene'=>$id,
            'page'=>'pages/wap/iframe/index',
            'width'=>'430',
        ],'json');
        if (strlen($code)>500) {
            write_file($img_path, $code);
            if ($logo!='') {
                self::make_logo($img_path,$logo);
            }
            return tempdir("uploads/wxapp_codeimg/{$id}.png");
        }else{
            return $code;
        }
    }
    
    /**
     * 生成带LOGO图的小程序二维码
     * @param string $qrcode_path 原小程序二维码图
     * @param string $logo_path LOGO图片
     */
    public static function make_logo($qrcode_path='',$logo_path=''){
        $temp_logo = '';
        if ($logo_path=='') {
            return ;
        }elseif (strstr($logo_path,'://')) {
            $temp = str_replace(request()->domain(), ROOT_PATH, $logo_path);
            if (is_file($temp)) {
                $logo_path = $temp;
            }elseif ($string = file_get_contents($logo_path)) {
                $temp_logo = UPLOAD_PATH.md5($logo_path).'.png';
                file_put_contents($temp_logo, $string);
            }
        }elseif(!is_file($logo_path)){
            if(is_file(ROOT_PATH.$logo_path)){
                $logo_path = ROOT_PATH.$logo_path;
            }elseif(is_file(UPLOAD_PATH.$logo_path)){
                $logo_path = UPLOAD_PATH.$logo_path;
            }else{
                return ;
            }
        }
        if (!getimagesize($logo_path)) {
            return ;
        }
        $round_logo = UPLOAD_PATH.md5($qrcode_path).'.png';
        self::change_round($logo_path,$round_logo);
        self::make_logo_qrcode($qrcode_path,$round_logo);
        if (is_file($temp_logo)){
            unlink($temp_logo);
        }
    }
    
    /**
     * 把LOGO生成圆形图片
     * @param string $logo 原图
     * @param string $round_logo 生成的圆形LOGO
     */
    private static function change_round($logo_path='',$round_logo=''){
        //处理LOGO为圆形
        $avatar = imagecreatefromstring(file_get_contents($logo_path));
        $w = imagesx($avatar);
        $h = imagesy($avatar);
        $w = min($w, $h);
        $h = $w;
        
        $img = imagecreatetruecolor($w, $h);
        imagesavealpha($img, true);
        $bg = imagecolorallocatealpha($img, 255, 255, 255, 127);
        imagefill($img, 0, 0, $bg);
        
        $r = $w / 2; //圆半径
        $y_x = $r; //圆心X坐标
        $y_y = $r; //圆心Y坐标
        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $rgbColor = imagecolorat($avatar, $x, $y);
                
                if (((($x - $r) * ($x - $r) + ($y - $r) * ($y - $r)) < ($r * $r))) {
                    imagesetpixel($img, $x, $y, $rgbColor);
                }
            }
        }
        
        imagepng($img,$round_logo);
        imagedestroy($img);
        imagedestroy($avatar);
    }
    
    /**
     * 把LOGO加在二维码上面去
     * @param string $qr_path 二维码
     * @param string $logo_path 圆形LOGO
     */
    private static function  make_logo_qrcode($qr_path='', $logo_path=''){  //二维码与LOGO组合
        $qr_code = file_get_contents($qr_path);
        $logo = file_get_contents($logo_path);
        $qr_code = imagecreatefromstring($qr_code);  //生成的二维码底色为白色
        
        //设置二维码为透明底
        imagesavealpha($qr_code, true);  //这个设置一定要加上
        $bg = imagecolorallocatealpha($qr_code, 255, 255, 255, 127);   //拾取一个完全透明的颜色,最后一个参数127为全透明
        imagefill($qr_code, 0, 0, $bg);
        
        $icon = imagecreatefromstring($logo);  //生成中间圆形logo （微信头像获取到的logo的大小为132px 132px）
        
        $qr_width = imagesx($qr_code);  //二维码图片宽度
        $lg_width = imagesx($icon);  //logo图片宽度
        $lg_height = imagesy($icon);  //logo图片高度
        
        $qr_lg_width = $qr_width / 2.2;
        $scale = $lg_width / $qr_lg_width;
        $qr_lg_height = $lg_height / $scale;
        
        $start_width = ($qr_width - $qr_lg_width) / 2;
        
        // 目标图 源图 目标X坐标点 目标Y坐标点 源的X坐标点 源的Y坐标点 目标宽度 目标高度 源图宽度 源图高度
        imagecopyresampled($qr_code, $icon, $start_width, $start_width, 0, 0, $qr_lg_width, $qr_lg_height, $lg_width, $lg_height);
        
        imagepng($qr_code,$qr_path);
        imagedestroy($qr_code);
        imagedestroy($icon);
        unlink($logo_path);
    }
    
}