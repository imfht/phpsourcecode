<?php
include './library/inc.php';

if (get_col("detail","id", "id = $id") === false || get_col("detail","id", "id = $id") == '') {
  alert_href($_lang['404'], './');
}
setUrlBack();

//获取上级频道字段
$channel = $objChannel->getChannel($detail['d_parent']);
$channel_parent = $objChannel->getParent($channel['id']);

// 获取频道相关
$c_main = $channel['c_main'];
$channel_slist = channel_slist($channel['c_ifsub'] == 1 ? $channel['id'] : $channel['c_parent'], $channel['id']);
$current_channel_location = current_channel_location($channel['id'], $channel['id']);

//获取顶级频道字段
$channel_main = $objChannel->getMain($channel['id']);

// prev&next
$prev = $objDetail->getPrev($detail['id']);
$next = $objDetail->getNext($detail['id']);

$objDetail->addCount($detail['id']);// 浏览自增

// 默认评论插件调用
// hook('changyan');

include $t_path . $channel['c_dmodel'];

// 释放资源
unset($objDetail);
unset($objChannel);
unset($detail);
unset($detail_parent);
unset($channel);
unset($c_main);
unset($channel_slist);
unset($current_channel_location);
unset($detail_main);
unset($prev);
unset($next);
