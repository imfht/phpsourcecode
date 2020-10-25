<?php
namespace app\member\controller\wxapp;

use app\common\controller\MemberBase;
use think\Db;

class Rmb extends MemberBase
{
    /**
     * 统计当前用户的RMB消费与收入日志.可以按月日星期
     * @param string $type 可以是week month day
     * @param string $nums 0代表当前,1代表上一个周期
     * @param string $in_out 可以是i 或o 收入或支出
     * @return void|\think\response\Json
     */
    public function index($type='week',$nums='0,1,2,3,4,5,6',$in_out='')
    {
        if (!in_array($type, ['week','day','month'])) {
            return $this->err_js('类型有误');
        }
        $data = [];
        foreach(explode(',',$nums) AS $num){
            if(!is_numeric($num)){
                continue ;
            }
            if($in_out=='i'){
                $data[] = $this->total($type,$num,true);    //收入
            }elseif($in_out=='o'){
                $data[] = $this->total($type,$num,false);    //支出
            }else{
                $t1 = $this->total($type,$num,true);    //收入
                $t2 = $this->total($type,$num,false);    //支出
                $data[] = [$t1,$t2];
            }
        }
        return $this->ok_js($data);
    }
    
    /**
     * 统计收入或支出
     * @param string $type 可以是week month day
     * @param string $add 是收入或支出
     * @param number $num 哪一个周期
     * @return number
     */
    private function total($type='week',$num=1,$add=true){
        if ($add) {
            $map = [
                'money'=>['>',0],
            ];
        }else{
            $map = [
                'money'=>['<',0],
            ];
        }
        $map['uid'] = $this->user['uid'];
        $map['posttime'] = fun('time@only',$type,$num);
        return Db::name('rmb_consume')->where($map)->sum('money');
    }
    
    /**
     * 打赏圈主
     * @param number $uid
     * @param number $money
     */
    public function give($uid=0,$money=0){
        if (empty(modules_config('qun'))) {
            return $this->err_js('你还没安装圈子');
        }elseif($money<0.01){
            return $this->err_js('打赏金额不能小于0.01元');
        }elseif($money>$this->user['rmb']){
            return $this->err_js('打赏金额不能大于你的可用余额',[
                'have_rmb'=>$this->user['rmb'],
                'pay_rmb'=>$money,
            ],2);
        }
        $info = fun('qun@getByid',-$uid);
        if (empty($info)) {
            return $this->err_js('圈子信息不存在');
        }elseif($info['uid']==$this->user['uid']){
            return $this->err_js('你不能自己给自己打赏');
        }
        add_rmb($this->user['uid'],-$money,0,'打赏圈主:'.$info['title']);
        add_rmb($info['uid'],$money,0,'作为 '.$info['title'].' 的圈主被 '.$this->user['username'].' 打赏');
        $quner = get_user($info['uid']);
        $_msg = '帐户余额';
        if ($money>=0.3 && $quner['weixin_api'] && $this->webdb['P__alilive']['wxgive_qun_rmb']) {
            $array = [
                'money'=>$money,
                'title'=>'打赏提现',
                'id'=>$quner['weixin_api'],
            ];
            $res = \app\common\util\Weixin::gave_moeny($array);
            if($res===true){
                add_rmb($info['uid'],-$money,0,'打赏提现');
                $msg = '感谢你的打赏，金额已转到圈主微信钱包';
                $_msg = '微信钱包';
            }else{
                $msg = '感谢你的打赏，金额已转到圈主帐户余额,无法转到圈主微信钱包,原因如下:'.$res;                
            }
        }else{
            $msg = '感谢你的打赏，金额已转到圈主帐户余额';
        }
        $title = '有人给你打赏了';
        $content = '作为 '.$info['title'].' 的圈主被 '.$this->user['username'].' 打赏了 '.$money.' 元，金额已转到你的'.$_msg.'，请注意查收';
        send_msg($info['uid'],$title,$content);
        $quner['weixin_api'] && send_wx_msg($quner['weixin_api'], $content);
        return $this->ok_js([],$msg);
    }
}
