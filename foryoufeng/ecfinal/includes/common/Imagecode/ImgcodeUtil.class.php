<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/18/16
 * Time: 2:33 PM
 */
include_once('/includes/common/Imagecode/GeetestLib.class.php');
include_once('/includes/common/Imagecode/Config.class.php');
trait ImgcodeUtil
{
    /**
     *生成验证码
     */
    function getCode(){

       $sdk=new GeetestLib(Config::CAPTCHA_ID,Config::PRIVATE_KEY); echo "aa";return;
        $user_id='1906592238@qq.com';
        $status=$sdk->pre_process($user_id);
        session('gtserver',$status);
        session('gtid',$user_id);
        $sdk->pre_process();
        echo $sdk->get_response_str();
   }

    /**
     * 对图片验证码进行验证
     * @param $change　
     * @param $validate
     * @param $seccode
     * @return int
     */
   protected function validCode($change,$validate,$seccode){
       $sdk=new GeetestLib(Config::CAPTCHA_ID,Config::PRIVATE_KEY);
      if(session('gtserver')==1){
          $result=$sdk->success_validate($change,$validate,$seccode,session('gtid'));
      }else{
          $result=$sdk->fail_validate($change,$validate,$seccode);
      }
       return $result;
   }
}