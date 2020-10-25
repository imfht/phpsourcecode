<?php
namespace Home\Model;
use Think\Model\ViewModel;
class UserViewModel extends ViewModel {
	public $viewFields = array(
		'page'=>array('id','title','content','type','date'),
		'meta'=>array('meta_key'=>'m_key','meta_value'=>'m_value','type'=>'m_type','_on'=>'page.id=meta.page_id')
	);
}