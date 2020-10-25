<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 14-3-8
 * Time: PM4:30
 */

namespace Group\Controller;

use Think\Controller;
use Weibo\Api\WeiboApi;

define('TOP_ALL', 2);
define('TOP_Group', 1);

class ManageController extends GroupController
{

    public function _initialize()
    {
        $_REQUEST['group_id']=intval($_REQUEST['group_id']);
        parent::_initialize();
        //判断是否有权限编辑
        $this->requireAllowEditGroup($_REQUEST['group_id']);
        $this->getNotice($_REQUEST['group_id']);
        $this->assign('group_id',$_REQUEST['group_id']);
        unset($e);
        $myInfo = query_user(array('avatar128', 'avatar64', 'nickname', 'uid', 'space_url', 'icons_html'), is_login());
        $this->assign('myInfo', $myInfo);
        //赋予贴吧列表

        $this->assign('current', 'mygroup');

    }

    private function group_detail($group_id){
        $group_id=intval($group_id);
        $group = $this->getGroup($group_id);
        $this->assign('group',$group);
        return $group;
    }
    public function index($group_id)
    {
        $group_id=intval($group_id);
        $this->group_detail($group_id);

        $this->getGroupTypes();
        $this->setTitle('群组管理--编辑群组');
        $this->display();
        //redirect(U('group', array('page' => $page)));
    }

    public function category($group_id)
    {
        $group_id=intval($group_id);
        $this->group_detail($group_id);
        $this->getGroupTypes();
        $this->setTitle('群组管理--帖子分类管理');
        $cate = D('GroupPostCategory')->where(array('group_id'=>$group_id,'status'=>1))->field()->select();
        $this->assign('cate',$cate);

        $this->display();

    }


    public function member($group_id,$status=1){
        $group_id=intval($group_id);
        $status=intval($status);

        $this->group_detail($group_id);
        $member = D('GroupMember')->where(array('group_id'=>$group_id,'status'=>$status))->select();
        foreach($member as &$v){
            $v['user'] = query_user(array('avatar128', 'avatar64', 'nickname', 'uid', 'space_url', 'icons_html'), $v['uid']);
            $v['isCreator'] = checkIsCreator($v['uid'],'Group',$v['group_id']);
        }
        $this->assign('member',$member);
        $this->assign('status',$status);
        $this->assign('sh_count',D('GroupMember')->where(array('group_id'=>$group_id,'status'=>1))->count());
        $this->assign('wsh_count',D('GroupMember')->where(array('group_id'=>$group_id,'status'=>0))->count());
        $this->setTitle('群组管理--群组成员');
        $this->display();
    }
    public function notice($group_id , $notice = ''){
        $group_id=intval($group_id);
        $notice=op_h($notice);

         $this->group_detail($group_id);
        if(IS_POST){
            $data['group_id'] = $group_id;
            $data['content'] = $notice;
            $data['create_time'] = time();
            $res =   D('GroupNotice')->add($data,array(),true);
            $this->clearcache($group_id);
            if($res){
                $this->success('添加成功','refresh');

            }else{
                $this->error('添加失败');
            }
        }
        else{

            $this->assign('group_id',$group_id);
            $this->setTitle('群组管理--公告');
            $this->display();
        }
    }


public function remove_group($uid,$group_id){
    $uid=intval($uid);
    $group_id=intval($group_id);
   $res = D('GroupMember')->where(array('uid'=>$uid,'group_id'=>$group_id))->delete();

    $dynamic['group_id'] = $group_id;
    $dynamic['uid'] = $uid;
    $dynamic['type'] = 'remove';
    $dynamic['create_time'] = time();
    D('GroupDynamic')->add($dynamic);

    if($res){
        $group =$this->getGroup($group_id);
        D('Message')->sendMessage($uid, get_nickname(is_login()) . "将您移出了群组【{$group['title']}】",  '移出群组', U('group/index/group',array('id'=>$group_id)), is_login());

        $this->clearcache($group_id);
        $this->success('删除成功','refresh');
    }else{
        $this->error('删除失败');
    }
}

    public function attend_group($uid,$group_id){
        $uid=intval($uid);
        $group_id=intval($group_id);

        $res = D('GroupMember')->where(array('uid'=>$uid,'group_id'=>$group_id))->save(array('status'=>1,'update_time'=>time()));

        $dynamic['group_id'] = $group_id;
        $dynamic['uid'] = $uid;
        $dynamic['type'] = 'attend';
        $dynamic['create_time'] = time();
        D('GroupDynamic')->add($dynamic);
        if($res){

            $group =$this->getGroup($group_id);
            D('Message')->sendMessage($uid, get_nickname(is_login()) . "通过了您加入群组【{$group['title']}】的请求",  '群组审核通过', U('group/index/group',array('id'=>$group_id)), is_login());

            $this->clearcache($group_id);
            $this->success('审核成功','refresh');
        }else{
            $this->error('审核失败');
        }
    }


    public function dismiss($group_id)
    {
        $group_id=intval($group_id);
        
        //设置相关数据状态
        $res = D('Group')->where(array('id' => $group_id))->setField('status',-1);
      /*   D('GroupMember')->where(array('group_id' => $group_id))->setField('status',-1);
         D('GroupPost')->where(array('group_id' => $group_id))->setField('status',-1);*/

        $this->clearcache($group_id);
        if ($res) {
            $this->success('解散成功', U('group/index/index'));
        } else {
            $this->error('解散失败');
        }
    }

    public function editCate($group_id,$cate_id = 0,$title=''){
        $cate_id = intval($cate_id);
        $title=op_t($title);
        if($title==''){
            $this->error('分类名不能为空');
        }
        if($cate_id == 0){
            $res = D('GroupPostCategory')->add(array('group_id'=>$group_id,'title'=>$title,'create_time'=>time(),'status'=>1,'sort'=>0));
            $res&& $this->success('添加分类成功','refresh');
            !$res&& $this->error('添加分类失败');
        }else{
            $res = D('GroupPostCategory')->where(array('id'=>$cate_id))->save(array('title'=>$title));
            $res&& $this->success('编辑分类成功','refresh');
            !$res&& $this->error('编辑分类失败');
        }

    }


    public function delCate($group_id,$cate_id = 0){
        $cate_id = intval($cate_id);
        $res = D('GroupPostCategory')->where(array('id'=>$cate_id))->setField('status',0);
        $res&& $this->success('删除分类成功','refresh');
        !$res&& $this->error('删除分类失败');

    }

}