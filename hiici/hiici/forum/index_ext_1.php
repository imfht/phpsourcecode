<?php 

$page = @intval($_GET['page']);
if ($page) {

$f_city = @intval($_GET['f_city']);

$f_ext = 1;
$cond = "WHERE pay > 0 AND city = $f_city";
$limit = 3; //require_once('inc/order_search_limit.php');

$topic_tops = dt_query("SELECT forum_topic.id, title, icon_url, user_id, user_name, view_c, reply_c, today, today_up_c, l_r_user_id, l_r_user_name, l_r_at, digest, pay, c_at, forum_topic_ext_$f_ext.* FROM forum_topic INNER JOIN forum_topic_ext_$f_ext ON forum_topic.id = forum_topic_ext_$f_ext.id $cond ORDER BY c_at DESC LIMIT ".$limit * ($page - 1).",$limit");
if (!mysql_num_rows($topic_tops)) die('s1');

?>
<?php while($topic_l = mysql_fetch_array($topic_tops)) { ?>
<?php require('forum_ext/ext_'.$f_ext.'/topic_list_item.phtml') ?>	
<?php } ?>

<?php } else { ?>

<link rel="stylesheet" href="forum/forum_ext/ext_1/topic_list.css">
<div class="container">
	<div class="forum-topic-list-ext-1">
		<div class="row clearfix">

		</div>
	</div>
</div>
<script>

var pg = 1, a_is_send = false;
$(window).scroll(function(){ if (700 > $('div.news').height() - $('body').scrollTop()) { if (pg && !a_is_send) get_ext_1(); } }); 

function get_ext_1() {
	a_is_send = true;
	$.ajax({
		type : 'get',
			url : '?c=forum&a=index_ext_1&page='+(pg++)+'&f_city=<?php echo $forum_city ?>',
			success : function(rs){
				if ('s1' == rs) { pg = 0; return; }
				$('div.forum-topic-list-ext-1 .row').append(rs);
				a_is_send = false;
			}
	});
}

</script>

<?php } ?>
