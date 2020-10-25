<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/19
 * Time: 20:53
 */

namespace backend\tools;


class ResponseUtils {


    public static function response_data($ret,$msg = '操作'){

        if($ret){

            return ['status'=>'1','msg'=>$msg.'成功'];
        }else{

            return ['status'=>'2','msg'=>$msg.'失败'];
        }

    }

}