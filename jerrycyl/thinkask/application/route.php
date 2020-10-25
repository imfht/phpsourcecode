<?php
/*
+--------------------------------------------------------------------------
|   thinkask [#开源系统#]
|   ========================================
|   http://www.thinkask.cn
|   ========================================
|   如果有兴趣可以加群{开发交流群} 485114585
|   ========================================
|   更改插件记得先备份，先备份，先备份，先备份
|   ========================================
+---------------------------------------------------------------------------
 */
// \think\Route::domain('www.thinkask.net','jofficial/index/index');
// \think\Route::setDomain('www.thinkask.net');
// \think\Route::domain('www', function(){
    // 动态注册域名的路由规则
    // \think\Route::rule('new/:id', 'index/news/read');
    // \think\Route::rule(':user', 'index/user/info');
// });
return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    //  '__alias__' =>  [
    //    'article'  =>  ['article/index/index',['ext'=>'html']],
    // ],
    
    // 添加路由规则 路由到 index控制器的hello操作方法
    // 'hello/:name' => 'index/index/hello',
   	// 'article/ajax/*' => 'article/ajax/*',
    'admin/index/index/[:topmenuname]' => ['admin/index/index'],


    'question/admin/lists' => ['question/admin/lists'],
    'question/post/edit' => ['question/post/edit'],
    'question/post/editanswer' => ['question/post/editanswer'],
    'question/detail/[:encry_id]' => ['question/index/detail'],
    'question/[:id]' => ['question/index/index/',['method'=>'get']],

    'article/admin/lists' => ['article/admin/lists'],
    'article/post/edit' => ['article/post/edit'],
    'article/[:id]' => ['article/index/index',['method'=>'get','id'=>'\d+']],

    'plus/[:plusname]/[:controller]/[:action]' => ['index/plus/index'],

    // 'people/[:encode_id]' => ['people/people/index/',['method'=>'get','encode_id'=>'\d+']],
    // 'people' => ['people/People/people_list/',['method'=>'get']],



];
