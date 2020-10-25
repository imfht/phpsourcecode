<?php

return [
	//自动表单 前台列表页母模板
	'automodel_listpage'=>APP_PATH.'cms/view/index/default/content/list.htm',
	//自动表单 前台详情展示页母模板
    'automodel_showpage'=>APP_PATH.'cms/view/index/default/content/show.htm',
	
	//自动表单 前台辅栏目列表页母模板
	'automodel_category_listpage'=>APP_PATH.'common/builder/listpage/category_list.htm',
	
	//发布信息选择模型页模板
    'post_choose_model'=>APP_PATH.'common/builder/sort/model_list.htm',
	//发布信息选择栏目页模板
    'post_choose_sort'=>APP_PATH.'common/builder/sort/layout.htm',
	'use_category'=>true,
    //发布内容必须要选择栏目
    'post_need_sort'=>true,
    //模块关键字，目录名，也是数据表区分符    
     'system_dirname'=>basename(__DIR__),
    
    //圈子接口,内容列表与发布页的链接地址
    'qun_url'=>[
        ['index','mid=1&id','资讯'],
        ['index','mid=2&id','图库'],
        ['index','mid=3&id','视频'],
        ['index','mid=4&id','音频'],
    ],
    'post_url'=>[
        ['add','mid=1&id','发布资讯'],
        ['add','mid=2&id','上传图片'],
        ['add','mid=3&id','上传视频'],
        ['add','mid=4&id','上传音频'],
    ],
];