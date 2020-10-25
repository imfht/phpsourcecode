<?php

/**
 * 常用的数据校验
 * Date: 16-10-6
 * Time: 下午10:21
 * author :李华 yehong0000@163.com
 */
namespace tool;
class check
{
    /**
     * 字符长度验证
     * @param $str
     * @param int $min
     * @param int $max
     * @param $remark
     * @param $must 是否必填验证
     * @return mixed
     * @throws \Exception
     */
    public static function checkStrLen($str,$min=1,$max=30,$remark,$must=true)
    {
        if($must===false && !$str){
            return $str;
        }
        $len=mb_strlen($str);
        if(!$len){
            throw new \Exception($remark.'是必填的哦',-4100);
        }
        if($len < $min){
            throw new \Exception($remark.'长度不能少于'.$min.'个字符哦',-4101);
        }
        if($len > $max){
            throw new \Exception($remark.$max.'个字符就够了~',-4102);
        }
        return $str;
    }

    /**
     * 人民币验证
     * @param $val
     * @param $remark
     * @param $must
     * @param $type
     * @param $min
     * @param $max
     * @return int
     * @throws \Exception
     */
    public static function checkMoney($val,$remark,$must,$type,$min,$max){
        if($must===false && !$val){
            return 0;
        }
        if(!$val || !is_numeric($val) || $val < 0){
            throw new \Exception($remark.'需要一个正确的金额数值~', -4103);
        }
        $valArr=explode('.',$val);
        if(strlen($valArr[1])>2){
            throw new \Exception($remark.'应该是如100.00的正确人民币金额数值', -4104);
        }
        if($type && ($val < $min || $val > $max)){
            throw new \Exception($remark."只能介于{$min}元到{$max}元之间", -4105);
        }
        return $val;
    }

    /**
     * 时间验证，默认返回时间戳
     * @param $time
     * @remark  时间称谓
     * $param $type 0返回时间戳,1返回格式化的标准日期
     */
    public static function checkTime($time,$remark,$type)
    {
        if(!$time){
            throw new \Exception($remark.'不能为空哦~',-4106);
        }
        if(is_numeric($time)){
            if($time < 0){
                throw new \Exception($remark.'不是正确的时间格式，请确认~',-4107);
            }
        }
        if(is_string($time)){
            $time=strtotime($time);
            if(!$time){
                throw new \Exception($remark.'不是正确的时间格式，请确认~',-4108);
            }
        }
        return $type?date('Y-m-d H:i:s',$time):$time;
    }

    /**
     * 邮箱验证
     * @param $val
     * @param $must 是否必填验证
     * @return mixed
     * @throws Exception
     */
    public static function checkEmail($val,$must)
    {
        if($must===false && !$val){
            return $val;
        }
        $rule= '/^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/';
        if(preg_match($rule,$val)){
            return $val;
        }else{
            throw new \Exception('请填写正确的邮箱~', -4109);
        }
    }
    /**
     * @param $val
     * @param $must 是否必填验证
     * @return mixed
     * @throws Exception
     */
    public static function checkMobile($val,$remark,$must=true){
        if($must===false && !$val){
            return $val;
        }
        if(preg_match("/^0?1[3|4|5|7|8][0-9]\d{8}$/",$val)){
            return $val;
        }else{
            throw new \Exception(($remark?:'手机号码').'格式不正确~', -4110);
        }
    }
    /**
     * 电话，传真
     * @param $val
     * @param $remark
     * @param $must 是否必填验证
     * @return mixed
     * @throws Exception
     */
    public static function checkTel($val,$remark,$must)
    {
        if($must===false && !$val){
            return $val;
        }
        $isTel="/^([0-9]{3,4}-)?[0-9]{7,8}$/";
        if(!preg_match($isTel,$val)){
            throw new \Exception($remark.'格式不正确哦~',-4111);
        }
        return $val;
    }

    /**
     * @param $val
     * @param $remark
     * @param $must 是否必填验证
     */
    public function checkNumber($val,$remark,$must)
    {
        if($must===false && !$val){
            return $val;
        }
        if(!is_numeric($val)){
            throw new \Exception($remark.'不是有效的数字',-4112);
        }
        return $val;
    }
    /**
     * 网址验证
     * @param $val
     * @param $must 是否必填验证
     * @return mixed
     * @throws Exception
     */
    public static function checkUrl($val,$must)
    {
        if($must===false && !$val){
            return $val;
        }
        $str=strtolower(substr($val,0,4));
        if($str != 'http'){
            $val='http://'.$val;
        }
        if (!filter_var($val, FILTER_VALIDATE_URL)){
            throw new \Exception('网址格式不正确',-4113);
        }
        return $val;
    }
}