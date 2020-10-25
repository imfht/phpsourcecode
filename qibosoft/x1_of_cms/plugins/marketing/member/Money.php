<?php
namespace plugins\marketing\member;

use app\common\controller\MemberBase;
use app\common\traits\AddEditList;
use plugins\marketing\model\Moneytype AS ModelMoneytype;

class Money extends MemberBase
{
    use AddEditList;
    protected $validate = '';
    protected $model;
    protected $form_items = [];
    protected $list_items;
    protected $tab_ext = [
            'page_title'=>'我的虚拟财富',
            'top_button'=>[],
    ];
    
    protected function _initialize(){
        parent::_initialize();
        $this->model = new ModelMoneytype();
    }
    
    public function index($type=0) {
        $this->list_items = [
                ['icon', '图标', 'icon'],
                ['name', '名称', 'text'],
                ['id','数量','callback',function($v,$rs){
                    return get_user_money($v,$this->user['uid']);
                }],
                ['id','兑换比例','callback',function($v,$rs){
                    $ratio = $this->check_ratio($rs);
                    return $ratio?"100：{$ratio}":'100：0';
                }],
                ['id','兑换','callback',function($v,$rs){
                    $ratio = $this->check_ratio($rs);
                    $url = purl('edit',['id'=>$v]);
                    return $ratio?"<a href='$url' class='glyphicon glyphicon-shopping-cart'>兑换</a>":'';
                }],
        ];
        $map = [];
        
        $this->tab_ext['right_button'] = [
//                 [
//                         'type'=>'edit',
//                         'title'=>'兑换积分',
//                         'icon'=>'glyphicon glyphicon-shopping-cart',
//                 ]
        ];
        
        return $this -> getMemberTable(self::getListData($map ,'list desc,id asc'));
    }
    
    public function edit($id=0){        
        $info = getArray($this->model->get($id));
        $ratio = $this->check_ratio($info);        
        if($ratio<=0){
            $this->error('系统不支持兑换!');
        }
        $max_num = floor(get_user_money($id,$this->user['uid']) * $ratio / 100);    //最多可换多少个
        $chang_ratio = $ratio/100;
        if (!is_int($chang_ratio)) {
            $chang_ratio = sprintf("%.2f",$chang_ratio);
        }
        if ($this->request->isPost()) {
            $data = $this->request->post();
            if($data['money']<1){
                $this->error('兑换积分不能小于1个');
            }elseif ($data['money']>$max_num) {
                $this->error('你可以兑换的积分数不能大于 '. $max_num .' 个');
            }
            $num = ceil( $data['money']*100/$ratio );
            add_jifen($this->user['uid'],$data['money'],$info['name'].' 兑换积分所得,兑换比率是 1：'.$chang_ratio);
            add_jifen($this->user['uid'],-$num,$info['name'].' 兑换积分消费,兑换比率是 1：'.$chang_ratio,$id);
            $this->success( "兑换成功,本次消费你 {$num} 个 {$info['name']} " );
        }
        if( $chang_ratio<1 ){
            $num = (100/$ratio);
            if (!is_int($num)) {
                $num = sprintf("%.2f",$num);
            }
            $msg ="(提醒：每1个积分需要 {$num} 个  {$info['name']} )";
        }else{
            $msg = "(提醒：每1个 {$info['name']} 可以兑换 {$chang_ratio} 个积分)";
        }
        $this->tab_ext['page_title'] ="{$info['name']}换积分，{$msg}，你当前积分数是 ".$this->user['money']." 个";
        
        $put_msg = '你当前有 '.get_user_money($id,$this->user['uid'])." 个{$info['name']}, 最多可兑换 {$max_num} 个积分";
        $this->form_items = [
                //['money','rmb','充值卡面额'],
                ['number','money','兑几个积分',$put_msg],
        ];
        
        return $this->editContent();
    }
    
    private function check_ratio($info=[]){
        $groups = $info['group_ratio']?json_decode($info['group_ratio'],true):[];
        $num = $groups[$this->user['groupid']]?:0;
        $array = $info?json_decode($info['more_ratio'],true):[];
        $udb = $array['user'];
        $tdb = $array['types'];
        if($udb['weixin_api'] && $this->user['weixin_api']){
            $num += $udb['weixin_api'];
        }
        if($udb['wx_attention'] && $this->user['wx_attention']){
            $num += $udb['wx_attention'];
        }
        if($udb['email_yz'] && $this->user['email_yz']){
            $num += $udb['email_yz'];
        }
        if($udb['mob_yz'] && $this->user['mob_yz']){
            $num += $udb['mob_yz'];
        }
        if($udb['idcard_yz'] && $this->user['idcard_yz']){
            $num += $udb['idcard_yz'];
        }
        if($udb['regdate']){
            if (!is_numeric($this->user['regdate'])) {
                $this->user['regdate'] = strtotime($this->user['regdate']);
            }
            $time = intval( (time()-$this->user['regdate'])/(3600*24*30) ); //已注册多少个月
            $num += $this->get_ratio($udb['regdate'],$time);
        }
        if($udb['addrmb']){ //已充值消费金额
            static $total_money=null;
            if ($total_money===null) {
                $total_money = fun('count@rmb',$this->user['uid']);
            }
            $num += $this->get_ratio($udb['addrmb'],$total_money);
        }
        if($udb['uid']){     //对指定用户的特别奖励或惩罚
            $num += $this->get_uid_ratio($udb['uid'],$this->user['uid']);
        }
        
        if(modules_config('bbs')){
            if($udb['addtopic']){ //论坛主题
                static $bbs_topic=null;
                if ($bbs_topic===null) {
                    $bbs_topic = fun('count@Info','bbs_content',$this->user['uid']);
                }
                $num += $this->get_ratio($udb['addtopic'],$bbs_topic);
            }
            if($udb['addreply']){ //论坛回复
                static $bbs_reply=null;
                if ($bbs_reply===null) {
                    $bbs_reply = fun('count@Info','bbs_reply',$this->user['uid']);
                }
                $num += $this->get_ratio($udb['addreply'],$bbs_reply);
            }
        }
        return intval($num);
    }
    
    private function get_uid_ratio($code='',$value=0){
        $array = str_array($code);
        foreach($array AS $k=>$v){
            if($value==$k){
                return $v;
            }
        }
        return 0;
    }
    
    private function get_ratio($code='',$value=0){
        $array = str_array($code);
        krsort($array);
        foreach($array AS $k=>$v){
            if($value>=$k){
                return $v;
            }
        }
        return 0;
    }
    
    public function delete($ids=0){
    }
    public function add(){
    }

}
