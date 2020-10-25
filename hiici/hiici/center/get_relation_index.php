<?php 

if (empty($_GET['user_id'])) die;
$user_id = intval($_GET['user_id']);
$my_fan = @intval($_GET['my_fan']);

?>

<br>
<ul class="nav nav-tabs" id="relation_nav">
	<li id="my_follow">
	<a href="javascript:get_follow_list()">我的关注</a>
	</li>
	<li id="my_fan">
	<a href="javascript:get_fan_list()">我的粉丝</a>
	</li>
	<li id="find_user">
	<a href="javascript:get_user_list()">找人</a>
	</li>
</ul> <br>

<div class="row clearfix" id="relation_area">

	<!-- 空间内容加载区 -->

</div>


<script type="text/javascript">

$(document).ready(function(){
	$('li#relation').addClass('active');
	(<?php echo $my_fan ?>) ? get_fan_list() : get_follow_list();
});

function get_follow_list(page) {
	$.get('?c=center&a=get_follow_list&user_id=<?php echo $user_id ?>&page='+page, function(rs){
		if ('e0' == rs) { 
			alert(rs);
			return;
		}
		get_relation_x_index_pub_do(rs);
	});
}
function get_fan_list(page) {
	$.get('?c=center&a=get_fan_list&user_id=<?php echo $user_id ?>&page='+page, function(rs){
		if ('e0' == rs) { 
			alert(rs);
			return;
		}
		get_relation_x_index_pub_do(rs);
	});
}
function get_user_list(page) {
	search = (0 < $('input#find_user_search').length) ? $('input#find_user_search').val() : '';
	$.get('?c=center&a=get_user_list&search='+search+'&page='+page, function(rs){
		if ('e0' == rs) { 
			alert(rs);
			return;
		}
		get_relation_x_index_pub_do(rs);
	});
}
function get_relation_x_index_pub_do(rs) {
	$('ul#relation_nav').find('li').removeClass('active');
	$('div#relation_area').html(rs);
}


</script>

