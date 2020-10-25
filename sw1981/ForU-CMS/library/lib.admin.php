<?php
// 订单状态 0未付款 1已付款 2等待收货 3确认收货 5撤销 6退款中 7退款完成 8交易完成 9已撤销
function setOrderState($id, $n) {
  $id = intval($id);
  return $GLOBALS['db']->query("UPDATE order SET o_state = $n WHERE id = $id");
}

// 回复评论
function answerRate($arr, $id) {
  $id = intval($id);
  // f_id订单号;f_star评星字串;f_content留言内容;f_answer留言回复;f_date留言期;f_adate回复日期;f_ok是否回复
  return $GLOBALS['db']->autoExecute('feedback', $arr, 'UPDATE', "id = $id");
}

function checkAdminPriv($priv=null) {
  if (!empty($priv)) {
    $res = $GLOBALS['db']->getOne("SELECT r.r_priv FROM user AS u INNER JOIN role AS r ON r.id=u.u_rid WHERE u.id = " . getUserToken('id'));
    $cids = 0;
    if ($res=='all') {
      $priv_obj = 'all';
    } else {
      $priv_obj = explode(',', $res);
      if (preg_replace('(,)', '', preg_replace('([a-z]+,?)', '', $res))) {
        $cids = rtrim(preg_replace('([a-z]+,?)', '', $res), ',');
      }
    }
    if ($priv_obj != 'all' && in_array($priv, $priv_obj) === false) {
      alert_back( $GLOBALS['lang']['priv']['failed']);
    }
    return $cids;
  }
}

function getChannelPriv($pid, $rid, $priv) {
  $pid = intval($pid);
  $rid = intval($rid);
  $str = '';

  $res = $GLOBALS['db']->getAll("SELECT * FROM channel WHERE c_parent=$pid ORDER BY c_order ASC, id ASC");
  foreach ($res as $val) {
    if ($val['c_ifsub'] || $val['c_parent']==0) {
      if ($val['c_ifsub']==0 && $val['c_parent']==0) {
        $str .= '<table class="am-table am-table-bordered common_table"><tr><th><label for="c' . $val['id'] . '"><input type="checkbox" name="c' . $val['id'] . '" id="c' . $val['id'] . '" value="c' . $val['id'] . '" ' . (in_array("c" . $val['id'],$priv) ? 'checked="checked"' : '') . '>' . $val['c_name'] . '</label></th><td class="gray">无子频道</td></tr></table>';
      } else {
        $str .= '<table class="am-table am-table-bordered common_table"><tr><th><label for="c' . $val['id'] . '"><input type="checkbox" name="c' . $val['id'] . '" id="c' . $val['id'] . '" value="c' . $val['id'] . '" ' . (in_array("c" . $val['id'],$priv) ? 'checked="checked"' : '') . '>' . $val['c_name'] . '</label></th><td>' . getChannelPriv($val['id'],$rid,$priv) . '</td></tr></table>';
      }
    } else {
      $str .= '<label for="c' . $val['id'] . '"><input type="checkbox" name="c' . $val['id'] . '" id="c' . $val['id'] . '" value="c' . $val['id'] . '" ' . (in_array("c" . $val['id'],$priv) ? 'checked="checked"' : '') . '>' . $val['c_name'] . '</label>';
    }
  }
  return $str;
}

//获取频道下拉列表
function channel_select_list($t0, $t1, $t2, $t3) {
  $tmp = '';
  $s = '';
  $t1 = intval($t1);
  $t2 = intval($t2);
  $t3 = intval($t3);
  $level = $t1;

  for ($i = 0; $i < $level; $i++) {
    $s = $s . '├ ';
  }
  $level = $level + 1;
  if (strpos($t0, ',')) {
    $sql = "SELECT * FROM channel WHERE id IN ($t0) AND id <> $t3";
  } else {
    $sql = "SELECT * FROM channel WHERE c_parent IN ($t0) AND id <> $t3";
  }
  $res = $GLOBALS['db']->getAll($sql);
  if (is_array($res)) {
    foreach ($res as $row) {
      $select = $row['id'] == $t2 ? 'selected="selected"' : '';
      $tmp .= '<option value="' . $row['id'] . '" ' . $select . '>' . $s . $row['c_name'] . '</option>' . (strpos($t0, ',') ? '' : channel_select_list($row['id'], $level, $t2, $t3));
    }
  }
  return $tmp;
}

//获取所有频道的ID
function get_channel_sub($t0, $t1) {
  $tmp = '';
  $s = ',';
  $t1 = intval($t1);

  $sql = "SELECT * FROM channel WHERE c_parent = $t0 ORDER BY c_order ASC , id ASC";
  $res = $GLOBALS['db']->getAll($sql);
  if (!empty($res)) {
    foreach ($res as $row) {
      $tmp .= $s . $row['id'] . ($row['c_ifsub'] ? get_channel_sub($row['id'], '') : '');
    }
  }
  return $t1 . $tmp;
}

//获取指定频道的最上级频道
function get_channel_main($parent) {
  $parent = intval($parent);

  $sql = "SELECT * FROM channel WHERE id =" . $parent;
  $res = $GLOBALS['db']->getRow($sql);
  if ($res['c_parent'] == 0) {
    return $res['id'];
  } else {
    return get_channel_main($res['c_parent']);
  }
}

//获取指定频道是否有子频道
function get_channel_ifsub($id) {
  $id = intval($id);
  $res = $GLOBALS['db']->getOne("SELECT id FROM channel WHERE c_parent = $id");
  if ($res) {
    return 1;
  } else {
    return 0;
  }
}

//更新所有频道
function update_channel() {
  $sql = "SELECT * FROM channel ORDER BY id ASC";
  $res = $GLOBALS['db']->getAll($sql);
  foreach ($res as $row) {
    $sql2 = "UPDATE channel SET c_sub='" . get_channel_sub($row['id'], $row['id']) . "',c_ifsub='" . get_channel_ifsub($row['id']) . "',c_main='" . get_channel_main($row['id']) . "' WHERE id = " . $row['id'];
    $GLOBALS['db']->query($sql2);
  }
}

//频道管理列表
function channel_list($t0, $t1) {
  $t0 = intval($t0);
  $t1 = intval($t1);
  $tmp = '';
  $level = $t1;
  $s = '';
  for ($i = 0; $i < $level; $i++) {
    $s = $s . '&nbsp;-&nbsp;';
  }
  $res = $GLOBALS['db']->getAll("SELECT * FROM channel WHERE c_parent = $t0 ORDER BY c_order ASC, id ASC");
  $level = $level + 1;
  if (!empty($res)) {
    foreach ($res as $row) {
      $tmp .= '<tr><td>' . $row['id'] . '</td><td>' . $row['c_order'] . '</td><td>' . $s . '<a href="../' . c_url($row['id']) . '" target="_blank">' . $row['c_name'] . '</a></td><td>' . get_channel_model_name($row['c_cmodel']) . '</td><td>' . get_detail_model_name($row['c_dmodel']) . '</td><td>' . ($row['c_link'] ? '<span class="am-badge am-badge-default">链接</span> ':'') . ($row['c_ifcover'] ? '<span class="am-badge am-badge-default">封面</span> ':'') . ($row['c_ifslideshow'] ? '<span class="am-badge am-badge-default">组图</span> ':'') . ($row['c_safe'] ? '<span class="am-badge am-badge-default">保护</span> ':'') . '</td><td><a href="cms_channel_edit.php?id=' . $row['id'] . '" class="am-btn am-btn-default am-btn-xs"><span class="am-icon-pencil"></span></a>&nbsp;<a href="cms_channel.php?del=' . $row['id'] . '" onclick="return confirm(\'确定要删除吗？\')" class="am-btn am-btn-default am-btn-xs"><span class="am-icon-times"></span></a></td></tr>' . channel_list($row['id'], $level);
    }
  }
  return $tmp;
}

//获取频道模型名称
function get_channel_model_name($t0) {
  $t0 = str_safe($t0);

  $res = $GLOBALS['db']->getRow("SELECT * FROM cmodel WHERE c_value='$t0'");
  if (!!($row = $res)) {
    return $row['c_name'];
  } else {
    return '自定义';
  }
}

//获取详情模型名称
function get_detail_model_name($t0) {
  $t0 = str_safe($t0);

  $res = $GLOBALS['db']->getRow("SELECT * FROM dmodel WHERE d_value='$t0'");
  if (!!($row = $res)) {
    return $row['d_name'];
  } else {
    return '自定义';
  }
}

function channel_model_select_list($t0 = 0) {
  $t0 = intval($t0);

  $tmp = '';
  @($res = $GLOBALS['db']->getAll("SELECT * FROM cmodel"));
  foreach ($res as $row) {
    $SELECT = $row['c_value'] == $t0 && !empty($t0) ? 'selected="selected"' : '';
    $tmp .= '<option value="' . $row['c_value'] . '" ' . $SELECT . '>' . $row['c_name'] . '</option>';
  }
  return $tmp;
}

function detail_model_select_list($t0 = 0) {
  $t0 = intval($t0);

  $tmp = '';
  $res = $GLOBALS['db']->getAll("SELECT * FROM dmodel");
  foreach ($res as $row) {
    $SELECT = $row['d_value'] == $t0 && !empty($t0) ? 'selected="selected"' : '';
    $tmp .= '<option value="' . $row['d_value'] . '" ' . $SELECT . '>' . $row['d_name'] . '</option>';
  }
  return $tmp;
}

//获得查询时间和次数
function assign_query_info() {
  if ($GLOBALS['db']->queryTime == '') {
    $query_time = 0;
  } else {
    if (PHP_VERSION >= '5.0.0') {
      $query_time = number_format(microtime(true) - $GLOBALS['db']->queryTime, 6);
    } else {
      list($now_usec, $now_sec) = explode(' ', microtime());
      list($start_usec, $start_sec) = explode(' ', $GLOBALS['db']->queryTime);
      $query_time = number_format($now_sec - $start_sec + ($now_usec - $start_usec), 6);
    }
  }
  // $query_info = '共处理 ' . $GLOBALS['db']->queryCount . ' 条查询, 用时 ' . $query_time . ' 秒';
  $query_info = '用时 ' . $query_time . ' 秒';
  //内存占用情况
  if (function_exists('memory_get_usage')) {
    $memory_info = '内存占用 ' . memory_get_usage() / 1048576 . ' m';
  }
  //是否启用了 gzip
  $gzip = gzip_enabled() ? '支持' : '不支持';
  return $query_info . '&nbsp;&nbsp;' . $memory_info . '&nbsp;&nbsp;gzip:' . $gzip;
}

function gzip_enabled() {
  static $enabled_gzip = NULL;
  if ($enabled_gzip === NULL) {
    $enabled_gzip = function_exists('ob_gzhandler');
  }
  return $enabled_gzip;
}

//后台操作日志
function admin_log($code) {
  $log['admin_id'] = getUserToken('id');
  $log['admin_name'] = getUserToken();
  $log['log_code'] = $code;
  $log['log_time'] = date('Y-m-d H:i:s', time());
  $log['log_ip'] = get_ip();
  if (ADMIN_LOG) {
    return $GLOBALS['db']->autoExecute("admin_log", $log);
  }
}

// 自动清理超期的数据
function clear_expire($tbl, $col, $limit, $where, $id = 'id') {
  $ids = '';
  $res = $GLOBALS['db']->getAll("SELECT {$id},{$col},{$limit} FROM {$tbl} WHERE {$where}");
  foreach ($res as $val) {
    if (gmtime() > ($val[$col] + $val[$limit])) {
      $ids .= $val[$id] . ',';
    }
  }
  if (!empty($ids)) {
    $idstr = rtrim($ids, ',');
    $sql = "DELETE FROM {$tbl} WHERE {$id} IN ({$idstr})";
    return $GLOBALS['db']->query($sql);
  } else {
    return false;
  }
}

// 获取中文首字母
function get_first_letter($str) {
  $fchar = ord($str[0]);
  if ($fchar >= ord('A') && $fchar <= ord('z')) {
    return strtoupper($str[0]);
  }
  $s1 = iconv('UTF-8', 'gb2312', $str);
  $s2 = iconv('gb2312', 'UTF-8', $s1);
  if ($s2 == $str) {
    $s = $s1;
  } else {
    $s = $str;
  }
  $asc = ord($s[0]) * 256 + ord($s[1]) - 65536;
  if ($asc >= -20319 && $asc <= -20284) {$albet = 'A';}
  if ($asc >= -20283 && $asc <= -19776) {$albet = 'B';}
  if ($asc >= -19775 && $asc <= -19219) {$albet = 'C';}
  if ($asc >= -19218 && $asc <= -18711) {$albet = 'D';}
  if ($asc >= -18710 && $asc <= -18527) {$albet = 'E';}
  if ($asc >= -18526 && $asc <= -18240) {$albet = 'F';}
  if ($asc >= -18239 && $asc <= -17923) {$albet = 'G';}
  if ($asc >= -17922 && $asc <= -17418) {$albet = 'I';}
  if ($asc >= -17417 && $asc <= -16475) {$albet = 'J';}
  if ($asc >= -16474 && $asc <= -16213) {$albet = 'K';}
  if ($asc >= -16212 && $asc <= -15641) {$albet = 'L';}
  if ($asc >= -15640 && $asc <= -15166) {$albet = 'M';}
  if ($asc >= -15165 && $asc <= -14923) {$albet = 'N';}
  if ($asc >= -14922 && $asc <= -14915) {$albet = 'O';}
  if ($asc >= -14914 && $asc <= -14631) {$albet = 'P';}
  if ($asc >= -14630 && $asc <= -14150) {$albet = 'Q';}
  if ($asc >= -14149 && $asc <= -14091) {$albet = 'R';}
  if ($asc >= -14090 && $asc <= -13319) {$albet = 'S';}
  if ($asc >= -13318 && $asc <= -12839) {$albet = 'T';}
  if ($asc >= -12838 && $asc <= -12557) {$albet = 'W';}
  if ($asc >= -12556 && $asc <= -11848) {$albet = 'X';}
  if ($asc >= -11847 && $asc <= -11056) {$albet = 'Y';}
  if ($asc >= -11055 && $asc <= -10247) {$albet = 'Z';}
  return !empty($albet) ? $albet : null;
}
