<?php
/**
 * 所属项目 阿里研究院.
 * 开发者: 陈一枭
 * 创建日期: 7/29/14
 * 创建时间: 3:44 PM
 * 版权所有 想天软件工作室(www.ourstu.com)
 */

namespace Group\Controller;

use Think\Controller;
use Think\Hook;
class GroupController extends Controller
{
    public function _initialize()
    {

        $sub_menu =
            array(
                'left' =>
                    array(
                        array('tab' => 'home', 'title' => '全部群组', 'href' => U('group/index/index')),
                        array('tab' => 'mygroup', 'title' => '我的群组', 'href' => is_login()?U('group/index/mygroup'):"javascript:toast.error('登录后才能操作')"),
                    ),
                'right' =>
                    array(
                        array('tab' => 'create', 'title' => '创建群组', 'href' =>is_login()?U('group/index/create'):"javascript:toast.error('登录后才能操作')"),
                    )
            );
        $this->assign('sub_menu', $sub_menu);
        $this->assign('current', 'home');
        /* 读取站点配置 */
        $config = api('Config/lists');
        C($config); //添加配置

    }



    public function getGroupByIds($ids)
    {
        $ids = is_array($ids) ? $ids : implode(',', $ids);
        $list = array();
        foreach ($ids as $v) {
            $list[] = $this->getGroup($v);
        }
        return $list;
    }

    public function getGroup($id)
    {
        $id=intval($id);
        $group = S('group_' . $id);
        if (empty($group)) {
            $group = D('Group/Group')->where(array('status' => 1, 'id' => $id))->find();
            $group['post_list'] = D('Group/GroupPost')->where(array('status' => 1, 'group_id' => $group['id']))->limit(4)->select();
            $group['member_count'] = D('GroupMember')->where(array('group_id' => $group['id'], 'status' => 1))->count();
            $group['month_count'] = D('GroupPost')->where(array('create_time' => array('between', array(strtotime("-1 month"), time())), 'group_id' => $group['id']))->count();
            $group['user'] = query_user(array('avatar128', 'avatar64', 'nickname', 'uid', 'space_url', 'icons_html'), $group['uid']);
            $group['user']['group_count'] = D('Group')->where(array('uid' => $group['uid'], 'status' => 1))->count();
            S('group_' . $id, $group, 300);
        }
        return $group;
    }



    /**
     * getGroupType 返回群组类型，0=>公共的  1=>私有的
     * @param $group_id
     * @return mixed
     * @author:xjw129xjt xjt@ourstu.com
     */
    protected  function getGroupType($group_id)
    {
        $group = D('Group')->where(array('id' => $group_id, 'status' => 1))->find();
        return $group['type'];
    }

    /**
     * getGroupCate  获取群组分类
     * @param $group_id
     * @return mixed
     * @author:xjw129xjt xjt@ourstu.com
     */
    protected   function getGroupCate($group_id)
    {
        $group = D('Group')->where(array('id' => $group_id, 'status' => 1))->find();
        return $this->getGroupCateByTypeId($group['type_id']);
    }

    /**
     * getGroupCateByTypeId  获取群组分类名称
     * @param $type_id
     * @return mixed
     * @author:xjw129xjt xjt@ourstu.com
     */
    protected  function getGroupCateByTypeId($type_id)
    {
        $type = D('GroupType')->where(array('id' => $type_id, 'status' => 1))->find();
        return $type['title'];
    }

    /**
     * getPostCateName  获取帖子分类名称
     * @param $type_id
     * @return mixed
     * @author:xjw129xjt xjt@ourstu.com
     */
    protected  function getPostCateName($type_id)
    {
        $type = D('GroupPostCategory')->where(array('id' => $type_id, 'status' => 1))->find();
        return $type['title'];
    }

    protected  function assignAllowPublish()
    {

        $group_id = $this->get('group_id');
        $allow_publish = $this->isGroupAllowPublish($group_id);
        $this->assign('allow_publish', $allow_publish);
    }

    protected  function requireLogin()
    {
        if (!$this->isLogin()) {
            $this->error('需要登录才能操作');
        }
    }

    protected  function isLogin()
    {
        return is_login() ? true : false;
    }

    protected  function requireGroupAllowPublish($group_id)
    {
        $this->requireGroupExists($group_id);
        $this->requireLogin();
        $this->requireGroupAllowCurrentUserGroup($group_id);
    }

    protected  function isGroupAllowPublish($group_id)
    {
        if (!$this->isLogin()) {
            return false;
        }
        if (!$this->isGroupExists($group_id)) {
            return false;
        }
        /*        if (!$this->isGroupAllowCurrentUserGroup($group_id)) {
                    return false;
                }*/

        if (!is_joined($group_id)) {
            return false;
        }
        return true;
    }

    protected  function isAllowEditGroup($group_id)
    {
        if (!$this->isGroupExists($group_id)) {
            return false;
        }
        if (!$this->isLogin()) {
            return false;
        }
        if (is_administrator()) {
            return true;
        }

        $group = D('Group')->where(array('id' => $group_id, 'status' => 1))->find();
        if ($group['uid'] != is_login()) {
            return false;
        }
        return true;


    }

    protected  function requireAllowEditGroup($group_id)
    {
        $this->requireGroupExists($group_id);
        $this->requireLogin();

        if (is_administrator()) {
            return true;
        }
        //确认帖子时自己的
        $group = D('Group')->where(array('id' => $group_id, 'status' => 1))->find();
        if ($group['uid'] != is_login()) {
            $this->error('没有权限编辑群组');
        } else {
            return true;
        }

    }

    protected  function requireAllowEditPost($post_id)
    {
        $this->requirePostExists($post_id);
        $this->requireLogin();

        if (is_administrator()) {
            return true;
        }
        //确认帖子时自己的
        $post = D('GroupPost')->where(array('id' => $post_id, 'status' => 1))->find();
        if ($post['uid'] != is_login()) {
            $this->error('没有权限编辑帖子');
        }
    }

    /**检查可视权限
     * @param $group_id
     * @auth 陈一枭
     */
    protected  function requireGroupAllowView($group_id)
    {
        $this->requireGroupExists($group_id);
    }

    protected  function requireGroupExists($group_id)
    {
        if (!$this->isGroupExists($group_id)) {
            $this->error('群组不存在');
        }
    }

    protected function isGroupExists($group_id)
    {
        $group = D('Group')->where(array('id' => $group_id, 'status' => 1))->find();
        return $group ? true : false;
    }

    protected function requireAllowReply($post_id)
    {
        $this->requirePostExists($post_id);
        $this->requireLogin();
    }

    protected function requirePostExists($post_id)
    {
        $post = D('GroupPost')->where(array('id' => $post_id))->find();
        if (!$post) {
            $this->error('帖子不存在');
        }
    }

    protected function requireGroupAllowCurrentUserGroup($group_id)
    {
        return true;
        /*        if (!$this->isGroupAllowCurrentUserGroup($group_id)) {
                    $this->error('该板块不允许发帖');
                }*/
    }

    protected function isGroupAllowCurrentUserGroup($group_id)
    {
        //如果是超级管理员，直接允许
        if (is_login() == 1) {
            return true;
        }

        //如果帖子不属于任何板块，则允许发帖
        if (intval($group_id) == 0) {
            return true;
        }

        //读取贴吧的基本信息
        $group = D('Group')->where(array('id' => $group_id))->find();
        $userGroups = explode(',', $group['allow_user_group']);

        //读取用户所在的用户组
        $list = M('AuthGroupAccess')->where(array('uid' => is_login()))->select();
        foreach ($list as &$e) {
            $e = $e['group_id'];
        }

        //每个用户都有一个默认用户组
        $list[] = '1';

        //判断用户组是否有权限
        $list = array_intersect($list, $userGroups);
        return $list ? true : false;
    }

    protected function getGroupTypes()
    {
        $groupType = D('GroupType')->where(array('status' => 1,'pid'=>0))->select();
        $this->assign('groupType', $groupType);

        $child =array();
        foreach($groupType as $v){
            $child[$v['id']] = D('GroupType')->where(array('status' => 1,'pid'=>$v['id']))->select();
        }
        $this->assign('childType', $child);

        foreach ($groupType as $k => $v) {
            $child = D('GroupType')->where(array('pid' => $v['id'], 'status' => 1))->order('sort asc')->select();
            //获取数组中第一父级的位置
            $key_name = array_search($v, $groupType);
            foreach ($child as $key => $val) {
                $val['title'] = '------' . $val['title'];
                //在父级后面添加数组
                array_splice($groupType, $key_name + 1, 0, array($val));
            }
        }
        $this->assign('groupTypeAll',$groupType);
        return $groupType;
    }



    protected function getNotice($group_id)
    {
        $notice = D('GroupNotice')->where('group_id=' . $group_id)->find();
        $this->assign('notice', $notice);
        return $notice;
    }

    protected function getGroupIdByPost($post_id)
    {
        $post = D('GroupPost')->where('id=' . $post_id)->find();
        return $post['group_id'];

    }



    protected function getPostCategory($group_id=0)
    {
        $map['status']=1;
        $group_id &&   $map['group_id']=$group_id;
        $cate = D('GroupPostCategory')->where($map)->order('sort asc')->select();
        $this->assign('post_cate', $cate);
        return $cate;
    }

    protected function clearcache($group_id=''){

        $group_id && S('group_'.$group_id,null);
        S('group_key_value_ids', null);
    }

} 