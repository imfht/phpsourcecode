<?php

return array(
     
'URL_ROUTER_ON'   => true, 
'URL_ROUTE_RULES'=>array( 
'tagart/:id\d/[:pageNum\d]'=>'Index/tagart', //规则路由
'alltag/[:pageNum\d]'=>'Index/alltag', //规则路由
'index/[:pageNum\d]'=>'Index/index', //规则路由
'zanart/[:pageNum\d]'=>'Index/zanart', //规则路由
'hotart/[:pageNum\d]'=>'Index/hotart', //规则路由
'gzart/[:pageNum\d]'=>'Index/gzart', //规则路由
'artrss'=>'Rss/article', //规则路由
'user/:uid\d'=>'Ucenter/index', //规则路由
'yzmail'=>'Ucenter/yzmail', //规则路由
'useravatarset'=>'Ucenter/useravatarset', //规则路由
'changepwd'=>'Ucenter/changepwd', //规则路由
'userart/[:uid|getdefaultid]/:pageNum\d'=>'Ucenter/userart', //规则路由
'usersc/[:uid|getdefaultid]/:pageNum\d'=>'Ucenter/usersc', //规则路由
'userfocus/[:pageNum\d]'=>'Ucenter/userfocus', //规则路由
'usertagfocus/[:pageNum\d]'=>'Ucenter/usertagfocus', //规则路由
'usersendmail/:uid\d'=>'Ucenter/usersendmail', //规则路由
'usermail/[:pageNum\d]'=>'Ucenter/usermail', //规则路由
'artlist/[:cid|getdefaultid]/[:pageNum\d]'=>'Index/artlist', //规则路由
'artc/:id\d/[:pageNum\d]'=>'Index/artc', //规则路由
)
);
