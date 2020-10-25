<?php require_once 'inc/header.php';?>
<!-- content start -->
    <div id="admin-content" class="admin-content">
        <div class="admin-content-body">
        	<table class="am-table am-table-striped am-table-hover">
        		<thead>
        			<tr>
        				<th>ID</th>
        				<th>昵称</th>
        				<th>注册时间</th>
        				<th>操作</th>
        			</tr>
        		</thead>
        		<tbody>
                    <?php if (is_array($u_lists)): ?>
            			<?php foreach ($u_lists as $_u): ?>
            				<tr>
            					<td><?=$_u['id']?></td>
            					<td><a href="/u/home/<?=$_u['id']?>" target="_blank"><?=$_u['nickname']?></a></td>
            					<td><?=$_u['signup_time']?></td>
            					<td>
                                    <div class="am-btn-group am-btn-group-xs">
                                        <?php if ($_u['freeze_status'] == 1): ?>
                                            <a class="am-btn am-btn-default am-btn-xs am-text-danger" user_id="<?=$_u['id']?>" onclick="u_freeze(this, 1);">冻结</a>
                                        <?php else: ?>
                                            <a class="am-btn am-btn-default am-btn-xs" user_id="<?=$_u['id']?>" onclick="u_freeze(this, 2);">解除冻结</a>
                                        <?php endif;?>
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
function u_freeze(This, freeze_status){
    var $this = $(This);
    var user_id = $this.attr('user_id');

    var confirm_tip = '确定要冻结这位用户吗？';
    if(freeze_status == 2){
        confirm_tip = '确定要解除冻结这位用户吗？';
    }
    layer.confirm(confirm_tip, {icon: 3, shade:0, title:'提示'}, function(index){
        layer.load();
        $.post(
            '/admin/api/u/freeze',
            {
                user_id: user_id,
                freeze_status: freeze_status
            },
            function(json){
                layer.closeAll('loading');
                if(json.error_code == 'ok'){
                    if(freeze_status == 1){
                        layer.msg('成功冻结');
                    }
                    else{
                        layer.msg('成功解除冻结');
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
    });
}
</script>
<?php require_once 'inc/footer.php';?>