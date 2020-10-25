<?php

define("ROLE_USER", "USER");
define("ROLE_ADMIN", "ADMIN");

define("INVILID_JSON_RES","json is invalid");

define("APP_TYPE_IMAGE","IMAGE");

define("PERMISSION_USER",1);//普通用户
define("PERMISSION_PRODUCT_MANAGER",2);//商品管理员
define("PERMISSION_ORDER_MANAGER",4);//订单管理员
define("PERMISSION_STORY_MANAGER",8);//故事管理员
define("PERMISSION_CATEGORY_MANAGER",16);//分类供应链
define("PERMISSION_MARKETING_MANAGER",32);//营销管理员
define("PERMISSION_USER_MANAGER",64);//用户管理员
define("PERMISSION_ADMIN",4095);//all PERMISSIONs 超级管理员

define("ORDER_STATE_UNPAID",1);//未支付
define("ORDER_STATE_UNSENT",2);//未发货
define("ORDER_STATE_SENDING",3);//未收货
define("ORDER_STATE_RECEIVED",4);//已收货

define("HISTORY_TYPE_ORDER","ORDER");

define('MAKE_COMMENT_IMAGES_COUNT',4);

define('SEO_LINK_PREFIX','/api/seo');

//ERROR_CODE
define('ERROR_JSON_INVILID',1);//JSON无法解析
define('ERROR_JSON_HALFBAKED',2);//JSON字段不完整
define('ERROR_MCODE_WRONG',3);//短信验证码错误
define('ERROR_NO_MCODE_PHONE_IN_SERVER',4);//没有获取过短信验证码
define('ERROR_ICODE_WRONG',5);//图片验证码错误
define('ERROR_NO_PERMISSION',6);//没有权限
define('ERROR_EXECUTE_FAIL',7);//执行失败
define('ERROR_NO_CURRENT_RECORD',8);//没有指定记录
define('ERROR_HAS_CURRENT_RECORD',9);//指定记录已存在
define('ERROR_NUMBER_INVILID',10);//无效的数字
define('ERROR_PRODUCT_NOT_ENOUGH',11);//商品库存不足
define('ERROR_MCODE_SEND_FAIL',12);//短信验证码发送失败
define("ERROR_PRODUCT_PRICE_CHANGED",13);//商品价格已变动
define("ERROR_USER_CAN_NOT_LOGIN",14);//用户登录失败
