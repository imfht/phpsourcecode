<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

/*Route::get('CLTPHP', function () {
    return 'hello,CLTPHP!';
});*/
return [
    '/'=>'home/index/index',
    'about/:catId'=>'home/about/index',
    'download/:catId'=>'home/download/index',
    'services/:catId'=>'home/services/index',
    'servicesInfo/:catId/[:id]'=>'home/services/info',
    'system/:catId'=>'home/system/index',
    'news/:catId'=>'home/news/index',
    'info/:catId/[:id]'=>'home/news/info',
    'team/:catId'=>'home/team/index',
    'contact/:catId'=>'home/contact/index',
    'senmsg'=>'home/index/senmsg',
    'down/:id'=>'home/index/down',
    'tags/:keyword'=>'home/tags/index',

];
