<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;

use Think\Model;
use Home\Model\CommentModel;
use Admin\Model\CategoryModel;
/**
 * 文档模型控制器
 * 文档模型列表和详情
 */
class ArticleController extends HomeController {
    

	public function index(){
		/* 分类信息 */
		$category = $this->category();
		$this->categoryList();
		$categoryId                                     =   I('category');
		if($categoryId!=0){
            $CategoryM                                      =   new \Home\Model\CategoryModel();
            $ids                                            =   $CategoryM->getChildrenId($categoryId);
            if(!empty($ids)){
                $ids                                        =   $ids.','.$categoryId;
            }

            $where['category_id']                           =   array('in',$ids);
        }else{

        }

		$list                                           =   $this->lists('Document',$where);
		$this->assign('list',$list);

		$title = $category['meta_title'] ? $category['meta_title']:$category['title'];
		$this->setSiteTitle($title);
		$keywords = $category['keywords'];
		if(empty($keywords)){
		    hook('getKeyWords',$category['title']);
		}
		$this->setKeyWords($keywords);
		$this->setDescription($category['description']);

		$this->show($category['template_index']);
	}

	/* 文档模型列表页 */
	public function Articlelist($p = 1){
		/* 分类信息 */
		$category = $this->category();
		
		/* 获取当前分类列表 */
		$Document = D('Document');
		$list = $Document->page($p, $category['list_row'])->lists($category['id']);
		if(false === $list){
			$this->error('获取列表数据失败！');
		}
	
		/* 模板赋值并渲染模板 */
		$this->assign('category', $category);
		$this->assign('list', $list);
		$this->display($category['template_lists']);
	}

	/* 文档模型详情页 */
	public function detail($id = 0, $p = 1){
		/* 标识正确性检测 */
		if(!($id && is_numeric($id))){
			$this->error('文档ID错误！');
		}
			
		
		/* 页码检测 */
		$p = intval($p);
		$p = empty($p) ? 1 : $p;

		/* 获取详细信息 */
		$Document = D('Document');
		$info = $Document->detail($id);
		if(!$info){
			$this->error($Document->getError());
		}

		/* 分类信息 */
		$category = $this->category($info['category_id']);

		/* 获取模板 */
		if(!empty($info['template'])){//已定制模板
			$tmpl = $info['template'];
		} elseif (!empty($category['template_detail'])){ //分类已定制模板
			$tmpl = $category['template_detail'];
		} else { //使用默认模板
			$tmpl = 'Article/'. get_document_model($info['model_id'],'name') .'/detail';
		}
        $this->setSiteTitle($info['title']);
        hook('getKeyWords',$info['description']);
        $this->setKeyWords();
        $this->assign('site_description',$info['description']);
		/* 更新浏览数 */
		$map = array('id' => $id);
		$Document->where($map)->setInc('view');
		$this->assign('default_url',U('index',array('category'=>1)));
		/* 模板赋值并渲染模板 */
		$this->assign('category', $category);
		$this->assign('info', $info);
		$this->assign('page', $p); //页码
		$this->commentList($id);
		$this->display($tmpl);
	}

	/* 文档分类检测 */
	private function category($id = 0){
		/* 标识正确性检测 */
		$id = $id ? $id : I('get.category', 0);


		/* 获取分类信息 */
		$category = D('Category')->info($id);
		if($category && 1 == $category['status']){
			switch ($category['display']) {
				case 0:
					$this->error('该分类禁止显示！');
					break;
				//TODO: 更多分类显示状态判断
				default:
				    $where['pid'] = $id;
				    $category_tree = $this->listAll('Category',$where);
				    $this->assign('category_tree',$category_tree);
					$this->assign('id',$id);
					$this->assign('url',U('/category/'.$id));
					return $category;
			}
		}
	}
	
	public function tag($id){
	    if(!is_numeric($id) || $id <= 0){
	        $this->error('非法的标签');
	    }
	  
	    $ids = D('TagRelation')->getObjectIdsByTagId($id);
	    $where['status'] = 1;
	    $ids = implode(',', $ids);
	    $where['id'] = array('in',$ids);
	    $info = D('Tag')->info($id); 
	    $list = $this->lists(D('Document'),$where); 
	   
	    $this->assign('_list',$list);
	    $this->assign('tag',$id);
	    $this->setSiteTitle($info['tag_name'].'-'.$this->site_title);
	    $this->display();
	}
	public function ajaxArticleList(){
	    /* 分类信息 */
	    $category = $this->category();

	    //频道页只显示模板，默认不读取任何内容
	    //内容可以通过模板标签自行定制
	    
	    /* 模板赋值并渲染模板 */
	  
	    $this->assign('category', $category);
	    $result['p']=I('get.p')+1;
	    $result['content']=$this->fetch();
	    $result['errno']=0;
	    $this->ajaxReturn($result);
	}
	public function ajaxTag($id){    
	    $ids = D('TagRelation')->getObjectIdsByTagId($id);
	    $where['status'] = 1;
	    $ids = implode(',', $ids);
	    $where['id'] = array('in',$ids);
	    $info = D('Tag')->info($id);
	    $list = $this->lists(D('Document'),$where);
	   
	    $this->assign('_list',$list);
	    $this->assign('tag',$id);
	    $result['p']=I('get.p')+1;
	    $result['content']=$this->fetch();
	    $result['errno']=0;
	    $this->ajaxReturn($result);
	}
	public function favor(){
	    $id = I('get.id',0,'intval');
	    if(session('set_favor'.$id)){
	        if(IS_AJAX){
	            $where['id'] = $id;
	            $json['status'] = 2;
	            $json['info'] = "取消喜欢";
	            D('Document')->where('id='.$where['id'])->setInc('favor',-1);
	            session('set_favor'.$id,false);
	            $this->ajaxReturn($json);
	        }
	    }else {
	        if(IS_AJAX){
	            $where['id'] = $id;
	            $json['status'] = 1;
	            $json['info'] = "喜欢";
	            D('Document')->where('id='.$where['id'])->setInc('favor');
	            session('set_favor'.$id,true);
	            $this->ajaxReturn($json);
	        }
	    }	    
	}
    public function comment(){
        if(empty($this->my)){
            $this->error('请先登录');
        }
        if(IS_AJAX){
            $CommentModel = new CommentModel();
            if($CommentModel->addComment()){
                $id = I('object_id');
                $where['id'] = $id;
                D('Document')->where($where)->setInc('comment');
                $this->success('评论成功');
            }else {
                $this->error($CommentModel->getError());
            }
        }
    }
    public function commentList($id){
        $where['object_id'] = $id;
        $commentList = $this->lists('Comment',$where);
        foreach ($commentList as $k=>$v){
            $user = D('Member')->info($v['uid']);
            $commentList[$k]['avatar'] = $user['avatar'];
            $commentList[$k]['nickname'] = $user['nickname'];
        }
        if(IS_AJAX){
            $result['p']=I('get.p')+1;
            $result['content']=$this->fetch();
            $result['errno']=0;
            $this->ajaxReturn($result);
        }else{
           $this->assign('comment_list',$commentList);
        }
    }
    public function categoryList(){
        $categoryList                       =   session('category_list');

        if(!empty($categoryList)){
            $this->assign('category_list',$categoryList);
            return $categoryList;
        }
        $CategoryM                          =   new \Home\Model\CategoryModel();
        $list                               =   $CategoryM->getTree();
        if(!empty($list)){
            $this->assign('category_list',$list);
            session('category_list',$list);
            return $list;
        }
    }

}