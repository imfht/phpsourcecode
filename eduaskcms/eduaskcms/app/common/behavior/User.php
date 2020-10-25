<?php
namespace app\common\behavior;

class User {
    
    //登录日志
    public function login($params)
    {
        $login_time=date('Y-m-d H:i:s');
        $data['user_id']  = $params['id'];
        $data['ip']       = request()->ip();
        $data['success']  = true;
        $data['create']   = $login_time ;
        
        model('UserLogin')->isValidate(false)->isUpdate(false)->save($data);        
        model('User')->isValidate(false)->save(['logined'=>$login_time, 'logined_ip' => request()->ip()], ['id' => $params['id']]);
    }  
}
