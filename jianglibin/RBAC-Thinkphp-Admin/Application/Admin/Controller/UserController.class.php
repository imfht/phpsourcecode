<?php
namespace Admin\Controller;
use Think\Controller;
/**
 *
 * @category   UserController
 * @package    User
 * @author  stone <shijiangbo929@163.com>
 * @license
 * @version    PHP version 5.4
 * @link
 * @since   2016年12月7日
 *
 **/
class UserController extends PrivilegeController{    
    /**
     *  会员列表
     *
     * @return void
     * @author stone
     * @since  2016年12月7日
     */
    public function userList(){
        if (I('get.start_time')){
            $time1 = strtotime(I('get.start_time'));
            if (I('get.end_time') && I('get.end_time') >= I('get.start_time')){                
                $time2 = strtotime('+1 days',I('get.end_time'))-1;                
            } else {
                $time2 = strtotime('+1 days',I('get.start_time'))-1;                
            }
            $where['add_dateline'] = array( array('egt',$time1), array('elt',$time2), 'and');
        }
        if (I('get.user')){             
            $where['uid|username|mobile'] = str_replace(' ','',I('get.user'));             
        }
        if (!$where){$where = "";}
        list($userData,$show) = D('User')->getUserData($where);         
        $this->assign("user",$userData);
        $this->assign("page",$show);
        $this->display();
    }
    /**
     *  查看会员详情
     *
     * @return void
     * @author stone
     * @since  2016年12月7日
     */
    public function userShow(){ 
        $this->assign("user",D('User')->getUserDetail(I('get.uid')));
        $this->display();
    }
}