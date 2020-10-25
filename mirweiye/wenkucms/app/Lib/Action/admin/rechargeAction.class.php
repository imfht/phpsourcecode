<?php
// 支付订单列表
class rechargeAction extends backendAction {
public function _initialize()
    {
        parent::_initialize();
        $this->_mod = D('recharge');
       
    }

    protected function _search() {
        $map = array();
        ($time_start = $this->_request('time_start', 'trim')) && $map['add_time'][] = array('egt', strtotime($time_start));
        ($time_end = $this->_request('time_end', 'trim')) && $map['add_time'][] = array('elt', strtotime($time_end)+(24*60*60-1));
        ($uname = $this->_request('uname', 'trim')) && $map['uname'] = array('like', '%'.$uname.'%');
       
        if( $_GET['status']==null ){
            $status = -1;
        }else{
            $status = intval($_GET['status']);
        }
        $status>=0 && $map['status'] = array('eq',$status);
        if( $_GET['have_pay']==null ){
            $have_pay = -1;
        }else{
            $have_pay = intval($_GET['have_pay']);
        }
        $have_pay>=0 && $map['have_pay'] = array('eq',$have_pay);
        ($keyword = $this->_request('keyword', 'trim')) && $map['sn'] = array('like', '%'.$keyword.'%');
        $this->assign('search', array(
            'time_start' => $time_start,
            'time_end' => $time_end,
            'uname' => $uname,
            'status' =>$status,
            'have_pay' =>$have_pay
        ));
        return $map;
    }
    protected function operate() {
    	
    	
    	
     	/*点击处理，首先判断这些传入的id中是不是全部都为已付款，但是积分未成功的，取出这些状态的id
     	然后逐一向用户user_scoresum中增加相应的积分，完成后将积分未成功状态全部更新为已成功*/
     }
}
?>