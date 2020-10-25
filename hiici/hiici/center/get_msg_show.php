<?php 
$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if (empty($_GET['user_id'])) die;

$user_id = intval($_GET['user_id']);

$user_info = dt_query_one("SELECT name FROM user_info WHERE id = $user_id");
if (!$user_info) die('获取数据失败！');

$msg_index = dt_query_one("SELECT user_id_a FROM msg_index WHERE (user_id_a = ".$auth['id']." AND user_id_b = $user_id) OR (user_id_a = $user_id AND user_id_b = ".$auth['id'].")");
if (!$msg_index) die('获取msg_index数据失败！');
if ($auth['id'] == $msg_index['user_id_a']) {
	$rs = dt_query("UPDATE msg_index SET  new_msg_c_a = 0 WHERE user_id_a = ".$auth['id']." AND user_id_b = $user_id");
} else {
	$rs = dt_query("UPDATE msg_index SET  new_msg_c_b = 0 WHERE user_id_b = ".$auth['id']." AND user_id_a = $user_id");
}
if (!$rs) die('数据变更失败！');

?>
<?php require_once('inc/umediter_varchar.html'); ?> 
<div id="msg_show">
	<div class="row clearfix">
		<div class="col-md-12 column">
			<div class="well msg-form">
				<form class="form-horizontal" id="msg_create">
					<fieldset>
						<div class="form-group">
							<div class="col-md-12">                     
								<textarea class="form-control input-lg" id="msg_content" name="content" style="width:105%;height:100px;"></textarea>
							</div>
						</div>
						<input type="hidden" name="token" value="<?php echo get_token() ?>"/>
						<div class="form-group">
							<label class="col-md-9 control-label"></label>
							<div id="send_msg" class="col-md-3">
								<a href="javascript:do_msg_add(<?php echo $user_id ?>)" class="btn btn-default btn-block"> 发送私信 </a>
							</div>
						</div>
					</fieldset>
				</form>
			</div>
		</div>
	</div>
	<div class="row clearfix">
		<div class="col-md-12 column">
			<div class="well" style="opacity:0.95">
				<div class="msg-header">
					<a href="javascript:get_msg_index()" class="btn btn-default pull-right"><span class="glyphicon glyphicon-fast-backward"></span> 返回 </a>
					<h3 id="msg_with"><span class="glyphicon glyphicon-envelope"></span> 与 <?php echo $user_info['name']; ?> 的私信 </h3>
				</div>
				<div class="msg-area">
					<br>
					<div id="msg_area">

						<!-- 私信加载区域 -->

					</div>	
					<hr class="col-md-11" style="margin-left:18px">
					<a id="get_msg_item_old" href="javascript:get_msg_item()" class="btn btn-default col-md-10" style="margin:20px;margin-left:60px;margin-bottom:30px"><span class="glyphicon glyphicon-chevron-down"></span> 更早的私信 </a>
				</div>	
			</div>
		</div>
	</div>
</div>
<audio id="q_audio" src="forum/inc/notify.mp3" autoplay style="display:none"></audio>
<script type="text/javascript">
var msg_content = UM.getEditor("msg_content");  
$(document).ready(function(){
	get_msg_item();
	auto_run('get_msg_item(1)', 15000);
});
function get_msg_item(is_new) {
	var direct = (is_new) ? 'first' : 'last' ;
	var m_c_at = $('span#msg_c_at:'+direct).text();
	$.get('?c=center&a=get_msg_item&user_id=<?php echo $user_id ?>&msg_c_at='+m_c_at+'&is_new='+is_new, function(rs){
		if ('' == rs) {
			if (!is_new) { alert('没有更多了！^_^'); $('a#get_msg_item_old').remove(); }
			return;
		}
		if (is_new) { q_audio.play(); $('div#msg_area').prepend(rs); } else $('div#msg_area').append(rs);
	});
}
function do_msg_add(user_id) {
	$.post('?c=center&a=do_msg_add&user_id='+user_id, {content: msg_content.getContent(), token: $('input[name=token]').val()}, function(rs){
		rs = $.parseJSON(rs);
		$('input[name=token]').val(rs.token);
		if ('s0' != rs.msg) { 
			alert(rs.msg);
			return;
		} 
		msg_content.setContent('');
		get_msg_item(1);
	});
}
function do_msg_del(msg_id) {
	if (!confirm (" O_O 确定要删除吗！")) return;
	$.get('?c=center&a=do_msg_del&msg_id='+msg_id, function(rs){
		if ('s0' != rs) { 
			alert(rs);
			return;
		} 
		$('div#msg_'+msg_id).remove();
	});
}
</script>
