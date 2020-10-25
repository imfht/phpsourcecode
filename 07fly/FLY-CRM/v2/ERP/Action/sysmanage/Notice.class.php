<?php
/*
 *
 * sysmanage.Notice  系统公告通知
 *
 * =========================================================
 * 零起飞网络 - 专注于网站建设服务和行业系统开发
 * 以质量求生存，以服务谋发展，以信誉创品牌 !
 * ----------------------------------------------
 * @copyright	Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license    For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author ：kfrs <goodkfrs@QQ.com> 574249366
 * @version ：1.0
 * @link ：http://www.07fly.top
 */
class Notice extends Action{
    private $cacheDir='';//缓存目录
    public function __construct() {
        $this->auth=_instance('Action/sysmanage/Auth');
        $this->sys_user= $this->L("sysmanage/User");
        $this->sys_role= $this->L("sysmanage/Role");
        $this->dept= $this->L("sysmanage/Dept");
        $this->msg= $this->L("sysmanage/Message");
    }
    public function notice(){
        //**获得传送来的数据作分页处理
        $pageNum = $this->_REQUEST("pageNum");//第几页
        $pageSize= $this->_REQUEST("pageSize");//每页多少条
        $pageNum = empty($pageNum)?1:$pageNum;
        $pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;
        //**************************************************************************

        //**获得传送来的数据做条件来查询
        $keywords = $this->_REQUEST("keywords");
        $where_str= "u.owner_user_id = ".SYS_USER_ID." ";

        if( !empty($keywords) ){
            $where_str .=" and u.name like '%$keywords%'";
        }
        //**************************************************************************
        $countSql   = "select u.id from fly_sys_user_notice as u where $where_str";
        $totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
        $beginRecord = ($pageNum-1)*$pageSize;
        $sql		 = "select u.* from fly_sys_user_notice as u where $where_str order by u.id desc limit $beginRecord,$pageSize";
        $list		 = $this->C($this->cacheDir)->findAll($sql);
        foreach($list as $key=>$row){
            $list[$key]['owner_user']	=$this->sys_user->user_get_one($row['owner_user_id']);
            $list[$key]['create_user']	=$this->sys_user->user_get_one($row['create_user_id']);
            $list[$key]['status_arr']	=$this->notice_status($row['status']);
        }
        $assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum);
        return $assignArray;
    }
    public function notice_show_json(){
        $assArr = $this->notice();
        echo json_encode($assArr);
    }
    public function notice_show(){
        $smarty = $this->setSmarty();
        $smarty->display('sysmanage/notice_show.html');
    }
    //定制调用显示公告
    public function notice_cron_json(){
        $sql ="select u.* from fly_sys_user_notice as u where status='-1' and u.owner_user_id = '".SYS_USER_ID."' order by create_time desc";
        $list= $this->C($this->cacheDir)->findAll($sql);
        foreach($list as $key=>$row){
            $list[$key]['title']='公告通知:'.$row['title'];
            $list[$key]['content']=$row['create_time'];
        }
        echo json_encode(array('list'=>$list));
    }

    //添加
    public function notice_add(){
        if(empty($_POST)){
            $dept_list=$this->dept->dept_select_tree('dept_id');
            $sys_user=$this->sys_user->user_list();
            $smarty = $this->setSmarty();
            $smarty->assign(array("dept_list"=>$dept_list,"sys_user"=>$sys_user));
            $smarty->display('sysmanage/notice_add.html');
        }else{
            $dept_id = $this->_REQUEST("dept_id");
            $user_id = $this->_REQUEST("sys_user_id");
            if($dept_id){
                $dept_ids=$this->dept->get_dept_self_son($dept_id);//得到权限子级
                $role_son_user=$this->sys_user->user_link_dept_list($dept_ids);
                $sub_user=array();
                foreach($role_son_user as $row){
                    $sub_user[]=$row['id'];
                }
            }else if(!empty($user_id)){
                $sub_user[]=$user_id;
            }else{
                $sub_user=$this->sys_user->user_get_sub_user(SYS_USER_ID);//得到当前用户下级员工数据
            }
            //对每个用户发送通知
            foreach($sub_user as $sub_user_id){
                $into_data=array(
                    'title'=>$this->_REQUEST("title"),
                    'content'=>$this->_REQUEST("content"),
                    'owner_user_id'=>$sub_user_id,
                    'create_time'=>NOWTIME,
                    'create_user_id'=>SYS_USER_ID,
                );

                $rtn_id=$this->C($this->cacheDir)->insert('fly_sys_user_notice',$into_data);

              //  $this->msg->message_add($sub_user_id,'公告通知',$this->_REQUEST("title"),'sys_user_notice',$rtn_id,$remind_time=null);

            }
            $this->L("Common")->ajax_json_success("操作成功");
        }
    }

    //通知查看
    public function notice_view(){
        $id	  	 = $this->_REQUEST("notice_id");
        if(empty($_POST)){
            $upt_sql=array('status'=>'1');
            $this->C($this->cacheDir)->modify('fly_sys_user_notice',$upt_sql,"id='$id'");
            $sql = "select * from fly_sys_user_notice where id='$id'";
            $one = $this->C($this->cacheDir)->findOne($sql);
            $one['owner_user_arr']	=$this->sys_user->user_get_one($one['owner_user_id']);
            $one['status_arr']	=$this->notice_status($one['status']);
            $smarty 	= $this->setSmarty();
            $smarty->assign(array("one"=>$one));
            $smarty->display('sysmanage/notice_view.html');
        }
    }

    //删除
    public function notice_del(){
        $id	  = $this->_REQUEST("notice_id");
        $sql  = "delete from fly_sys_user_notice where id in ($id) and id!='1'";
        $this->C($this->cacheDir)->update($sql);
        $this->L("Common")->ajax_json_success("操作成功");
    }

    //标记已读
    public function notice_read(){
        $id	 = $this->_REQUEST("notice_id");
        $upt_sql=array('status'=>'1');
        $this->C($this->cacheDir)->modify('fly_sys_user_notice',$upt_sql,"id in ($id)");
        $this->L("Common")->ajax_json_success("操作成功");
    }

    //状态
    public function notice_status($key=null){
        $data=array(
            "-1"=>array(
                'status_name'=>'未查看',
                'color'=>'#FAD733',
                'status_name_html'=>'<span class="label label-info">未查看<span>',
            ),
            "1"=>array(
                'status_name'=>'已查看',
                'color'=>'#23B7E5',
                'status_name_html'=>'<span class="label label-default">已查看<span>',
            )
        );
        return ($key)?$data[$key]:$data;
    }

}//
?>