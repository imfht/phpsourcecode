<?php
namespace Admin\Model;

class CategoryModel extends \Common\Model\CategoryModel{

    protected $_auto = array (
        array('setting','serialize',3,'function'),
    );

//同步添加/删除附属附件
// $data : Array ( [thumb] => [add_time] => 2015-07-10 10:56:59 [cid] => 25 [title] => sdasd [keywords] => asd [description] => asdasd [content] => asdasdasd [id] => 561 )
// $options : Array ( [table] => sy_article [model] => Article )
	protected function _after_insert($data,$options){
		//联动增加单页
		if($data['mid']==2 && $data['is_menu']==0){
			$page=$this->create();
			$page['cid']=$data['id'];
			$page['title']=$data['title'];
			$page['add_time']=$data['add_time'];
			D('Page')->add($page);
		}

		//联动增加路由
		$route['url']=D('Common/Urlmap')->categoryUrl($data['mid'],$data['name']);
		$route['route']=$data['name'];
		D('Common/Urlmap')->add($route);

		//清理Category缓存
		$this->delCache();
	}

//同步添加/删除附属附件
//$data : Array ( [thumb] => [add_time] => 2015-07-10 10:56:59 [cid] => 25 [title] => sdasd [keywords] => asd [description] => asdasd [content] => asdasdasd )
//$options : Array ( [table] => sy_article [model] => Article [where] => Array ( [id] => 558 ) )
	protected function _before_update(&$data,$options){
		//联动修改单页标题
		if($data['mid']==2){
			M('Page')->where("cid={$data['id']}")->setField('title',$data['title']);
		}

		//联动修改路由
		$name=M('Category')->where("id={$data['id']}")->getField('name');
		$route['url']=D('Common/Urlmap')->categoryUrl($data['mid'],$data['name']);
		$route['route']=$data['name'];
		D('Common/Urlmap')->where("route='{$name}'")->save($route);

		//清理Category缓存
		$this->delCache();
	}

//同步删除附属附件
//array  'where' =>     array      'id' => int 106  'table' => string 'sy_category' (length=11)  'model' => string 'Category' (length=8)
	protected function _before_delete($options) {
		$id=$options['where']['id'];
		$cate=M('Category')->where("id={$id}")->field('id,mid,name,is_menu')->find();
		//单页联动删除
		if($cate['mid']=='2') M('Page')->where("cid={$cate['id']}")->delete();

		//路由联动删除
		D('Common/Urlmap')->where("route='{$cate['name']}'")->delete();

		//清理Category缓存
		$this->delCache();
	}




}
