<?php require_once 'inc/header.php';?>
<!-- content start -->
    <div id="admin-content" class="admin-content">
        <div class="admin-content-body">
        	<table class="am-table am-table-striped am-table-hover">
        		<thead>
        			<tr>
        				<th>ID</th>
        				<th>标题</th>
        				<th style="width: 50px;">置顶</th>
        				<th style="width: 50px;">精华</th>
        				<th style="width: 90px;">评论/阅读</th>
        				<th style="width: 120px;">作者</th>
        				<th style="width: 100px;">时间</th>
        				<th style="width: 220px;">操作</th>
        			</tr>
        		</thead>
        		<tbody>
                    <?php if (is_array($article_lists)): ?>
            			<?php foreach ($article_lists as $_article): ?>
            				<tr>
            					<td><?=$_article['id']?></td>
            					<td><a href="/<?=$config['enum_show']['article_type'][$_article['article_type']]?>/detail/<?=$_article['id']?>" target="_blank"><?=xss_filter($_article['article_title'])?></a></td>
            					<td><?=$_article['is_top'] == 2 ? '置顶' : ''?></td>
            					<td><?=$_article['is_fine'] == 2 ? '精华' : ''?></td>
            					<td><?=$_article['comment_counts']?>/<?=$_article['view_counts']?></td>
            					<td><a href="/u/home/<?=$_article['user_id']?>" target="_blank"><?=$_article['nickname']?></a></td>
            					<td><?=$_article['add_time']?></td>
            					<td>
                                    <div class="am-btn-group am-btn-group-xs">
                                        <?php if ($_article['is_top'] == 1): ?>
                                            <a class="am-btn am-btn-default am-btn-xs" article_id="<?=$_article['id']?>" onclick="article_set_top(this, 1);">置顶</a>
                                        <?php else: ?>
                                            <a class="am-btn am-btn-default am-btn-xs" article_id="<?=$_article['id']?>" onclick="article_set_top(this, 2);">取消置顶</a>
                                        <?php endif;?>
                                        <?php if ($_article['is_fine'] == 1): ?>
                                            <a class="am-btn am-btn-default am-btn-xs" article_id="<?=$_article['id']?>" onclick="article_set_fine(this, 1);">加精</a>
                                        <?php else: ?>
                                            <a class="am-btn am-btn-default am-btn-xs" article_id="<?=$_article['id']?>" onclick="article_set_fine(this, 2);">取消加精</a>
                                        <?php endif;?>
                                        <a class="am-btn am-btn-default am-btn-xs am-text-danger" article_id="<?=$_article['id']?>" onclick="article_del(this);">删除</a>
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
//文章置顶
function article_set_top(This, is_top){
    var $this = $(This);
    var article_id = $this.attr('article_id');

    layer.load();
    $.post(
        '/admin/api/article/set_top',
        {
            article_id: article_id,
            is_top: is_top
        },
        function(json){
            layer.closeAll('loading');
            if(json.error_code == 'ok'){
                if(is_top == 1){
                    layer.msg('成功置顶');
                }
                else{
                    layer.msg('成功取消置顶');
                }
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
}

//文章加精
function article_set_fine(This, is_fine){
    var $this = $(This);
    var article_id = $this.attr('article_id');

    layer.load();
    $.post(
        '/admin/api/article/set_fine',
        {
            article_id: article_id,
            is_fine: is_fine
        },
        function(json){
            layer.closeAll('loading');
            if(json.error_code == 'ok'){
                if(is_fine == 1){
                    layer.msg('成功加精');
                }
                else{
                    layer.msg('成功取消加精');
                }
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
}

//文章删除
function article_del(This){
    var $this = $(This);
    var article_id = $this.attr('article_id');

    layer.confirm('确定要删除这篇文章吗？', {icon: 3, shade:0, title:'提示'}, function(index){
        layer.load();
        $.post(
            '/admin/api/article/del',
            {
                article_id: article_id
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