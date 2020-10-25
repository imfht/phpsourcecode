<?php
/*
 *  2016年12月30日 星期五
 *  数据库服务逻辑库 - 技术交流
*/
namespace app\Server;
use hyang\Logic;
use think\Db;
class Geek extends Logic
{
    public function checkVisit()
    {
        $ret = false;
        $code = isset($_GET['code'])? $_GET['code']:null;
        if($code) $map = ['pro_code'=>$code];
        $uCode = uInfo('code');
        if(empty($uCode)) $map['private_mk'] = 'N';
        
        if($code){ // 过滤无效名称
            $valid = Db::table('project_list')->where($map)->count();
            $code = $valid? $code:null;
        }
        if($code){
            $data = Db::table('project_list')->where($map)->find();
            // 公共项目有效
            if($data['private_mk'] == 'N') return $data;
            else{                
                // 本人登录 有效
                if($uCode == $data['user_code']) return $data;
            }
            //&& uInfo('code')
        }
        return $ret;
    }
}