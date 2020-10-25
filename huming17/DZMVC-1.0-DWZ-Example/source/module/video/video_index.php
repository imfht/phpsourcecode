<?php

//DEBUG 请求获取一页列表数据例子
$page = max(1, intval($_GET['page']));
$perpage = $limit = 6;
$start=(($page-1) * $perpage);
$cate_id = isset($_GET['id']) ? $_GET['id'] : '';


include template('video/index');
?>