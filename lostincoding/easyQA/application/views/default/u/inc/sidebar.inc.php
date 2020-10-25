<div class="edge">
    <div class="user-about">
        <a href="/u/avatar" title="修改头像">
            <img class="user-avatar" src="<?=create_avatar_url($user['id'], $user['avatar_ext'])?>">
        </a>
        <p>
            <span style="color:#333"><?=$user['nickname']?></span>
            <?php if ($user['gender'] == 'm'): ?>
                <i class="iconfont icon_gender_<?=$user['gender']?>">&#xe606;</i>
            <?php elseif ($user['gender'] == 'f'): ?>
                <i class="iconfont icon_gender_<?=$user['gender']?>">&#xe66b;</i>
            <?php endif;?>
        </p>
        <p>积分：<?=isset($user['points']) ? $user['points'] : 0?></p>
        <?=create_verify_info($user)?>
        <p>
            <span><i class="iconfont">&#xe622;</i> 加入时间：<?=date('Y-m-d', strtotime($user['signup_time']))?></span>
        </p>
    </div>
</div>