<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 14-3-11
 * Time: PM5:41
 */

namespace Admin\Controller;

use Admin\Builder\AdminListBuilder;
use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminSortBuilder;
use Think\Model;
use Weibo\Api\WeiboApi;
class GroupController extends AdminController
{

    function _initialize()
    {
        parent::_initialize();
    }
    public function index()
    {
        redirect(U('group'));
    }

    /**
     * group  群组首页
     * @param int $page
     * @param int $r
     * @param int $type_id
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function group($page = 1, $r = 20, $type_id = 0)
    {
        //读取数据
        $map = array('status' => 1);
        if ($type_id != 0) {
            $map['type_id'] = $type_id;
        }
        $model = M('Group');
        $list = $model->where($map)->page($page, $r)->order('sort asc')->select();
        $totalCount = $model->where($map)->count();

        //显示页面
        $builder = new AdminListBuilder();
        $builder
            ->title('群组管理')
            ->buttonNew(U('Group/editGroup'))
            ->setStatusUrl(U('Group/setGroupStatus'))->buttonDisable('','审核不通过')->buttonDelete()
            ->buttonSort(U('Group/sortGroup'))
            ->keyId()->keyLink('title', '标题', 'Group/post?group_id=###')
            ->keyCreateTime()->keyText('post_count', '文章数量')->keyStatus()->keyDoActionEdit('editGroup?id=###')
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }


    /**
     * unverify 未审核群组
     * @param int $page
     * @param int $r
     * @param int $type_id
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function unverify($page = 1, $r = 20, $type_id = 0)
    {
        //读取数据
        $map = array('status' => 0);
        if ($type_id != 0) {
            $map['type_id'] = $type_id;
        }
        $model = M('Group');
        $list = $model->where($map)->page($page, $r)->order('sort asc')->select();
        $totalCount = $model->where($map)->count();

        //显示页面
        $builder = new AdminListBuilder();
        $builder
            ->title('群组管理')
            ->setStatusUrl(U('Group/setGroupStatus'))->buttonEnable(U('Group/setGroupStatus',array('tip'=>'verify')),'审核通过')->buttonDelete()
            ->buttonSort(U('Group/sortGroup'))
            ->keyId()->keyLink('title', '标题', 'Group/post?group_id=###')
            ->keyCreateTime()->keyText('post_count', '文章数量')->keyStatus()->keyDoActionEdit('editGroup?id=###')
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }



    /**群组分类列表
     * @param int $page
     * @param int $r
     * @auth 陈一枭
     */
    public function groupType()
    {
        //读取数据
        $map = array('status' => array('GT', -1),'pid'=>0);

        $model = M('GroupType');
        $list = $model->where($map)->order('sort asc')->select();

        foreach ($list as $k => $v) {
            $child = $model->where(array('pid' => $v['id'], 'status' => 1))->order('sort asc')->select();
            //获取数组中第一父级的位置
            $key_name = array_search($v, $list);
            foreach ($child as $key => $val) {
                $val['title'] = '------' . $val['title'];
                //在父级后面添加数组
                array_splice($list, $key_name + 1, 0, array($val));
            }
        }

        foreach ($list as &$type) {
            $type['group_count'] = D('Group')->where(array('type_id' => $type['id']))->count();
        }
        unset($type);
        //显示页面
        $builder = new AdminListBuilder();
        $builder
            ->title('分类管理')
            ->buttonNew(U('Group/editGroupType'))
            ->setStatusUrl(U('Group/setGroupTypeStatus'))->buttonEnable()->buttonDisable()->buttonDelete()
            ->buttonSort(U('Group/sortGroupType'))
            ->keyId()->keyLink('title', '标题', 'Group/group?type_id=###')
            ->keyCreateTime()->keyText('group_count', '群组数量')->keyStatus()->keyDoActionEdit('editGroupType?id=###')
            ->data($list)
            ->display();
    }

    /**
     * setGroupTypeStatus  设置群组分类状态
     * @param $ids
     * @param $status
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function setGroupTypeStatus($ids, $status)
    {

        $builder = new AdminListBuilder();
        $builder->doSetStatus('GroupType', $ids, $status);

    }

    /**
     * editGroupType  编辑群组分类
     * @param int $id
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function editGroupType($id = 0)
    {
        if (IS_POST) {
            if ($id != 0) {
                $data = D('GroupType')->create();
                $res = D('GroupType')->save($data);
            } else {
                $data = D('GroupType')->create();
                $res = D('GroupType')->add($data);
            }
            if ($res) {
                $this->success(($id == 0 ? '添加' : '编辑') . '成功');
            } else {
                $this->error(($id == 0 ? '添加' : '编辑') . '失败');
            }

        } else {
            $builder = new AdminConfigBuilder();

            $types = M('GroupType')->where(array('pid' => 0))->select();
            $opt = array();
            foreach ($types as $type) {
                $opt[$type['id']] = $type['title'];
            }


            if ($id != 0) {
                $wordCate1 = D('GroupType')->find($id);
            } else {
                $wordCate1 = array('status' => 1, 'sort' => 0);
            }
            $builder->title('新增分类')->keyId()->keyText('title', '标题')->keySelect('pid', '父分类', '选择父级分类', array('0' => '顶级分类') + $opt)
                ->keyStatus()->keyCreateTime()->keyText('sort', '排序')
                ->data($wordCate1)
                ->buttonSubmit(U('Group/editGroupType'))->buttonBack()->display();
        }
    }

    /**
     * sortGroupType  对群组分类排序
     * @param null $ids
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function sortGroupType($ids = null)
    {
        if (IS_POST) {
            $builder = new AdminSortBuilder();
            $builder->doSort('GroupType', $ids);
        } else {
            $map['status'] = array('egt', 0);
            $list = D('GroupType')->where($map)->order("sort asc")->select();
            foreach ($list as $key => $val) {
                $list[$key]['title'] = $val['title'];
            }
            $builder = new AdminSortBuilder();
            $builder->meta_title = '分组排序';
            $builder->data($list);
            $builder->buttonSubmit(U('sortGroupType'))->buttonBack();
            $builder->display();
        }
    }


    /**
     * groupTrash
     * @param int $page
     * @param int $r
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function groupTrash($page = 1, $r = 20)
    {
        //读取回收站中的数据
        $map = array('status' => '-1');
        $model = M('Group');
        $list = $model->where($map)->page($page, $r)->order('sort asc')->select();
        $totalCount = $model->where($map)->count();

        //显示页面
        $builder = new AdminListBuilder();
        $builder
            ->title('群组回收站')
            ->setStatusUrl(U('Group/setGroupStatus'))->buttonRestore()
            ->keyId()->keyLink('title', '标题', 'Group/post?group_id=###')
            ->keyCreateTime()->keyText('post_count', '文章数量')
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    /**
     * sortGroup 群组排序页面
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function sortGroup()
    {
        //读取群组列表
        $list = M('Group')->where(array('status' => array('EGT', 0)))->order('sort asc')->select();

        //显示页面
        $builder = new AdminSortBuilder();
        $builder->title('群组排序')
            ->data($list)
            ->buttonSubmit(U('doSortGroup'))->buttonBack()
            ->display();
    }

    /**
     * setGroupStatus  设置群组状态
     * @param $ids
     * @param $status
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function setGroupStatus($ids, $status)
    {
        if(I('get.tip')=='verify'){
            foreach($ids as $v){
                $postUrl = "http://$_SERVER[HTTP_HOST]" . U('Group/Index/group', array('id' => $v));
                $title = D('Group/Group')->where(array('id'=>$v))->field('title')->find();
                $weiboApi = new WeiboApi();
                $weiboApi->resetLastSendTime();
                $weiboApi->sendWeibo("管理员通过了群组【" . $title['title'] . "】的审核：" . $postUrl);
            }
        }
        $builder = new AdminListBuilder();
        $builder->doSetStatus('Group', $ids, $status);
    }

    /**
     * doSortGroup 对群组排序
     * @param $ids
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function doSortGroup($ids)
    {
        $builder = new AdminSortBuilder();
        $builder->doSort('Group', $ids);
    }

    /**
     * editGroup  编辑群组
     * @param int $id
     * @param string $title
     * @param int $create_time
     * @param int $status
     * @param int $allow_user_group
     * @param int $logo
     * @param int $type_id
     * @param string $detail
     * @param int $type
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function editGroup($id = 0, $title = '', $create_time = 0, $status = 1, $allow_user_group = 0, $logo = 0, $type_id = 0, $detail = '', $type = 0)
    {
        if (!IS_POST) {
            //判断是否为编辑模式
            $isEdit = $id ? true : false;

            //如果是编辑模式，读取群组的属性
            if ($isEdit) {
                $group = M('Group')->where(array('id' => $id))->find();
            } else {
                $group = array('create_time' => time(), 'post_count' => 0, 'status' => 1);
            }


            $groupType = D('GroupType')->where(array('status' => 1,'pid'=>0))->limit(100)->select();
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
            foreach ($groupType as $type) {
                $opt[$type['id']] = $type['title'];
            }


            //显示页面
            $builder = new AdminConfigBuilder();
            $builder
                ->title($isEdit ? '编辑群组' : '新增群组')
                ->keyId()->keyTitle()->keyTextArea('detail', '群组介绍')
                ->keyRadio('type', '群组类型', '群组的类型', array(0 => '公共群组', 1 => '私有群组'))
                ->keySelect('type_id', '分类', '选择分类', $opt)
                /* ->keyMultiUserGroup('allow_user_group', '允许发帖的用户组')*/
                ->keyStatus()
                ->keySingleImage('logo', '群组logo', '群组logo，300px*300px')
                ->keySingleImage('background', '群组背景', '用于显示的背景，1050px*200px')->keyCreateTime()
                ->data($group)
                ->buttonSubmit(U('editGroup'))->buttonBack()
                ->display();

        } else { //判断是否为编辑模式
            $isEdit = $id ? true : false;


            //生成数据
            $data = array('title' => $title, 'create_time' => $create_time, 'status' => $status, 'allow_user_group' => $allow_user_group, 'logo' => $logo, 'type_id' => $type_id, 'detail' => $detail, 'type' => $type);
            //写入数据库
            $model = M('Group');
            if ($isEdit) {
                $data['id'] = $id;
                $data = $model->create($data);
                $result = $model->where(array('id' => $id))->save($data);

            } else {
                $data = $model->create($data);
                $data['uid']=1;
                $result = $model->add($data);
                if (!$result) {
                    $this->error('创建失败');
                }
            }
            S('group_list', null);
            //返回成功信息
            $this->success($isEdit ? '编辑成功' : '保存成功');
        }
    }

    /**
     * post   帖子列表页面
     * @param int $page
     * @param null $group_id
     * @param int $r
     * @param string $title
     * @param string $content
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function post($page = 1, $group_id = null, $r = 20, $title = '', $content = '')
    {
        //读取文章数据
        $map = array('status' => array('EGT', 0));
        if ($title != '') {
            $map['title'] = array('like', '%' . $title . '%');
        }
        if ($content != '') {
            $map['content'] = array('like', '%' . $content . '%');
        }
        if ($group_id) $map['group_id'] = $group_id;
        $model = M('GroupPost');
        $list = $model->where($map)->order('last_reply_time desc')->page($page, $r)->select();
        $totalCount = $model->where($map)->count();

        foreach ($list as &$v) {
            if ($v['is_top'] == 1) {
                $v['top'] = '版内置顶';
            } else if ($v['is_top'] == 2) {
                $v['top'] = '全局置顶';
            } else {
                $v['top'] = '不置顶';
            }
        }
        //读取群组基本信息
        if ($group_id) {
            $group = M('Group')->where(array('id' => $group_id))->find();
            $groupTitle = ' - ' . $group['title'];
        } else {
            $groupTitle = '';
        }

        //显示页面
        $builder = new AdminListBuilder();
        $builder->title('文章管理' . $groupTitle)
            ->setStatusUrl(U('Group/setPostStatus'))->buttonEnable()->buttonDisable()->buttonDelete()
            ->keyId()->keyLink('title', '标题', 'Group/reply?post_id=###')
            ->keyCreateTime()->keyUpdateTime()->keyTime('last_reply_time', '最后回复时间')->keyText('top', '是否置顶')->keyStatus()->keyDoActionEdit('editPost?id=###')
            ->setSearchPostUrl(U('post'))->search('标题', 'title')->search('内容', 'content')
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    /**
     * postTrash  帖子回收站
     * @param int $page
     * @param int $r
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function postTrash($page = 1, $r = 20)
    {
        //读取文章数据
        $map = array('status' => -1);
        $model = M('GroupPost');
        $list = $model->where($map)->order('last_reply_time desc')->page($page, $r)->select();
        $totalCount = $model->where($map)->count();

        //显示页面
        $builder = new AdminListBuilder();
        $builder->title('文章回收站')
            ->setStatusUrl(U('Group/setPostStatus'))->buttonRestore()
            ->keyId()->keyLink('title', '标题', 'Group/reply?post_id=###')
            ->keyCreateTime()->keyUpdateTime()->keyTime('last_reply_time', '最后回复时间')->keyBool('is_top', '是否置顶')
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    /**
     * editPost  编辑帖子
     * @param null $id
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function editPost($id = null)
    {
        //判断是否在编辑模式
        $isEdit = $id ? true : false;

        //读取文章内容
        if ($isEdit) {
            $post = M('GroupPost')->where(array('id' => $id))->find();


        } else {
            $post = array();
        }
        $group_lists = D('Group/Group')->where(array('status' => 1))->order('sort asc')->select();
        $group_list = array();
        foreach ($group_lists as $v) {
            $group_list[$v['id']] = $v['title'];
        }
        $cate_lists = D('Group/GroupPostCategory')->where(array('status' => 1))->order('sort asc')->select();
        $cate_list = array();
        foreach ($cate_lists as $v) {
            $cate_list[$v['id']] = $v['title'];
        }

        //显示页面
        $builder = new AdminConfigBuilder();
        $builder->title($isEdit ? '编辑文章' : '新建文章')
            ->keyId()->keySelect('group_id', '所在群组', '', $group_list)->keySelect('cate_id', '分类', '', $cate_list)->keyTitle()
            ->keyEditor('content', '内容')
            ->keyRadio('is_top', '置顶', '选择置顶形式', array(0 => '不置顶', 1 => '本版置顶', 2 => '全局置顶'))->keyCreateTime()->keyUpdateTime()
            ->keyTime('last_reply_time', '最后回复时间')
            ->buttonSubmit(U('doEditPost'))->buttonBack()
            ->data($post)
            ->display();
    }

    /**
     * setPostStatus  设置帖子状态
     * @param $ids
     * @param $status
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function setPostStatus($ids, $status)
    {
        $builder = new AdminListBuilder();
        $builder->doSetStatus('GroupPost', $ids, $status);
    }

    /**
     * doEditPost  编辑帖子操作
     * @param null $id
     * @param $title
     * @param $content
     * @param $create_time
     * @param $update_time
     * @param $last_reply_time
     * @param $is_top
     * @param $group_id
     * @param $cate_id
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function doEditPost($id = null, $title, $content, $create_time, $update_time, $last_reply_time, $is_top, $group_id, $cate_id)
    {

        //判断是否为编辑模式
        $isEdit = $id ? true : false;

        //写入数据库
        $model = M('GroupPost');
        $data = array('title' => $title, 'content' => $content, 'create_time' => $create_time, 'update_time' => $update_time, 'last_reply_time' => $last_reply_time, 'is_top' => $is_top, 'group_id' => $group_id, 'cate_id' => $cate_id);
        if ($isEdit) {
            $result = $model->where(array('id' => $id))->save($data);
        } else {
            $result = $model->keyDoActionEdit($data);
        }

        //如果写入不成功，则报错
        /*        if (!$result) {
                    $this->error($isEdit ? '编辑失败' : '创建成功');
                }*/

        //返回成功信息
        $this->success($isEdit ? '编辑成功' : '创建成功');
    }

    /**
     * reply 回复列表
     * @param int $page
     * @param null $post_id
     * @param int $r
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function reply($page = 1, $post_id = null, $r = 20, $uid = 0, $keyword = '')
    {
        //读取回复列表


        $map = array('status' => array('EGT', 0));
        $keyword != '' && $map['content'] = array('like', '%' . $keyword . '%');
        $uid != 0 && $map['uid'] = $uid;
        if ($post_id) $map['post_id'] = $post_id;
        $model = M('GroupPostReply');
        $list = $model->where($map)->order('create_time asc')->page($page, $r)->select();
        $totalCount = $model->where($map)->count();

        foreach ($list as &$v) {
            $v['uname'] = get_nickname($v['uid']);
            $post = D('GroupPost')->field('title')->find($v['post_id']);
            $v['post_title'] = $post['title'];
            $v['show'] ='查看楼中楼回复';
        }
        //显示页面
        $builder = new AdminListBuilder();
        $builder->title('回复管理')
            ->setStatusUrl(U('setReplyStatus'))->buttonEnable()->buttonDisable()->buttonDelete()
            ->keyId()->keyLinkByFlag('post_title', '帖子标题', 'group/index/detail?id=###','post_id')->keyTruncText('content', '内容', 50)->keyText('uname', '发布者')->keyCreateTime()
            ->keyUpdateTime()->keyStatus()->keyLink('show','楼中楼','Admin/Group/lzlreply?id=###')->keyDoActionEdit('editReply?id=###')
            ->data($list)
            ->setSearchPostUrl(U('reply'))->search('用户ID', 'uid')->search('关键词', 'keyword')
            ->pagination($totalCount, $r)
            ->display();
    }


    /**
     * replyTrash   回复回收站
     * @param int $page
     * @param int $r
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function replyTrash($page = 1, $r = 20)
    {
        //读取回复列表
        $map = array('status' => -1);
        $model = M('GroupPostReply');
        $list = $model->where($map)->order('create_time asc')->page($page, $r)->select();
        $totalCount = $model->where($map)->count();
        foreach ($list as &$v) {
            $v['uname'] = get_nickname($v['uid']);

            $v['show'] ='查看楼中楼回复';
        }
        //显示页面
        $builder = new AdminListBuilder();
        $builder->title('回复回收站')
            ->setStatusUrl(U('setReplyStatus'))->buttonRestore()->buttonClear('GroupPostReply')
            ->keyId()->keyTruncText('content', '内容', 50)->keyText('uname', '发布者')->keyCreateTime()->keyUpdateTime()->keyStatus()->keyLink('show','楼中楼','Admin/Group/lzlreplyTrash?id=###')
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    /**
     * setReplyStatus  设置回复状态
     * @param $ids
     * @param $status
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function setReplyStatus($ids, $status)
    {

        D('GroupLzlReply')->where(array('to_f_reply_id'=>array('in',$ids)))->setField('status',$status);

        $builder = new AdminListBuilder();
        $builder->doSetStatus('GroupPostReply', $ids, $status);
    }

    /**
     * editReply  编辑回复
     * @param null $id
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function editReply($id = null)
    {
        //判断是否为编辑模式
        $isEdit = $id ? true : false;

        //读取回复内容
        if ($isEdit) {
            $model = M('GroupPostReply');
            $reply = $model->where(array('id' => $id))->find();
        } else {
            $reply = array('status' => 1);
        }

        //显示页面
        $builder = new AdminConfigBuilder();
        $builder->title($isEdit ? '编辑回复' : '创建回复')
            ->keyId()->keyEditor('content', '内容')->keyCreateTime()->keyUpdateTime()->keyStatus()
            ->data($reply)
            ->buttonSubmit(U('doEditReply'))->buttonBack()
            ->display();
    }

    /**
     * doEditReply  执行编辑回复操作
     * @param null $id
     * @param $content
     * @param $create_time
     * @param $update_time
     * @param $status
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function doEditReply($id = null, $content, $create_time, $update_time, $status)
    {
        //判断是否为编辑模式
        $isEdit = $id ? true : false;

        //写入数据库
        $data = array('content' => $content, 'create_time' => $create_time, 'update_time' => $update_time, 'status' => $status);
        $model = M('GroupPostReply');
        if ($isEdit) {
            $result = $model->where(array('id' => $id))->save($data);
        } else {
            $result = $model->add($data);
        }

        //如果写入出错，则显示错误消息
        if (!$result) {
            $this->error($isEdit ? '编辑失败' : '创建失败');
        }

        //返回成功消息
        $this->success($isEdit ? '编辑成功' : '创建成功', U('reply'));
    }

    /**
     * postType  帖子分类
     * @param int $page
     * @param int $r
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function postType($page = 1, $r = 20)
    {
        //读取数据
        $map = array('status' => array('GT', -1));
        $model = M('GroupPostCategory');
        $list = $model->where($map)->page($page, $r)->order('group_id asc, sort asc')->select();
        $totalCount = $model->where($map)->count();
        foreach ($list as &$cate) {
            $group =  D('Group')->where(array('id'=>$cate['group_id']))->find();
            $cate['group_name'] = $group['title'];
            $cate['post_count'] = D('GroupPost')->where(array('cate_id' => $cate['id']))->count();
        }
        unset($cate);
        //显示页面
        $builder = new AdminListBuilder();
        $builder
            ->title('分类管理')
            ->buttonNew(U('Group/editPostCate'))
            ->setStatusUrl(U('Group/setGroupPostCateStatus'))->buttonEnable()->buttonDisable()->buttonDelete()
          /*  ->buttonSort(U('Group/sortPostCate'))*/
            ->keyId()->keyText('group_name','所属群组')->keyText('title', '标题')
            ->keyCreateTime()->keyText('post_count', '群组数量')->keyStatus()->keyDoActionEdit('editPostCate?id=###')
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    /**
     * setGroupPostCateStatus  设置帖子分类状态
     * @param $ids
     * @param $status
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function setGroupPostCateStatus($ids, $status)
    {

        $builder = new AdminListBuilder();
        $builder->doSetStatus('GroupPostCategory', $ids, $status);

    }

    /**
     * editPostCate  编辑帖子分类
     * @param int $id
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function editPostCate($id = 0)
    {
        if (IS_POST) {
            $data = D('GroupPostCategory')->create();
            if ($id != 0) {
                $res = D('GroupPostCategory')->save($data);
            } else {
                $res = D('GroupPostCategory')->add($data);
            }
            if ($res) {
                $this->success(($id == 0 ? '添加' : '编辑') . '成功');
            } else {
                $this->error(($id == 0 ? '添加' : '编辑') . '失败');
            }

        } else {
            $builder = new AdminConfigBuilder();

            $groups = D('Group')->where(array('status' => 1))->select();
            foreach ($groups as $group) {
                $opt[$group['id']] = $group['title'];
            }

            if ($id != 0) {
                $wordCate1 = D('GroupPostCategory')->find($id);
            } else {
                $wordCate1 = array('status' => 1, 'sort' => 0);
            }
            $builder->title('新增分类')->keyId()->keySelect('group_id','所属群组','',$opt)->keyText('title', '标题')
                ->keyStatus()->keyCreateTime()
                ->data($wordCate1)
                ->buttonSubmit(U('Group/editPostCate'))->buttonBack()->display();
        }
    }

    /**
     * sortPostCate  对帖子分类排序
     * @param null $ids
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function sortPostCate($ids = null)
    {
        if (IS_POST) {
            $builder = new AdminSortBuilder();
            $builder->doSort('GroupPostCategory', $ids);
        } else {
            $map['status'] = array('egt', 0);
            $list = D('GroupPostCategory')->where($map)->order("sort asc")->select();
            foreach ($list as $key => $val) {
                $list[$key]['title'] = $val['title'];
            }
            $builder = new AdminSortBuilder();
            $builder->meta_title = '分组排序';
            $builder->data($list);
            $builder->buttonSubmit(U('sortPostCate'))->buttonBack();
            $builder->display();
        }
    }

    /**
     * lzlreply  楼中楼回复列表
     * @param int $page
     * @param null $post_id
     * @param int $r
     * @param int $uid
     * @param string $keyword
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function lzlreply($page = 1, $id = null, $r = 20, $uid = 0, $keyword = '')
    {
        //读取回复列表

        $map = array('status' => array('EGT', 0));

        $keyword != '' && $map['content'] = array('like', '%' . $keyword . '%');
        $uid != 0 && $map['uid'] = $uid;
        if ($id) $map['to_f_reply_id'] = $id;

        $model = M('GroupLzlReply');
        $list = $model->where($map)->order('create_time asc')->page($page, $r)->select();

        $totalCount = $model->where($map)->count();

        foreach ($list as &$v) {
            $v['uname'] = get_nickname($v['uid']);
        }
        //显示页面
        $builder = new AdminListBuilder();
        $builder->title('楼中楼回复管理')
            ->setStatusUrl(U('setLzlReplyStatus'))->buttonEnable()->buttonDisable()->buttonDelete()
            ->keyId()->keyTruncText('content', '内容', 50)->keyText('uname', '发布者')->keyTime('ctime', '创建时间')->keyStatus()->keyDoActionEdit('editLzlReply?id=###')
            ->data($list)
            ->setSearchPostUrl(U('lzlreply',array('id'=>$id)))->search('用户ID', 'uid')->search('关键词', 'keyword')
            ->pagination($totalCount, $r)
            ->display();
    }

    /**
     * lzlreplyTrash  楼中楼回复回收站
     * @param int $page
     * @param int $r
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function lzlreplyTrash($page = 1, $r = 20,$id=null)
    {
        //读取回复列表
        $map = array('status' => -1);
        if ($id) $map['to_f_reply_id'] = $id;
        $model = M('GroupLzlReply');
        $list = $model->where($map)->order('create_time asc')->page($page, $r)->select();
        $totalCount = $model->where($map)->count();
        foreach ($list as &$v) {
            $v['uname'] = get_nickname($v['uid']);
        }
        //显示页面
        $builder = new AdminListBuilder();
        $builder->title('回复回收站')
            ->setStatusUrl(U('setLzlReplyStatus'))->buttonRestore()->buttonClear('GroupLzlReply')
            ->keyId()->keyTruncText('content', '内容', 50)->keyText('uname', '发布者')->keyCreateTime()->keyUpdateTime()->keyStatus()
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    /**
     * setLzlReplyStatus  设置楼中楼回复状态
     * @param $ids
     * @param $status
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function setLzlReplyStatus($ids, $status)
    {
        $builder = new AdminListBuilder();

     if($status == 1){
            D('GroupLzlReply')->where(array('id'=>array('in',$ids)))->setField('is_del',0);
        }else{
        D('GroupLzlReply')->where(array('id'=>array('in',$ids)))->setField('is_del',1);
    }
        $builder->doSetStatus('GroupLzlReply', $ids, $status);

    }

    /**
     * editLzlReply   编辑楼中楼回复
     * @param null $id
     * @param string $content
     * @param string $ctime
     * @param string $status
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function editLzlReply($id = null, $content = '', $ctime = '', $status = '')
    {
        if (IS_POST) {
            //判断是否为编辑模式
            $isEdit = $id ? true : false;

            //写入数据库
            $data = array('content' => $content, 'ctime' => $ctime, 'status' => $status);
            $model = M('GroupLzlReply');
            if ($isEdit) {
                $result = $model->where(array('id' => $id))->save($data);
            } else {
                $result = $model->add($data);
            }

            //如果写入出错，则显示错误消息
            if (!$result) {
                $this->error($isEdit ? '编辑失败' : '创建失败');
            }

            //返回成功消息
            $this->success($isEdit ? '编辑成功' : '创建成功', U('lzlreply'));
        } else {
            //判断是否为编辑模式
            $isEdit = $id ? true : false;

            //读取回复内容
            if ($isEdit) {
                $model = M('GroupLzlReply');
                $reply = $model->where(array('id' => $id))->find();
            } else {
                $reply = array('status' => 1);
            }

            //显示页面
            $builder = new AdminConfigBuilder();
            $builder->title($isEdit ? '编辑回复' : '创建回复')
                ->keyId()->keyTextArea('content', '内容')->keyTime('ctime', '创建时间')->keyStatus()
                ->data($reply)
                ->buttonSubmit(U('editLzlReply'))->buttonBack()
                ->display();
        }

    }




    /**
     * banList 用户禁言列表
     * @param int $page
     * @param int $r
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function banList($page=1,$r=20,$uid = 0,$type = 'post'){

        $map = array('app'=>'Group','mod'=>$type,'action'=>'ban');
        $uid != 0 && $map['uid'] =$uid;
        $model = M('UserAction');
        $list = $model->where($map)->order('create_time asc')->page($page, $r)->select();
        $totalCount = $model->where($map)->count();
        foreach ($list as &$v) {
            $v['uname'] = get_nickname($v['uid']);
        }
        //显示页面
        $builder = new AdminListBuilder();
        $type == 'post' && $title =  '禁止发帖列表';
        $type == 'reply' && $title =  '禁止发表回复列表';
        $type == 'lzlreply' && $title =  '禁止发表楼中楼回复列表';
        $builder->title($title)
            ->buttonNew(U('addBan',array('type'=>$type)))->setStatusUrl(U('deleteUser'))->buttonDelete()
            ->setSearchPostUrl(U('banList',array('type'=>$type)))->search('用户ID', 'uid')
            ->keyId()->keyText('uid','用户id')->keyText('uname', '用户名')->keyCreateTime()
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    /**
     * addBan   添加禁言用户
     * @param string $ids
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function addBan($ids ='',$type = 'post'){
        if(IS_POST){

            $qian=array(" ","　","\t","\n","\r");$hou=array("","","","","");
            $ids = str_replace($qian,$hou,$ids);
            $ids = explode(',',$ids);
            $data=array();
            $time = time();
            foreach($ids as $v){
                if($v !=''){
                    $data[] = array('uid'=>$v,'create_time'=>$time,'app'=>'Group','mod'=>$type,'action'=>'ban');
                }

            }
            $res = D('UserAction')->addAll($data);
            if($res){
                $this->success('添加成功',U('banList',array('type'=>$type)));
            }else{
                $this->error('添加失败');
            }
        }else{
            $builder = new AdminConfigBuilder();
            $type == 'post' && $title =  '添加禁止发帖用户';
            $type == 'reply' && $title =  '添加禁止发表楼层回复用户';
            $type == 'lzlreply' && $title =  '添加禁止发表楼中楼回复用户';
            $builder->title($title)
                ->keyTextArea('ids', '用户Uid','需要禁言的用户，用‘,’分隔。')
                ->buttonSubmit(U('addBan',array('type'=>$type)))->buttonBack()
                ->display();
        }
    }

    /**
     * deleteUser 取消禁言用户
     * @param $ids
     * @param $status
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function deleteUser($ids, $status)
    {
        if($status == -1){
            $res =  D('UserAction')->where(array('id'=>array('in',$ids)))->delete();
            if(!$res){
                $this->error('删除失败');
            }
            $this->success('删除成功');
        }

    }

    public function config()
    {
        $admin_config = new AdminConfigBuilder();
        $data = $admin_config->handleConfig();
        $admin_config->title('群组基本设置')
            ->keyBool('NEED_VERIFY', '创建群组是否需要审核','默认无需审核')
            ->buttonSubmit('', '保存')->data($data);
        $admin_config->display();
    }


}
