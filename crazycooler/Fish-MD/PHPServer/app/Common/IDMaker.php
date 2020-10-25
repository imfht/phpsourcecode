<?php

namespace App\Common;

class IDMaker
{
    public static function guid()
    {
        if (function_exists('com_create_guid')){
            return com_create_guid();
        }else{
            mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            /*$uuid = chr(123)// "{"
                .substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12)
                .chr(125);// "}"*/

            $uuid = substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12);
            return $uuid;
        }
    }


    public static function token($raw)
    {
        $ran = IDMaker::guid();

        str_replace('-','',$raw);
        str_replace('-','',$ran);

        $goal = '';

        $i = strlen($raw) - 1;
        $j = strlen($ran) - 1;

        while($i >= 0 && $j >= 0)
        {
            if($i >= 0)
                $goal .= $raw[$i--];

            if($j >= 0)
                $goal .= $ran[$j--];
        }
        return base64_encode(base64_encode($goal));
    }

    protected static function decode($cryptkey, $iv, $secretdata)
    {
        return openssl_decrypt($secretdata,'aes-256-cbc',$cryptkey,false,$iv);
    }

    protected static function encode($cryptkey, $iv, $secretdata)
    {
        return openssl_encrypt($secretdata,'aes-256-cbc',$cryptkey,false,$iv);
    }

    public static function new_token($user_id)
    {
        $cryptkey = hash('sha256','v3SfZgnWIEu5BcTt61umy0F1Pnjr1EbI',true);
        $iv = 'HJRfZ7nW9Eq6BaJc';
        $buf = time().'_'.$user_id.'_genolysesv1.1';
        $enc = IDMaker::encode($cryptkey,$iv,$buf);

        return $enc;
    }

    public static function get_user_id_by_token($token)
    {
        $cryptkey = hash('sha256','v3SfZgnWIEu5BcTt61umy0F1Pnjr1EbI',true);
        $iv = 'HJRfZ7nW9Eq6BaJc';
        $dec = IDMaker::decode($cryptkey,$iv,$token);

        $arr = explode('_',$dec);
        if(count($arr) == 3 && $arr[2] == "genolysesv1.1"){
            return $arr[1];
        }

        return 0;
    }
}