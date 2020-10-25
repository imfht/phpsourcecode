<?php 

if (empty($_GET['user_id'])) die;

$user_id = intval($_GET['user_id']);
$blog_id = @intval($_GET['blog_id']);

?>

<div id="blog_index">

</div>

<script type="text/javascript">

$(document).ready(function(){
	$('li#blog').addClass('active');
	<?php if (empty($blog_id)) { ?>
	get_blog_list();
	<?php } else { ?>
	get_blog_show(<?php echo $blog_id ?>);
	<?php } ?>
});

function get_blog_list(page) {
	$.get('?c=center&a=get_blog_list&user_id=<?php echo $user_id ?>&page='+page, function(rs){
		if ('e0' == rs) { 
			alert(rs);
			return;
		}
		$('div#blog_index').html(rs);
	});
}

function get_blog_show(blog_id, offset) {
	$.get('?c=center&a=get_blog_show&blog_id='+blog_id+'&offset='+offset, function(rs){
		if ('e0' == rs) { 
			alert('没有更多了！^_^');
			return;
		}
		$('div#blog_index').html(rs);
	});
}

function do_blog_up(blog_id) {
	$.get('?c=center&a=do_blog_up&blog_id='+blog_id, function(rs){
		if ('s0' != rs) { 
			alert(rs);
			return;
		}
		$('div#blog_'+blog_id).find('span#blog_up_c').text(parseInt($('div#blog_'+blog_id).find('span#blog_up_c:first').text()) + 1)
	});
}

function do_blog_reply() {
	$.post('?c=center&a=do_blog_reply', $('form#blog_reply').serialize(), function(rs){
		rs = $.parseJSON(rs);
		$("form#blog_reply").find('input[name=token]').val(rs.token);
		if ('s0' != rs.msg) { 
			alert(rs.msg);
			return;
		}
		alert('发布成功！^_^');
		blog_reply_content.setContent('');
		get_blog_reply_list();
	});
}

function do_blog_reply_up(blog_reply_id) {
	$.get('?c=center&a=do_blog_reply_up&blog_reply_id='+blog_reply_id, function(rs){
		if ('s0' != rs) { 
			alert(rs);
			return;
		}
		$('div#blog_reply_'+blog_reply_id).find('span#blog_reply_up_c').text(parseInt($('div#blog_reply_'+blog_reply_id).find('span#blog_reply_up_c').text()) + 1)
	});
}

function blog_r_r(to_user_id, to_user_name) {
	blog_reply_content.setContent('[回复 <b>'+to_user_name+'</b>]');
	$('form#blog_reply').find('input[name=to_user_id]').val(to_user_id);	
	$("html, body").animate({scrollTop: $("div#blog_reply_form").offset().top},100);
}

<?php if (!empty($_SESSION['auth']) && $user_id == $_SESSION['auth']['id']) { ?>
function get_blog_add() {
	$.get('?c=center&a=get_blog_add', function(rs){
		if ('e0' == rs) { 
			alert(rs);
			return;
		}
		$('div#blog_index').html(rs);
	});
}

function do_blog_add() {
	$.post('?c=center&a=do_blog_add', $("form#blog_add").serialize(), function(rs){
		if ('s0' != rs) { 
			rs = $.parseJSON(rs);
			$("form#blog_add").find('input[name=token]').val(rs.token);
			alert(rs.msg);
			return;
		} 
		alert('发布成功！^_^');
		get_blog_list();
	});
}

function get_blog_edit(blog_id) {
	$.get('?c=center&a=get_blog_edit&blog_id='+blog_id, function(rs){
		if ('e0' == rs) { 
			alert(rs);
			return;
		}
		$('div#blog_index').html(rs);
	});
}

function do_blog_edit(blog_id) {
	$.post('?c=center&a=do_blog_edit', $("form#blog_edit").serialize(), function(rs){
		if ('s0' != rs) { 
			rs = $.parseJSON(rs);
			$("form#blog_edit").find('input[name=token]').val(rs.token);
			alert(rs.msg);
			return;
		} 
		alert('修改成功！^_^');
		get_blog_show(blog_id);
	});
}

function do_blog_del(blog_id) {
	if (!confirm (" O_O 确定要删除吗！")) return;

	$.get('?c=center&a=do_blog_del&blog_id='+blog_id, function(rs){
		if ('s0' != rs) { 
			alert(rs);
			return;
		}
		get_blog_list();
	});
}
<?php } ?>

</script>

