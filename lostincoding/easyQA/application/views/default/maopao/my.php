<?php require_once VIEWPATH . "$theme_id/inc/header.inc.php";?>
<div id="main" class="main layui-clear">
    <div class="wrap">
        <div class="content">
            <?php require_once VIEWPATH . "$theme_id/maopao/inc/nav.inc.php";?>
            <div class="maopao_wrap clearfix">
                <h3>冒个泡吧！</h3>
                <div class="layui-form layui-form-pane">
                    <form onsubmit="return maopao_add(this, 2);">
                        <div id="maopao_rich_editor" style="height: 152px;"></div>
                        <div class="layui-form-item">
                            <button type="submit" class="layui-btn">冒泡</button>
                        </div>
                    </form>
                </div>
                <ul id="latest_maopao" class="jieda">
                    <?php if (is_array($maopao_lists)): ?>
                        <?php foreach ($maopao_lists as $_pao): ?>
                            <?php require VIEWPATH . "$theme_id/maopao/inc/pao.inc.php";?>
                        <?php endforeach;?>
                    <?php else: ?>
                        <li class="fly-none">没有任何冒泡</li>
                    <?php endif;?>
                </ul>
            </div>
        </div>
    </div>
    <div class="edge">
        <div class="maopao_wrap" class="clearfix">
            <?php require_once VIEWPATH . "$theme_id/inc/maopao_by_comment_hot_lists.inc.php";?>
        </div>
        <?php require_once VIEWPATH . "$theme_id/inc/friends_link.inc.php";?>
    </div>

<script type="text/javascript">
//创建富文本编辑器
$(function(){
    create_rich_editor('maopao_rich_editor', '', '请输入冒泡内容', 40, false);
});

//相册
layer.photos({
    photos: '.photo'
    ,zIndex: 9999999999,
    shift: 5,
    shade: [0.3, '#000000']
});
</script>

</div>

<?php require_once VIEWPATH . "$theme_id/inc/footer.inc.php";?>