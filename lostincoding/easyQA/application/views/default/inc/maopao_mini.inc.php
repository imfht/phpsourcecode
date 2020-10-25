<div class="maopao_wrap clearfix">
	<h3>
		<a class="pjax" href="/maopao">冒个泡吧！</a>
		<span class="fr"><?=sign_html()?></span>
	</h3>
	<form class="maopao_form" onsubmit="return maopao_add(this, 1);">
		<textarea placeholder="今天你冒泡了吗？"></textarea>
		<button type="submit">冒泡</button>
	</form>
	<ul id="latest_maopao" class="jieda maopao_mini">
	    <?php if (is_array($latest_maopao_lists)): ?>
	        <?php foreach ($latest_maopao_lists as $_pao): ?>
	            <?php require VIEWPATH . "$theme_id/maopao/inc/pao.inc.php";?>
	        <?php endforeach;?>
	    <?php else: ?>
	        <li class="fly-none">没有任何冒泡</li>
	    <?php endif;?>
	</ul>
	<ul id="hot_maopao" class="jieda maopao_mini" style="display:none;">
		<?php if (is_array($hot_maopao_lists)): ?>
	        <?php foreach ($hot_maopao_lists as $_pao): ?>
	            <?php require VIEWPATH . "$theme_id/maopao/inc/pao.inc.php";?>
	        <?php endforeach;?>
	    <?php else: ?>
	        <li class="fly-none">没有任何冒泡</li>
	    <?php endif;?>
	</ul>
	<ol class="maopao_switch">
		<li class="on sw" id="newSwitcher" selected="selected" onclick="maopao_switch(0);"><a href="javascript:;">最新冒泡</a></li>
		<li class="img img_new_on" id="imgNew"></li>
		<li class="sw" id="hotSwitcher" onclick="maopao_switch(1);"><a href="javascript:;">热门冒泡</a></li>
		<li class="img img_top_off" id="imgTop"></li>
		<li class="more"><a class="pjax" href="/maopao">更多冒泡&raquo;</a></li>
	</ol>
</div>