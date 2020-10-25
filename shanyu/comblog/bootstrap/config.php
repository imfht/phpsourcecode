<?php
$debug = strtolower(getenv('APP_DEBUG')) == 'true';
return [
    'app_debug'=>$debug,
    'app_name'=>getenv('APP_NAME'),

    'view_theme'=>'blog',
    'view_replace'=>[
        '__ASSETS__'=>'/assets/blog',
    ],

    'encrypt'=>getenv('APP_KEY'),

];