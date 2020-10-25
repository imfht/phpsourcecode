<?php

return array(
	"PRIVILEGE"		=> array("item"=>"商品管理",
							 "promotion"=>"促销管理",
							 "user"=>"会员管理",
							 "trade"=>"订单管理",
							 "content"=>"内容管理",
							 "report"=>"统计报表",
							 "basicset"=>"商城设置",
							 "tools"=>"工具箱"),

	"TRADESTATUS"	=> array("wait_pay"=>"等待支付",
							 "wait_send"=>"待发货",
							 "wait_rece"=>"已发货",
							 "finish"=>"已完成",
							 "canceled"=>"已作废"),

	"AFTERSALE"		=> array("back"=>"退货","change"=>"换货","repair"=>"返修"),

	"PRINTOPT"		=> array("tradeid"			=>array("name"=>"订单编号",	 "map"=>"trade|tradeid"),
						"sendername"		=>array("name"=>"发件人姓名","map"=>"sender|linkman"),
						"senderlink"		=>array("name"=>"发件人手机","map"=>"sender|link"),
						"sendercompany"		=>array("name"=>"发件人公司","map"=>"sender|company"),
						"senderaddr"		=>array("name"=>"发件人地址","map"=>"sender|address"),
						"senderzipcode"		=>array("name"=>"发件人邮编","map"=>"sender|zipcode"),
						"receivername"		=>array("name"=>"收件人姓名","map"=>"trade|receiver_name"),
						"receivertel"		=>array("name"=>"收件人手机","map"=>"trade|receiver_link"),
						"receiveraddr"		=>array("name"=>"收件人地址","map"=>"trade|receiver_address"),
						"receiverzipcode"	=>array("name"=>"收件人邮编","map"=>"trade|receiver_zip")),

	"PAYMENT"		=> 	array("alipay"			=>"支付宝担保交易","cod"=>"货到付款","tenpay2"=>"财付通中介担保","tenpay"=>"财付通即时到帐"),
	"AUTODEL"		=> 	array("7"=>"一周前","15"=>"半月前","30"=>"一月前","90"=>"三月前"),
	"GROUP"			=>  array(array("t"=>"华东","c"=>array("310000"=>"上海","320000"=>"江苏省","330000"=>"浙江省","340000"=>"安徽省","360000"=>"江西省")),
							  array("t"=>"华北","c"=>array("110000"=>"北京","120000"=>"天津","140000"=>"山西省","370000"=>"山东省","130000"=>"河北省","150000"=>"内蒙古自治区")),
							  array("t"=>"华中","c"=>array("430000"=>"湖南省","420000"=>"湖北省","410000"=>"河南省")),
							  array("t"=>"华南","c"=>array("440000"=>"广东省","450000"=>"广西壮族自治区","350000"=>"福建省","460000"=>"海南省")),
							  array("t"=>"东北","c"=>array("210000"=>"辽宁省","230000"=>"黑龙江省","220000"=>"吉林省")),
							  array("t"=>"西北","c"=>array("610000"=>"陕西省","650000"=>"新疆维吾尔自治区","620000"=>"甘肃省","640000"=>"宁夏回族自治区","630000"=>"青海省")),
							  array("t"=>"西南","c"=>array("500000"=>"重庆","530000"=>"云南省","520000"=>"贵州省","540000"=>"西藏自治区","510000"=>"四川省")),
							  array("t"=>"港澳台","c"=>array("810000"=>"香港特别行政区","820000"=>"澳门特别行政区","710000"=>"台湾省")),
							  array("t"=>"海外","c"=>array("990000"=>"海外"))
	)
);