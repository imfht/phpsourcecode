<?php 
	class TasksViewModel extends ViewModel{
		public $viewFields = array(
			'tasks'=>array('tasks_id', 'subject', 'due_date' ,'status', 'priority', 'send_email', 'recurring', 'description', '_type'=>'LEFT'),
		);

	} 