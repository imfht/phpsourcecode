<?php
//频道链接地址
function c_url($t0, $t1='') {
  $t0 = intval($t0);

  if (!empty($t1)) {
    $ret = $t1;
  } else {
    $tmp = $GLOBALS['db']->getOne("SELECT c_link FROM channel WHERE id = $t0");
    if ($tmp) {
      $ret =  $tmp;
    } else {
      if (REWRITE) {
        $ret = "channel-{$t0}.html";
      } else {
        $ret = "channel.php?id={$t0}";
      }
    }
  }
  return $ret;
}

//详情链接地址
function d_url($t0, $t1='') {
  $t0 = intval($t0);

  if (!empty($t1)) {
    $ret =  $t1;
  }else{
    $tmp = $GLOBALS['db']->getOne("SELECT d_link FROM detail WHERE id = $t0");
    if ($tmp) {
      $ret =  $tmp;
    } else {
      if (REWRITE) {
        $ret =  "detail-{$t0}.html";
      } else {
        $ret =  "detail.php?id={$t0}";
      }
    }
  }
  return $ret;
}

//TAG链接地址
function tag_url($t0) {
  $t0 = str_safe($t0);
  return "search.php?tag={$t0}";
}

//无限级导航(宽屏)
function navigation($t0, $t1, $t2, $t3=2, $t4="nav am-hide-md-down", $t5="sub") {
  $tmp = '';
  $t0 = intval($t0);
  $t2 = !empty($t2) ? intval($t2) : 0;
  $t3 = intval($t3);
  $t4 = str_safe($t4);
  $t5 = str_safe($t5);

  $root = cms('nav_level_'.$t0)!==NULL ? cms('nav_level_'.$t0) : '';
  if ($root != '') {
    cms('nav_level_'.$t0, $t0);
    $root = $t0;
  }
  if ($t3>0) {
    $res = $GLOBALS['db']->getAll("SELECT * FROM channel WHERE c_navigation = 1 AND c_parent = " . $t0 . " ORDER BY c_order ASC , id ASC");
    $t3--;
    foreach ($res AS $row) {
      $nav_name = !empty($row['c_nname']) ? $row['c_nname'] : $row['c_name'];
      if ($t0==0) {
        $tmp .= '<li ' . ($row['c_main']==$t2 ? 'class="active"' : '') . '><a href="' . c_url($row['id'], $row['c_link']) . '" target="' . $row['c_target'] . '">' . $nav_name . '</a>' . navigation($row['id'], '', $t2, $t3) . '</li>';
      }else{
        $tmp .= '<li ' . ($row['id']==$t2 ? 'class="active"' : '') . '><a href="' . c_url($row['id'], $row['c_link']) . '" target="' . $row['c_target'] . '">' . $nav_name . '</a>' . navigation($row['id'], '', $t2, $t3) . '</li>';
      }
    }
  }
  if (!empty($tmp)) {
    return '<ul class="' . ($t0==$root ? $t4 : $t5). '">' . $t1 . $tmp . '</ul>';
  }
}

//无限级导航（移动端)
// $s[mainul|subul|liclass|liactive|lidata|aclass|adata|icondown]
// $s[0     |1    |2      |3       |4     |5     |6    |7]
function navigation_m($t0, $t1, $t2, $t3=2, $class='am-nav am-nav-pills am-topbar-nav|am-dropdown-content|am-dropdown| am-active|data-am-dropdown|am-dropdown-toggle|data-am-dropdown-toggle|am-icon-caret-down', $t4='') {
  $tmp = '';
  $t0 = intval($t0);
  $t2 = !empty($t2) ? $t2 : 0;
  $t3 = intval($t3);
  $class = str_safe($class);
  $t4 = str_safe($t4);

  $arr = explode('|', $class);
  if ($t3 > 0) {
    $res = $GLOBALS['db']->getAll("SELECT * FROM channel WHERE c_navigation = 1 AND c_parent = " . $t0 . " ORDER BY c_order ASC , id ASC");
    $t3--;
    foreach ($res AS $row) {
      $nav_name = !empty($row['c_nname']) ? $row['c_nname'] : $row['c_name'];
      if ($row['c_ifsub']==1 && $t3!=0) {
        $tmp .= '<li class="' . $arr[2] . ($row['c_main']==$t2 ? $arr[3] : '') . '" ' . $arr[4] . '><a class="' . $arr[5] . '" ' . $arr[6] . ' href="' . c_url($row['id'], $row['c_link']) . '">' . $nav_name .  '<span class="'.$arr[7].'"></span>' . '</a>' . navigation_m($row['id'], '', $t2, $t3, $class, '') . '</li>';
      } else {
        $tmp .= '<li class="' . ($row['id']==$t2 ? $arr[3] : '') . '"><a href="' . c_url($row['id'], $row['c_link']) . '">' . $nav_name . '</a>' . navigation_m($row['id'], '', $t2, $t3, $class, '') . '</li>';
      }
    }
    if (!empty($tmp)) {
      return '<ul class="' . ($t0==0 ? $arr[0] : $arr[1]). '">' . $t1 . $tmp . $t4 . '</ul>';
    }
  }
}

//一级导航
function navigation_s($t0, $t1='') {
  $t0 = intval($t0);

  $tmp = '';
  $res = $GLOBALS['db']->getAll("SELECT * FROM channel WHERE c_navigation = 1 AND c_parent = " . $t0 . " ORDER BY c_order ASC , id ASC");
  foreach ($res as $row) {
    $nav_name = !empty($row['c_nname']) ? $row['c_nname'] : $row['c_name'];
    $tmp .= '<li><a href="' . c_url($row['id'],$row['c_link']) . '" target="' . $row['c_target'] . '">' . $nav_name . '</a></li>';
  }
  if (!empty($tmp)) {
    return $t1 . $tmp;
  }

}

//频道列表
function channel_slist($t0, $t1, $t2=2) {
  $t0 = intval($t0);
  $t1 = intval($t1);
  $t2 = intval($t2);

  if ($t2>0) {
    $tmp = '';
    $res = $GLOBALS['db']->getAll("SELECT * FROM channel WHERE c_parent = '" . $t0 . "' AND c_navigation=1 ORDER BY c_order ASC , id ASC ");
    foreach ($res as $row) {
      $tmp .= '<li ' . ($row['id'] == $t1 ? ' class="active"' : '') . '><a href="' . c_url($row['id'], $row['c_link']) . '" target="' . $row['c_target'] . '">' . $row['c_name'] . '</a></li>';
    }
  }
  $t2--;
  if ($t2==2) {
    return $tmp;
  } else {
    return '<ul>' . $tmp . '</ul>';
  }
}

//频道和内容页的当前位置
function current_channel_location($t0, $t1, $t2='<li class="am-active"><a href="|</a></li>|<li><a href="|">|</a></li>|') {
  $t0 = intval($t0);
  $t1 = intval($t1);
  $t2 = str_safe($t2);

  $tmp = '';
  $arr = explode('|', $t2);
  $res = $GLOBALS['db']->getAll("SELECT * FROM channel WHERE id = " . $t0 );
  foreach ($res as $row) {
    if ($row['id'] == $t1) {
      $tmp = $arr[5] . $arr[0] . c_url($row['id'], $row['c_link']) . $arr[3] . $row['c_name'] . $arr[4];
    } else {
      $tmp = $arr[5] . $arr[2] . c_url($row['id'], $row['c_link']) . $arr[3] . $row['c_name'] . $arr[4];
    }
    if ($row['c_parent'] != 0) {
      $tmp = current_channel_location($row['c_parent'], $t1, $t2) . $tmp;
    }
  }
  return html_entity_decode($tmp, ENT_QUOTES, 'UTF-8');
}

//获取频道字段
function get_channel($t0, $t1="") {
  $t0 = intval($t0);

  if ($t1) {
    return $GLOBALS['db']->getOne("SELECT $t1 FROM channel WHERE id = " . $t0);
  } else {
    return $GLOBALS['db']->getRow("SELECT * FROM channel WHERE id = " . $t0);
  }
}

function setUrlBack() {
  setcookie('cms[url_back]', get_url(), 0);
}

// 调试
function dump($var, $echo=true, $label=null, $strict=true) {
  $label = ($label === null) ? '' : rtrim($label) . ' ';
  if (!$strict) {
    if (ini_get('html_errors')) {
      $output = print_r($var, true);
      $output = "<pre>" . $label . htmlspecialchars($output, ENT_QUOTES) . "</pre>";
    } else {
      $output = $label . print_r($var, true);
    }
  } else {
    @ob_start();
    var_dump($var);
    $output = ob_get_clean();
    if (!extension_loaded('xdebug')) {
      $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
      $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
    }
  }
  if ($echo) {
    echo $output;
    return null;
  } else {
    return $output;
  }
}
