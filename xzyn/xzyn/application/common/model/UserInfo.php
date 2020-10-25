<?php
namespace app\common\model;
// 用户信息模型
use think\Model;

class UserInfo extends Model
{
    protected $insert  = ['avatar','wx_imgurl','birthday'];

    protected function setAvatarAttr($value)	//头像url
    {
        if ($value){
            return $value;
        }else{
            return '/static/common/img/default.png';
        }
    }

    protected function setWxImgurlAttr($value) 	//微信二维码url wx_imgurl 字段[修改器]
    {
        if ($value){
            return $value;
        }else{
            return '/static/common/img/logo.jpg';
        }
    }

    protected function setBirthdayAttr($value) 	//生日字段  birthday 字段[修改器]
    {

        return strtotime($value);

    }

    public function getBirthdayAttr($value, $data)	//生日字段 birthday 字段[获取器]
    {
    	if ($value){
            return date('Y-m-d', $value);
        }else{
        	return date('Y-m-d', time());
		}

    }

    public function getAvatarTurnAttr($value, $data)	//头像url [获取器]
    {
		if(strstr($data['avatar'],"http")){
		 	$img_url = $data['avatar'];
		}else{
			if(strstr($data['avatar'],"uploads")){
				$img_url = request()->domain(). $data['avatar'];
			}else{
				$img_url = request()->domain(). '/static/common/img/default.png';
			}
		}
        return $img_url;
    }

    public function getWxImgurlTurnAttr($value, $data)	//微信二维码url[获取器]
    {
		if(strstr($data['wx_imgurl'],"http")){
		 	$img_url = $data['wx_imgurl'];
		}else{
			if(strstr($data['wx_imgurl'],"uploads")){
				$img_url = request()->domain(). $data['wx_imgurl'];
			}else{
				$img_url = request()->domain(). '/static/common/img/logo.jpg';
			}
		}
        return $img_url;
    }


}