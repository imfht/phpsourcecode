<?php
/**
 * Date: 16-4-26
 * Time: 上午10:21
 * @author 大蒙
 */

namespace Admin\Controller;


use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminListBuilder;
use Admin\Builder\AdminTreeListBuilder;
use Common\Model\ContentHandlerModel;

class ArticlesController extends AdminController{

    protected $articlesModel;
    protected $articlesDetailModel;
    protected $articlesCategoryModel;

    function _initialize()
    {
        parent::_initialize();
        $this->articlesModel = D('Articles/Articles');
        $this->articlesDetailModel = D('Articles/ArticlesDetail');
        $this->articlesCategoryModel = D('Articles/ArticlesCategory');
    }

    public function articlesCategory()
    {
        //显示页面
        $builder = new AdminTreeListBuilder();

        $tree = $this->articlesCategoryModel->getTree(0, 'id,title,sort,pid,status');

        $builder->title('文章分类管理')
            ->suggest('禁用、删除分类时会将分类下的文章转移到默认分类下')
            ->buttonNew(U('Articles/add'))
            ->data($tree)
            ->display();
    }

    /**分类添加
     * @param int $id
     * @param int $pid
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function add($id = 0, $pid = 0)
    {
        $title=$id?"编辑":"新增";
        if (IS_POST) {
            if ($this->articlesCategoryModel->editData()) {
                S('SHOW_EDIT_BUTTON',null);
                $this->success($title.'成功。', U('Articles/articlesCategory'));
            } else {
                $this->error($title.'失败!'.$this->articlesCategoryModel->getError());
            }
        } else {
            $builder = new AdminConfigBuilder();

            if ($id != 0) {
                $data = $this->articlesCategoryModel->find($id);
            } else {
                $father_category_pid=$this->articlesCategoryModel->where(array('id'=>$pid))->getField('pid');
                if($father_category_pid!=0){
                    $this->error('分类不能超过二级！');
                }
            }
            if($pid!=0){
                $categorys = $this->articlesCategoryModel->where(array('pid'=>0,'status'=>array('egt',0)))->select();
            }
            $opt = array();
            foreach ($categorys as $category) {
                $opt[$category['id']] = $category['title'];
            }
            $builder->title($title.'分类')
                ->data($data)
                ->keyId()->keyText('title', '标题')
                ->keySelect('pid', '父分类', '选择父级分类', array('0' => '顶级分类') + $opt)->keyDefault('pid',$pid)
                ->keyRadio('can_post','前台是否可投稿','',array(0=>'否',1=>'是'))->keyDefault('can_post',1)
                ->keyRadio('need_audit','前台投稿是否需要审核','',array(0=>'否',1=>'是'))->keyDefault('need_audit',1)
                ->keyInteger('sort','排序')->keyDefault('sort',0)
                ->keyStatus()->keyDefault('status',1)
                ->buttonSubmit(U('Articles/add'))->buttonBack()
                ->display();
        }

    }

    /**
     * 设置资讯分类状态：删除=-1，禁用=0，启用=1
     * @param $ids
     * @param $status
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function setStatus($ids, $status)
    {
        !is_array($ids)&&$ids=explode(',',$ids);
        if(in_array(1,$ids)){
            $this->error('id为 1 的分类是网站基础分类，不能被禁用、删除！');
        }
        if($status==0||$status==-1){
            $map['category']=array('in',$ids);
            $this->articlesModel->where($map)->setField('category',1);
        }
        $builder = new AdminListBuilder();
        $builder->doSetStatus('articlesCategory', $ids, $status);
    }
//分类管理end

    public function config()
    {
        $builder=new AdminConfigBuilder();
        $data=$builder->handleConfig();
        $default_position=<<<str
1:幻灯展示
2:首页推荐
4:栏目推荐
str;

        $builder->title('文章基础设置')
            ->data($data);

        $builder->keyTextArea('ARTICLES_SHOW_POSITION','展示位配置')
            ->keyDefault('ARTICLES_SHOW_POSITION',$default_position)
            ->keyText('ARTICLES_SHOW_TITLE', '标题名称', '在首页展示块的标题')->keyDefault('ARTICLES_SHOW_TITLE','热门资讯')
            ->keyText('ARTICLES_SHOW_DESCRIPTION', '简短描述', '精简的描述模块内容')->keyDefault('ARTICLES_SHOW_DESCRIPTION','模块简单描述')
            ->keyText('ARTICLES_SHOW_COUNT', '显示文章的个数', '只有在网站首页模块中启用了资讯块之后才会显示')->keyDefault('ARTICLES_SHOW_COUNT',4)
            ->keyRadio('ARTICLES_SHOW_TYPE', '资讯的筛选范围', '', array('1' => '后台推荐', '0' => '全部'))->keyDefault('ARTICLES_SHOW_TYPE',0)
            ->keyRadio('ARTICLES_SHOW_ORDER_FIELD', '排序值', '展示模块的数据排序方式', array('view' => '阅读数', 'create_time' => '发表时间', 'update_time' => '更新时间'))->keyDefault('ARTICLES_SHOW_ORDER_FIELD','view')
            ->keyRadio('ARTICLES_SHOW_ORDER_TYPE', '排序方式', '展示模块的数据排序方式', array('desc' => '倒序，从大到小', 'asc' => '正序，从小到大'))->keyDefault('ARTICLES_SHOW_ORDER_TYPE','desc')
            ->keyText('ARTICLES_SHOW_CACHE_TIME', '缓存时间', '默认600秒，以秒为单位')->keyDefault('ARTICLES_SHOW_CACHE_TIME','600')

            ->group('基本配置', 'ARTICLES_SHOW_POSITION')->group('首页展示配置', 'ARTICLES_SHOW_COUNT,ARTICLES_SHOW_TITLE,ARTICLES_SHOW_DESCRIPTION,ARTICLES_SHOW_TYPE,ARTICLES_SHOW_ORDER_TYPE,ARTICLES_SHOW_ORDER_FIELD,ARTICLES_SHOW_CACHE_TIME')
            ->groupLocalComment('本地评论配置','index')
            ->buttonSubmit()
            ->buttonBack()
            ->display();
    }


    //资讯列表start
    public function index($page=1,$r=20)
    {
        $aCate=I('cate',0,'intval');
        if($aCate){
            $cates=$this->articlesCategoryModel->getCategoryList(array('pid'=>$aCate));
            if(count($cates)){
                $cates=array_column($cates,'id');
                $cates=array_merge(array($aCate),$cates);
                $map['category']=array('in',$cates);
            }else{
                $map['category']=$aCate;
            }
        }

        $aPos=I('pos',0,'intval');
        /* 设置推荐位 */
        if($aPos>0){
            $map[] = "position & {$aPos} = {$aPos}";
        }

        $map['status']=1;

        $positions=$this->_getPositions(1);

        list($list,$totalCount)=$this->articlesModel->getListByPage($map,$page,'update_time desc','*',$r);
        $category=$this->articlesCategoryModel->getCategoryList(array('status'=>array('egt',0)),1);
        $category=array_combine(array_column($category,'id'),$category);
        foreach($list as &$val){
            $val['category']='['.$val['category'].'] '.$category[$val['category']]['title'];
        }
        unset($val);

        $optCategory=$category;
        foreach($optCategory as &$val){
            $val['value']=$val['title'];
        }
        unset($val);

        $builder=new AdminListBuilder();
        $builder->title('文章列表')
            ->data($list)
            ->setSelectPostUrl(U('Admin/Articles/index'))
            ->select('','cate','select','','','',array_merge(array(array('id'=>0,'value'=>'全部')),$optCategory))
            ->select('推荐位：','pos','select','','','',array_merge(array(array('id'=>0,'value'=>'全部(含未推荐)')),$positions))
            ->buttonNew(U('Articles/editArticles'))
            ->buttonModalPopup(U('Articles/doAudit'),null,'审核不通过',array('data-title'=>'设置审核失败原因','target-form'=>'ids'))
            ->keyId()
            ->keyUid()
            ->keyText('title','标题')
            ->keyText('category','分类')
            ->keyText('description','摘要')
            ->keyText('sort','排序')
            ->keyStatus()
            ->keyCreateTime()
            ->keyUpdateTime();

        $builder->keyDoActionEdit('Articles/editArticles?id=###');
        $builder->keyDoAction('Articles/setDel?ids=###','回收站');

        $builder->pagination($totalCount,$r);
        $builder->display();
    }

    //待审核列表
    public function audit($page=1,$r=20)
    {
        $aAudit=I('audit',0,'intval');
        if($aAudit==2){
            $map['status']=array('in',array(0,2));
        }elseif($aAudit==1){
            $map['status']=0;
        }else{
            $map['status']=2;
        }
        list($list,$totalCount)=$this->articlesModel->getListByPage($map,$page,'update_time desc','*',$r);
        $cates=array_column($list,'category');
        $category=$this->articlesCategoryModel->getCategoryList(array('id'=>array('in',$cates),'status'=>1),1);
        $category=array_combine(array_column($category,'id'),$category);
        foreach($list as &$val){
            $val['category']='['.$val['category'].'] '.$category[$val['category']]['title'];
        }
        unset($val);

        $builder=new AdminListBuilder();

        $builder->title('审核列表（审核通过的不在该列表中）')
            ->data($list)
            ->setStatusUrl(U('Articles/setArticlesStatus'))
            ->buttonEnable(null,'审核通过')
            ->buttonModalPopup(U('Articles/doAudit'),null,'审核不通过',array('data-title'=>'设置审核失败原因','target-form'=>'ids'))
            ->setSelectPostUrl(U('Admin/Articles/audit'))
            ->select('','audit','select','','','',array(array('id'=>0,'value'=>'待审核'),array('id'=>1,'value'=>'审核失败'),array('id'=>2,'value'=>'全部审核')))
            ->keyId()->keyUid()->keyText('title','标题')->keyText('category','分类')->keyText('description','摘要')->keyText('sort','排序');
        if($aAudit==1){
            $builder->keyText('reason','审核失败原因');
        }
        $builder->keyCreateTime()->keyUpdateTime()
            ->keyDoActionEdit('Articles/editArticles?id=###')
            ->pagination($totalCount,$r)
            ->display();
    }

    //回收站列表
    public function recycle($page=1,$r=20)
    {

        $map['status']=-1;

        list($list,$totalCount)=$this->articlesModel->getListByPage($map,$page,'update_time desc','*',$r);
        $cates=array_column($list,'category');
        $category=$this->articlesCategoryModel->getCategoryList(array('id'=>array('in',$cates),'status'=>1),1);
        $category=array_combine(array_column($category,'id'),$category);
        foreach($list as &$val){
            $val['category']='['.$val['category'].'] '.$category[$val['category']]['title'];
        }
        unset($val);

        $builder=new AdminListBuilder();

        $builder->title('回收站列表')
            ->data($list)
            ->setStatusUrl(U('Articles/setArticlesStatus'))
            ->buttonEnable(null,'审核通过')
            ->setSelectPostUrl(U('Admin/Articles/recycle'))

            ->keyId()->keyUid()->keyText('title','标题')->keyText('category','分类')->keyText('description','摘要')->keyText('sort','排序')
            ->keyCreateTime()->keyUpdateTime();

        $builder->keyDoActionEdit('Articles/editArticles?id=###')
            ->buttonModalPopup(U('Articles/setTrueDel'),'','彻底删除',array('data-title'=>'是否彻底删除','target-form'=>'ids'))
            ->pagination($totalCount,$r)
            ->display();
    }

    /**
     * 审核失败原因设置
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function doAudit()
    {
        if(IS_POST){
            $ids=I('post.ids','','text');
            $ids=explode(',',$ids);
            $reason=I('post.reason','','text');
            $res=$this->articlesModel->where(array('id'=>array('in',$ids)))->setField(array('reason'=>$reason,'status'=>-1));
            if($res){
                $result['status']=1;
                $result['url']=U('Admin/Articles/audit');
                //发送消息
                $messageModel=D('Common/Message');
                foreach($ids as $val){
                    $articles=$this->articlesModel->getData($val);
                    $tip = '你的投稿【'.$articles['title'].'】审核失败，失败原因：'.$reason;
                    $messageModel->sendMessage($articles['uid'], '文章投稿审核失败！',$tip,  'Articles/Index/detail',array('id'=>$val), is_login(), 2);
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
            $this->display(T('Articles@Admin/audit'));
        }
    }

    public function setArticlesStatus($ids,$status=1)
    {
        !is_array($ids)&&$ids=explode(',',$ids);
        $builder = new AdminListBuilder();
        S('articles_home_data',null);
        //发送消息
        $messageModel=D('Common/Message');
        foreach($ids as $val){
            $articles=$this->articlesModel->getData($val);
            $tip = '你的投稿【'.$articles['title'].'】审核通过。';
            $messageModel->sendMessage($articles['uid'],'文章投稿审核通过！', $tip,  'Articles/Index/detail',array('id'=>$val), is_login(), 2);
        }
        //发送消息 end
        $builder->doSetStatus('Articles', $ids, $status);
    }

    public function editArticles()
    {
        $aId=I('id',0,'intval');
        $title=$aId?"编辑":"新增";
        if(IS_POST){
            
            $aId&&$data['id']=$aId;
            $data['uid']=I('post.uid',get_uid(),'intval');
            $data['title']=I('post.title','','op_t');
            $data['keywords']=I('post.keywords','','op_t');
            $data['content']=I('post.content','','op_h');
            $data['category']=I('post.category',0,'intval');
            $data['description']=I('post.description','','op_t');
            $data['cover']=I('post.cover',0,'intval');
            $data['view']=I('post.view',0,'intval');
            $data['comment']=I('post.comment',0,'intval');
            $data['collection']=I('post.collection',0,'intval');
            $data['sort']=I('post.sort',0,'intval');
            $data['template']=I('post.template','','op_t');
            $data['status']=I('post.status',1,'intval');
            $data['source']=I('post.source','','op_t');
            $data['position']=0;
            $position=I('post.position','','op_t');
            $position=explode(',',$position);
            foreach($position as $val){
                $data['position']+=intval($val);
            }
            $this->_checkOk($data);
            

            $result=$this->articlesModel->editData($data);
            //这里可以放关键字写入方法
            D('Common/keywords')->editKeywords('Articles',$aId?$aId:$result,$data['keywords']);

            if($result){
                S('articles_home_data',null);
                $aId=$aId?$aId:$result;
                $this->success($title.'成功！',U('Articles/editArticles',array('id'=>$aId)));
            }else{
                $this->error($title.'失败！',$this->articlesModel->getError());
            }
        }else{

            $position_options=$this->_getPositions();
            if($aId){
                $data=$this->articlesModel->find($aId);
                $detail=$this->articlesDetailModel->find($aId);
                $data['content']=$detail['content'];
                $data['template']=$detail['template'];
                $position=array();
                foreach($position_options as $key=>$val){
                    if($key&$data['position']){
                        $position[]=$key;
                    }
                }
                $data['position']=implode(',',$position);
            }
            $category=$this->articlesCategoryModel->getCategoryList(array('status'=>array('egt',0)),1);
            $options=array();
            foreach($category as $val){
                $options[$val['id']]=$val['title'];
            }
            $builder=new AdminConfigBuilder();
            $builder->title($title.'文章')
                ->data($data)
                ->keyId()
                ->keyReadOnly('uid','发布者')->keyDefault('uid',get_uid())
                ->keyText('title','标题')
                ->keyText('keywords','关键字','多个关键字用（,）分隔')
                ->keyEditor('content','内容','','all',array('width' => '700px', 'height' => '400px'))
                ->keySelect('category','分类','',$options)

                ->keyTextArea('description','摘要')
                ->keySingleImage('cover','封面','尺寸宽带至少大于753px,高度大于304px')
                ->keyInteger('view','阅读量')->keyDefault('view',0)
                ->keyInteger('comment','评论数')->keyDefault('comment',0)
                ->keyInteger('collection','收藏量')->keyDefault('collection',0)
                ->keyInteger('sort','排序')->keyDefault('sort',0)
                ->keyText('template','模板')
                ->keyText('source','来源','原文地址')
                ->keyCheckBox('position','推荐位','多个推荐，则将其推荐值相加',$position_options)
                ->keyStatus()->keyDefault('status',1)

                ->group('基础','id,uid,title,keywords,cover,content,category')
                ->group('扩展','description,view,comment,sort,position,source,template,status')

                ->buttonSubmit()->buttonBack()
                ->display();
        }
    }

    public function setDead($ids)
    {
        !is_array($ids)&&$ids=explode(',',$ids);
        $res=$this->articlesModel->setDead($ids);
        if($res){
            //发送消息
            $messageModel=D('Common/Message');
            foreach($ids as $val){
                $articles=$this->articlesModel->getData($val);
                $tip = '你的文章投稿【'.$articles['title'].'】被设为过期。';
                $messageModel->sendMessage($articles['uid'],'文章投稿被设为过期！',  $tip, 'Articles/Index/detail',array('id'=>$val), is_login(), 2);
            }
            //发送消息 end
            S('articles_home_data',null);
            $this->success('操作成功！',U('Articles/index'));
        }else{
            $this->error('操作失败！'.$this->articlesModel->getError());
        }
    }

    public function setDel($ids)
    {
        !is_array($ids)&&$ids=explode(',',$ids);
        $res=$this->articlesModel->setDel($ids);
        if($res){
            S('articles_home_data',null);
            $this->success('操作成功！',U('Articles/index'));
        }else{
            $this->error('操作失败！'.$this->articlesModel->getError());
        }
    }
    //真实删除
    public function setTrueDel($ids)
    {
    if(IS_POST){
        $ids=I('post.ids','','text');
        $ids=explode(',',$ids);
        //!is_array($ids)&&$ids=explode(',',$ids);
        $res=$this->articlesModel->setTrueDel($ids);
        if($res){
            S('articles_home_data',null);
            $this->success('彻底删除成功！',U('Articles/recycle'));
        }else{
            $this->error('操作失败！'.$this->articlesModel->getError());
        }
    }else{
        $ids=I('ids');
            $ids=implode(',',$ids);
            $this->assign('ids',$ids);
            $this->display(T('Articles@Admin/truedel'));
        }
    }

    private function _checkOk($data=array()){
        if(!mb_strlen($data['title'],'utf-8')){
            $this->error('标题不能为空！');
        }
        if(mb_strlen($data['content'],'utf-8')<20){
            $this->error('内容不能少于20个字！');
        }
        return true;
    }

    private function _getPositions($type=0)
    {
        $default_position=<<<str
1:推荐
str;
        $positons=modC('ARTICLES_SHOW_POSITION',$default_position,'Articles');
        $positons = str_replace("\r", '', $positons);
        $positons = explode("\n", $positons);
        $result=array();
        if($type){
            foreach ($positons as $v) {
                $temp = explode(':', $v);
                $result[] = array('id'=>$temp[0],'value'=>$temp[1]);
            }
        }else{
            foreach ($positons as $v) {
                $temp = explode(':', $v);
                $result[$temp[0]] = $temp[1];
            }
        }

        return $result;
    }
} 