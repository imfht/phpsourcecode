<?php
namespace app\admin\Controller;

use app\admin\controller\Admin;
use app\admin\builder\AdminConfigBuilder;
use app\admin\builder\AdminListBuilder;
use app\admin\builder\AdminTreeListBuilder;
use think\Db;

class UserTag extends Admin
{
    protected $userTagModel;

    public function _initialize()
    {
        parent::_initialize();
        $this->userTagModel=model('ucenter/UserTag');
    }
    /**
     * 标签分类
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function userTag()
    {
        //显示页面
        $builder = new AdminTreeListBuilder();
        $attr['class'] = 'btn ajax-post';
        $attr['target-form'] = 'ids';

        $tree = $this->userTagModel->getTree(0,'id,title,sort,pid,status');

        $builder
            ->title(lang('_USER_TAG_MANAGER_'))
            ->suggest(lang('_USER_TAG_MANAGER_VICE_'))
            ->buttonNew(Url('UserTag/add'))
            ->button(lang('_RECYCLE_BIN_'),['href'=>Url('UserTag/TagTrash')])
            ->data($tree)
            ->display();
    }

    /**
     * 分类添加
     */
    public function add($id=0,$pid=0)
    {
        if (request()->isPost()) {
            $data = input('');
            if ($data['id'] != 0) {
                $result=Db::name('UserTag')->where(['id'=>$data['id']])->update($data);
                if ($result) {
                    $this->success(lang('_SUCCESS_EDIT_').lang('_PERIOD_'), Url('UserTag/userTag'));
                } else {
                    $this->error(lang('_FAIL_EDIT_').lang('_PERIOD_').$this->userTagModel->getError());
                }
            } else {
                $result=Db::name('UserTag')->insert($data);
                if ($result) {
                    $this->success(lang('_SUCCESS_ADD_').lang('_PERIOD_'));
                } else {
                    $this->error(lang('_FAIL_ADD_').lang('_PERIOD_').$this->userTagModel->getError());
                }
            }
        } else {
            $builder = new AdminConfigBuilder();
            $opt = array();
            if ($id != 0) {
                $category = $this->userTagModel->find($id);
                if($category['pid']!=0){
                    $categorys = $this->userTagModel->where(['pid'=>0])->select();
                    foreach ($categorys as $cate) {
                        $opt[$cate['id']] = $cate['title'];
                    }
                }
            } else {
                $category = ['pid' => $pid, 'status' => 1];
                $father_category_pid=$this->userTagModel->where(['id'=>$pid])->value('pid');
                if($father_category_pid!=0){
                    $this->error(lang('_ERROR_CATEGORY_HIR_LIMIT_').lang('_EXCLAMATION_'));
                }
                $categorys = $this->userTagModel->where(['pid'=>0])->select();
                foreach ($categorys as $cate) {
                    $opt[$cate['id']] = $cate['title'];
                }
            }

            $builder->title(lang('_TAG_ADD_'));

            $builder
            ->keyId()
            ->keyText('title', lang('_TITLE_'))
            ->keySelect('pid', lang('_FATHER_CLASS_'), lang('_FATHER_CLASS_SELECT_'), [0 => lang('_TOP_CLASS_')] + $opt)
            ->keyStatus()
            ->data($category)
            ->buttonSubmit(Url('UserTag/add'))
            ->buttonBack()
            ->display();
        }
    }

    /**
     * 分类回收站
     * @param int $page
     * @param int $r
     */
    public function tagTrash()
    {
        $builder = new AdminListBuilder();
        //读取微博列表
        $map = array('status' => -1);
        $list = $this->userTagModel->where($map)->paginate(20);
        $page = $list->render();
        //显示页面
        $builder
            ->title(lang('_TRASH_TAG_CATEGORY_'))
            ->setStatusUrl(Url('setStatus'))->buttonRestore()->buttonDeleteTrue(Url('UserTag/userTagClear'))
            ->keyId()
            ->keyText('title', lang('_TITLE_'))
            ->keyText('pid',lang('_ID_CATEGORY_FATHER_'))
            ->data($list)
            ->page($page)
            ->display();
    }

    public function userTagClear($ids)
    {
        $builder=new AdminListBuilder();
        $builder->doDeleteTrue('UserTag',$ids);
    }
} 