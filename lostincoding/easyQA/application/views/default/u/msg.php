<?php require_once VIEWPATH . "$theme_id/inc/header.inc.php";?>
<div id="main" class="main layui-clear">
    <div class="wrap">
        <div class="content">
            <?php require_once VIEWPATH . "$theme_id/u/inc/nav.inc.php";?>
            <div class="user-mine">
                <ul class="mine-msg">
                    <?php if (is_array($msg_lists)): ?>
                        <?php foreach ($msg_lists as $_msg): ?>
                            <li id="msg_<?=$_msg['id']?>">
                                <div><?=$_msg['msg_title']?></div>
                                <div>
                                    <i style="color: #999;">内容：</i>
                                    <?=html_newline(xss_filter($_msg['msg_content']))?>
                                </div>
                                <p><span><?=time_tran($_msg['send_time'])?></span><a href="javascript:;" class="layui-btn layui-btn-small fly-delete" onclick="msg_del(<?=$_msg['id']?>);">删除</a></p>
                            </li>
                        <?php endforeach;?>
                    <?php else: ?>
                        <li class="fly-none" style="min-height: 50px; padding:30px 0; height:auto;"><div style="font-size: 14px;">没有啦</div></li>
                    <?php endif;?>
                </ul>
            </div>
            <?=$page_html?>
        </div>
    </div>
    <?php require_once VIEWPATH . "$theme_id/u/inc/sidebar.inc.php";?>

<script type="text/javascript">
create_element('js', '/static/' + CONFIG['theme_id'] + '/js/u/msg.min.js');
</script>

</div>

<?php require_once VIEWPATH . "$theme_id/inc/footer.inc.php";?>