<?php
/**

 */

namespace Admin\Controller;


use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminListBuilder;

class AboutController extends AdminController{

    protected $aboutModel;
    protected $aboutCategoryModel;
    protected $feedbackModel;

    function _initialize()
    {
        parent::_initialize();
        $this->aboutModel = D('About/About');
        $this->aboutCategoryModel = D('About/AboutCategory');
        $this->feedbackModel = D('About/Feedback');
    }

    /**
     * 分类
     */
    public function aboutCategory()
    {
        //显示页面
        $builder = new AdminListBuilder();

        $list=$this->aboutCategoryModel->getCategoryList(array('status'=>array('egt',0)));

        $builder->title('分类管理')
            ->suggest('删除分类时会将分类下的文章转移到默认分类(id为1)下')
            ->setStatusUrl(U('About/setCategoryStatus'))
            ->buttonNew(U('About/editCategory'))
            ->buttonEnable()->buttonDisable()->buttonDelete()
            ->keyId()
            ->keyText('title','分类名')
            ->keyText('sort','排序')
            ->keyStatus('status','状态')
            ->keyDoActionEdit('About/editCategory?id=###')
            ->data($list)
            ->display();
    }

    /**
     * 编辑分类
     * @param  integer $id [description]
     * @return [type]      [description]
     */
    public function editCategory($id = 0)
    {
        $title=$id?"编辑":"新增";
        if (IS_POST) {
            if ($this->aboutCategoryModel->editData()) {
                $this->success($title.'成功。', U('About/aboutCategory'));
            } else {
                $this->error($title.'失败!'.$this->aboutCategoryModel->getError());
            }
        } else {
            $builder = new AdminConfigBuilder();

            if ($id != 0) {
                $data = $this->aboutCategoryModel->find($id);
            }
            $builder->title($title.'分类')
                ->data($data)
                ->keyId()->keyText('title', '标题')
                ->keyInteger('sort','排序')->keyDefault('sort',0)
                ->keyStatus()->keyDefault('status',1)
                ->buttonSubmit(U('About/editCategory'))->buttonBack()
                ->display();
        }

    }

    /**
     * 设置文章分类状态：删除=-1，禁用=0，启用=1
     * @param [type] $ids    [description]
     * @param [type] $status [description]
     */
    public function setCategoryStatus($ids, $status)
    {
        !is_array($ids)&&$ids=explode(',',$ids);
        if($status==-1){
            if(in_array(1,$ids)){
                $this->error('id为 1 的分类是基础分类，不能被删除！');
            }
            $map['category']=array('in',$ids);
            $this->aboutModel->where($map)->setField('category',1);
        }
        $builder = new AdminListBuilder();
        $builder->doSetStatus('AboutCategory', $ids, $status);
    }
    /**
     * 单页配置
     * @author 大蒙<59262424@qq.com>
     */
     public function config()
    {
        $builder=new AdminConfigBuilder();
        $data=$builder->handleConfig();

        $builder
        ->title('基础设置')
        ->data($data);

        $builder
        //基础设置部分
        ->keyText('ABOUT_CATEGORY_TITLE','文章顶部标题')
        ->keyDefault('ABOUT_CATEGORY_TITLE','关于我们')
        ->keyText('ABOUT_DEFAULT_ID','默认显示的内容id')

        //联盟动态首页设置部分
        ->keyText('ABOUT_NEWS_SHOW_TITLE', '标题名称', '在首页展示块的标题')->keyDefault('ABOUT_NEWS_SHOW_TITLE','企业动态')
        ->keyText('ABOUT_NEWS_SHOW_DESCRIPTION', '简短描述', '精简的描述模块内容')->keyDefault('ABOUT_NEWS_SHOW_DESCRIPTION','模块简单描述')
        ->keyText('ABOUT_NEWS_SHOW_COUNT', '显示动态的个数', '只有在网站首页模块中启用了模块之后才会显示')->keyDefault('ABOUT_NEWS_SHOW_COUNT',4)
        ->keyRadio('ABOUT_NEWS_SHOW_TYPE', '动态的筛选范围', '', array('1' => '后台推荐', '0' => '全部'))->keyDefault('ABOUT_NEWS_SHOW_TYPE',0)
        ->keyRadio('ABOUT_NEWS_SHOW_ORDER_FIELD', '排序值', '展示模块的数据排序方式', array('sort' => '排序值', 'create_time' => '发表时间', 'update_time' => '更新时间'))->keyDefault('GOODS_SHOW_ORDER_FIELD','sort')
        ->keyRadio('ABOUT_NEWS_SHOW_ORDER_TYPE', '排序方式', '展示模块的数据排序方式', array('desc' => '倒序，从大到小', 'asc' => '正序，从小到大'))->keyDefault('ABOUT_NEWS_SHOW_ORDER_TYPE','desc')
        ->keyText('ABOUT_NEWS_SHOW_CACHE_TIME', '缓存时间', '默认600秒，以秒为单位')->keyDefault('ABOUT_NEWS_SHOW_CACHE_TIME','600')

       ->group('基础配置', 'ABOUT_CATEGORY_TITLE,ABOUT_DEFAULT_ID')
        ->group('内容调用配置', 'ABOUT_NEWS_SHOW_COUNT,ABOUT_NEWS_SHOW_TITLE,ABOUT_NEWS_SHOW_DESCRIPTION,ABOUT_NEWS_SHOW_TYPE,ABOUT_NEWS_SHOW_ORDER_TYPE,ABOUT_NEWS_SHOW_ORDER_FIELD,ABOUT_NEWS_SHOW_CACHE_TIME')


        ->buttonSubmit()
        ->buttonBack()
        ->display();
    }


    //文章文章列表start
    public function index($page=1,$r=20)
    {
        $aCate=I('cate',0,'intval');
        if($aCate==-1){
            $map['category']=0;
        }else if($aCate!=0){
            $map['category']=$aCate;
        }
        $map['status']=array('neq',-1);

        list($list,$totalCount)=$this->aboutModel->getListByPage($map,$page,'sort asc,update_time desc','*',$r);
        $category=$this->aboutCategoryModel->getCategoryList(array('status'=>array('egt',0)));
        $category=array_combine(array_column($category,'id'),$category);
        foreach($list as &$val){
            if($val['category']){
                $val['category']='['.$val['category'].'] '.$category[$val['category']]['title'];
            }else{
                $val['category']='未分类';
            }
        }
        unset($val);

        $optCategory=$category;
        foreach($optCategory as &$val){
            $val['value']=$val['title'];
        }
        unset($val);

        $builder=new AdminListBuilder();
        $builder->title('内容列表')
            ->data($list)
            ->buttonNew(U('About/editAbout'))
            ->setStatusUrl(U('About/setAboutStatus'))
            ->buttonEnable()->buttonDisable()->buttonDelete()
            ->setSelectPostUrl(U('Admin/About/index'))
            ->select('','cate','select','','','',array_merge(array(array('id'=>0,'value'=>'全部')),$optCategory,array(array('id'=>-1,'value'=>'未分类'))))
            ->keyId()->keyUid()->keyLink('title','标题','About/Index/index?id=###')->keyText('category','分类','可选')->keyText('sort','排序')
            ->keyStatus()->keyCreateTime()->keyUpdateTime()
            ->keyDoActionEdit('About/editAbout?id=###')
            ->pagination($totalCount,$r)
            ->display();
    }

    public function setAboutStatus($ids,$status=1)
    {
        !is_array($ids)&&$ids=explode(',',$ids);
        $builder = new AdminListBuilder();
        $builder->doSetStatus('About', $ids, $status);
    }

    /**
     * [editAbout description]
     * @return [type] [description]
     */
    public function editAbout()
    {
        $aId=I('id',0,'intval');
        $title=$aId?"编辑":"新增";
        if(IS_POST){
            $aId&&$data['id']=$aId;
            $data['uid']=I('post.uid',get_uid(),'intval');
            $data['title']=I('post.title','','text');
            $data['cover']=I('post.cover',0,'intval');
            $data['content']=I('post.content','','html');
            $data['category']=I('post.category',0,'intval');
            $data['sort']=I('post.sort',0,'intval');
            $data['template']=I('post.template','','text');
            $data['status']=I('post.status',1,'intval');
            if(!mb_strlen($data['title'],'utf-8')){
                $this->error('标题不能为空！');
            }
            $result=$this->aboutModel->editData($data);
            if($result){
                $aId=$aId?$aId:$result;
                $this->success($title.'成功！',U('About/editAbout',array('id'=>$aId)));
            }else{
                $this->error($title.'失败！',$this->aboutModel->getError());
            }
        }else{
            if($aId){
                $data=$this->aboutModel->find($aId);
            }
            $category=$this->aboutCategoryModel->getCategoryList(array('status'=>array('egt',-1)));
            $options=array(0=>'无分类');
            foreach($category as $val){
                $options[$val['id']]=$val['title'];
            }
            $builder=new AdminConfigBuilder();
            $builder->title($title.'文章')
                ->data($data)
                ->keyId()
                ->keyReadOnly('uid','发布者')->keyDefault('uid',get_uid())
                ->keyText('title','标题')
                ->keySingleImage('cover','封面')
                ->keyEditor('content','内容','','all',array('width' => '850px', 'height' => '600px'))
                ->keySelect('category','分类','',$options)
                ->keyInteger('sort','排序')->keyDefault('sort',0)
                ->keyText('template','模板')
                ->keyStatus()->keyDefault('status',1)
                ->buttonSubmit()->buttonBack()
                ->display();
        }
    }

    /**
     * 反馈列表
     * @author 大蒙<59262424@qq.com>
    **/
    public function feedBack($page=1,$r=20)
    {
        $title="反馈列表";
        //$model = D('feedback');
        $list = $this->feedbackModel->where($map)->page($page, $r)->select();
        unset($li);
        $totalCount = $this->feedbackModel->where($map)->count();

        $builder=new AdminListBuilder();
        $builder->title('反馈列表')
            ->data($list)
            ->keyId()
            ->keyLink('email','标题','About/Index/feedBack?id=###')
            ->keyCreateTime()
            ->keytext('content','内容');
        $builder->buttonModalPopup(U('About/setTrueDel'),'','彻底删除',array('data-title'=>'是否彻底删除','target-form'=>'ids'))
            ->pagination($totalCount,$r)
            ->display();

    }
    //真实删除
    public function setTrueDel($ids)
    {
    if(IS_POST){
        $ids=I('post.ids','','text');
        $ids=explode(',',$ids);
        
        $res=$this->feedbackModel->setTrueDel($ids);
        if($res){
            $this->success('彻底删除成功！',U('About/feedBack'));
        }else{
            $this->error('操作失败！'.$this->feedbackModel->getError());
        }
    }else{
        $ids=I('ids');
            $ids=implode(',',$ids);
            $this->assign('ids',$ids);
            $this->display(T('About@admin/trueDel'));
        }
    }
} 