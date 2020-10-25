<?php require_once 'inc/header.php';?>
<!-- content start -->
    <div id="admin-content" class="admin-content">
        <div class="admin-content-body">
        	<table class="am-table am-table-striped am-table-hover">
        		<thead>
        			<tr>
        				<th>ID</th>
        				<th>话题名称</th>
        				<th>讨论数</th>
                        <th>查看文章</th>
                        <th>创建时间</th>
        				<th>操作</th>
        			</tr>
        		</thead>
        		<tbody>
                    <?php if (is_array($topic_lists)): ?>
            			<?php foreach ($topic_lists as $_topic): ?>
            				<tr>
            					<td><?=$_topic['id']?></td>
            					<td><?=$_topic['topic']?></td>
            					<td><?=$_topic['used_times']?></td>
                                <td><a href="/topic/articles/<?=$_topic['id']?>" target="_blank">查看文章</a></td>
                                <td><?=$_topic['add_time']?></td>
            					<td>
                                    <div class="am-btn-group am-btn-group-xs">
                                        <a class="am-btn am-btn-default am-btn-xs am-text-danger" topic_id="<?=$_topic['id']?>" onclick="topic_del(this);">删除</a>
                                    </div>
                                </td>
            				</tr>
            			<?php endforeach;?>
                    <?php else: ?>
                        <tr>
                            <td>没有内容。</td>
                        </tr>
                    <?php endif;?>
        		</tbody>
        	</table>
            <?=$page_html?>
		</div>
<script type="text/javascript">
//用户冻结
function topic_del(This){
    var $this = $(This);
    var topic_id = $this.attr('topic_id');

    layer.confirm('确定要删除此话题吗？此话题对应的文章不会被删除。', {icon: 3, shade:0, title:'提示'}, function(index){
        layer.load();
        $.post(
            '/admin/api/topic/del',
            {
                topic_id: topic_id
            },
            function(json){
                layer.closeAll('loading');
                if(json.error_code == 'ok'){
                    layer.msg('成功删除');
                    setTimeout(
                        function(){
                            document.location = document.location;
                        },
                        1500
                    );
                }
                else{
                    show_error(json.error_code);
                }
            },
            'json'
        );
    });
}
</script>
<?php require_once 'inc/footer.php';?>