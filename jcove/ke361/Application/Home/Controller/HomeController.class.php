<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;
use Think\Action;
use Think\Controller;
use Think\Page;
use Think\Upload;;
use Home\Common\MyPage;
use Home\Model\CategoryGoodsModel;

class HomeController extends Controller {

    private $nextPage;

    public $my;
    public $site_title;
    public $pre;

    public function _empty(){
        header("HTTP/1.0 404 Not Found");
        $this->display('Public/404');
    }
   
    public function _initialize() {
        /* 读取站点配置 */
        $config = api('Config/lists');
        C($config); //添加配置
        
        if(!C('WEB_SITE_CLOSE')){
            $this->error('站点已经关闭，请稍后访问~');
        }
                
        C('DEFAULT_THEME',C('FRONT_THEME'));
       
        if(is_mobile()){
            C('DEFAULT_THEME','mobile');
        }
        //是否是用户分享
        $u = I('u',0);
        if($u > 0){
            session('share_user',$u);
        }
        define(UID, is_login());
        if (UID) {    
            $this->my = D('Member')->info(UID);
            $this->assign('my', $this->my);  
        }
        $this->site_title = C('WEB_SITE_TITLE').' Powered by Ke361';
        /*标签*/
        $this->assign('tags',$this->listAll(D('Tag')));
     
        $this->pre = C('DB_PREFIX');
     
      
        $this->recommendArticle();
        $Cate = new CategoryGoodsModel();
        $where['status'] = 1;
        $where['pid'] = 0;
        $this->assign('category_goods_tree',$this->listAll($Cate,$where,' sort asc '));
        $this->assign('topic_tree',$this->listAll(D('Topic')));
        $this->assign('config',$config);
        $this->assign('site_title',$this->site_title);
        $this->dynamicAssign();
        $this->assign('url',$this->getCurrentUrl());
           
    }
    public function isLogin() {
        if (!UID) {      
             $this->redirect('User/login');    
        }
    }
 
    public function iniPage($count, $listRows = 20) {
        import('ORG.Util.Page');
        return new Page($count, $listRows);
    }

    public function saveFile($path, $size = 3145728, $exts = array('jpg', 'gif', 'png', 'jpeg')) {
        import('ORG.Net.UploadFile');
        $upload = new Upload(); // 实例化上传类
        $upload->maxSize = $size; // 设置附件上传大小
        $upload->thumb = true;
        $upload->thumbMaxWidth = "236";
        $upload->thumbMaxHeight = "150";
        $upload->allowExts = $exts; // 设置附件上传类型
        $upload->rootPath = "./Uploads/avatar_img/"; // 设置头像上传目录
        $upload->exts     = array('jpg', 'gif', 'png', 'jpeg');
        $upload->savePaht = $path.'/';
        $info   = $upload->upload($_FILES);
        
        if($info){ //文件上传成功，记录文件信息
            
        }else {
            return array('state' => -1, 'msg' => $upload->getError());
        }
       
        return array('state' => 1, 'img' => $info);
    }

    public function myCheckToken($data = '') {
        $data == '' && $data = $_POST;
        if (!M()->autoCheckToken($_POST))
            $this->error('error_token');
    }
    /**
     * 获取控制器菜单数组,二级菜单元素位于一级菜单的'_child'元素中
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    final public function getMenus($controller=CONTROLLER_NAME){
        // $menus  =   session('ADMIN_MENU_LIST'.$controller);
        if(empty($menus)){
            // 获取主菜单
            $where['pid']   =   0;
            $where['hide']  =   0;
            if(!C('DEVELOP_MODE')){ // 是否开发者模式
                $where['is_dev']    =   0;
            }
            $menus['main']  =   M('Menu')->where($where)->order('sort asc')->select();
    
            $menus['child'] = array(); //设置子节点
    
            //高亮主菜单
            $current = M('Menu')->where("url like '%{$controller}/".ACTION_NAME."%'")->field('id')->find();
            if($current){
                $nav = D('Menu')->getPath($current['id']);
                $nav_first_title = $nav[0]['title'];
    
                foreach ($menus['main'] as $key => $item) {
                    if (!is_array($item) || empty($item['title']) || empty($item['url']) ) {
                        $this->error('控制器基类$menus属性元素配置有误');
                    }
                    if( stripos($item['url'],MODULE_NAME)!==0 ){
                        $item['url'] = MODULE_NAME.'/'.$item['url'];
                    }
                    // 判断主菜单权限
                    if ( !IS_ROOT && !$this->checkRule($item['url'],AuthRuleModel::RULE_MAIN,null) ) {
                        unset($menus['main'][$key]);
                        continue;//继续循环
                    }
    
                    // 获取当前主菜单的子菜单项
                    if($item['title'] == $nav_first_title){
                        $menus['main'][$key]['class']='current';
                        //生成child树
                        $groups = M('Menu')->where("pid = {$item['id']}")->distinct(true)->field("`group`")->select();
                        if($groups){
                            $groups = array_column($groups, 'group');
                        }else{
                            $groups =   array();
                        }
    
                        //获取二级分类的合法url
                        $where          =   array();
                        $where['pid']   =   $item['id'];
                        $where['hide']  =   0;
                        if(!C('DEVELOP_MODE')){ // 是否开发者模式
                            $where['is_dev']    =   0;
                        }
                        $second_urls = M('Menu')->where($where)->getField('id,url');
    
                        if(!IS_ROOT){
                            // 检测菜单权限
                            $to_check_urls = array();
                            foreach ($second_urls as $key=>$to_check_url) {
                                if( stripos($to_check_url,MODULE_NAME)!==0 ){
                                    $rule = MODULE_NAME.'/'.$to_check_url;
                                }else{
                                    $rule = $to_check_url;
                                }
                                if($this->checkRule($rule, AuthRuleModel::RULE_URL,null))
                                    $to_check_urls[] = $to_check_url;
                            }
                        }
                        // 按照分组生成子菜单树
                        foreach ($groups as $g) {
                            $map = array('group'=>$g);
                            if(isset($to_check_urls)){
                                if(empty($to_check_urls)){
                                    // 没有任何权限
                                    continue;
                                }else{
                                    $map['url'] = array('in', $to_check_urls);
                                }
                            }
                            $map['pid'] =   $item['id'];
                            $map['hide']    =   0;
                            if(!C('DEVELOP_MODE')){ // 是否开发者模式
                                $map['is_dev']  =   0;
                            }
                            $menuList = M('Menu')->where($map)->field('id,pid,title,url,tip')->order('sort asc')->select();
                            $menus['child'][$g] = list_to_tree($menuList, 'id', 'pid', 'operater', $item['id']);
                        }
                        if($menus['child'] === array()){
                            //$this->error('主菜单下缺少子菜单，请去系统=》后台菜单管理里添加');
                        }
                    }
                }
            }
            // session('ADMIN_MENU_LIST'.$controller,$menus);
        }
        return $menus;
    }
    /**
     * 通用分页列表数据集获取方法
     *
     *  可以通过url参数传递where条件,例如:  index.html?name=asdfasdfasdfddds
     *  可以通过url空值排序字段和方式,例如: index.html?_field=id&_order=asc
     *  可以通过url参数r指定每页数据条数,例如: index.html?r=5
     *
     * @param sting|Model  $model   模型名或模型实例
     * @param array        $where   where查询条件(优先级: $where>$_REQUEST>模型设定)
     * @param array|string $order   排序条件,传入null时使用sql默认排序或模型属性(优先级最高);
     *                              请求参数中如果指定了_order和_field则据此排序(优先级第二);
     *                              否则使用$order参数(如果$order参数,且模型也没有设定过order,则取主键降序);
     *
     * @param array        $base    基本的查询条件
     * @param boolean      $field   单表模型用不到该参数,要用在多表join时为field()方法指定参数
     * @author 朱亚杰 <xcoolcc@gmail.com>
     *
     * @return array|false
     * 返回数据集
     */
    protected function lists ($model,$where=array(),$order='',$base = array('status'=>array('egt',0)),$field=true){
        $options    =   array();
        $REQUEST    =   (array)I('request.');
        if(is_string($model)){
            $model  =   M($model);
        }
    
        $OPT        =   new \ReflectionProperty($model,'options');
        $OPT->setAccessible(true);
    
        $pk         =   $model->getPk();
        if($order===null){
            //order置空
        }else if ( isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']),array('desc','asc')) ) {
            $options['order'] = '`'.$REQUEST['_field'].'` '.$REQUEST['_order'];
        }elseif( $order==='' && empty($options['order']) && !empty($pk) ){
            $options['order'] = $pk.' desc';
        }elseif($order){
            $options['order'] = $order;
        }
        unset($REQUEST['_order'],$REQUEST['_field']);
    
        $options['where'] = array_filter(array_merge( (array)$base, /*$REQUEST,*/ (array)$where ),function($val){
            if($val===''||$val===null){
                return false;
            }else{
                return true;
            }
        });
        if( empty($options['where'])){
            unset($options['where']);
        }
        $options      =   array_merge( (array)$OPT->getValue($model), $options );
        $total        =   $model->where($options['where'])->count();
    
        if( isset($REQUEST['r']) ){
            $listRows = (int)$REQUEST['r'];
        }else{
            $listRows = 12;
            
        }
       
        $page = new MyPage($total, $listRows, $REQUEST);
      
        $page->setConfig('header', '');
        $page->setConfig('prev', '上一页');
        $page->setConfig('next', '下一页');
        $page->setConfig('first', '1...');
        $page->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
        if($total>$listRows){
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        $p =$page->show();
        $this->assign('_page', $p? $p: '');
        $this->assign('_total',$total);
        $this->nextPage                                      =   $page->nextPage();
      
        $this->assign("total_page",$page->totalPages);
        
        $options['limit'] = $page->firstRow.','.$page->listRows;
    
        $model->setProperty('options',$options);
    
        $res =$model->field($field)->select();    
        return $res;
    }
    public function listAll($model,$where=array(),$order='',$base = array('status'=>array('egt',0)),$field=true){
        $options    =   array();
        $REQUEST    =   (array)I('request.');
        if(is_string($model)){
            $model  =   M($model);
        }
        
        $OPT        =   new \ReflectionProperty($model,'options');
        $OPT->setAccessible(true);
        
        $pk         =   $model->getPk();
        if($order===null){
            //order置空
        }else if ( isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']),array('desc','asc')) ) {
            $options['order'] = '`'.$REQUEST['_field'].'` '.$REQUEST['_order'];
        }elseif( $order==='' && empty($options['order']) && !empty($pk) ){
            $options['order'] = $pk.' desc';
        }elseif($order){
            $options['order'] = $order;
        }
        unset($REQUEST['_order'],$REQUEST['_field']);
        
        $options['where'] = array_filter(array_merge( (array)$base, /*$REQUEST,*/ (array)$where ),function($val){
            if($val===''||$val===null){
                return false;
            }else{
                return true;
            }
        });
        if( empty($options['where'])){
            unset($options['where']);
        }
        $options      =   array_merge( (array)$OPT->getValue($model), $options );
        $model->setProperty('options',$options);
        
        $res =$model->field($field)->select();
        return $res;
    }
    /**
     * 推荐文章
     */
    public function recommendArticle(){
        $Document = D('Document');
        $where = array(
            'status'=>1,
            'position'=>array('gt',0)
        );
        
        $list=$Document->where($where)->limit(9)->select();
      
        $recommendArticle = array(
            'index' =>array(),
            'list'  =>array(),
            'channel'=>array()
        );
        foreach ($list as $k=>$v){
            if(in_array($v['position'],array(1,3,5,7))){
                $recommendArticle['list'][]=$v;
            }
            if(in_array($v['position'],array(2,3,6,7))){
                $recommendArticle['detail'][]=$v;
            }
            if(in_array($v['position'],array(4,5,6,7))){
                $recommendArticle['index'][]=$v;
            }
        }
       
        $this->assign('recommend_article',$recommendArticle);
    }
    public function setSiteTitle($title){
        $this->site_title = $title.'-'.$this->site_title;
        $this->assign('site_title',$this->site_title);
    }
    public function setKeyWords($keyword){
        if(empty($keyword)){
            $this->assign('site_keywords',session('get_keywords'));
            session('get_keywords',null);
        }else {
            $this->assign('site_keywords',$keyword);
        }
        
    }
    public function setDescription($description){
        $this->assign('site_description',$description);
    }
    /*最新文章*/
    private function news($p = 1){
    
        /* 获取当前分类列表 */
        $Document = D('Document');
        $where = array('status'=>1);
        $list= $this->lists($Document,$where,'create_time DESC');
    
        $this->assign('news',$list);
    }

    public function indexCate() {
        $indexCate = (C('INDEX_GOODS_CATE'));
        $cate = array();
        if(is_array($indexCate)){
            foreach ($indexCate as $k =>$v){
                $c['cate_name'] = $v;
                $c['cate_id'] = $k;
                $in = D('CategoryGoods')->getAllChildrenId($k);
                $indexContent = C('INDEX_CONTENT');
                if($indexContent =='goods'){
                    $_REQUEST['r'] = C('INDEX_GOODS_COUNT');//首页分类下的显示的商品数量
                }
                
                $c['goods_list']= $this->lists('Goods',array('cate_id'=>array('in',$in)));
                foreach ($c['goods_list'] as $k=>$v){
                   
                    $c['goods_list'][$k]['url'] = U('goods/info',array('id'=>$v['id']));
                }
                $cate[] = $c;
            }
        }
     
        $this->assign('cate',$cate);
    }
    public function dynamicAssign(){
        switch (CONTROLLER_NAME){
            case 'Article':
                //$this->categroy();
                break;
            case 'Index':
                $this->indexCate();
                break;
        }
    }
    public function show(){
        $this->assign('next_page',$this->nextPage);
        if(IS_AJAX){
            $result                             =   array('error'=>0,'content'=>'');
            $list                               =   $this->get('list');

            if(!empty($list)){
                $result['next_url']             =   $this->nextPage;
                $result['content']              =   $this->fetch('ajax_'.lcfirst(ACTION_NAME));
            }else{
                $result['error']                =   1;
            }
            $this->ajaxReturn($result);
        }else{
             $this->display();
        }

    }
    public function getCurrentUrl(){
        return (($_SERVER["REQUEST_URI"]!='/') ? $_SERVER['REQUEST_URI'] : '');
    }
    
}
