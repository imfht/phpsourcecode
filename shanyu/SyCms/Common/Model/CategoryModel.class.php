<?php
namespace Common\Model;
use Think\Model;

class CategoryModel extends Model {

    public function getCache(){
    	$category=F('Category');
    	if(!$category){
	    	$category=$this
	    		->where('status=1')
	    		->order('sort asc')
	    		->getField('id,pid,title,name,mid,is_menu',true);
	    	if(!$category) return false;

	    	$model=M('Model')->getField('id,model_table');
			foreach ($category as &$v) {
				$_url=D('Common/Urlmap')->getRouteRule();
				$v['url']=UU($_url[$v['name']]);

				if($v['mid']){
					$v['table']=$model[$v['mid']];
				}else{
					$v['table']=$v['name'];
				}
				unset($v['mid']);
			}

			F('Category',$category);
    	}
		return $category;
    }

    public function delCache(){
    	F('Category',NULL);
    }



}
