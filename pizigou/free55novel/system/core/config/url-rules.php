<?php
return array(
    '/' => 'site/index',
    'chapter/<id:\d+>' => 'article/view',
    'book/<id:\d+>' => 'book/view',
    'category/<title:\w+>' => 'category/index',
    'news/list-<id:\d+>' => 'news/index',
    'news/<id:\d+>' => 'news/view',
    'search/<keywords:\w+>' => 'book/search',
    'login' => 'site/login',
    'logout' => 'site/logout',
    'register' => 'site/register',
    'm/<_c:\w+>/<_a:\w+>' => 'stub/<_a>',
//    '<_c:book>/<_a:\w+>' => 'stub/<_a>',
);