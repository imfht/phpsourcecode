<?php

namespace Articles\Controller;

use Think\Controller;
use Common\Controller\CommonController;

class IndexController extends CommonController{

    protected $articlesModel;
    protected $articlesDetailModel;
    protected $articlesCategoryModel;

    function _initialize()
    {   
        //基础公共控制器
        parent::_initialize();
        
        $this->articlesModel = D('Articles/Articles');
        $this->articlesDetailModel = D('Articles/ArticlesDetail');
        $this->articlesCategoryModel = D('Articles/ArticlesCategory');

        $tree = $this->articlesCategoryModel->getTree(0,true,array('status' => 1));
        $this->assign('tree', $tree);
        foreach ($tree as $category) {
            $menu = array('tab' => 'category_' . $category['id'], 'title' => $category['title'], 'href' => U('Articles/index/category', array('id' => $category['id'])));
            if ($category['_']) {
                $menu['children'][] = array( 'title' => '全部', 'href' => U('Articles/index/category', array('id' => $category['id'])));
                foreach ($category['_'] as $child)
                    $menu['children'][] = array( 'title' => $child['title'], 'href' => U('Articles/index/category', array('id' => $child['id'])));
            }
            $menu_list['left'][] = $menu;
        }

        
        $show_edit=S('SHOW_EDIT_BUTTON');
        if($show_edit===false){
            $map['can_post']=1;
            $map['status']=1;
            $show_edit=$this->articlesCategoryModel->where($map)->count();
            S('SHOW_EDIT_BUTTON',$show_edit);
        }
        $menu_list['right']=array();
        if(is_login()){
            $menu_list['right'][]=array('tab' => 'myArticles', 'title' => '<i class="icon-th-list"></i> 我发布的', 'href' =>U('Articles/index/my'));
            if($show_edit){
            $menu_list['right'][]=array('tab' => 'create', 'title' => '<i class="icon-edit"></i> 发布文章', 'href' =>is_login()?U('Articles/index/edit'):"javascript:toast.error('登录后才能操作')");
            }
        }

        $this->assign('tab','home');
        $this->assign('sub_menu', $menu_list);
    }

    public function index($page=1,$r=20)
    {
        /* 首页信息 */
        $map['status']=1;
        /* 获取当前分类下文章列表 */
        list($list,$totalCount) = $this->articlesModel->getListByPage($map,$page,'sort desc,update_time desc','*',$r);
        foreach($list as &$val){
            $val['user']=query_user(array('space_url','avatar32','nickname'),$val['uid']);
        }
        unset($val);
        /* 模板赋值并渲染模板 */
        $this->assign('list', $list);
        $this->assign('totalCount',$totalCount);
        $this->display();
    }

    public function category($page=1,$r=20)
    {
        /* 分类信息 */
        $category = I('id',0,'intval');
        if($category){
            $categoryT = $this->_category($category);
            $cates=$this->articlesCategoryModel->getCategoryList(array('pid'=>$category));
            if(count($cates)){
                $cates=array_column($cates,'id');
                $cates=array_merge(array($category),$cates);
                $map['category']=array('in',$cates);
            }else{
                $map['category']=$category;
            }
        }
        $map['status']=1;
        /* 获取当前分类下文章列表 */
        list($list,$totalCount) = $this->articlesModel->getListByPage($map,$page,'sort desc,update_time desc','*',$r);
        foreach($list as &$val){
            $val['user']=query_user(array('space_url','avatar32','nickname'),$val['uid']);
        }
        unset($val);
        /* 模板赋值并渲染模板 */
        $this->assign('list', $list);
        $this->assign('category', $category);
        $this->assign('categoryT',$categoryT);//栏目信息，可作为seo规则标题
        $this->assign('totalCount',$totalCount);

        $this->display();
    }

    public function my($page=1,$r=20)
    {
        $this->_needLogin();
        $map['uid']=get_uid();
        /* 获取当前分类下文章列表 */
        list($list,$totalCount) = $this->articlesModel->getListByPage($map,$page,'update_time desc','*',$r);
        foreach($list as &$val){
             $val['user']=query_user(array('space_url','avatar32','nickname'),$val['uid']);

                if($val['status']==1){
                    $val['audit_status']='<span style="color: green;">审核通过</span>';
                }elseif($val['status']==2){
                    $val['audit_status']='<span style="color:#4D9EFF;">待审核</span>';
                }elseif($val['status']==-1){
                    $val['audit_status']='<span style="color: #b5b5b5;">审核失败</span>';
                }


        }
        unset($val);
        /* 模板赋值并渲染模板 */
        $this->assign('list', $list);
        $this->assign('totalCount',$totalCount);

        $this->assign('tab','myArticles');
        $this->display();
    }

    public function detail()
    {
        header("Content-Type:text/html; charset=utf-8");
        $aId=I('id',0,'intval');

        /* 标识正确性检测 */
        if (!($aId && is_numeric($aId))) {
            $this->error('文档ID错误！');
        }

        $info=$this->articlesModel->getData($aId);
        
        $author=query_user(array('uid','space_url','nickname','avatar32','avatar64','signature'),$info['uid']);
        $author['articles_count']=$this->articlesModel->where(array('uid'=>$info['uid']))->count();
        //关键字转化成数组
        $keywords = explode(',',$info['keywords']);
        //dump($keywords);

        /*用户所要文章访问量*/
        $author['articles_view']=$this->_totalView($info['uid']);
        /* 获取模板 */
        if (!empty($info['detail']['template'])) { //已定制模板
            $tmpl = 'Index/tmpl/'.$info['detail']['template'];
        } else { //使用默认模板
            $tmpl = 'Index/tmpl/detail';
        }

        $this->_category($info['category']);

        /* 更新浏览数 */
        $map = array('id' => $aId);
        $this->articlesModel->where($map)->setInc('view');
        /* 模板赋值并渲染模板 */

        $this->assign('author',$author);
        $this->assign('info', $info);
        $this->assign('keywords',$keywords);
        $this->setTitle('{$info.title|text} —— {$MODULE_ALIAS}');
        $this->setDescription('{$info.description|text} ——{$MODULE_ALIAS}');
        $this->display($tmpl);
    }

    public function edit()
    {
        $this->_needLogin();
        if(IS_POST){
            $this->_doEdit();
        }else{
            $aId=I('id',0,'intval');
            if($aId){
                $data=$this->articlesModel->getData($aId);
                $this->checkAuth(null,$data['uid'],'你没有编辑该文章权限！');
                if($data['status']==1){
                    $this->error('该文章已被审核，不能被编辑！');
                }
                $this->assign('data',$data);
            }else{
                
                $this->checkAuth('Articles/Index/edit',-1,'你没有发布权限！');
            }
            $title=$aId?"编辑":"新增";
            $category=$this->articlesCategoryModel->getCategoryList(array('status'=>1,'can_post'=>1),1);
            $this->assign('category',$category);
            $this->assign('title',$title);
        }
        $this->assign('tab','create');
        $this->display();
    }
    public function debug(){
        //分类
        $type = MODULE_NAME;
        echo $type;
        static $Auth = null;
        if (!$Auth) {
            $Auth = new \Think\Auth();
        }
        if (!$Auth->check('Articles/Index/edit', get_uid(), $type)) {
            echo 'error';exit;
        }
        echo 'success';
    }
    private function _doEdit()
    {
        $aId=I('post.id',0,'intval');
        $data['category']=I('post.category',0,'intval');

        if($aId){
            $data['id']=$aId;
            $now_data=$this->articlesModel->getData($aId);
            $this->checkAuth(null,$now_data['uid'],'你没有编辑该文章权限！');
            if($now_data['status']==1){
                $this->error('该内容已被审核，不能被编辑！');
            }
            $category=$this->articlesCategoryModel->where(array('status'=>1,'id'=>$data['category']))->find();
            if($category){
                if($category['can_post']){
                    if($category['need_audit']){
                        $data['status']=2;
                    }else{
                        $data['status']=1;
                    }
                }else{
                    $this->error('该分类不能发布！');
                }
            }else{
                $this->error('该分类不存在或被禁用！');
            }
            $data['status']=2;
            $data['template']=$now_data['detail']['template']?:'';
        }else{
            $this->checkAuth('Articles/Index/edit',-1,'你没有发布权限！');
            $this->checkActionLimit('add_articles','Articles',0,is_login(),true);
            $data['uid']=get_uid();
            $data['sort']=$data['position']=$data['view']=$data['comment']=$data['collection']=0;
            $category=$this->articlesCategoryModel->where(array('status'=>1,'id'=>$data['category']))->find();
            if($category){
                if($category['can_post']){
                    if($category['need_audit']){
                        $data['status']=2;
                    }else{
                        $data['status']=1;
                    }
                }else{
                    $this->error('该分类不能投稿！');
                }
            }else{
                $this->error('该分类不存在或被禁用！');
            }
            $data['template']='';
        }
        $data['title']=I('post.title','','text');
        $data['cover']=I('post.cover',0,'intval');
        $data['description']=I('post.description','','text');
        $data['source']=I('post.source','','text');
        $data['content']=I('post.content','','html');
        if(!mb_strlen($data['title'],'utf-8')){
            $this->error('标题不能为空！');
        }
        if(mb_strlen($data['content'],'utf-8')<20){
            $this->error('内容不能少于20个字！');
        }

        $res=$this->articlesModel->editData($data);
        $title=$aId?"编辑":"新增";
        if($res){
            if(!$aId){
                $aId=$res;
                if($category['need_audit']){
                    $this->success($title.'文章成功！请等待审核~',U('Articles/Index/detail',array('id'=>$aId)));
                }
            }
            $this->success($title.'文章成功！',U('Articles/Index/detail',array('id'=>$aId)));
        }else{
            $this->error($title.'文章失败！'.$this->articlesModel->getError());
        }
    }

    private function _category($id=0)
    {
        $now_category=$this->articlesCategoryModel->getTree($id,'id,title,pid,sort');
        $this->assign('now_category',$now_category);
        return $now_category;
    }
    private function _needLogin()
    {   
        //调用通用用户授权方法
        if(!_need_login()){
            $this->error('请先登录！');
        }
    }
    //获取用户文章数的总阅读量
    private function _totalView($uid=0)
    {
        $total = S("article_total_view_uid_{$uid}");
        if(!$total){
            $res=$this->articlesModel->where(array('uid'=>$uid))->select();
            $total=0;
            foreach($res as $value){ 
                $total=$total+$value['view'];
            }
            unset($value);
            S("article_total_view_uid_{$uid}",$total,3600);
        }
        return $total;
    }
} 