<?php

include "template/Wetpl.php";

echo Wetpl::render('html/demo.html',[
    'title' => 'Wetpl Demo',
    'conf' => [
        'name' => '小卓',
        'age' => 15,
        'sex' => '男',
        'hobby' => ['编程','游戏'],
        'about' => '人生苦短，我用python'
    ]
    ]);