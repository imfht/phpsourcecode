<?php
namespace Admin\Controller;
use Common\Api\CategoryApi;

class ArticleController extends CommonController {
	
	public function _initialize(){
		$cate=new CategoryApi();
		$catelist=$cate->get_catelist(0,1);
		$this->assign('clist',$catelist);
		
		parent::_initialize();
	}
   function before_selectedDelete($ids){
		
		$condition = array ('id' => array ('in', explode ( ',', $ids ) ) );
		$uidarr=M('Article')->where ( $condition )->getField('uid',true);

		foreach ($uidarr as $key =>$vo){
			setuserscore($vo, C('ARTSCORE'),false);
			
		}
		
					
		
	      
		
	}
function before_foreverdelete($ids){
		
		$condition = array ('id' => array ('in', explode ( ',', $ids ) ) );
		$uidarr=M('Article')->where ( $condition )->getField('uid',true);

		foreach ($uidarr as $key =>$vo){
			setuserscore($vo, C('ARTSCORE'),false);
			
		}
		
					
		
	      
		
	}
	

	
function after_pass($ids){
		
		$condition = array ('id' => array ('in', explode ( ',', $ids ) ) );
		$uidarr=M('Article')->where ( $condition )->getField('uid',true);

		foreach ($uidarr as $key =>$vo){
			setuserscore($vo, C('ARTSCORE'));
			
		}
		
					
		
	      
		
	}

	
}

?>