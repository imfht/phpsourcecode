<?php
namespace Admin\Controller;

use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminListBuilder;
use Admin\Builder\AdminTreeListBuilder;


class MuueventController extends AdminController
{
    protected $muueventModel;
    protected $muueventTypeModel;

    function _initialize()
    {
        $this->muueventModel = D('Muuevent/Muuevent');
        $this->muueventTypeModel = D('Muuevent/MuueventType');
        parent::_initialize();
    }
    /**
     * 应用配置
     * @return [type] [description]
     */
    public function config()
    {
        $admin_config = new AdminConfigBuilder();
        $data = $admin_config->handleConfig();

        $admin_config->title('活动基本设置')
            ->keyBool('MUUEVENT_CONFIG_NEED_VERIFY', '创建活动是否需要审核','默认无需审核')

            ->keyText('MUUEVENT_CONFIG_BAIDUMAP_AK','百度地图秘钥')
            ->group('基本配置','MUUEVENT_CONFIG_NEED_VERIFY')
            ->group('百度地图配置','MUUEVENT_CONFIG_BAIDUMAP_AK')
            ->groupLocalComment('本地评论配置','event')
            ->buttonSubmit('', '保存')->data($data);
        $admin_config->display();
    }
    /**
     * 系统默认海报管理列表
     * @return [type] [description]
     */
    public function cover_config(){

    }
    /**
     * 活动列表
     * @param  integer $page [description]
     * @param  integer $r    [description]
     * @return [type]        [description]
     */
    public function eventList($page = 1, $r = 20)
    {
        //读取列表v
        $map = array('status' => 1);
        list($list,$totalCount) = $this->muueventModel->getListByPage($map,$page,$order='create_time desc','*',$r);
        
        //显示页面
        $builder = new AdminListBuilder();

        $attr['class'] = 'btn ajax-post';
        $attr['target-form'] = 'ids';

        $builder->title('内容管理')
            ->setStatusUrl(U('setMuueventContentStatus'))
            ->buttonDelete()
            ->button('设为推荐', array_merge($attr, array('url' => U('doRecommend', array('tip' => 1)))))
            ->button('取消推荐', array_merge($attr, array('url' => U('doRecommend', array('tip' => 0)))))
            ->keyId()
            ->keyLink('title', '标题', 'Muuevent/Index/detail?id=###')
            ->keyUid()
            ->keyCreateTime()
            ->keyStatus()
            ->keyMap('is_recommend', '是否推荐', array(0 => '否', 1 => '是'));

        $builder->keyDoActionEdit('Muuevent/editEvent?id=###','查看/编辑');
        $builder->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }
    /**
     * 活动待审核
     * @param  integer $page [description]
     * @param  integer $r    [description]
     * @return [type]        [description]
     */
    public function verify($page = 1, $r = 10)
    {
        //读取列表
        $map = array('status' => 0);
        $model = $this->muueventModel;
        $list = $model->where($map)->page($page, $r)->select();
        unset($li);
        $totalCount = $model->where($map)->count();

        //显示页面
        $builder = new AdminListBuilder();
        $attr['class'] = 'btn ajax-post';
        $attr['target-form'] = 'ids';
        $builder
            ->title('审核内容')
            ->setStatusUrl(U('setMuueventContentStatus'))
            ->buttonEnable('', '审核通过')
            ->buttonModalPopup(U('doAudit'),null,'审核不通过',array('data-title'=>'设置审核失败原因','target-form'=>'ids'))
            ->buttonDelete()
            ->keyId()
            ->keyLink('title', '标题', 'Muuevent/Index/detail?id=###')
            ->keyUid()
            ->keyCreateTime()
            ->keyStatus()
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }
    /**
     * 回收站
     * @param  integer $page [description]
     * @param  integer $r    [description]
     * @return [type]        [description]
     */
    public function trash($page = 1, $r = 10)
    {
        //读取列表
        $map = array('status' => -1);
        $model = D('Muuevent');
        $list = $model->where($map)->page($page, $r)->select();
        $totalCount = $model->where($map)->count();

        //显示页面
        $builder = new AdminListBuilder();
        $builder->title('内容回收站')
            ->setStatusUrl(U('setMuueventContentStatus'))->buttonRestore()
            ->buttonModalPopup(U('setTrueDel'),'','彻底删除',array('data-title'=>'是否彻底删除','target-form'=>'ids'))
            ->keyId()
            ->keyLink('title', '标题', 'Muuevent/Index/detail?id=###')
            ->keyUid()
            ->keyCreateTime()
            ->keyStatus()
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    /**
     * 设置状态
     * @param $ids
     * @param $status
     * autor:xjw129xjt
     */
    public function setMuueventContentStatus($ids, $status)
    {
        $builder = new AdminListBuilder();
        if ($status == 1) {
            foreach ($ids as $id) {
                $content = D('Muuevent')->find($id);
                D('Common/Message')->sendMessage($content['uid'],$title = '活动审核通知', "管理员审核通过了您发布的活动。现在可以在列表看到该活动了。",  'Muuevent/Index/detail', array('id' => $id ), is_login(), 2);
            }

        }
        $builder->doSetStatus('Muuevent', $ids, $status);

    }

    /**
     * 审核失败原因设置
     */
    public function doAudit()
    {
        if(IS_POST){
            $ids=I('post.ids','','text');
            $ids=explode(',',$ids);
            $reason=I('post.reason','','text');
            $res=D('Muuevent/Muuevent')->where(array('id'=>array('in',$ids)))->setField(array('reason'=>$reason,'status'=>-1));
            if($res){
                $result['status']=1;
                $result['url']=U('Admin/Muuevent/verify');
                //发送消息
                $messageModel=D('Common/Message');
                foreach($ids as $val){
                    $event=D('Muuevent/Muuevent')->getDataById($val);
                    $tip = '你的发布的【'.$event['title'].'】审核失败，失败原因：'.$reason;
                    $messageModel->sendMessage($event['uid'], '活动审核失败！',$tip,  'Muuevent/Index/detail',array('id'=>$val), is_login(), 2);
                }
                //发送消息 end
            }else{
                $result['status']=0;
                $result['info']='操作失败！';
            }
            $this->ajaxReturn($result);
        }else{
            $ids=I('ids');
            $ids=implode(',',$ids);
            $this->assign('ids',$ids);
            $this->display(T('Muuevent@Admin/audit'));
        }
    }
    /**
     * 推荐或取消
     * @param  [type] $ids [description]
     * @param  [type] $tip [description]
     * @return [type]      [description]
     */
    public function doRecommend($ids, $tip)
    {
        D('Muuevent')->where(array('id' => array('in', $ids)))->setField('is_recommend', $tip);
        $this->success('设置成功', $_SERVER['HTTP_REFERER']);
    }

    /**
     * 编辑活动
     * @return [type] [description]
     */
    public function editEvent()
    {
        $aId=I('id',0,'intval');
        $title=$aId?"查看/编辑":"新增";
        if(IS_POST){
            
            $result=$this->muueventModel->editData();
            
            if($result){
                $aId=$aId?$aId:$result;
                $this->success($title.'成功！',U('Muuevent/editEvent',array('id'=>$aId)));
            }else{
                $this->error($title.'失败！',$this->muueventModel->getError());
            }
        }else{

            if($aId){
                $data=$this->muueventModel->find($aId);
            }
            $category=$this->muueventTypeModel->getTypeList(array('status'=>array('egt',0)),1);
            $options=array();
            foreach($category as $val){
                $options[$val['id']]=$val['title'];
            }

            $builder=new AdminConfigBuilder();
            $builder->title($title.'活动')
                ->data($data)
                ->keyId()
                ->keyReadOnly('uid','发布者')->keyDefault('uid',get_uid())
                ->keyText('title','活动标题')
                ->keySelect('type_id','活动分类','',$options)
                ->keyCity()
                ->keyText('address','详细地址')
                ->keyTime('sTime','开始时间')
                ->keyTime('eTime','结束时间')
                ->keyText('limitCount','限制人数')
                ->keySingleImage('cover_id','活动海报','尺寸宽带400px,高度300px')
                ->keyTextArea('description','摘要')
                ->keyInteger('view_count','阅读量')->keyDefault('view',0)
                ->keyEditor('explain','内容','','wangeditor','','min-height:400px;height:auto;')
                ->keyStatus()

                ->buttonSubmit()->buttonBack()
                ->display();
        }
    }
    /**
     * 真实删除（谨慎使用）
     * @param [type] $ids [description]
     */
    public function setTrueDel($ids)
    {
    if(IS_POST){
        $ids=I('post.ids','','text');
        $ids=explode(',',$ids);
        //!is_array($ids)&&$ids=explode(',',$ids);
        $res=$this->muueventModel->setTrueDel($ids);
        if($res){
            $this->success('彻底删除成功！',U('trash'));
        }else{
            $this->error('操作失败！'.$this->muueventModel->getError());
        }
    }else{
            $ids=I('ids');
            $ids=implode(',',$ids);
            $this->assign('ids',$ids);
            $this->display(T('Muuevent@Admin/trueDel'));
        }
    }

    /**
     * 活动分类管理
     * @return [type] [description]
     */
    public function index()
    {
        //显示页面
        $builder = new AdminTreeListBuilder();

        $tree = D('Muuevent/MuueventType')->getTree(0, 'id,title,sort,pid,status');


        $builder
            ->title('活动分类管理')
            ->buttonNew(U('Muuevent/add'))
            ->setLevel(2)
            ->data($tree)
            ->display(); 
    }

    /**
     * 新增、编辑分类
     * @param  integer $id  [description]
     * @param  integer $pid [description]
     * @return [type]       [description]
     */
    public function add($id = 0, $pid = 0)
    {
        if (IS_POST) {
     
            if ($this->muueventTypeModel->editData()) {

                $this->success('编辑成功。');
            } else {
                $this->error('编辑失败。');
            }
            
        } else {
            $builder = new AdminConfigBuilder();

            if ($id != 0) {
                $data = $this->muueventTypeModel->find($id);
            } else {
                $father_category_pid=$this->muueventTypeModel->where(array('id'=>$pid))->getField('pid');
                if($father_category_pid!=0){
                    $this->error('分类不能超过二级！');
                }
            }
            if($pid!=0){
                $categorys = $this->muueventTypeModel->where(array('pid'=>0,'status'=>array('egt',0)))->select();
            }
            
            $opt = array();
            foreach ($categorys as $category) {
                $opt[$category['id']] = $category['title'];
            }


            $builder->title('新增分类')
                ->data($data)
                ->keyId()
                ->keySelect('pid', '父分类', '选择父级分类', array('0' => '顶级分类') + $opt)->keyDefault('pid',$pid)
                ->keyText('title', '标题')

                ->keyStatus()->keyDefault('status',1)
                ->keyCreateTime()
                

                ->buttonSubmit(U('Muuevent/add'))
                ->buttonBack()
                ->display();
        }

    }

    public function operate($type = 'move', $from = 0)
    {
        $builder = new AdminConfigBuilder();
        $from = D('MuueventType')->find($from);

        $opt = array();
        $types = $this->muueventTypeModel->select();
        foreach ($types as $event) {
            $opt[$event['id']] = $event['title'];
        }
        if ($type === 'move') {

            $builder->title('移动分类')->keyId()->keySelect('pid', '父分类', '选择父分类', $opt)->buttonSubmit(U('Muuevent/add'))->buttonBack()->data($from)->display();
        } else {

            $builder->title('合并分类')->keyId()->keySelect('toid', '合并至的分类', '选择合并至的分类', $opt)->buttonSubmit(U('Muuevent/doMerge'))->buttonBack()->data($from)->display();
        }

    }

    public function doMerge($id, $toid)
    {
        $effect_count=D('Muuevent')->where(array('type_id'=>$id))->setField('type_id',$toid);
        D('MuueventType')->where(array('id'=>$id))->setField('status',-1);
        $this->success('合并分类成功。共影响了'.$effect_count.'个内容。',U('index'));
        //TODO 实现合并功能 issue
    }




    public function typeTrash($page = 1, $r = 20)
    {
        //读取列表
        $map = array('status' => -1);
        $model = $this->muueventTypeModel;
        $list = $model->where($map)->page($page, $r)->select();
        $totalCount = $model->where($map)->count();

        //显示页面
        $builder = new AdminListBuilder();
        $builder->title('活动类型回收站')
            ->setStatusUrl(U('setStatus'))->buttonRestore()
            ->keyId()->keyText('title', '标题')->keyStatus()->keyCreateTime()
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }
    /**
     * 设置活动分类状态：删除=-1，禁用=0，启用=1
     * @param $ids
     * @param $status
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function setStatus($ids, $status)
    {
        !is_array($ids)&&$ids=explode(',',$ids);
        if(in_array(1,$ids)){
            $this->error('id为 1 的分类是活动基础分类，不能被禁用、删除！');
        }
        $builder = new AdminListBuilder();
        $builder->doSetStatus('MuueventType', $ids, $status);
    }
}
