<?php
include './library/inc.php';

if (get_channel($id, 'id') === false || get_channel($id, 'id') == '') {
  alert_href($_lang['illegal'], './');
}
setUrlBack();

$c_main = $channel['c_main'];
// 获取相关信息
$channel_slist = channel_slist($channel['c_ifsub'] == 1 ? $channel['id'] : $channel['c_parent'], $channel['id']);
$current_channel_location = current_channel_location($channel['id'], $channel['id']);

// 获取上级信息
$channel_parent = $objChannel->getParent($channel['id']);
$channel_main = $objChannel->getMain($channel['id']);

// 获取子集列表
$channel_sub = $objChannel->getSon($channel['id']);

// 分页&列表
if (strpos($channel['c_cmodel'], '_list')) {
  include LIB_PATH . 'cls.page.php';
  $pager = new Page($channel['c_page']);
  $pager->handle($db->getOne("SELECT COUNT(id) FROM detail WHERE d_parent IN (" . $channel['c_sub'] . ")"));
  $list_pager = $db->getAll("SELECT * FROM detail WHERE d_parent IN (" . $channel['c_sub'] . ") ORDER BY d_order ASC,id DESC LIMIT " . $pager->page_start . "," . $pager->page_size);
}

include $t_path . $channel['c_cmodel'];

// 释放资源
unset($objChannel);
unset($channel);
unset($c_main);
unset($channel_slist);
unset($current_channel_location);
unset($channel_parent);
