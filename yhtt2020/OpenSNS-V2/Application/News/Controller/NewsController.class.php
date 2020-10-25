<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-4-27
 * Time: 上午10:21
 * @author 郑钟良<zzl@ourstu.com>
 */

namespace Admin\Controller;


use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminListBuilder;
use Admin\Builder\AdminTreeListBuilder;
use Common\Model\ContentHandlerModel;

class NewsController extends AdminController{

    protected $newsModel;
    protected $newsDetailModel;
    protected $newsCategoryModel;

    function _initialize()
    {
        parent::_initialize();
        $this->newsModel = D('News/News');
        $this->newsDetailModel = D('News/NewsDetail');
        $this->newsCategoryModel = D('News/NewsCategory');
    }

    public function newsCategory()
    {
        //显示页面
        $builder = new AdminTreeListBuilder();

        $tree = $this->newsCategoryModel->getTree(0, 'id,title,sort,pid,status');

        $builder->title(L('_CATEGORY_MANAGER_'))
            ->suggest(L('_CATEGORY_MANAGER_SUGGEST_'))
            ->buttonNew(U('News/add'))
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
        $title=$id?L('_EDIT_'):L('_ADD_');
        if (IS_POST) {
            if ($this->newsCategoryModel->editData()) {
                S('SHOW_EDIT_BUTTON',null);
                $this->success($title.L('_SUCCESS_'), U('News/newsCategory'));
            } else {
                $this->error($title.L('_FAIL_').$this->newsCategoryModel->getError());
            }
        } else {
            $builder = new AdminConfigBuilder();

            if ($id != 0) {
                $data = $this->newsCategoryModel->find($id);
            } else {
                $father_category_pid=$this->newsCategoryModel->where(array('id'=>$pid))->getField('pid');
                if($father_category_pid!=0){
                    $this->error(L('_ERROR_CATEGORY_HIERARCHY_'));
                }
            }
            if($pid!=0){
                $categorys = $this->newsCategoryModel->where(array('pid'=>0,'status'=>array('egt',0)))->select();
            }
            $opt = array();
            foreach ($categorys as $category) {
                $opt[$category['id']] = $category['title'];
            }
            $builder->title($title.L('_CATEGORY_'))
                ->data($data)
                ->keyId()->keyText('title', L('_TITLE_'))
                ->keySelect('pid',L('_FATHER_CLASS_'), L('_FATHER_CLASS_SELECT_'), array('0' =>L('_TOP_CLASS_')) + $opt)->keyDefault('pid',$pid)
                ->keyRadio('can_post',L('_PLAY_YN_'),'',array(0=>L('_NO_'),1=>L('_YES_')))->keyDefault('can_post',1)
                ->keyRadio('need_audit',L('_PLAY_YN_AUDIT_'),'',array(0=>L('_NO_'),1=>L('_YES_')))->keyDefault('need_audit',1)
                ->keyInteger('sort',L('_SORT_'))->keyDefault('sort',0)
                ->keyStatus()->keyDefault('status',1)
                ->buttonSubmit(U('News/add'))->buttonBack()
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
            $this->error(L('_ERROR_CANNOT_'));
        }
        if($status==0||$status==-1){
            $map['category']=array('in',$ids);
            $this->newsModel->where($map)->setField('category',1);
        }
        $builder = new AdminListBuilder();
        $builder->doSetStatus('newsCategory', $ids, $status);
    }
//分类管理end

    public function config()
    {
        $builder=new AdminConfigBuilder();
        $data=$builder->handleConfig();
        $default_position=<<<str
1:系统首页
2:推荐阅读
4:本类推荐
str;

        $builder->title(L('_NEWS_BASIC_CONF_'))
            ->data($data);

        $builder->keyTextArea('NEWS_SHOW_POSITION',L('_GALLERY_CONF_'))->keyDefault('NEWS_SHOW_POSITION',$default_position)
            ->keyRadio('NEWS_ORDER_FIELD',L('_FRONT_LIST_SORT_'),L('_SORT_RULE_'),array('view'=>L('_VIEWS_'),'create_time'=>L('_CREATE_TIME_'),'update_time'=>L('_UPDATE_TIME_')))->keyDefault('NEWS_ORDER_FIELD','create_time')
            ->keyRadio('NEWS_ORDER_TYPE',L('_LIST_SORT_STYLE_'),'',array('asc'=>L('_ASC_'),'desc'=>L('_DESC_')))->keyDefault('NEWS_ORDER_TYPE','desc')
            ->keyInteger('NEWS_PAGE_NUM','',L('_LIST_IN_PAGE_'))->keyDefault('NEWS_PAGE_NUM','20')

            ->keyText('NEWS_SHOW_TITLE', L('_TITLE_NAME_'), L('_HOME_BLOCK_TITLE_'))->keyDefault('NEWS_SHOW_TITLE',L('_HOT_NEWS_'))
            ->keyText('NEWS_SHOW_COUNT', L('_NEWS_SHOWS_'), L('_TIP_NEWS_ARISE_'))->keyDefault('NEWS_SHOW_COUNT',4)
            ->keyRadio('NEWS_SHOW_TYPE', L('_NEWS_SCREEN_'), '', array('1' => L('_BG_RECOMMEND_'), '0' => L('_EVERYTHING_')))->keyDefault('NEWS_SHOW_TYPE',0)
            ->keyRadio('NEWS_SHOW_ORDER_FIELD', L('_SORT_VALUE_'), L('_TIP_SORT_VALUE_'), array('view' => L('_VIEWS_'), 'create_time' => L('_DELIVER_TIME_'), 'update_time' => L('_UPDATE_TIME_')))->keyDefault('NEWS_SHOW_ORDER_FIELD','view')
            ->keyRadio('NEWS_SHOW_ORDER_TYPE', L('_SORT_TYPE_'), L('_TIP_SORT_TYPE_'), array('desc' => L('_COUNTER_'), 'asc' => L('_DIRECT_')))->keyDefault('NEWS_SHOW_ORDER_TYPE','desc')
            ->keyText('NEWS_SHOW_CACHE_TIME', L('_CACHE_TIME_'),L('_TIP_CACHE_TIME_'))->keyDefault('NEWS_SHOW_CACHE_TIME','600')

            ->group(L('_BASIC_CONF_'), 'NEWS_SHOW_POSITION,NEWS_ORDER_FIELD,NEWS_ORDER_TYPE,NEWS_PAGE_NUM')->group(L('_HOME_SHOW_CONF_'), 'NEWS_SHOW_COUNT,NEWS_SHOW_TITLE,NEWS_SHOW_TYPE,NEWS_SHOW_ORDER_TYPE,NEWS_SHOW_ORDER_FIELD,NEWS_SHOW_CACHE_TIME')
            ->groupLocalComment(L('_LOCAL_COMMENT_CONF_'),'index')
            ->buttonSubmit()->buttonBack()
            ->display();
    }


    //资讯列表start
    public function index($page=1,$r=20)
    {
        $aCate=I('cate',0,'intval');
        if($aCate){
            $cates=$this->newsCategoryModel->getCategoryList(array('pid'=>$aCate));
            if(count($cates)){
                $cates=array_column($cates,'id');
                $cates=array_merge(array($aCate),$cates);
                $map['category']=array('in',$cates);
            }else{
                $map['category']=$aCate;
            }
        }
        $aDead=I('dead',0,'intval');
        if($aDead){
            $map['dead_line']=array('elt',time());
        }else{
            $map['dead_line']=array('gt',time());
        }
        $aPos=I('pos',0,'intval');
        /* 设置推荐位 */
        if($aPos>0){
            $map[] = "position & {$aPos} = {$aPos}";
        }

        $map['status']=1;

        $positions=$this->_getPositions(1);

        list($list,$totalCount)=$this->newsModel->getListByPage($map,$page,'update_time desc','*',$r);
        $category=$this->newsCategoryModel->getCategoryList(array('status'=>array('egt',0)),1);
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
        $builder->title(L('_NEWS_LIST_'))
            ->data($list)
            ->setSelectPostUrl(U('Admin/News/index'))
            ->select('','cate','select','','','',array_merge(array(array('id'=>0,'value'=>L('_EVERYTHING_'))),$optCategory))
            ->select('','dead','select','','','',array(array('id'=>0,'value'=>L('_NEWS_CURRENT_')),array('id'=>1,'value'=>L('_NEWS_HISTORY_'))))
            ->select(L('_RECOMMENDATIONS_'),'pos','select','','','',array_merge(array(array('id'=>0,'value'=>L('_ALL_DEFECTIVE_'))),$positions))
            ->buttonNew(U('News/editNews'))->buttonDelete(U('News/setNewsStatus'))
            ->keyId()->keyUid()->keyText('title',L('_TITLE_'))->keyText('category',L('_CATEGORY_'))->keyText('description',L('_NOTE_'))->keyText('sort',L('_SORT_'))
            ->keyStatus()->keyTime('dead_line',L('_PERIOD_TO_'))->keyCreateTime()->keyUpdateTime()
            ->keyDoActionEdit('News/editNews?id=###');
        if(!$aDead){
            $builder->ajaxButton(U('News/setDead'),'',L('_SET_EXPIRE_'))->keyDoAction('News/setDead?ids=###',L('_SET_EXPIRE_'))
                ->buttonModalPopup(U('News/changeCategory',array('id'=>$map['id'])), array(),'迁移分类',array('data-title'=>'迁移分类','target-form'=>'ids'));
        }
        $builder->pagination($totalCount,$r)
            ->display();
    }

    //待审核列表
    public function audit($page=1,$r=20)
    {
        $aAudit=I('audit',0,'intval');
        if($aAudit==3){
            $map['status']=array('in',array(-1,2));
        }elseif($aAudit==2){
            $map['dead_line']=array('elt',time());
            $map['status']=2;
        }elseif($aAudit==1){
            $map['status']=-1;
        }else{
            $map['status']=2;
            $map['dead_line']=array('gt',time());
        }
        list($list,$totalCount)=$this->newsModel->getListByPage($map,$page,'update_time desc','*',$r);
        $cates=array_column($list,'category');
        $category=$this->newsCategoryModel->getCategoryList(array('id'=>array('in',$cates),'status'=>1),1);
        $category=array_combine(array_column($category,'id'),$category);
        foreach($list as &$val){
            $val['category']='['.$val['category'].'] '.$category[$val['category']]['title'];
        }
        unset($val);

        $builder=new AdminListBuilder();

        $builder->title(L('_AUDIT_LIST_'))
            ->data($list)
            ->setStatusUrl(U('News/setNewsStatus'))
            ->buttonEnable(null,L('_AUDIT_SUCCESS_'))
            ->buttonModalPopup(U('News/doAudit'),null,L('_AUDIT_UNSUCCESS_'),array('data-title'=>L('_AUDIT_FAIL_REASON_'),'target-form'=>'ids'))
            ->setSelectPostUrl(U('Admin/News/audit'))
            ->select('','audit','select','','','',array(array('id'=>0,'value'=>L('_AUDIT_READY_')),array('id'=>1,'value'=>L('_AUDIT_FAIL_')),array('id'=>2,'value'=>L('_EXPIRE_AND_UNAUDITED_')),array('id'=>3,'value'=>L('_AUDIT_ALL_'))))
            ->keyId()->keyUid()->keyText('title',L('_TITLE_'))->keyText('category',L('_CATEGORY_'))->keyText('description',L('_NOTE_'))->keyText('sort',L('_SORT_'));
        if($aAudit==1){
            $builder->keyText('reason',L('_FAULT_REASON_'));
        }
        $builder->keyTime('dead_line',L('_PERIOD_TO_'))->keyCreateTime()->keyUpdateTime()
            ->keyDoActionEdit('News/editNews?id=###')
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
            $res=$this->newsModel->where(array('id'=>array('in',$ids)))->setField(array('reason'=>$reason,'status'=>-1));
            if($res){
                $result['status']=1;
                $result['url']=U('Admin/News/audit');
                //发送消息
                $messageModel=D('Common/Message');
                foreach($ids as $val){
                    $news=$this->newsModel->getData($val);
                    $tip = L('_YOUR_NEWS_').'【'.$news['title'].'】'.L('_FAIL_AND_REASON_').$reason;
                    $messageModel->sendMessage($news['uid'], L('_NEWS_AUDIT_FAIL_'),$tip,  'News/Index/detail',array('id'=>$val), is_login(), 2);
                }
                //发送消息 end
            }else{
                $result['status']=0;
                $result['info']=L('_OPERATE_FAIL_');
            }
            $this->ajaxReturn($result);
        }else{
            $ids=I('ids');
            $ids=implode(',',$ids);
            $this->assign('ids',$ids);
            $this->display(T('News@Admin/audit'));
        }
    }

    public function setNewsStatus($ids,$status=1)
    {
        !is_array($ids)&&$ids=explode(',',$ids);
        $builder = new AdminListBuilder();
        S('news_home_data',null);
        //发送消息
        $messageModel=D('Common/Message');
        foreach($ids as $val){
            $news=$this->newsModel->getData($val);
            $tip = L('_YOUR_NEWS_').'【'.$news['title'].'】'.L('_AUDIT_SUCCESS_').'。';
            $messageModel->sendMessage($news['uid'],L('_NEWS_AUDIT_SUCCESS_'), $tip,  'News/Index/detail',array('id'=>$val), is_login(), 2);
        }
        //发送消息 end
        $builder->doSetStatus('News', $ids, $status);
    }

    public function editNews()
    {
        $aId=I('id',0,'intval');
        $title=$aId?L('_EDIT_'):L('_ADD_');
        if(IS_POST){
            $aId&&$data['id']=$aId;
            $data['uid']=I('post.uid',get_uid(),'intval');
            $data['title']=I('post.title','','op_t');
            $data['content']=I('post.content','','filter_content');
            $data['category']=I('post.category',0,'intval');
            $data['description']=I('post.description','','op_t');

            $aCover=I('post.cover',0,'intval');
            $aBanner=I('post.banner',0,'intval');
            if($aCover==0&&$aBanner==0)
            {
                $match=get_pic($data['content']);
                if($match==null){
                    $data['cover']=0;
                    $data['banner']=0;
                }else{
                    if(substr($match,0,4)=='http'){
                        $str=$match;
                    }else{
                        $str=substr($match,1,strlen($match)-1);
                        $str=substr($str,strpos($str,'/'),strlen($str)-strpos($str,'/'));
                    }
                    $coverId=M('picture')->where(array('path'=>$str))->getField('id');
                    //$this->error($coverId['id']);
                    $data['cover']=$coverId;
                    $data['banner']=$coverId;
                }
            }elseif ($aCover==0 && $aBanner!=0){
                $data['cover']=$aBanner;
                $data['banner']=$aBanner;
            }elseif ($aCover!=0 && $aBanner==0){
                $data['cover']=$aCover;
                $data['banner']=$aCover;
            }else{
                $data['cover']=$aCover;
                $data['banner']=$aBanner;
            }

            $data['view']=I('post.view',0,'intval');
            $data['comment']=I('post.comment',0,'intval');
            $data['collection']=I('post.collection',0,'intval');
            $data['sort']=I('post.sort',0,'intval');
            $data['dead_line']=I('post.dead_line',2147483640,'intval');
            if($data['dead_line']==0){
                $data['dead_line']=2147483640;
            }
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
            $result=$this->newsModel->editData($data);
            if($result){
                S('news_home_data',null);
                $aId=$aId?$aId:$result;
                $this->success($title.L('_SUCCESS_'),U('News/editNews',array('id'=>$aId)));
            }else{
                $this->error($title.L('_SUCCESS_'),$this->newsModel->getError());
            }
        }else{
            $position_options=$this->_getPositions();
            if($aId){
                $data=$this->newsModel->find($aId);
                $detail=$this->newsDetailModel->find($aId);
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
            $category=$this->newsCategoryModel->getCategoryList(array('status'=>array('egt',0)),1);
            $options=array();
            foreach($category as $val){
                $options[$val['id']]=$val['title'];
            }
            $config = get_editor_config('NEWS_ADMIN_ADD', '[all]', 1) ;
            $builder=new AdminConfigBuilder();
            $builder->title($title.L('_NEWS_'))
                ->data($data)
                ->keyId()
                ->keyReadOnly('uid',L('_PUBLISHER_'))->keyDefault('uid',get_uid())
                ->keyText('title',L('_TITLE_'))
                ->keyEditor('content',L('_CONTENT_'), '', $config, array('width' => '700px', 'height' => '400px'))
                ->keySelect('category',L('_CATEGORY_'),'',$options)

                ->keyTextArea('description',L('_NOTE_'))
                ->keySingleImage('cover',L('_COVER_'))
                ->keySingleImage('banner','Banner图')
                ->keyInteger('view',L('_VIEWS_'))->keyDefault('view',0)
                ->keyInteger('comment',L('_COMMENTS_'))->keyDefault('comment',0)
                ->keyInteger('collection',L('_COLLECTS_'))->keyDefault('collection',0)
                ->keyInteger('sort',L('_SORT_'))->keyDefault('sort',0)
                ->keyTime('dead_line',L('_PERIOD_TO_'))->keyDefault('dead_line',2147483640)
                ->keyText('template',L('_TEMPLATE_'))
                ->keyText('source',L('_SOURCE_'),L('_SOURCE_ADDRESS_'))
                ->keyCheckBox('position',L('_RECOMMENDATIONS_'),L('_TIP_RECOMMENDATIONS_'),$position_options)
                ->keyStatus()->keyDefault('status',1)

                ->group(L('_BASIS_'),'id,uid,title,cover,banner,content,category')
                ->group(L('_EXTEND_'),'description,view,comment,sort,dead_line,position,source,template,status')

                ->buttonSubmit()->buttonBack()
                ->display();
        }
    }
    //回收站
    public function  newsTrash($page = 1, $r = 20,$model=''){
        $builder = new AdminListBuilder();
        $builder->clearTrash($model);
        $map = array('status' => -1);
        $data = M('news')->where($map)->page($page, $r)->select();
        $totalCount = M('news')->where($map)->count();
        $builder->title('资讯回收站')->buttonRestore(U('News/setNewsStatus'))
            ->buttonClear('news')
            ->data($data)
            ->keyId()->keyUid()->keyText('title',L('_TITLE_'))->keyText('category',L('_CATEGORY_'))->keyText('description',L('_NOTE_'))->keyText('sort',L('_SORT_'))
            ->keyStatus()->keyTime('dead_line',L('_PERIOD_TO_'))->keyCreateTime()->keyUpdateTime()
            ->pagination($totalCount, $r)
            ->display();
    }

    public function setDead($ids)
    {
        !is_array($ids)&&$ids=explode(',',$ids);
        $res=$this->newsModel->setDead($ids);
        if($res){
            //发送消息
            $messageModel=D('Common/Message');
            foreach($ids as $val){
                $news=$this->newsModel->getData($val);
                $tip = L('_YOUR_NEWS_').'【'.$news['title'].'】'.L('_SET_TO_EXPIRE_').'。';
                $messageModel->sendMessage($news['uid'],L('_NEWS_TO_EXPIRE_'),  $tip, 'News/Index/detail',array('id'=>$val), is_login(), 2);
            }
            //发送消息 end
            S('news_home_data',null);
            $this->success(L('_SUCCESS_TIP_'),U('News/index'));
        }else{
            $this->error(L('_OPERATE_FAIL_').$this->newsModel->getError());
        }
    }


    private function _checkOk($data=array()){
        if(!mb_strlen($data['title'],'utf-8')){
            $this->error(L('_TIP_TITLE_EMPTY_'));
        }
        if(mb_strlen($data['content'],'utf-8')<20){
            $this->error(L('_TIP_CONTENT_LENGTH_'));
        }
        return true;
    }

    private function _getPositions($type=0)
    {
        $default_position=<<<str
1:系统首页
2:推荐阅读
4:本类推荐
str;
        $positons=modC('NEWS_SHOW_POSITION',$default_position,'News');
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
    public function changeCategory(){
        if(IS_POST){
            $aIds=I('post.ids','','op_t');
            $aCid=I('post.cid','','op_t');
            $ids=explode(',',$aIds);
            $map['id']=array('in',$ids);
            $data['category']=$aCid;
            $res=M('news')->where($map)->save($data);
            if($res){
                $this->ajaxReturn(1);
            }else{
                $this->ajaxReturn(0);
            }
            
        }else{
        $aIds=I('get.ids');
        $ids=implode(',',$aIds);
        $map['id']=array('in',$ids);
        $newsdata=M('news')->where($map)->field('id,title')->select();
        $cat=M('news_category')->where('status=1')->field('id,title')->select();
        $this->assign('cat',$cat);
        $this->assign('data',$newsdata);
        $this->assign('ids',$ids);
        $this->display(T('News@Admin/changecategory'));
    }
    }
 
} 