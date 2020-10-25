<?php
namespace app\member\controller;

use app\common\model\User AS UserModel;
use app\common\controller\MemberBase;
use app\common\traits\AddEditList;
use app\common\fun\Cfgfield;
use app\common\field\Post AS FieldPost;
use app\common\model\Grouplog AS GrouplogModel;

class Group extends MemberBase
{
    use AddEditList;
    protected $validate = '';
    protected $model;
    protected $form_items;
    protected $tab_ext;
    protected $money_name='积分';
    protected $money_dw='个';
    
    protected function _initialize()
    {
        parent::_initialize();
        //$this->model = new UserModel();
        if($this->webdb['up_group_use_rmb']){
            $this->money_name='金额';
            $this->money_dw='元';
        }
    }
    
    /**
     * 列出可购买的升级用户组
     * @param string $tag 是否指定只显示某些频道相关的用户组
     * @return mixed|string
     */
    public function index($tag='')
    {
        $groupdb = [];
        $data_list = getGroupByid(null,false);
        foreach($data_list AS $gid=>$rs){
            if ($tag!='') {
                if ( strstr($tag,',') ) {
                    if( !in_array($rs['tag'], explode(',', $tag)) ){
                        continue;
                    }
                }elseif ( strstr($rs['tag'],',') ) {
                    if(!in_array($tag, explode(',', $rs['tag']))){
                        continue;
                    }
                }elseif($tag!=$rs['tag']){
                    continue;
                }
            }
            if($rs['type']==0&&$gid!=8){
                $groupdb[] = $rs;
            }
        }
        if ($tag!='' && empty($groupdb)) {
            foreach($data_list AS $gid=>$rs){
                if($rs['type']==0&&$gid!=8){
                    $groupdb[] = $rs;
                }
            }
        }
        $this->assign('groupdb',$groupdb);
        $this->assign('money_dw',$this->money_dw);
        $this->assign('money_name',$this->money_name);
        return $this->fetch();
    }
    
    /**
     * 充值积分
     * @param number $money
     */
    public function pay($money=0,$gid=0){
        $money = abs($money);
        if($this->user['rmb']<$money){
            $this->error('你的余额不足: '.$money.'元，不能申请当前认证，请先充值！');
        }elseif($money<0.01){
            $this->error('充值金额不能小于0.01元！');
        }
        $this->webdb['P__marketing']['money_ratio']>0 || $this->webdb['P__marketing']['money_ratio']=10;
        add_jifen($this->user['uid'],$money*$this->webdb['P__marketing']['money_ratio'],'在线充值积分');
        add_rmb($this->user['uid'], -abs($money), 0,'充值积分消费');
        $this->success('充值成功,请继续下一步升级',url('buy',['gid'=>$gid]),[],1);
    }
    
    /**
     * 升级用户组
     * @param number $gid
     * @return mixed|string
     */
    public function buy($gid=0,$day=0)
    {
        if ($gid<1) {
            $this->error('请选择要认证的用户组');
        }
        $data_list = getGroupByid(null,false);
        $ginfo = $data_list[$gid];
        if (empty($ginfo)){
            $this->error('用户组不存在!');
        }elseif ($ginfo['type']) {
            $this->error('系统组,不可以购买');
        }
        
        if($this->admin){
            $this->error('你是管理员,级别很高了,无须认证升级');
        }
        
        
        $need_money = $ginfo['level'];
        if (count($ginfo['_level'])>1) {
            if (empty($day)) {
                $this->error('你没有选择升级多少天?');
            }
            $need_money = $ginfo['_level'][$day];
            if (!isset($ginfo['_level'][$day])) {
                $this->error('选择的天数有误!');
            }
        }else{
            $day = $ginfo['daytime'];
        }
        
        if($this->webdb['up_group_use_rmb']){       //需要RMB
            if($this->user['rmb']<$need_money){                
                $payurl = post_olpay([
                    'money'=>$need_money-$this->user['rmb'],     //有部分余额的话,就不用充值那么多
                    'return_url'=>url('buy',['gid'=>$gid,'day'=>$day]),
                    'banktype'=>'',
                    'numcode'=>'g'.date('ymdHis').rands(3),
                    //'callback_class'=>mymd5('app\\member\\controller\\Group@pay@'.$money),
                ], false);                
                $this->error('你的余额不足: '.$need_money .'元，不能申请当前认证，你确认要充值吗？',$payurl);
            }
        }else{  //需要积分
            if($this->user['money']<$need_money){
                $money = ($need_money - $this->user['money']) / ($this->webdb['P__marketing']['money_ratio']?:10);
                $money = ceil($money*100)/100;
                $payurl = url('pay',['money'=>$money,'gid'=>$gid,'day'=>$day]);     //提示充值积分
                if($this->user['rmb']<$money){
                    $payurl = post_olpay([
                            'money'=>$money-$this->user['rmb'],     //有部分余额的话,就不用充值那么多
                            'return_url'=>$payurl,
                            'banktype'=>'',
                            'numcode'=>'g'.date('ymdHis').rands(3),
                            //'callback_class'=>mymd5('app\\member\\controller\\Group@pay@'.$money),
                    ], false);
                }          
                $this->error('你的'.jf_name(0).'不足: '.$need_money .'个，你仅有 '.$this->user['money'].' 个，需要先充值 '.$money.' 元，才能认证升级，你确认要充值吗？',$payurl);
            }
        } 
        
        $this->form_items = Cfgfield::get_form_items($gid,'upgroup');
        if (empty($this->form_items)) { //没有需要填写的项目  , 直接入库处理
            return $this->post($gid,$need_money,$day);
        }else{
            if ($this->request->isPost()) {
                $data = $this->request->post();            
                $data = FieldPost::format_php_all_field($data,$this->form_items);            
                
                //form_items之外的项目不允许伪造表单修改
                $allow = [];
                foreach($this->form_items AS $key=>$ar){
                    $allow[] = $ar[1];
                }
                foreach($data AS $key=>$value){
                    if(!in_array($key, $allow)){
                        unset($data[$key]);     //不允许伪造表单修改
                    }elseif(empty($value)){
                        $this->error('必填项不能为空!');
                    }
                }
                return $this->post($gid,$need_money,$day,$data);
            }
            
            if ($this->request->isAjax()) {
                $this->success('请稍候...',get_url('location'));   //AJAX访问的话,要跳出去
            }
            
            $this->assign('money_name',$this->money_name);
            $this->assign('money_dw',$this->money_dw);
            
            $this->tab_ext['page_title'] = '申请认证为: '.$ginfo['title'].($need_money ?($this->webdb['up_group_use_rmb']?" 本次认证需要支付RMB {$need_money} 元":" 本次认证需要消费".$this->money_name." {$need_money} ".$this->money_dw):' 本次认证免费');
            $info = $this->user;
            return $this->editContent($info);
        }
    }
    
    /**
     * 自动通过审核
     * @param number $gid
     * @param number $day 升级天数
     * @param array $data
     * @return string|boolean
     */
    protected function auto_upgroup($gid=0,$day=0,$data=[]){
        $gdb = getGroupByid($gid,false);
        if (count($gdb['_level'])<=1) {
            $day = $gdb['daytime'];
        }
        $data['uid'] = $this->user['uid'];        
        $data['old_groupid'] = $this->user['groupid'];  //记录之前的用户组ID,方便到期后,恢复
        $data['groupid'] = $gid;
        $data['group_endtime'] = $day?($day*3600*24+time()):0;
        $content = "恭喜你，成功升级为:“".$gdb['title']."”，将可享受更多权利";
        send_msg($this->user['uid'],"恭喜你，成功升级会员等级",$content);
        send_wx_msg($this->user['uid'], $content); 
        $result = UserModel::edit_user($data);
        return $result;
    }
    
    /**
     * 处理修改用户组
     * @param number $gid 新的用户组ID
     * @param number $money 升级所需财富
     * @param number $day 升级天数
     * @param array $data
     */
    protected function post($gid=0,$money=0,$day=0,$data=[]){
        $array = [
                'uid'=>$this->user['uid'],
                'gid'=>$gid,
        ];
        $info = GrouplogModel::where($array)->order('id desc')->find();

        if($this->user['groupid']==$gid){
            $msg = '你当前的用户组已经是'.getGroupByid($gid).'，未到期不可重复申请!';
            if ($this->user['group_endtime']) {
                $msg.= "请在 ".date('Y-m-d H:i',$this->user['group_endtime']).' 到期后，再过来申请！';
            }
            $this->error($msg);
        }elseif ($info && empty($info['status'])) {
            $this->error('你之前的认证资料还没通过审核,暂时不能重复申请!');
        }
        if (!$this->webdb['forbid_auto_upgroup']) {
            $array['status'] = 1;
            $array['check_time'] = time();
        }
        
        $array['create_time'] = time();
        $array['daytime'] = $day;
        $result = GrouplogModel::create($array);
        if ($result) {
            if ($money>0) {
                if($this->webdb['up_group_use_rmb']){   //要求RMB升级
                    add_rmb($this->user['uid'],-$money,0,'认证升级用户身份');                    
                }else{ //积分升级
                    add_jifen($this->user['uid'],-$money,'认证升级用户身份');
                }                
            }
            $this->fx($money);  //奖励RMB
            $this->fx($money,'introduce_reward_jfnum_'); //奖励虚拟币
            if (!$this->webdb['forbid_auto_upgroup']) {
                $this->auto_upgroup($gid,$day,$data);
                $this->success('成功升级!',urls('index'));
            }else{
                $data['uid'] = $this->user['uid'];
                edit_user($data);
                $title = $this->user['username'] . '申请升级用户组为 ' . getGroupByid($gid) . '请尽快进后台审核处理！';
                $content = $title."\r\n 申请日期：".date('Y-m-d H:i');
                send_admin_msg($title,$content);
                $this->success('信息已提交,请等待管理员审核!',urls('index'));
            }            
        }else{
            $this->error('数据提交失败');
        }
    }
    
    /**
     * 处理推荐人奖励
     * @param number $paymoney 当前升级用户消耗的RMB或积分
     * @param string $type introduce_vip_reward_ 或者是 introduce_reward_jfnum_ 分别是RMB或虚拟币的奖励
     */
    private function fx($paymoney=0,$type='introduce_vip_reward_'){
        $tzr_uid = $this->user['introducer_1'];
        if (empty($tzr_uid) || empty($this->webdb['P__propagandize']) || empty($this->webdb['P__propagandize'][$type])){
            return ;
        }
        $tzr_info = get_user($tzr_uid);
        $money = $this->webdb['P__propagandize'][$type][$tzr_info['groupid']];
        if(empty($money)){
            return ;
        }
        if( strstr($money,'=') ){  //这种格式 1=5,10=30,20=50
            $detail = explode(',',$money);
            $array = [];
            foreach($detail AS $value){
                if($value!=''){
                    list($num,$_money) = explode('=',$value);
                    $array[$num] = $_money;
                }
            }
            krsort($array); //由高到低排序
        }else{ //可能是数字,也有可能是百分比
            $array[1] = $money;
        }

        $introducer_num = UserModel::where('introducer_1',$tzr_uid)->count('uid');
        foreach($array AS $num=>$money){
            if($introducer_num%$num==0){
                if (!is_numeric($money)) {  //百分比
                    $money = str_replace(['%','％'], '', $money);
                    $money = ($paymoney * $money/100);
                    $money = $type=='introduce_vip_reward_' ? number_format($money,2) : ceil($money);
                }
                $title = '每推荐 '.$num.' 个用户升级用户组奖励';
                $content = '感谢你推荐 '.$this->user['username'].' 付费升级用户组，每推荐 '.$num.' 个用户付费升级可奖励 '.$money.($type=='introduce_vip_reward_'?' 元':' 个'.jf_name($this->webdb['P__propagandize']['introduce_reward_jftype']));
                if($type=='introduce_vip_reward_'){   //奖励RMB
                    add_rmb($tzr_uid,$money,0,$title);
                }else{
                    add_jifen($tzr_uid,$money,$title,$this->webdb['P__propagandize']['introduce_reward_jftype']);
                }                
                send_msg($tzr_uid,$title,$content);
                if ($tzr_info['weixin_api']) {
                    send_wx_msg($tzr_info['weixin_api'],$content);
                }
                break;
            }
        }
    }
    
    public function delete(){
        die('禁止访问!');
    }
    public function add(){
        die('禁止访问!');
    }
    public function edit(){
        die('禁止访问!');
    }
    
}
