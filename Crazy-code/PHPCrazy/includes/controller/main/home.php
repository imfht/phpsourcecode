<?php

// 防止直接访问出错
if (!defined('IN_PHPCRAZY')) exit;

// 加载index展示语言包
require Lang('indexDemo');

// 定义网页标题
$PageTitle = $C['sitename'];

// 加载 index 模版
include T('index');