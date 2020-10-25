<?php 
	class SalesViewModel extends ViewModel {
	   public $viewFields = array(
		 'Business'=>array('product_amount','sales_price','customer_id','product_id','name'=>'business_name','create_time','_type'=>'LEFT'),
		 'Customer'=>array('name'=>'customer_name', '_on'=>'Business.customer_id=Customer.customer_id'),
		 'Product'=>array('name'=>'product_name','cost_price', '_on'=>'Business.product_id=Product.product_id'),
	   );
	}