<?php

return array(
	'kd_tips1'=>array(
		'title'=>'快递代码是用于物流查询，可点击<a href="https://www.kuaidi100.com/download/api_kuaidi100_com(20140729).doc" target="_blank" style="font-weight:bold;">这里</a>查看，到“商城->基础设置->快递管理”中进行设置',
		'type'=>'hidden',
		'value'=>''
	),
	'kuaidiCode'=>array(
		'title'=>'快递100快递代码&nbsp;&nbsp;&nbsp;&nbsp;<span ><a target="_blank" href="https://www.kuaidi100.com/download/api_kuaidi100_com(20140729).doc" style="color:blue">查看快递代码</a></span>',
		'type'=>'hidden',
		'value'=>'',
		'tips'=>''
	),
	'kuaidiDes'=>array(
		'title'=>'<span ><a target="_blank" href="https://www.kuaidi100.com/openapi/applyapi.shtml" style="color:blue">在线申请密匙(Key)【企业版】</a></span><div>因快递100免费版对部分快递公司查询接口进行了限制，导致数据查询不出来，<span style="color:red;font-size:15px;">建议使用企业版</span>！</div>',
		'type'=>'hidden',
		'value'=>'',
		'tips'=>''
	),
	'kuaidiType'=>array(
		'title'=>"快递查询接口类型",
		'type'=>'radio',
		'options'=>array(
			'1'=>'企业版',	
			'2'=>'免费版'
		),
		'value'=>'1',
	),
	'kuaidiKey'=>array(
		'title'=>'快递100授权密匙(Key)',
		'type'=>'text',
		'value'=>'',
		'tips'=>''
	),
	'kuaidiCustomer'=>array(
		'title'=>'快递100实时查询(Customer)<span style="color:red">企业版必填</span>',
		'type'=>'text',
		'value'=>'',
		'tips'=>''
	)
);
