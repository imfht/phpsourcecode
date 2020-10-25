<?php
$c_main = 'search';
include './library/inc.php';
include LIB_PATH . 'cls.page.php';

if (!empty($_POST['keyword']) || !empty($_SESSION['keyword'])) {
  if (!empty($_POST['keyword'])) {
    $_SESSION['keyword'] = $keyword = str_safe($_POST['keyword']);
  } else {
    $keyword = $_SESSION['keyword'];
  }

  include 'tpl.php';
} else {
  alert_back($_lang['keyword']);
}
