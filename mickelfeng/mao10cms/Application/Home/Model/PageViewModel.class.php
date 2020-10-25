<?php
namespace Home\Model;
use Think\Model\ViewModel;
class PageViewModel extends ViewModel {
	public $viewFields = array(
		'page'=>array('id','title','content','type','date'),
		'meta'=>array('meta_key'=>'m_key','meta_value'=>'m_value','type'=>'m_type','_on'=>'page.id=meta.page_id'),
		'action'=>array('action_key'=>'a_key','action_value'=>'a_value','date'=>'a_date','_on'=>'page.id=action.page_id'),
	);
}