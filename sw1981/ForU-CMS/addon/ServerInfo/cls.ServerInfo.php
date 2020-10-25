<?php
include_once LIB_PATH . 'cls.addon.php';

class ServerInfo extends Addon{

  public function adminIndex() {
    echo '<div class="am-margin am-text-center">
    <span class="am-badge am-badge-secondary">运行环境：' . $_SERVER['SERVER_SOFTWARE'] . '</span>
    <span class="am-badge am-badge-secondary">PHP版本：' . phpversion() . '</span>
    <span class="am-badge am-badge-secondary">MYSQL版本：' . $GLOBALS['db']->getOne("SELECT VERSION()") . '</span>
    <span class="am-badge am-badge-secondary">上传限制：' . ini_get('upload_max_filesize') . '</span>
    </div>';
  }
}
