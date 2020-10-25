<?php
class Main{
    /**
     * 把数组中的某些值变为key，和value
     * @param array $arr
     * @param string   $key
     * @param string $val
     *
     */

    public static function arrayKeyValue($arr,$key,$val){
        $new = array();
        foreach($arr as $k=>$v)
        {

            if(is_object($v))
            {
                $v = @((array)$v->attributes());
                $v = @$v['@attributes'];

            }

            $new[$v[$key]] = $v[$val];

        }
        return $new;
    }

    /**
     * @param $string
     * @return string
     */
    function String2Hex($string){
        $hex='';
        for ($i=0; $i < strlen($string); $i++){
            $hex .= dechex(ord($string[$i]));
        }
        return $hex;
    }

    /**
     * @param $hex
     * @return string
     */
    function Hex2String($hex){
        $string='';
        for ($i=0; $i < strlen($hex)-1; $i+=2){
            $string .= chr(hexdec($hex[$i].$hex[$i+1]));
        }
        return $string;
    }

    /**
     * @param $data
     * @return string
     */
    public function decryKey($pws){

        $private = '-----BEGIN RSA PRIVATE KEY-----
MIICXgIBAAKBgQDi7SpFLpsAtn17R8yQrEI1d+Y7wfMnosfDzLGl9d2mkPB715/T
AOEh+SYNz5luMecntldXQyAmRQ2nafl/evjhw7P4lvH7rGlO6EPvepZDuawdyjFA
/TDyZrT7LGfk41felDU2nLzMx+uX35Q2NzopYJpGzK376Ny+pJYV1XUiDwIDAQAB
AoGBAL/v6bjqWqCXujrz18rmaHnhGBOjUI9N17l9ASVmeDvSjBWzo7NNIx8hJVa5
KQVToDiuueFNHXxBG/NmZ2m0EZ0LtrrS7CrQbvyPBi+z7iv5KRVAT1IUIU0eAWMP
8gUkKQCJYxOvf4lA2U6xqYM4SC5GE/Ht14qq4HhjVYrbAq4BAkEA8hvjl4oLs4FX
6OItZirIvBT5Uo7iNrnZQQfVcx4fE+0LgYvQNAgH34XI5Cm42LT49LBNxWLg+vHJ
/6A9OS+iLwJBAO/yRj1CMFkkfx3QFdOzMYH7a/OkNNzD3doN8P7VlyfWia/QZEqL
DbCiVBXeVJKGV1V6NehTYPfXgcLb/f9V5iECQQC2IR7L//AfgFy2d3c5lVPekVSh
s5UfIB38Gr2K1Q0B+1+de6ULj0ME9mqSoYRJmZJy2DIZG/ItNTkFEPEdlOTHAkBj
Yl03eFfgRF6mcY7o3bru1L2079m5ayNT8xxT4RI3vQPQn6c6vPfRpprfZ/RtsFky
HWmArjBm14t2s3o0LVchAkEA0RjHhvc0rJdA1Rt0QxVD+sEo8BhVOWpaggnI1gIU
qtG1DeMIDJE/nVfA6yxP58UlyCHYtjMycIw9R4FIhNINrQ==
-----END RSA PRIVATE KEY-----';
        if (!$privateKey = openssl_pkey_get_private($private)) return false;

        //Decrypt
        $decrypted_text = "";
        if (!openssl_private_decrypt(base64_decode($pws), $decrypted_text, $privateKey)) return false;

        //Decrypted :)

        //Free key
        openssl_free_key($privateKey);
        return $decrypted_text;
    }

    /*
       * 取得useragent
       * */

    public function userAgens(){
        $mobile = array(
            'Mozilla/5.0 (iPhone; CPU iPhone OS 8_0 like Mac OS X) AppleWebKit/600.1.3 (KHTML, like Gecko) Version/8.0 Mobile/12A4345d Safari/600.1.4',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 7_0 like Mac OS X; zh-cn) AppleWebKit/537.51.1 (KHTML, like Gecko) Version/7.0 Mobile/11A465 Safari/9537.53',
            'Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_2_1 like Mac OS X; zh-cn) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8C148 Safari/6533.18.5',
            'Mozilla/5.0 (Linux; Android 4.3; Nexus 10 Build/JSS15Q) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2307.2 Mobile Safari/537.36',
            'Mozilla/5.0 (PlayBook; U; RIM Tablet OS 2.1.0; en-US) AppleWebKit/536.2+ (KHTML like Gecko) Version/7.2.1.0 Safari/536.2+',
            'Mozilla/5.0 (Linux; Android 4.3; Nexus 10 Build/JSS15Q) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2307.2 Safari/537.36',
            'Mozilla/5.0 (Linux; U; Android 4.4.2; en-us; LGMS323 Build/KOT49I.MS32310c) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/30.0.1599.103 Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; U; Android 4.3; en-us; SM-N900T Build/JSS15J) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30',
            'Mozilla/5.0 (Linux; Android 4.4.4; HM NOTE 1LTE Build/KTU84P) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/33.0.0.0 Mobile Safari/537.36 LieBaoFast/3.0.9',
            'Mozilla/5.0 (iPad; CPU OS 7_0 like Mac OS X) AppleWebKit/537.51.1 (KHTML, like Gecko) Version/7.0 Mobile/11A465 Safari/9537.53',
            'Mozilla/5.0 (iPad; CPU OS 4_3_5 like Mac OS X; zh-cn) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8L1 Safari/6533.18.5',


        );
        $desk = array(
            'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:39.0) Gecko/20100101 Firefox/39.0',
            'Windows / Chrome 34: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.137 Safari/537.36',

        );

        return array('mobile'=>$mobile,'desk'=>$desk);
    }
    /**
     * 以不同的useragent file_get_contents
     *
     */
    public static function curl2($url, $userAgent = '',$cookieFile='') {
        $url = preg_replace("/&amp;/", "&", $url);

        $uas = Main::userAgens();
        $ua = array_merge($uas['mobile'],$uas['desk']);
        $k = array_rand($ua);
        $u = $userAgent ? $userAgent : $ua[$k];
        $ch = curl_init();


        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_USERAGENT, $u);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if($cookieFile)
        {

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
        }


        $r = curl_exec($ch);
        curl_close($ch);


        return $r;
    }
    /*
 * api code init
 * */
    public static function apiCodeInit($status = 0) {
        if ($status) $res['status'] = array(
            'errorcode' => 'OK',
            "errorinfo" => ""
        );
        else $res['status'] = array(
            'errorcode' => 'ERROR',
            "errorinfo" => "some error"
        );
        $res['data'] = array();
        return $res;
    }
    /*
    * 密码加密
    * */
    public function myMd5($str, $length = 10, $param = '!%#$%#$%$^#') {
        return substr(md5(md5($str . $param)) , 10, $length);
    }

    /**
     * @param $err
     * @return array
     */
    public function getErrors($err){
        $arr = [];
        foreach(($err) as $key=>$val)
        {
            foreach($val as $k=>$v)
                $arr[] = $v;
        }
        return $arr;
    }
}
?>