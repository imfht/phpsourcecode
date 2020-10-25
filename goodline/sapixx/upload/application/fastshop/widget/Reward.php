<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 奖励计算
 */
namespace app\fastshop\widget;
use app\fastshop\model\BankAll;
use app\fastshop\model\RegNum;

class Reward{

     /**
     * 代理收益
     * @param integer $miniapp_id   来源小程序
     * @param integer $uid  用户ID
     * @param float $cash_fee  (元)
     * @param object $config  系统配置
     * @return void
     */
    public function agent(int $miniapp_id,int $uid,$order,$config){
        $cash_fee = $order->real_amount;
        $rel = model('Agent')->agentUid(['user_id' => $uid],$miniapp_id);
        if(empty($rel)){
            return;
        }
        $rebate = $rel['rebate']/1000;  //反比(千分之x)
        if($rebate <= 0){
            return;
        }
        $user_id  = $rel['user_id'];          //反比
        $shopping = $config['shopping']/100;  //购物金比例
        //推荐收益结算
        $print = $cash_fee*$rebate;
        $shop  = money($print*$shopping);    //购物
        $due   = money($print-$shop);       //剩下多少
        $shop_print = intval($shop*100);    //换算成分
        $due_print  = intval($due*100);
        if ($due_print > 0) {
            model('Bank')->due_up($miniapp_id, $user_id,$due_print,$shop_print);
            model('BankLogs')->add($miniapp_id, $user_id,intval($print*100),money($print).'代理奖励',$order->user_id,$order->order_no);
        }
    } 

    /**
     * 推荐人收益(方式1)
     * 直接按照推荐人和间接推荐人的成交额的比例奖励给推荐人
     * @param integer $miniapp_id   来源小程序
     * @param integer $uid  用户ID
     * @param float   $cash_fee  (元)
     * @param object  $config  系统配置
     * @return void
     */
    public function level(int $miniapp_id,int $uid,$order,$config){
        $cash_fee = $order->real_amount;
        $level = model('SystemUserLevel')->where(['user_id' => $uid,'level'=>[1,2]])->select();
        $level1 = 0;
        $level2 = 0;
        foreach ($level as $value) {
            if($value['level'] == 1){
                $level1 = $value['parent_id'];
            }
            if($value['level'] == 2){
                $level2 = $value['parent_id'];
            }
        }
        $shopping = $config['shopping']/100;  //购物金比例
        //一级
        if($level1){
            $small_print = $cash_fee*($config->reward_nth/100);
            $small_shop = money($small_print*$shopping);    //购物
            $small_due  = money($small_print-$small_shop);  //剩下多少
            $small_shop_print = intval($small_shop*100);    //换算成分
            $small_due_print  = intval($small_due*100);
            if($small_due_print > 0){
                model('Bank')->due_up($miniapp_id,$level1,$small_due_print,$small_shop_print);
                model('Bank')->isProfit($level1,$small_print); 
                model('BankLogs')->add($miniapp_id,$level1,intval($small_print*100),'活动奖励['.money($small_print).']',$order->user_id,$order->order_no);
            }
        }    
        //二级
        if($level2){
            $big_print = $cash_fee*($config->reward_ratio/100);
            $big_shop = money($big_print*$shopping);    //购物
            $big_due  = money($big_print-$big_shop);  //剩下多少
            $big_shop_print = intval($big_shop*100);           //换算成分
            $big_due_print  = intval($big_due*100);
            if($big_due_print > 0){
                model('Bank')->due_up($miniapp_id,$level2,$big_due_print,$big_shop_print);
                model('Bank')->isProfit($level2,$big_print); 
                model('BankLogs')->add($miniapp_id,$level2,intval($big_print*100),'活动奖励['.money($big_print).']',$order->user_id,$order->order_no);
            }
        }
    }

    /**
     * 推荐人收益（2）
     * 直接按照产品利润的50%递减给满足条件的推荐人
     * @param integer $miniapp_id   来源小程序
     * @param integer $uid  用户ID
     * @param float  $cash_fee  (元)
     * @param object $config  系统配置
     * @return void
     */
    public function performance(int $miniapp_id,int $uid,$order,$config){
        $cash_fee = $order->real_amount;
        if($cash_fee <= 0){
            return;
        }
        //计算利润    
        $cash_fee = $cash_fee*($config->profit/100);  //计算利润比
        if($cash_fee <= 0){
            return;
        }
        //递归计算层级利润
        $money = numProgress($cash_fee,$config->reward_nth);
        $level = count($money);
        //查询曾经用户
        $levelUser = model('SystemUserLevel')->where(['user_id' => $uid])->where('level','<=',$level)->field('parent_id,level')->select()->toArray();
        if(!empty($levelUser)){
            $temp_key  = array_column($levelUser,'level');  //键值
            $levelUser = array_combine($temp_key,$levelUser) ;
        }
        //查找每个用户的直推人数,如果没有就实时统计并增加,如果有就读取当前用户
        $uids = array_column($levelUser,'parent_id');
        $num  = RegNum::where(['uid'  => $uids])->select()->toArray();
        $num_uid = array_column($num,'uid');
        $uids = array_diff($uids,$num_uid);
        foreach ($uids as $key => $value) {
            RegNum::countMum($miniapp_id,$value);  
        }
        //人数统计
        $regnum = [];
        foreach ($num as $key => $value) {
            $regnum[$value['uid']] = $value['num'];
        }
        //用户ID和利润配对
        $uida = [];
        foreach ($money as $key => $value) {
            if (isset($levelUser[$key])) {
                $uida[$levelUser[$key]['parent_id']]['money'] = intval($value);
                $uida[$levelUser[$key]['parent_id']]['level'] = $levelUser[$key]['level'];
                $uida[$levelUser[$key]['parent_id']]['num'] =   empty($regnum[$levelUser[$key]['parent_id']]) ? 0 : $regnum[$levelUser[$key]['parent_id']];
            }
        }
        foreach ($uida as $key => $value) {
            if($value['num'] <= 4){
                if($value['num'] >= $value['level']){
                    model('Bank')->due_up($miniapp_id,$key,$value['money']*100,0);
                    model('Bank')->isProfit($key,$value['money']);
                    model('BankLogs')->add($miniapp_id,$key,$value['money']*100,'任务奖励['.money($value['money']).']',$order->user_id,$order->order_no);
                }
            }else{
                model('Bank')->due_up($miniapp_id,$key,$value['money']*100,0);
                model('Bank')->isProfit($key,$value['money']);
                model('BankLogs')->add($miniapp_id,$key,$value['money']*100,'任务奖励['.money($value['money']).']',$order->user_id,$order->order_no);
            }
        }
    }

    /**
     * 任务计算
     * @param integer $miniapp_id   来源小程序
     * @param integer $uid  用户ID
     * @param float $cash_fee  (元)
     * @param object $config  系统配置
     * @return void
     */
    public function range(int $miniapp_id,int $uid,$order,$config){
        $cash_fee = $order->real_amount;
        if ($cash_fee <= 0) {
            return;
        }
        $cash_fee = $order->real_amount;
        if($cash_fee <= 0){
            return;
        }
        if($cash_fee <= 0 || $config->reward_ratio <= 0){
            return;
        }
        //规则
        $rules = empty($config->rules) ? [] : json_decode($config->rules,true);
        if(empty($rules)){
            return;
        }
        //查询溯源用户
        $level  = model('SystemUserLevel')->children_user($uid);
        if(empty($level)){
            return;
        }
        ksort($level);
        //增加流水
        if(isset($level[1]['user_id'])){
            BankAll::add($miniapp_id,$level[1]['user_id'],$cash_fee);  
        }
        //查询伞下直推人数,伞下业绩和分成比例
        $account = [];
        $i = 0;
        foreach ($level as $value) {
            $selectMoney = self::selectMoney($value['parent_id'],$rules);
            if($selectMoney['ratio'] > 0){
                $account[$i]          = self::selectMoney($value['parent_id'],$rules);
                $account[$i]['uid']   = $value['parent_id'];
                $account[$i]['level'] = $value['level'];
                $i++;
            }
        }
        //查询UID和应分配业绩
        $data = self::howMoney($account,$cash_fee,$config);
        foreach ($data as $value) {
            model('Bank')->due_up($miniapp_id,$value['uid'],$value['money']*100,0);
            model('Bank')->isProfit($value['uid'],$value['money']);
            model('BankLogs')->add($miniapp_id,$value['uid'],$value['money']*100,'绩效奖励['.$value['money'].']',$order->user_id,$order->order_no);
        }
        //平台奖励
        self::platformMoney($order,$config);
    }

    /**
     * 计算极差规则奖金比例
     */
    protected function howMoney($account,float $cash_fee,$config){
        $base = 100;
        $money = $cash_fee*($config->reward_ratio/100);
        foreach ($account as $key => $value) {
            if($key == 0){
                $ratio = $value['ratio'];
            }else{
                $ratio = $value['ratio'] - $account[$key-1]['ratio'];
            }
            $base = $base-$ratio;
            if($base > 0 && $ratio > 0){
                $data[$key]['uid']   = $value['uid'];
                $data[$key]['ratio'] = $ratio;
                $data[$key]['money'] = money($money*($ratio/100));
            }
        }
        return $data;
    }

    /**
     * 查询某个用户的伞下绩效和直推用户
     * @param integer $uid      用户ID
     * @param object  $rules    系统配置
     * @return void
     */
    protected function selectMoney(int $uid,$rules){
        //统计推荐人数
        $peple_num = model('SystemUserLevel')->where(['parent_id' => $uid,'level'=>1])->count();
        if($peple_num <= 0){
            return ['num' => 0,'money' => 0,'ratio' => 0];
        }
        //查询伞下业绩
        $uids = model('SystemUserLevel')->where(['parent_id' => $uid])->column('user_id');
        $uids[] = $uid;
        $account = BankAll::where(['uid' => $uids])->sum('account');
        $account = intval($account);
        BankAll::where(['uid' => $uid])->update(['pyramid' => $account]);  //更新伞下业绩
        krsort($rules);
        $ratio = 0;
        foreach ($rules as $value) {
            if($peple_num >= $value['num'] && $account >= $value['much']){
                $ratio = $value['ratio'];
                break;
            }
        }
        //判断分配比例
        return ['ratio' => $ratio,'money' => $account,'num' => $peple_num];
    }

    /**
     * 平台评价奖励
     * @param integer $uid  用户ID
     * @param object  $config  系统配置
     * @return void
     */
    protected function platformMoney($order,$config){
        $cash_fee = $order->real_amount;
        if($config->platform_ratio <= 0 || $config->platform_amout <= 0){
            return;
        }
        $info = BankAll::where(['member_miniapp_id' => $config->member_miniapp_id])->where('pyramid','>=',$config->platform_amout)->select();
        if(empty($info)){
            return;
        }
        $info = $info->toArray();
        $peple_num = count($info);
        $money = money($cash_fee*($config->platform_ratio/100)/$peple_num);
        if($money >= 0.01){
            $uid = array_column($info,'uid');
            foreach ($uid  as $value) {
                model('Bank')->due_up($config->member_miniapp_id,$value,$money*100,0);
                model('Bank')->isProfit($value,$money); 
                model('BankLogs')->add($config->member_miniapp_id,$value,$money*100,'平台奖励['.$money.']',$order->user_id,$order->order_no);
            }
        }
    }
}