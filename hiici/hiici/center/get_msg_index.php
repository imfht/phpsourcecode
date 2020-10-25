<?php 

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if (empty($_GET)) die;

$page = @intval($_GET['page']);

if (empty($page)) { $page = 1; }
$limit = 15;

$cond = "WHERE user_id_a = ".$auth['id']." OR user_id_b = ".$auth['id'];

$msg_indexs = dt_query("SELECT * FROM msg_index $cond ORDER BY last_msg_c_at DESC LIMIT ".$limit * ($page - 1).",$limit");
if (!$msg_indexs) die('获取数据失败！');

?>

<div id="msg_index_area">
	<div id="msg_index" class="list-group msg_index">
		<?php while($msg_index = mysql_fetch_array($msg_indexs)) { ?>
<?php 
if ($auth['id'] == $msg_index['user_id_a']) {
	$user_id = $msg_index['user_id_b'];
	$user_name = $msg_index['user_name_b'];
	$new_msg_c = $msg_index['new_msg_c_a'];
} else {
	$user_id = $msg_index['user_id_a'];
	$user_name = $msg_index['user_name_a'];
	$new_msg_c = $msg_index['new_msg_c_b'];
}
?>
		<div class="msg-index-list-group-item" id="msg_i_<?php echo $msg_index['id'] ?>">
			<div class="row clearfix">
				<a href="javascript:do_msg_i_del(<?php echo $msg_index['id'] ?>)" class="close">&times;</a>
				<div class="content" onclick="javascript:get_msg_show(<?php echo $user_id ?>)">
					<h4 class="pull-right"> <span class="glyphicon glyphicon-time"></span><?php echo fmt_date($msg_index['last_msg_c_at']) ?></h4>
					<h4><strong><?php echo $user_name ?></strong> <?php if ($new_msg_c > 0) { ?><span id="new_msg_c_<?php echo $user_id ?>" class="label label-info"><?php echo $new_msg_c ?></span><?php } ?></h4>
					<h4 style="height:46px;width:100%;overflow:hidden"><?php echo $msg_index['content'] ?></h4>
				</div>
				<div class="userface">
					<img src="<?php echo FACE_URL.$user_id ?>_min.jpg" class="img-rounded">
				</div>
			</div>
		</div>
		<?php } ?>
		<?php pagination('msg_index', $cond, $page, $limit, 'javascript:get_msg_index(', ')') ?>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$('li#msg').addClass('active');
});
function get_msg_show(user_id) {
	$.get('?c=center&a=get_msg_show&user_id='+user_id, function(rs){
		if ('e0' == rs) { 
			alert(rs);
			return;
		}
		$('div#msg_index_area').html(rs);
	});
}
function do_msg_i_del(msg_i_id) {
	if (!confirm (" O_O 确定要删除吗！")) return;
	$.get('?c=center&a=do_msg_i_del&msg_i_id='+msg_i_id, function(rs){
		if ('s0' != rs) { 
			alert(rs);
			return;
		} 
		$('div#msg_i_'+msg_i_id).remove();
	});
}
</script>
