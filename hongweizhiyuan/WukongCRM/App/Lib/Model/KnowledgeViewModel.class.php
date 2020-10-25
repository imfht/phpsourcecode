<?php 
	class KnowledgeViewModel extends ViewModel{
		public $viewFields = array(
			'knowledge'=>array('knowledge_id','category_id','role_id','title','content','create_time','update_time','hits' ,'_type'=>'LEFT'),
			'knowledge_category'=>array('name'=>'name', '_on'=>'knowledge.category_id=knowledge_category.category_id'),
		);

	}