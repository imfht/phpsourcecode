<?php 

if (empty($_GET)) die;

$item_id = intval($_GET['item_id']);
$c_at_l = intval($_GET['c_at_l']);
$cond_c_at_l = (empty($c_at_l)) ? '' : "AND c_at < ".$c_at_l;

$i_is = dt_query("SELECT user_name, money, c_at FROM finance_item_invest WHERE item_id = $item_id $cond_c_at_l ORDER BY c_at DESC LIMIT 2");
if (!$i_is) {
	die('e0');  //获取投资用户数据失败！
}

while($i_i = mysql_fetch_array($i_is)) { ?>
<tr title="<?php echo $i_i['c_at'] ?>"><td><?php echo $i_i['user_name'] ?></td> <td style="text-align: right"><?php echo $i_i['money'] ?></td></tr>
<?php } ?>
