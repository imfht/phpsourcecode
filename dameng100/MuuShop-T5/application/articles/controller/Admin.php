<?php
namespace app\articles\controller;

use app\admin\builder\AdminConfigBuilder;
use app\admin\builder\AdminTreeListBuilder;
use app\admin\builder\AdminListBuilder;
use app\admin\controller\Admin as MuuAdmin;


class Admin extends MuuAdmin
{
    public function _initialize()
    {
        parent::_initialize();
    }
    /**
     * 配置页面
     * @return [type] [description]
     */
    public function config()
    {
        $builder=new AdminConfigBuilder();
        $data=$builder->handleConfig();
        $default_position=<<<str
1:幻灯展示
2:首页推荐
4:栏目推荐
str;

        $builder
            ->title('文章基础设置')
            ->data($data);

        $builder
            ->keyTextArea('ARTICLES_SHOW_POSITION','展示位配置')
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
    /**
     * 栏目分类
     * @return [type] [description]
     */
    public function category()
    {
        //显示页面
        $builder = new AdminTreeListBuilder();

        $tree = model('ArticlesCategory')->getTree(0, 'id,title,sort,pid,status');

        $builder
            ->title('文章分类管理')
            ->suggest('禁用、删除分类时会将分类下的文章转移到默认分类下')
            ->buttonNew(Url('add'))
            ->data($tree)
            ->display();
    }

    /**
     * 分类添加
     * @param int $id
     * @param int $pid
     */
    public function add($id = 0, $pid = 0)
    {
        $title=$id?"编辑":"新增";
        if (request()->isPost()) {
            $data = input();
            if($data['id']){
                $res = model('ArticlesCategory')->save($data,['id'=>$data['id']]);
            }else{
                $res = model('ArticlesCategory')->save($data);
            }
            if ($res) {
                cache('SHOW_EDIT_BUTTON',null);
                $this->success($title.'成功。', Url('category'));
            } else {
                $this->error($title.'失败!'.model('ArticlesCategory')->getError());
            }
        } else {
            $data = [];
            if ($id != 0) {
                $data = model('ArticlesCategory')->find($id);
            } else {
                $father_category_pid=model('ArticlesCategory')->where(['id'=>$pid])->value('pid');
                if($father_category_pid!=0){
                    $this->error('分类不能超过二级！');
                }
            }
            if($pid!=0){
                $categorys = model('ArticlesCategory')->where(['pid'=>0,'status'=>['egt',0]])->select();
            }else{
                $categorys = [];
            }
            $opt = [];
            foreach ($categorys as $category) {
                $opt[$category['id']] = $category['title'];
            }

            $builder = new AdminConfigBuilder();
            $builder
                ->title($title.'分类')
                ->data($data)
                ->keyId()
                ->keyText('title', '分类名')
                ->keySelect('pid', '父分类', '选择父级分类', ['0' => '顶级分类'] + $opt)
                ->keyDefault('pid',$pid)
                ->keyRadio('can_post','前台是否可投稿','',[0=>'否',1=>'是'])
                ->keyDefault('can_post',1)
                ->keyRadio('need_audit','前台投稿是否需要审核','',[0=>'否',1=>'是'])
                ->keyDefault('need_audit',1)
                ->keyInteger('sort','排序')
                ->keyDefault('sort',1)
                ->keyStatus()
                ->keyDefault('status',1)
                ->buttonSubmit(Url('add'))
                ->buttonBack()
                ->display();
        }
    }

    public function setStatus(){
        $ids = input('ids/a');
        $status = input('status');
        !is_array($ids)&&$ids=explode(',',$ids);
        if(in_array(1,$ids)){
            $this->error('id为 1 的分类是网站基础分类，不能被禁用、删除！');
        }
        $builder = new AdminTreeListBuilder();
        $builder->doSetStatus('ArticlesCategory', $ids, $status);
    }
    /**
     * 文章列表页
     * @param  integer $r    [description]
     * @return [type]        [description]
     */
    public function index($r=20)
    {
        $aCate=input('cate',0,'intval');
        if($aCate){
            $cates=model('ArticlesCategory')->getCategoryList(['pid'=>$aCate]);
            if(count($cates)){
                $cates=array_column($cates,'id');
                $cates=array_merge(array($aCate),$cates);
                $map['category']=['in',$cates];
            }else{
                $map['category']=$aCate;
            }
        }
        $aPos=input('pos',0,'intval');
        /* 设置推荐位 */
        if($aPos>0){
            $map[] = "position & {$aPos} = {$aPos}";
        }
        //搜索关键字
        $aKeyword = input('keyword','','text');
        if($aKeyword){
            $map['title']=['like','%'.$aKeyword.'%'];
        }
        $map['status']=1;

        $positions=$this->_getPositions(1);
        $positions = array_merge([['id'=>0,'value'=>'全部(含未推荐)']],$positions);

        $list = model('Articles')->where($map)->order('id', 'desc')->paginate($r);
        $page = $list->render();
        $category=$this->_category();
        foreach($list as &$val){
            $val['category']='['.$val['category'].'] '.$category[$val['category']]['title'];
        }
        unset($val);
        
        $optCategory=$category;
        foreach($optCategory as &$val){
            $val['value']=$val['title'];
        }
        unset($val);
        $optCategory = array_merge([['id'=>0,'value'=>'全部']],$optCategory);
        
        $builder=new AdminListBuilder();
        $builder
            ->title('文章列表')
            ->data($list)
            ->setSelectPostUrl(url('index'))
            ->select('分类：','cate','select','','','',$optCategory)
            ->select('推荐位：','pos','select','','','',$positions)
            ->search('搜索','keyword','text','检索文章标题关键字','搜索')
            ->buttonNew(url('editArticles'))
            ->setStatusUrl(url('setArticleStatus'))
            ->buttonModalPopup(url('doAudit'),null,'审核不通过',['data-title'=>'设置审核失败原因','target-form'=>'ids'])
            ->keyId()
            ->keyUid()
            ->keyText('title','标题')
            ->keyText('category','分类')
            ->keyText('description','摘要')
            ->keyText('sort','排序')
            ->keyStatus()
            ->keyCreateTime()
            ->keyUpdateTime()
            ->page($page);

        $builder->keyDoActionEdit('editArticles?id=###');
        $builder->keyDoActionDelete('setArticleStatus?ids=###&status=-1','回收站');
        $builder->display();
    }


    /**
     * 待审核列表
     * @param  integer $r    每页显示数量
     * @return [type]        [description]
     */
    public function audit($r=20)
    {
        $map['status']=array('in',[0]);
        $list = model('Articles')->where($map)->order('id', 'desc')->paginate($r);
        $page = $list->render();

        $category=$this->_category();
        foreach($list as &$val){
            $val['category']='['.$val['category'].'] '.$category[$val['category']]['title'];
        }
        unset($val);
        
        $builder = new AdminListBuilder();
        $builder
            ->title('待审核列表')
            ->data($list)
            ->setStatusUrl(Url('setArticleStatus'))
            ->buttonEnable(null,'审核通过')
            ->buttonModalPopup(Url('doAudit'),null,'审核不通过',['data-title'=>'设置审核失败原因','target-form'=>'ids'])
            ->keyId()
            ->keyUid()
            ->keyText('title','标题')
            ->keyText('category','分类')
            ->keyText('description','摘要')
            ->keyText('sort','排序');

        //if($aAudit==1){
            $builder->keyText('reason','审核失败原因');
        //}
        $builder
            ->keyCreateTime()
            ->keyUpdateTime()
            ->keyDoActionEdit('editArticles?id=###')
            ->keyDoActionAjax('setArticleStatus?ids=###&status=-1','回收站')
            ->page($page)
            ->display();
    }

    /**
     * 文章回收站列表
     * @param  integer $r [description]
     * @return [type]     [description]
     */
    public function recycle($r=20)
    {
        $map['status']=-1;
        $list = model('Articles')->where($map)->order('id', 'desc')->paginate($r);
        $page = $list->render();

        $category=$this->_category();
        foreach($list as &$val){
            $val['category']='['.$val['category'].'] '.$category[$val['category']]['title'];
        }
        unset($val);

        $builder=new AdminListBuilder();
        $builder
            ->title('回收站')
            ->data($list)
            ->setStatusUrl(Url('setArticleStatus'))
            ->buttonEnable(null,'还原')
            ->buttonDeleteTrue(Url('setTrueDel'))
            ->keyId()
            ->keyUid()
            ->keyText('title','标题')
            ->keyText('category','分类')
            ->keyText('description','摘要')
            ->keyText('sort','排序')
            ->keyCreateTime()
            ->keyUpdateTime();

        $builder
            ->keyDoActionAjax('setArticleStatus?ids=###&status=1','还原')
            ->page($page)
            ->display();
    }

    /**
     * 审核失败原因设置
     */
    public function doAudit()
    {
        if(request()->isPost()){
            $ids=input('post.ids','','text');
            $ids=explode(',',$ids);
            $reason=input('post.reason','','text');
            $res=model('Articles')->where(['id'=>['in',$ids]])->update(['reason'=>$reason,'status'=>2]);
            if($res){
                //发送消息
                $messageModel=model('Common/Message');
                foreach($ids as $val){
                    $articles=model('Articles')->get($val);
                    $tip = '你的投稿【'.$articles['title'].'】审核失败，失败原因：'.$reason;
                    $messageModel->sendMessage($articles['uid'], '文章投稿审核失败！',$tip,  'articles/Index/detail',['id'=>$val], is_login(), 2);
                }
                //发送消息 end
                $this->success('操作成功', 'audit');
            }else{
                $this->error('操作失败');
            }
            
        }else{
            $ids=input('ids/a');
            $ids=implode(',',$ids);
            $this->assign('ids',$ids);
            return $this->fetch('articles@admin/audit');
        }
    }
    /**
     * 设置状态
     */
    public function setArticleStatus()
    {
        $ids = input('ids/a');
        $status = input('status');
        !is_array($ids)&&$ids=explode(',',$ids);
        
        $builder = new AdminTreeListBuilder();
        $builder->doSetStatus('Articles', $ids, $status);
    }
    /**
     * 真实删除
     */
    public function setTrueDel()
    {
        $ids = input('ids/a');
        !is_array($ids)&&$ids=explode(',',$ids);
        //删除内容表
        model('ArticlesDetail')->where(['articles_id' => ['in',$ids]])->delete();
        //删除信息表
        $builder = new AdminListBuilder();
        $builder->doDeleteTrue('Articles', $ids);
    }

    public function editArticles()
    {
        $aId=input('id',0,'intval');
        $title=$aId?"编辑":"新增";
        if(request()->isPost()){
            
            $aId&&$data['id']=$aId;
            $data = input();
            
            $position=explode(',',$data['position']);
            $data['position'] = 0;
            foreach($position as $val){
                $data['position']+=intval($val);
            }
            $res = model('Articles')->editData($data);

            if($res){
                cache('articles_home_data',null);
                //$aId=$aId?$aId:$res;
                $this->success($title.'成功！',Url('index'));
            }else{
                $this->error($title.'失败！',model('Articles')->getError());
            }
        }else{

            $position_options=$this->_getPositions();

            if($aId){
                $data=model('Articles')->getDataById($aId);
                
                $position=[];
                foreach($position_options as $key=>$val){
                    if($key&$data['position']){
                        $position[]=$key;
                    }
                }

                $data['content']=$data['detail']['content'];
                $data['template']=$data['detail']['template'];
                $data['position']=implode(',',$position);
            }else{
                $data = null;
            }
            $category=model('ArticlesCategory')->getCategoryList(['status'=>['egt',0]],1);
            $options=[];
            foreach($category as $val){
                $options[$val['id']]=$val['title'];
            }

            $builder = new AdminConfigBuilder();
            $builder
                ->title($title.'文章')
                ->data($data)
                ->keyId()
                ->keyReadOnly('uid','发布者')->keyDefault('uid',get_uid())
                ->keyText('title','标题')
                ->keyText('keywords','关键字','多个关键字用（,）分隔')
                //->keyEditor('content','内容','','')
                ->keyEditor('content','内容','','wangeditor','','min-height:400px;height:auto;')
                ->keySelect('category','分类','',$options)

                ->keyTextArea('description','摘要')
                ->keySingleImage('cover','封面','尺寸宽带至少大于753px,高度大于304px')
                ->keyInteger('view','阅读量')->keyDefault('view',0)
                ->keyInteger('comment','评论数')->keyDefault('comment',0)
                ->keyInteger('collection','收藏量')->keyDefault('collection',0)
                ->keyInteger('sort','排序')->keyDefault('sort',0)
                //->keyText('template','模板')
                ->keyText('source','来源','原文地址')
                ->keyText('template','模板')
                ->keyCheckBox('position','推荐位','多个推荐，则将其推荐值相加',$position_options)
                ->keyStatus()
                ->keyDefault('status',1)

                ->group('基础','id,uid,title,keywords,cover,content,category')
                ->group('扩展','description,view,comment,sort,position,source,template,status')

                ->buttonSubmit()
                ->buttonBack()
                ->display();
        }
    }



    private function _category()
    {
        $category=model('ArticlesCategory')->getCategoryList(['status'=>['egt',0]],1);
        $category = collection($category)->toArray();
        $category=array_combine(array_column($category,'id'),$category);
        return $category;
    }
    /**
     * 文章定位
     * @param  integer $type [description]
     * @return [type]        [description]
     */
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