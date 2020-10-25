<?php
namespace Home\Controller;
use Home\Model\GoodsModel;
use Home\Model\CategoryGoodsModel;
use TopSDK\Api\TopApi;
use TopSDK\Api\WirelessShareTpwdCreateRequest;

class GoodsController extends HomeController{
    public function index(){     
        $where['status'] = 1;
        $GoodsModel = new GoodsModel();
        $sort = I('sort','create_time');
        $type= I('type','desc');
         
        $goods = $this->lists($GoodsModel,$where,array($sort=>$type));
        $type = ($type=='desc') ? 'asc':'desc';
        $this->assign('list',$goods);
        $this->assign('sort',$sort);
        $this->assign('type',$type);
        $this->setSiteTitle('商品');
        $this->show();
    }
    public function cate($id=0){
        $where['id'] = $id;
        $CategoryModel = new CategoryGoodsModel();
      
        $cate = $CategoryModel->where($where)->find();
       
        if(!$cate) $this->error ('栏目不存在');
        
        unset($where);
        $cateId= I('get.id','','intval');
        $where['status'] = 1;
        $in = D('CategoryGoods')->getAllChildrenId($cateId);
        $where['cate_id'] = array('in',$in);
        $GoodsModel = new GoodsModel();
        $sort = I('sort','create_time');
        $type= I('type','desc');
        
        //商品列表 
        $goods = $this->lists($GoodsModel,$where,array($sort=>$type));
        $type = ($type=='desc') ? 'asc':'desc';
        foreach ($goods as $k=>$v){  
            $goods[$k]['url'] = U('/goods/'.$v['id']);
        }
        $this->assign('list',$goods);
        
        //推荐商品
        $recommendGoods = $GoodsModel->recommendGoods();
        $this->assign('recommend_goods',$recommendGoods);
        
        //子分类
        unset($where);
       
        if($cate['pid'] == 0){
            $where['id'] = array('in',$in);
            $childrenTree = $this->listAll('CategoryGoods',$where,' pid asc');
            $topCategory = $cate;
        }else {
            $parent = $CategoryModel->info($cate['pid']);
            while ($parent['pid']!=0){
                $parent = $CategoryModel->info($parent['pid']);                                            
            }
            unset($where);
            $in = $CategoryModel->getAllChildrenId($parent['id']);
            $where['id'] = array('in',$in);
            $where['status'] = 1;
            $childrenTree = $this->listAll('CategoryGoods',$where,' pid asc');
            $topCategory = $parent;
        }
        $this->assign('url',U('cate',array('id'=>$id)));
        $this->assign('children_tree',$childrenTree);
        $this->assign('top_category',$topCategory);
        $this->assign('cate',$cate);
        $this->assign('sort',$sort);
        $this->assign('type',$type);      
        $title = $cate['meta_title'] ? $cate['meta_title']:$cate['category_name'];
        $this->setSiteTitle($title);
        $keywords = $cate['keywords'];
        if(empty($keywords)){
            hook('getKeyWords',$cate['category_name']);
        }
        $this->setKeyWords($keywords);
        $this->setDescription($cate['description']);
        $this->show();
    }
    
    public function info(){
        $id = I('get.id','','intval');
        $GoodsM                                         =   new GoodsModel();
        
        $goods                                          =   $GoodsM->info($id);
        if($goods){
            $GoodsM->where('id='.$id)->setInc('hits');
            $hot_goods = D('goods')->hotGoods($goods['cate_id']);
            if(empty($goods['click_url'])){
                $goods['click_url']                     =   U('Goods/goBuy',array('id'=>$goods['num_iid']));
            }
            //淘口令
            if(empty($goods['tpwd'])){
                $top                                        =   new TopApi(C('APP_KEY'), C('APP_SECRET'));
                $pwd                                        =   $top->getTpwd($goods['click_url'],$goods['name'],$goods['pic_url']);
                $GoodsM->where('id='.$id)->save(array('tpwd'=>$pwd));
                $goods['tpwd']                              =   $pwd;
            }
            $this->assign('hot_goods',$hot_goods);
            $this->assign('goods',$goods);
            if(!empty($goods['seo_title'])){
                $this->setSiteTitle($goods['seo_title']);
            }else {
                $this->setSiteTitle($goods['name']);
            }
            
            $this->assign('site_description',$goods['seo_description']);
            if(empty($goods['seo_keywords'])){
                if(empty($goods['seo_title'])){
                    $goods['seo_title'] = $goods['name'];
                }
                hook('getKeyWords',$goods['seo_title']);
                $keywords = session('get_keywords');
                session('get_keywords',null);
                $goods['seo_keywords'] = $keywords;
                $where['id'] = $goods['id'];
            
                $keywords = addslashes($keywords);

                $GoodsM->where($where)->save(array('seo_keywords'=>$keywords));
               
            }
            $this->setKeyWords($goods['seo_keywords']);
           
            $this->display();   
        }else{
            $this->error('商品不存在');
        }
    }
    public function buttonInfo(){
        $id = I('post.id','','intval');
        $article = I('post.article',0);
        $result = array(
            'errno' => 0,
            'content' => '',
            'message' => ''
        );
        if($id > 0){            
            $info = D('Goods')->info($id);
     
            if($info){
               
                $goods['src'] = get_image_url($info['pic_url']);
                $goods['price'] = $info['discount_price'] > 0  ? $info['discount_price'] : $info['price'];
                $goods['url']   = U('Goods/info?id='.$info['id']);
                $goods['name'] = $info['name'];
                $goods['description'] = $info['seo_description'];
                $result['content'] = $goods;
                $data['object_id'] = $article;
                $data['goods_id']  = $id;
                if(D('GoodsRelation')->field('id')->where($data)->find()){
                    
                }else {
                    D('GoodsRelation')->add($data);
                }
            }else {
                $result['errno'] = 2;
                $result['content'] = '未找到该商品';
            }
           
        }else {
            $result['errno'] = 1;
            $result['content'] = '数据错误';
            
        }
        $this->ajaxReturn($result);
    }
    public function ajGetGoodsDetial(){
       
            $url = I('url');
           
            $data = file_get_contents($url);
            if(empty($data)){
                $data = get_remote_contents_with_ssl($url);
            }
            
            if(empty($data)){
                $data = get_remote_contents($url);
            }
            
            $data = mb_convert_encoding($data, 'utf-8', 'GBK,UTF-8,ASCII');
            preg_match('/dsc\.taobaocdn\.com[\w\.\%\/]*[\']/i', $data,$descurl);
            if(empty($descurl)){
                preg_match('/dsc\.taobaocdn\.com[\w\.\%\/]*[\"]/i', $data,$descurl);
            }
       
            $descurl = "http://".trim($descurl[0],'"');
            if(is_mobile()){
                $num_iid = I('num_iid');
                $descurl = "http://hws.m.taobao.com/cache/mtop.wdetail.getItemFullDesc/4.1/?data={item_num_id:$num_iid}";
                $data = file_get_contents($descurl);
                $data = mb_convert_encoding($data, 'utf-8', 'GBK,UTF-8,ASCII');
               
                preg_match('/desc\"\:\"(.*)\"\}/U', $data,$a);
                $content= stripcslashes($a['1']);
            }else {
                $content = file_get_contents($descurl);
            }
            
            if($content){
                $content = mb_convert_encoding($content, 'utf-8', 'GBK,UTF-8,ASCII');
                $content = trim($content,"var desc='");
                $content= stripcslashes($content);
                $where['item_url'] = htmlspecialchars_decode($url);
                $save['item_body'] = $content;
                $save['item_body'] = substr($save['item_body'],0,-3);
               // M('goods')->where($where)->save($save);
                $result['status']= 1;
                $result['content'] = $content;
                $this->ajaxReturn($result);
            }else{
               
                $this->ajaxReturn("商品详情获取失败，请刷新重试");
            }
            
        
    }
 
    public function tag($id){
        if(!is_numeric($id) || $id <= 0){
            $this->error('非法的标签');
        }  
        $where['status'] = 1;
        $where['tag']    = $id;
        $info = D('Tag')->info($id);
        $list = $this->lists(D('Goods'),$where);
    
        $this->assign('goods',$list);
        $this->setSiteTitle($info['tag_name']);
        $this->display();
    }
    public function ajaxGoodsInfo($id){
        $GoodsModel = new GoodsModel();
        $goods = $GoodsModel->info($id);
        $result = array(
            'errno' =>0,
            'content' =>'',
            'message' => ''    
        );
        if(!empty($goods)){
           $goods['url'] = U('Goods/info',array('id'=>$goods['id']));
           $result['content'] = $goods;
        }else {
            $result['errno'] = 1;
            $result['message'] = '无效的商品';
        }
        $this->ajaxReturn($result);
    }
    public function goBuy(){
        $id = get_tk_goods_id(I('id'));
        $this->assign('id',$id);
        $this->display();
    }
    public function getTpwd(){
        $url                                        =   I('url');
        $title                                      =   I('title');
        $top                                        =   new TopApi(C('APP_KEY'), C('APP_SECRET'));
        $pwd                                        =   $top->getTpwd($url,$title);
        $result = array(
            'errno' =>0,
            'content' =>'',
            'message' => ''
        );
        if($pwd){
            $result['content']                      =   $pwd;
        }else{
            $result['errno']                        =   2;
            $result['message']                      =   $top->error();
        }
    }
}