<?php require_once VIEWPATH . "$theme_id/inc/header.inc.php";?>
<div id="main" class="main layui-clear">
    <div class="wrap">
        <div class="content" style="margin-right: 0;">
        	<div class="skins_nav pt20">
				<span class="sub_title">换肤：</span>
				<a class="item<?=$class == 'new' ? ' active' : ''?>" href="/skin/new">最新</a>
				<a class="item<?=$class == 'hot' ? ' active' : ''?>" href="/skin/hot">最热</a>
				<a class="item<?=$class == 1 ? ' active' : ''?>" href="/skin/1">明星</a>
				<a class="item<?=$class == 2 ? ' active' : ''?>" href="/skin/2">动漫</a>
				<a class="item<?=$class == 3 ? ' active' : ''?>" href="/skin/3">萌宠</a>
				<a class="item<?=$class == 4 ? ' active' : ''?>" href="/skin/4">风景</a>
				<a class="item<?=$class == 5 ? ' active' : ''?>" href="/skin/5">其它</a>
				<span class="fr f12">
					<label class="mr10 t_green"><input type="checkbox" class="mr2 vm"<?=(isset($skin) && $skin['lock_background'] == 2) ? ' checked="checked"' : ''?> onClick="lock_background(this)" />锁定皮肤背景(背景图不随页面滚动)</label>
					<a href="/skin/setting/0" rel="nofollow">不使用皮肤</a>
				</span>
			</div>
			<div class="skins pt20 clearfix">
				<?php if (is_array($skin_lists)): ?>
					<?php foreach ($skin_lists as $_skin): ?>
						<div class="skin">
							<div class="skin_img_wrap">
								<a class="skin_link" href="/skin/setting/<?=$_skin['id']?>" rel="nofollow"><img class="skin_img" src="http://<?=$config['qiniu']['static_bucket_domain']?>/skins/little/<?=$_skin['id']?>.jpg" title="<?=xss_filter($_skin['skin_name'])?>" alt="<?=xss_filter($_skin['skin_name'])?>" /></a>
							</div>
							<div class="skin_meta clearfix">
								<span class="skin_name"><?=xss_filter($_skin['skin_name'])?></span>
								<span class="skin_stats"><?=xss_filter($_skin['skin_stats'])?>人使用</span>
							</div>
						</div>
					<?php endforeach;?>
				<?php endif;?>
			</div>
			<?=$page_html?>
        </div>
    </div>

<script type="text/javascript">
create_element('js', '/static/' + CONFIG['theme_id'] + '/js/skin.min.js');
</script>

</div>
<?php require_once VIEWPATH . "$theme_id/inc/footer.inc.php";?>