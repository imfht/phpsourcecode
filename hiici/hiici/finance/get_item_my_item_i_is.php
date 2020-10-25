<?php 

$auth = $_SESSION['auth'];
if (empty($auth)) die('e0');

if (empty($_GET)) die;

$item_id = intval($_GET['item_id']);

global $config;
if (1 > dt_count('finance_item', 'WHERE id = '.$item_id.' AND user_id = '.$auth['id']) 
	&& 1 > dt_count('finance_item', 'WHERE id = '.$item_id.' AND lmcy_id = (SELECT id FROM finance_lmcy WHERE user_id = '.$auth['id'].')') 
	&& !in_array($auth['id'], $config['manager'])) 
	die('用户权限不够！^_^');

$page = @intval($_GET['page']);

if (empty($page)) { $page = 1; }
$limit = 20;
$cond = 'WHERE item_id = '.$item_id;

$i_is = dt_query("SELECT * FROM finance_item_invest $cond ORDER BY money DESC LIMIT ".$limit * ($page - 1).",$limit");
if (!$i_is) die('e0');

$item_name = dt_query_one("SELECT name FROM finance_item WHERE id = $item_id")['name'];

?>
<hr style="border-top:1px solid #ccc">
<div class="well finance-item-well">
	<h3 style="font-size:18px">项目 “<?php echo $item_name ?>” 的意向投资人 </h3>
	<hr class="dashed">
	<div class="row clearfix">
		<div class="col-md-12 column">
			<table class="table">
				<thead>
					<tr>
						<th>投资人</th>
						<th>意向投资额</th>
						<th>联系电话</th>
						<th>提交时间</th>
					</tr>
				</thead>
				<tbody>
				<?php while($i_i = mysql_fetch_array($i_is)) { ?>
				<tr id="<?php echo $i_i['id'] ?>">
					<td><a href="?c=center&user_id=<?php echo $i_i['user_id'] ?>"><?php echo $i_i['user_name'] ?></a></td>
					<td><?php echo $i_i['money'] ?></td>
					<td><?php echo $i_i['phone'] ?></td>
					<td><?php echo fmt_date($i_i['c_at']) ?></td>
				</tr>
				<?php } ?>
				</tbody>
			</table>

			<?php pagination('finance_item_invest', $cond, $page, $limit, 'javascript:get_i_is('.$item_id.', ', ')') ?>
		</div>

	</div>
</div>
