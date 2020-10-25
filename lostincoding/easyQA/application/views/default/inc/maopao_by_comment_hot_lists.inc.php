<h3>热门冒泡！</h3>
<ul id="hot_maopao" class="jieda maopao_mini">
    <?php if (is_array($hot_maopao_lists)): ?>
        <?php foreach ($hot_maopao_lists as $_pao): ?>
            <?php require VIEWPATH . "$theme_id/maopao/inc/pao.inc.php";?>
        <?php endforeach;?>
    <?php else: ?>
        <li class="fly-none">没有任何冒泡</li>
    <?php endif;?>
</ul>