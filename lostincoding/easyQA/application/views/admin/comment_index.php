<?php require_once 'inc/header.php';?>
<!-- content start -->
    <div id="admin-content" class="admin-content">
        <div class="admin-content-body">
            <table class="am-table am-table-striped am-table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>内容</th>
                        <th style="width: 80px;">原文ID</th>
                        <th style="width: 80px;">原文</th>
                        <th style="width: 120px;">作者</th>
                        <th style="width: 100px;">时间</th>
                        <th style="width: 200px;">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (is_array($comment_lists)): ?>
                        <?php foreach ($comment_lists as $_comment): ?>
                            <tr>
                                <td><?=$_comment['id']?></td>
                                <td><?=html_newline(content_xss_filter($_comment['comment_content']))?></td>
                                <td><?=$_comment['article_id']?></td>
                                <td><a href="/<?=$config['enum_show']['article_type'][$_comment['article_type']]?>/detail/<?=$_comment['article_id']?>" target="_blank">查看原文</a></td>
                                <td><a href="/u/home/<?=$_comment['user_id']?>" target="_blank"><?=$_comment['nickname']?></a></td>
                                <td><?=$_comment['add_time']?></td>
                                <td>
                                    <div class="am-btn-group am-btn-group-xs">
                                        <a class="am-btn am-btn-default am-btn-xs am-text-danger" comment_id="<?=$_comment['id']?>" onclick="comment_del(this);">删除</a>
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
//评论删除
function comment_del(This){
    var $this = $(This);
    var comment_id = $this.attr('comment_id');

    layer.confirm('确定要删除这篇评论吗？', {icon: 3, shade:0, title:'提示'}, function(index){
        layer.load();
        $.post(
            '/admin/api/comment/del',
            {
                comment_id: comment_id
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