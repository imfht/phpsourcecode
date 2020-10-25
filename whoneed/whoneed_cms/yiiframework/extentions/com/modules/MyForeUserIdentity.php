<?php
/*
 用户登入验证，前台验证
 UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class MyForeUserIdentity extends CUserIdentity
{
    private  $id;
    //重写getId
    public function getId(){
        return $this->id;
    }
    
    public function authenticate(){
        $api_url = Yii::app()->params['patabom_api_url'].'?c=home&m=login&account='.$this->username.'&password='.$this->password;
        $result = MyFunction::get_url($api_url);
        //var_dump($api_url);var_dump($result);die();
        if($result && $result['code']==200 && !empty($result['content'])){
            $login_obj=json_decode($result['content']);
            if( isset($login_obj->flag) && $login_obj->flag==='success' ){
                $this->setState('user_id', $login_obj->data->user_id);
				$this->setState('account', $login_obj->data->account);
				$this->setState('account_type', $login_obj->data->account_type);
				$this->setState('user_name',$login_obj->data->user_name);
                $this->setState('head_url', $login_obj->data->head_url);
				$this->setState('sex', $login_obj->data->sex);
                $this->setState('pwd_code', $login_obj->data->pwd_code);
                $this->id = $login_obj->data->user_id;
                $this->errorMessage = self::ERROR_NONE;
            }else if( isset($login_obj->info) ){
                $this->errorMessage = $login_obj->info;
            }else{
                $this->errorMessage = '系统繁忙!请稍候重试';
            }
        }else{
            $this->errorMessage = '系统繁忙!请稍候重试';
        }
        return !$this->errorMessage;
    }

    public function registAuthenticate($type){
        $com_imei =$this->createComImei();
        $api_url = Yii::app()->params['patabom_api_url']
                  .'?c=home&m=regist&account='.urlencode($this->username).'&password='.$this->password.'&type='.$type.'&com_imei='.$com_imei;
        $result = MyFunction::get_url($api_url);
        if($result && $result['code']==200 && !empty($result['content'])){
            $regist_obj=json_decode($result['content']);
            if( isset($regist_obj->flag) && $regist_obj->flag==='success' ){
                //自动登录
                $this->authenticate();
            }else if( isset($regist_obj->info) ){
                $this->errorMessage = $regist_obj->info;
            }else{
                $this->errorMessage = '系统繁忙!请稍候重试';
            }
        }else{
            $this->errorMessage = '系统繁忙!请稍候重试';
        }
        return !$this->errorMessage;
    }

    //时间戳加随机数生成唯一id
    public function createComImei(){
        $cookie  = Yii::app()->request->getCookies();
        if( empty($cookie['imei']->value) || !preg_match('/^pc:[A-Za-z1-9]{32}$/',$cookie['imei']->value)){
            $com_imei  = 'pc:';
            $timestamp = time();
            //随机数
            $str       = rand();
            $com_imei .= md5($timestamp.$str);
            $cookie = new CHttpCookie('imei',$com_imei);
            $cookie->expire = time()+60*60*24*365;  //有限期一年
            Yii::app()->request->cookies['imei']=$cookie;
            return $com_imei;
        }else{
            return $cookie['imei']->value;
        }
    }
}