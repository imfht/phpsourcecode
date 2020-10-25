<?php 
	class EventViewModel extends ViewModel{
		public $viewFields = array(
			'event'=>array('event_id', 'subject', 'start_date','end_date' ,'venue', 'send_email', 'recurring', 'description', '_type'=>'LEFT'),
			'user'=>array('user_id'=>'owner_id','name'=>'owner_name', '_on'=>'user.user_id=event.owner_id','_type'=>'LEFT'),
			'business'=>array('business_id','name'=>'business_name', '_on'=>'business.business_id=event.business_id','_type'=>'LEFT'),
			'customer'=>array('customer_id','name'=>'customer_name', '_on'=>'customer.customer_id=event.customer_id','_type'=>'LEFT'),
			'contacts'=>array('contacts_id','name'=>'contacts_name', '_on'=>'contacts.contacts_id=event.contacts_id','_type'=>'LEFT'),
			'leads'=>array('leads_id','last_name','first_name','_on'=>'leads.leads_id=event.leads_id','_type'=>'LEFT')
		);
	}