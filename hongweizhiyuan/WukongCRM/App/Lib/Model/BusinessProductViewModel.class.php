<?php 
	class BusinessProductViewModel extends ViewModel{
		public $viewFields = array(
			'r_business_product'=>array('business_id','product_id','sales_price','amount','description','_type'=>'LEFT'),
			'business'=>array('name'=>'name', '_on'=>'r_business_product.business_id=business.business_id','_type'=>'LEFT'),
			'product'=>array('name'=>'name','cost_price','_on'=>'r_business_product.product_id=product.product_id'),
		);
	}