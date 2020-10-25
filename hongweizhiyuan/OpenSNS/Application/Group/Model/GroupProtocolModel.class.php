<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-7-21
 * Time: 上午11:25
 * @author 郑钟良<zzl@ourstu.com>
 */

namespace Group\Model;

use Think\Model;

class GroupProtocolModel extends Model{

    private $group_postModel;
    private $groupModel;
    private $group_replyModel;
    private $group_lzl_replyModel;

    public function _initialize()
    {
        $this->group_postModel = new Model('GroupPost');
        $this->groupModel = new Model('Group');
        $this->group_replyModel = new Model('GroupPostReply');
        $this->group_lzl_replyModel = new Model('GroupLzlReply');
    }
    // 在个人空间里查看该应用的内容列表
    public function profileContent($uid=null,$page=1,$count=15,$tab=null) {
        $tab=$tab?$tab:'group';
        $groups = $this->_getGroupList();
        $group_ids = getSubByKey($groups,'id');
        $map['group_id']=array('in',$group_ids);
        $group_key_value = array();
        foreach ($groups as $f) {
            $group_key_value[$f['id']] = $f;
        }
        if ($uid != 0) {
            $map['uid']=$uid;
        } else {
            $map['uid']=is_login();
        }
        if($tab=='group'){
            $map['status']=1;
            $result = $this->group_postModel->where($map)->page($page,$count)->order('update_time desc')->select();
            foreach ($result as &$v) {
                $v['group'] = $group_key_value[$v['group_id']];
            }
        }elseif($tab=='group_in'){
            $map_in=$this->_getInMap($map);
            $map_in['status']=1;
            $map_in['group_id']=array('in',$group_ids);
            $result = $this->group_postModel->where($map_in)->page($page,$count)->order('update_time desc')->select();
            foreach ($result as &$v) {
                $v['group'] = $group_key_value[$v['group_id']];
            }
        }
        $view=new \Think\View();
        $view->assign('list',$result);
        $view->assign('tab',$tab);
        $view->assign('uid',$uid);
        $view->assign('type','group');
        $content='';
        $content=$view->fetch(T('Application://Group@Index/profile_content'),$content);
        return $content;
    }
    //返回列表项总数，分页用
    public function getTotalCount($uid=null,$tab='group'){
        $tab=$tab?$tab:'group';
        if ($uid != 0) {
            $map['uid']=$uid;
        } else {
            $map['uid']=is_login();
        }
        $groups = $this->_getGroupList();
        $group_ids = getSubByKey($groups,'id');
        $map['group_id']=array('in',$group_ids);
        if($tab=='group'){
            $map['status']=1;
            $totalCount = $this->group_postModel->where($map)->count();
        }
        elseif($tab=='group_in'){
            $map_in=$this->_getInMap($map);
            $map_in['status']=1;
            $map_in['group_id']=array('in',$group_ids);
            $totalCount= $this->group_postModel->where($map_in)->count();
        }
        return $totalCount;
    }
    //返回中文名称
    public function getModelInfo()
    {
        return array('title' => "群组", 'sort' => 91);
    }

    private function _getGroupList()
    {
        $group_list = S('group_list');
        if (empty($group_list)) {
            //读取板块列表
            $group_list = D('Group/Group')->where(array('status' => 1))->order('sort asc')->select();
            S('group_list', $group_list, 300);
        }
        return $group_list;
    }

    /**我参与的$map
     * @author 郑钟良<zzl@ourstu.com>
     */
    private function _getInMap($map=array())
    {
        $map_reply=$map;
        $map_reply['status']=1;
        $reply_ids=$this->group_replyModel->where($map_reply)->field('post_id')->select();
        $reply_ids=array_column($reply_ids,'post_id');
        $map_lzl_reply=$map;
        $map_lzl_reply['is_del']=0;
        $lzl_reply_ids=$this->group_lzl_replyModel->where($map_lzl_reply)->field('post_id')->select();
        $lzl_reply_ids=array_column($lzl_reply_ids,'post_id');
        $in_ids=array_unique(array_merge($reply_ids,$lzl_reply_ids));
        $map['id']=array('in',$in_ids);
        return $map;
    }
}