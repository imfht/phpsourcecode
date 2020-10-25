<?php
/**
 * api接口安全验证
 * @author 大蒙<59262424@qq.com>
 */

namespace Restful\Model;

use Think\Model;

class SignModel extends Model{

	/**验证请求合法性
     * [checkSign description]
     * @return [type] [description]
     */
    public function checkSign($timestamp,$noce,$signature){

    	$s_signature = $this->createSignature($timestamp,$noce);

    	if($s_signature === $signature){
    		return ture;
    	}
    }

    /**
     * 时间戳，随机数，口令按照首字母大小写顺序排序
	 * 然后拼接成字符串
	*  进行sha1加密
	 * 再进行MD5加密
	 * 转换成大写。
     * @param $timeStamp 时间戳
     * @param $noce, 随机字符串
     * @return string 返回签名
     */
    private function createSignature($timestamp,$noce){
        $arr['timestamp'] = $timestamp;
        $arr['noce'] = $noce;
        $arr['secret'] = modC('RESTFUL_CONFIG_SECRET','','Restful');
        //按照首字母大小写顺序排序
        sort($arr,SORT_STRING);
        //拼接成字符串
        $str = implode($arr);
        //进行加密
        $signature = sha1($str);
        $signature = md5($signature);
        //转换成大写
        $signature = strtoupper($signature);
        return $signature;
    }

}
