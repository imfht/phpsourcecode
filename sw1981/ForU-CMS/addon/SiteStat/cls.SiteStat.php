<?php
include_once LIB_PATH . 'cls.addon.php';

class SiteStat extends Addon{

  public function adminIndex() {
    $chn = $GLOBALS['db']->getOne("SELECT COUNT(id) FROM channel");
    $dtl = $GLOBALS['db']->getOne("SELECT COUNT(id) FROM detail");
    $sld = $GLOBALS['db']->getOne("SELECT COUNT(id) FROM slideshow");
    $chip = $GLOBALS['db']->getOne("SELECT COUNT(id) FROM chip");
    $usr = $GLOBALS['db']->getOne("SELECT COUNT(id) FROM user WHERE u_isadmin=0");
    $fdb = $GLOBALS['db']->getOne("SELECT COUNT(id) FROM feedback");
    $mail = $GLOBALS['db']->getOne("SELECT COUNT(id) FROM subscribe");
    $link = $GLOBALS['db']->getOne("SELECT COUNT(id) FROM link");
    $tpl = $GLOBALS['db']->getOne("SELECT COUNT(id) FROM template");
    $addon = $GLOBALS['db']->getOne("SELECT COUNT(id) FROM addon");
    $adm = $GLOBALS['db']->getOne("SELECT COUNT(id) FROM user WHERE u_isadmin=1");
    $priv = $GLOBALS['db']->getOne("SELECT COUNT(id) FROM role");

    echo '<ul class="am-avg-sm-1 am-avg-md-4 am-margin am-padding am-text-center admin-content-list"><li><a href="cms_channel.php"><span class="am-icon-btn am-icon-th-large"></span><br>站点频道: ' . $chn . '</a></li><li><a href="cms_detail.php"><span class="am-icon-btn am-icon-file-text"></span><br>频道详情: ' . $dtl . '</a></li><li><a href="cms_slideshow.php"><span class="am-icon-btn am-icon-archive"></span><br>幻灯片: ' . $sld . '</a></li><li><a href="cms_chip.php"><span class="am-icon-btn am-icon-file-code-o"></span><br>代码碎片: ' . $chip . '</a></li></ul>';
    echo '<ul class="am-avg-sm-1 am-avg-md-4 am-margin am-padding am-text-center admin-content-list "><li><a href="cms_member.php"><span class="am-icon-btn am-icon-users"></span><br>会员: ' . $usr . '</a></li><li><a href="cms_feedback.php"><span class="am-icon-btn am-icon-comment"></span><br>在线留言: ' . $fdb . '</a></li><li><a href="cms_link.php"><span class="am-icon-btn am-icon-link"></span><br>友情连接: ' . $link . '</a></li><li><a href="cms_mail.php"><span class="am-icon-btn am-icon-envelope"></span><br>邮件订阅: ' . $mail . '</a></li></ul>';
    echo '<ul class="am-avg-sm-1 am-avg-md-4 am-margin am-padding am-text-center admin-content-list "><li><a href="cms_admin.php"><span class="am-icon-btn am-icon-user"></span><br>管理员: ' . $adm . '</a></li><li><a href="cms_role.php"><span class="am-icon-btn am-icon-graduation-cap"></span><br>权限管理: ' . $priv . '</a></li><li><a href="cms_addon.php"><span class="am-icon-btn am-icon-plug"></span><br>插件: ' . $addon . '</a></li><li><a href="cms_template.php"><span class="am-icon-btn am-icon-laptop"></span><br>站点模版: ' . $tpl . '</a></li></ul>';
  }
}
