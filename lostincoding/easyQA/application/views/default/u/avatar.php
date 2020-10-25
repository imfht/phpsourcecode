<?php require_once VIEWPATH . "$theme_id/inc/header.inc.php";?>
<div id="main" class="main layui-clear">
    <div class="wrap">
        <div class="content">
            <?php require_once VIEWPATH . "$theme_id/u/inc/nav.inc.php";?>
            <div class="user-mine">
                <div class="fl mr10" style="width: 75px;">
                    <img id="current_avatar" src="<?=create_avatar_url($user['id'], $user['avatar_ext'])?>" style="display: block; width: 75px; height: 75px;">
                    <div class="tc">
                        <button id="select_avatar_btn" class="layui-btn layui-btn-small layui-btn-normal" style="width: 100%; margin-top: 5px; display: block;">选择图片</button>
                    </div>
                </div>
                <div class="fl" style="max-width: 740px;">
                    <ul class="avatars clearfix">
                        <?php for ($i = 0; $i <= 23; $i++): ?>
                        <li><a href="javascript:;" avatar_name="<?=$i?>.png" onclick="set_system_avatar(this);"><img class="avatar" src="http://<?=$config['qiniu']['static_bucket_domain']?>/avatar/s/<?=$i?>.png!avatar"></a></li>
                        <?php endfor;?>
                    </ul>
                    <ul class="avatars clearfix">
                        <?php for ($i = 100; $i <= 121; $i++): ?>
                        <li><a href="javascript:;" avatar_name="<?=$i?>.png" onclick="set_system_avatar(this);"><img class="avatar" src="http://<?=$config['qiniu']['static_bucket_domain']?>/avatar/s/<?=$i?>.png!avatar"></a></li>
                        <?php endfor;?>
                    </ul>
                    <ul class="avatars clearfix">
                        <?php for ($i = 200; $i <= 223; $i++): ?>
                        <li><a href="javascript:;" avatar_name="<?=$i?>.png" onclick="set_system_avatar(this);"><img class="avatar" src="http://<?=$config['qiniu']['static_bucket_domain']?>/avatar/s/<?=$i?>.png!avatar"></a></li>
                        <?php endfor;?>
                    </ul>
                </div>
            </div>
            <div id="LAY-page"></div>
        </div>
    </div>
    <?php require_once VIEWPATH . "$theme_id/u/inc/sidebar.inc.php";?>

<script type="text/javascript">
create_element('js', '/static/' + CONFIG['theme_id'] + '/js/u/avatar.min.js');
</script>

</div>
<?php require_once VIEWPATH . "$theme_id/inc/footer.inc.php";?>