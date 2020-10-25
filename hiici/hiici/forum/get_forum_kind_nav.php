<?php 

require_once('inc/forum_city.php');
$kinds = dt_query("SELECT id, name FROM forum_kind WHERE city = $forum_city ORDER BY today_up_c DESC");
if (!$kinds) die('e0');

?>
<?php while($kind = mysql_fetch_array($kinds)) { ?>
<li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo s_url('?c=forum&a=forum_list&m_falter=5&kind='.$kind['id']) ?>"><img src="img/finance/hengxin_logo_sm_1.png" height="20px"> <?php echo $kind['name'] ?></a></li>
<li role="presentation" class="divider"></li>
<?php } ?>
