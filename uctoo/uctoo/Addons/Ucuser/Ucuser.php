<?php

namespace addons\ucuser;

use think\addons\Addons;
use com\TPWechat;
use com\Wxauth;
use app\common\model\Ucuser as UcuserModel;

/**
 * 微会员插件
 * @author UCToo
 */

    class Ucuser extends Addons{

        public $info = array(
            'name'=>'Ucuser',
            'title'=>'微会员',
            'description'=>'微会员用户中心',
            'status'=>1,
            'author'=>'UCToo',
            'version'=>'0.1'
        );

        public function install(){

            return true;
        }

        public function uninstall(){

            return true;
        }


        /**
         * 实现的init_ucuser钩子方法，对公众号粉丝进行初始化，在需要初始化粉丝信息的地方通过 hook('init_ucuser',$params); 调用
         * @params string $mp_id   公众号在系统中的唯一标识，member_public表的id，必填
         * @params string $weObj   公众号实例
         * @return void      hook函数木有返回值
         * 注意：
         */
        public function init_ucuser($params){

            if($params['mp_id'] && $params['weObj'] instanceof TPWechat){   //带有公众号在系统中唯一ID，存在公众号实例，例如weixincontroller中的被动响应
                   $map['openid'] = get_openid();
                   $map['mp_id'] = $params['mp_id'];
                   $ucuser = model('Ucuser');
                   $data = $ucuser->where($map)->find();
                    if(!$data){                                             //公众号没有这个粉丝信息，就注册一个
                        $mid = $ucuser->registerUser( $map['mp_id'] ,$map['openid']);    //微信粉丝表ucuser表的mid字段
                        get_ucuser_mid($mid);                               //设置session中mid
                    }else{
                        get_ucuser_mid( $data['mid']);                               //设置session中mid
                    }
            }else{                                                          //不存在公众号实例或没显式传mp_id参数，例如分享到朋友圈的内容,访问参数中必须带有公众号在系统中唯一标识mp_id
                $umap['openid'] = get_openid();                           //只存在公众号信息的，在get_openid中通过oauth获取用户openid
                $umap['mp_id'] = input ( 'mp_id' );                          //从controller的访问请求中获取mp_id
                if(!empty($umap['mp_id'])){
                    $ucuser = new UcuserModel();
                    $data = $ucuser->where($umap)->find();
                    if(!$data){                                             //公众号没有这个粉丝信息，就注册一个
                          $mid = $ucuser->registerUser($umap['mp_id'] ,$umap['openid']);    //微信粉丝表ucuser表的mid字段
                        get_ucuser_mid($mid);                               //设置session中mid
                    }else{
                        get_ucuser_mid( $data['mid']);                               //设置session中mid
                    }
                }else{                                                      //没有公众号信息，未能初始化粉丝

                }
            }
        }
    }