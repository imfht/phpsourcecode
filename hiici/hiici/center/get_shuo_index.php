<?php 

if (empty($_GET['user_id'])) die;

$user_id = intval($_GET['user_id']);
$shuo_id = @intval($_GET['shuo_id']);
$my_shuo = @intval($_GET['my_shuo']);

?> 

<?php require_once('inc/umediter_varchar.html'); ?> 

<?php if (empty($shuo_id)) { ?>
<?php if (!empty($_SESSION['auth']) && $user_id == $_SESSION['auth']['id']) { ?>
<div class="row clearfix">
	<div class="col-md-12 column">
		<div class="well replyform">
			<form class="form-horizontal" id="shuo_add">
				<fieldset>
					<div class="form-group">
						<div class="col-md-12">                     
							<textarea class="form-control input-lg" id="shuo_content" name="content" style="width:105%;height:90px"></textarea>
						</div>
					</div>
					<!-- token -->
					<input type="hidden" name="token" value="<?php echo get_token() ?>"/>
					<div class="form-group">
						<label class="col-md-9 control-label"></label>
						<div class="col-md-3">
							<a href="javascript:do_shuo_add()" class="btn btn-default btn-block"> 发布说说 </a>
						</div>
					</div>
				</fieldset>
			</form>
		</div>
	</div>
</div>
<ul class="nav nav-tabs">
	<li id="follow_shuo">
	<a href="javascript:get_shuo_index()">关注的说说</a>
	</li>
	<li id="my_shuo">
	<a href="javascript:get_shuo_index(1)">我的说说</a>
	</li>
</ul> <br>
<?php } ?>
<?php } ?>
<div id="shuo_list_area">

	<!-- 说说加载区域 -->

</div>
<?php if (empty($shuo_id)) { ?>
<div class="row clearfix" id="get_shuo_list_c_clean" style="margin-bottom:20px">
	<div class="col-md-12 column">
		<a href="javascript:get_shuo_list_c_clean()" class="btn btn-default btn-block comment"><span class="glyphicon glyphicon-chevron-down"></span> 更早的说说</a>
	</div>
</div>
<?php } ?>

<script type="text/javascript">

var shuo_reply_content = new Array();

<?php if (empty($shuo_id)) { ?>
<?php if (!empty($_SESSION['auth']) && $user_id == $_SESSION['auth']['id']) { ?>
var shuo_content = UM.getEditor("shuo_content");  
(<?php echo $my_shuo ?>) ? $('li#my_shuo').addClass('active') : $('li#follow_shuo').addClass('active');
<?php } ?>
<?php } ?>

$(document).ready(function(){
	$('li#shuo').addClass('active');
	<?php if (empty($shuo_id)) { ?>
	get_shuo_list();
	$(window).scroll(function(){ if (500 > $("div#shuo_list_area").height() - $("body").scrollTop()) { get_shuo_list(); } }); //ajax加载说说 
	<?php } else { ?>
	get_shuo_show();
	<?php } ?>
});

<?php if (empty($shuo_id)) { ?>
//获取说说列表 --------------->
var get_shuo_list_c = 0;
var get_shuo_list_end = false;

function get_shuo_list() {
	if (5 < ++get_shuo_list_c || get_shuo_list_end || !ajax_start('div#shuo_list_area')) return;

	var s_c_at = $('span#shuo_c_at:last').text();
	$.get('?c=center&a=get_shuo_list&user_id=<?php echo $user_id ?>&shuo_c_at='+s_c_at+'&my_shuo=<?php echo $my_shuo ?>', function(rs){
		ajax_stop();
		if ('e0' == rs) { 
			alert(rs);
			return;
		} else if ('' == rs) {
			get_shuo_list_end = true;
			$('div#get_shuo_list_c_clean').remove();
			$("div#shuo_list_area").append('<div class="col-md-12 column alert alert-success"><h3 align="center">  ^_^ 没 有 更 多 了... </h3></div>');
		}
		$('div#shuo_list_area').append(rs);
	});
}
function get_shuo_list_c_clean() {
	get_shuo_list_c = 0;
	get_shuo_list();
}
<?php } else { ?>
function get_shuo_show() {
	$.get('?c=center&a=get_shuo_show&shuo_id=<?php echo $shuo_id ?>', function(rs){
		if ('e0' == rs) { 
			alert(rs);
			return;
		}
		$('div#shuo_list_area').append(rs);
	});
}
<?php } ?>
<?php if (!empty($_SESSION['auth']) && $user_id == $_SESSION['auth']['id']) { ?>
//发布说说 --------------->
function do_shuo_add() {
	$.post('?c=center&a=do_shuo_add', $("form#shuo_add").serialize(), function(rs){
		rs = $.parseJSON(rs);
		$('form#shuo_add').find('input[name=token]').val(rs.token);
		if ('s0' != rs.msg) { 
			alert(rs.msg);
			return;
		} 
		shuo_content.setContent('');
		$("p#shuoshuo").text(parseInt($("p#shuoshuo").text()) + 1);
		$('div#shuo_list_area').html('');
		get_shuo_list_c = 0;
		get_shuo_list_end = false;
		get_shuo_list();
	});
}
function do_shuo_del(shuo_id) {
	if (!confirm (" O_O 确定要删除吗！")) return;

	$.get('?c=center&a=do_shuo_del&shuo_id='+shuo_id, function(rs){
		if ('s0' != rs) { 
			alert(rs);
			return;
		}
		$('div#shuo_'+shuo_id).remove();
		$("p#shuoshuo").text(parseInt($("p#shuoshuo").text()) - 1)
	});
}
<?php } ?>
function do_shuo_up(shuo_id) {
	$.get('?c=center&a=do_shuo_up&shuo_id='+shuo_id, function(rs){
		if ('s0' != rs) { 
			alert(rs);
			return;
		}
		$('div#shuo_'+shuo_id).find('span#shuo_up_c').text(parseInt($('div#shuo_'+shuo_id).find('span#shuo_up_c').text()) + 1)
	});
}
function do_shuo_reply(shuo_id) {
	$.post('?c=center&a=do_shuo_reply', $('div#shuo_'+shuo_id).find("form").serialize(), function(rs){
		rs = $.parseJSON(rs);
		$("form").find('input[name=token]').val(rs.token);
		if ('s0' != rs.msg) { 
			alert(rs.msg);
			return;
		}
		shuo_reply_content[shuo_id].setContent('');
		$('div#shuo_'+shuo_id).find('span#shuo_reply_c').text(parseInt($('div#shuo_'+shuo_id).find('span#shuo_reply_c').text()) + 1);
		get_shuo_reply_list(shuo_id);
	});
}
function do_shuo_reply_up(shuo_reply_id) {
	$.get('?c=center&a=do_shuo_reply_up&shuo_reply_id='+shuo_reply_id, function(rs){
		if ('s0' != rs) { 
			alert(rs);
			return;
		}
		$('div#shuo_reply_'+shuo_reply_id).find('span#shuo_reply_up_c').text(parseInt($('div#shuo_reply_'+shuo_reply_id).find('span#shuo_reply_up_c').text()) + 1)
	});
}
function get_shuo_reply_form(shuo_id) {
	if ('' != $('div#shuo_'+shuo_id).find("div#shuo_reply_form").text()) { shuo_reply_content[shuo_id].setContent(''); $('div#shuo_'+shuo_id).find('input[name=to_user_id]').val(''); return; }
		if (0 == $("div#shuo_reply_list_"+shuo_id).length) get_shuo_reply_list(shuo_id);

	$.get('?c=center&a=get_shuo_reply_form&shuo_id='+shuo_id, function(rs){
		if ('e0' == rs) { 
			alert(rs);
			return;
		}
		$('div#shuo_'+shuo_id).find('div:first').after(rs);
	});
}
function get_shuo_reply_list(shuo_id) {
	if (0 == $("div#shuo_reply_list_"+shuo_id).length) {
		$("div#shuo_"+shuo_id).append('<div class="row clearfix"><div class="col-md-1 column"></div><div class="col-md-11 column" id="shuo_reply_list_'+shuo_id+'"></div></div>');	
		$("div#shuo_reply_list_"+shuo_id).parent("div").append('<div id="get_shuo_reply_list" class="row clearfix"><div class="col-md-2 column" ></div><div class="col-md-5 column"><a class="pull-right" href="javascript:get_shuo_reply_list('+shuo_id+')"><span class="glyphicon glyphicon-zoom-in"></span> 查看更多</a></div></div>');
	}

	var s_r_c_at = $('div#shuo_'+shuo_id).find('span#shuo_reply_c_at:last').text();
	$.get('?c=center&a=get_shuo_reply_list&shuo_id='+shuo_id+'&s_r_c_at='+s_r_c_at, function(rs){
		if(rs == "e0"){
			alert(rs);
			return;
		} else if ('' == rs) {
			$('div#shuo_'+shuo_id).find('div#get_shuo_reply_list').remove();
		}

		$('div#shuo_reply_list_'+shuo_id).append(rs);	

	})
}
function shuo_r_r(shuo_id, to_user_id, to_user_name) {
	shuo_reply_content[shuo_id].setContent('[回复 <b>'+to_user_name+'</b>]');
	$('div#shuo_'+shuo_id).find('input[name=to_user_id]').val(to_user_id);	
	$("html, body").animate({scrollTop: $('div#shuo_'+shuo_id).find("div#shuo_reply_form").offset().top},100);
}
</script>
