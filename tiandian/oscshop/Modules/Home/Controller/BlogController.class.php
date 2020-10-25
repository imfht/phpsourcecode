<?php
/**
 * oscshop 电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 */
namespace Home\Controller;

class BlogController extends CommonController {
	
	public function verify(){
        $verify = new \Think\Verify();
        $verify->entry(1);
    }
	
	public function check_verify(){
		$verify=I('verify');
		if(!check_verify($verify)){
            die('false');
        }else{
        	die('true');
        }
	}
	
	public function show_reply(){
		
		$id=get_url_id('id');
		$this->reply=M('blog_reply')->where(array('blog_id'=>$id,'status'=>1))->select();
		
		$this->display('reply');
		
	}
	
	
	//显示博客内容
	public function show_blog_content(){		
		$id=get_url_id('id');
		
		$sql="select b.title,b.image,b.meta_keywords,b.meta_description,b.allow_reply,bc.content from "
		.C('DB_PREFIX')."blog b,".C('DB_PREFIX')."blog_content bc where "
		." b.blog_id=bc.blog_id and b.blog_id=".$id;
		
		$content=M()->query($sql);
		
		$image=M('blog_image')->where(array('blog_id'=>$id))->field('image,title')->select();
		
		$this->content=$content[0];		
		$this->title=$content[0]['title'].'-';				
		$this->meta_keywords=$content[0]['meta_keywords'];
		$this->meta_description=$content[0]['meta_description'];			
		$this->images=$image;
		
		$this->reply=M('blog_reply')->where(array('blog_id'=>$id,'status'=>1))->select();
		
		//点击量		
		$r['blog_id']=	$id;
		$r['read_num']		=	array('exp','read_num+1');
		M('blog')->save($r);
		
		cookie('_current_url_',__SELF__);	
		
		$this->display('content');
	}	
	
	//显示所有博客
	public function index(){
		
		$count=M('blog')->where(array('status'=>1))->count();
		
		$Page = new \Think\Page($count,C('FRONT_PAGE_NUM'));
		
		$show  = $Page->show();// 分页显示输出	
		$sql="select bc.summary,b.blog_id,b.title,b.image,b.author,b.create_time,b.reply from ".C('DB_PREFIX')."blog b,".C('DB_PREFIX')
		.'blog_content bc where b.blog_id=bc.blog_id and b.status=1 order by b.blog_id desc LIMIT '.$Page->firstRow.','.$Page->listRows;	
		
		$list=M()->query($sql);
		
		$hashids = new \Lib\Hashids(C('PWD_KEY'), C('URL_ID'));
		
		foreach ($list as $k => $v) {
				$list[$k]['blog_id']=$hashids->encode($v['blog_id']);
				$list[$k]['image']=resize($v['image'], C('blog_list_thumb_width'), C('blog_list_thumb_height'));
		}
		
		$this->title=C('BLOG_TITLE').' ';
		
		$this->meta_keywords=C('BLOG_TITLE').','.C('SITE_KEYWORDS');
	    $this->meta_description=C('BLOG_TITLE').','.C('SITE_DESCRIPTION');

		$show=str_replace("/blog/index/p/","/blogs/p/", $show);
		
		$this->assign('empty','没有数据');// 赋值数据集
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出	
		
		$this->display();
			
		
			
	}
	//按分类显示博客	
	public function category(){
		
		$cid=get_url_id('cid');;
		
		$sql="select bc.summary,b.blog_id,b.title,b.image,b.author,b.create_time,b.reply from ".C('DB_PREFIX')."blog b,".C('DB_PREFIX')
		."blog_content bc where b.blog_id=bc.blog_id and b.status=1 and b.category_id=".$cid;
	
		$count=count(M()->query($sql));
		
		$Page = new \Think\Page($count,C('FRONT_PAGE_NUM'));
		
		$show  = $Page->show();// 分页显示输出	
		
		$sql.=' order by b.blog_id desc LIMIT '.$Page->firstRow.','.$Page->listRows;	
		
		$list=M()->query($sql);
		
		$hashids = new \Lib\Hashids(C('PWD_KEY'), C('URL_ID'));
		
		foreach ($list as $k => $v) {
				$list[$k]['blog_id']=$hashids->encode($v['blog_id']);				
				$list[$k]['image']=resize($v['image'], C('blog_list_thumb_width'), C('blog_list_thumb_height'));
		}
		
		$category=M('blog_category')->field('title')->find($cid);
		
		$this->title=$category['title'].' ';
		
		$this->meta_keywords=$category['title'].','.C('SITE_KEYWORDS');
	    $this->meta_description=$category['title'].','.C('SITE_DESCRIPTION');		
	
		$show=str_replace("/blog/category/cid/","/blogc/", $show);
			
		$this->assign('empty','~~没有数据~~');// 赋值数据集
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出	
		
		$this->display('index');
			
		
			
	}		
		
	
}