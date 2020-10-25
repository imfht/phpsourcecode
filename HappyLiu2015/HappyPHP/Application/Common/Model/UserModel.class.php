<?php
/**
 * Created by PhpStorm.
 * $$Id User: Administrator Date: 15-11-16 Time: 下午2:09
 */

namespace Common\Model;

use Com\WechatAuth;
use Think\Model;

class UserModel extends Model {
    protected $tableName = 'User';

    /**
     * 添加用户
     * @param array $data 用户数组
     */
    public function addUser($data) {
        $uid = null;
        $where = array();
        $where['openid'] = $data['openid'];
        $is_exist = $this->where($where)->find();

        // 新增
        if (!$is_exist && $data['openid']) {
            $data['updatetime'] = time();
            $data['state'] = $data['state'] ? $data['state'] : 1;
            $data['addtime'] = time();
            $userinfo = $this->getUserDetailInfo($data['openid']);
            $uid = $this->add(array_merge($data, $userinfo));
        } elseif ($is_exist && $data['openid']) {
            $uid = $is_exist['uid'];
            $where = array();
            $where['uid'] = $uid;

            $update = array();
            $update['updatetime'] = time();
            $update = array(
                $update,
                $data
            );
            $this->where($where)->save($update);
        } else {
            write_log('add user fail: union_id is null :' . implode('，', $data));
        }
        return $uid;
    }

    /**
     * 根据openid来获取用户信息
     *
     * @param string $openid
     */
    public function getUserinfoByOpenid($openid = null) {
        if($openid != null) {
            return $this->where(array('openid'=>$openid))->find();
        }
        return array();
    }


    /**
     * 获取用户详细信息，并且入库
     *
     * @param string $openid
     */
    public function getUserDetailInfo($openid, $uid = 0) {
        $access_token = S("access_token");
        $appid     = C('AppID');
        $appsecret = C('AppSecret');
        if(!$access_token) {
            $auth  = new WechatAuth($appid, $appsecret);
            $token = $auth->getAccessToken();
            $access_token = $token['access_token'];
            $expires_in = $token['expires_in'];
            if ($expires_in <= 0) {
                $expires_in = 5;
            }
            S('access_token', $access_token, $expires_in);
        }

        if($access_token) {
            $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $access_token . '&openid=' . $openid . '&lang=zh_CN';
            $rs1 = https_request($url);

            $rs_obj = json_decode($rs1);
            $rs = obj_to_arr($rs_obj);

            if (empty($rs) && json_last_error() != 0) {
                $str = substr(str_replace('\"', '"', json_encode($rs1, JSON_UNESCAPED_UNICODE)), 1, - 1);
                $rs = json_decode($str, true);
                $rs['headimgurl'] = str_replace('\\', "", $rs['headimgurl']);
            }
            if (is_array($rs) && !empty($rs['openid'])) {
                $update = array();
                $update['logo'] = $rs['headimgurl'];
                $update['wx_nickname'] = $rs['nickname'];
                $update['sex'] = $rs['sex'];
                $update['language'] = $rs['language'];
                $update['city'] = $rs['city'];
                $update['province'] = $rs['province'];
                $update['country'] = $rs['country'];
                $update['union_id'] = $rs['unionid'];

                if ($uid) {
                    $update['updatetime'] = time();
                    $where = array();
                    $where['id'] = $uid;

                    $user_rs = $this->where($where)->save($update);
                    if (!$user_rs) {
                        write_log('update UserDetailInfo fail:' . $this->getLastSql());
                        return false;
                    }
                }

                return $update;
            } else {
                write_log('getUserDetailInfo fail:' . $rs1.'.openid:'.$openid.'.error code:'.json_last_error());
            }
        } else {
            write_log('Get Access Token error.');
        }

        return false;
    }
} 