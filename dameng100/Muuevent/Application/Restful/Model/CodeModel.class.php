<?php
/**
 * @author 大蒙<59262424@qq.com>
 */

namespace Restful\Model;


use Think\Model;

class CodeModel extends Model{

	/**
	 * 返回码及说明
	 * @param  integer $code [description]
	 * @return [array]        [description]
	 */
	public function code($code=200,$info='请求成功'){

		switch ($code) {
		//接口授权部分
            //通用部分
            case 400:
                $result['code'] = 400; //语法格式有误，服务器无法理解此请求
                $result['info'] = '请求出错';
            break;
            case 403:
                $result['code'] = 403; //未授权的请求
                $result['info'] = '服务器接受请求，但是被拒绝处理';
            break;
            case 415:
                $result['code'] = 415; //未授权的请求
                $result['info'] = '数据格式并不被请求的资源支持';
            break;
            case 200:
            	$result['code'] = 200;
            	$result['info'] = $info;
            break;
            //用户授权部分
            case 1000:
            	$result['code'] = 1000;
            	$result['info'] = '用户名错误';
            break;
            case 1001:
            	$result['code'] = 1001;
            	$result['info'] = '密码错误';
            break;
            case 1002:
            	$result['code'] = 1002;
            	$result['info'] = '需要登陆';
            break;
            case 1003:
                $result['code'] = 1003;
                $result['info'] = '需要用户授权token';
            break;
            case 1004:
                $result['code'] = 1004;
                $result['info'] = '不存在的用户';
            break;
            case 1005;
                  $result['code'] = 1005;
                  $result['info'] = '验证失败';
            break;
            case 1006;
                  $result['code'] = 1006;
                  $result['info'] = '已存在的用户';
            break;


            //资源错误
            case 2000:
            	$result['code'] = 2000;
            	$result['info'] = '资源过期';
            break;
            case 2001:
            	$result['code'] = 2001;
            	$result['info'] = '资源不存在或已删除';
            break;
            //验证码
            case 3000:
                  $result['code'] = 3000;
                  $result['info'] = '错误的手机号或邮箱';
            case 3001:
                  $result['code'] = 3001;
                  $result['info'] = '验证超时，请重新发送';
            case 3002:
                  $result['code'] = 3002;
                  $result['info'] = '发送失败';
            break;
            case 3003:
                  $result['code'] = 3003;
                  $result['info'] = '验证码错误';
            break;
            //默认输出未知错误
            default:
            	$result['code'] = 10000;
            	$result['info'] = '未知错误';
		}

        return $result;
	}
}