<?php
    // 为方便并保证您以后的快速升级 请使用SHL提供的如下全局数组
	
	// 数组定义/config/doc-global.php
	
	// 如有需要， 请去掉注释，输出数据。
	/*
	echo '<pre>';
		print_r($tag);
	echo '</pre>';
	*/
?>
<style type="text/css">
*{ padding:0; margin:0;}
img{ border:none;}
#comindex{ width:100%; margin:15px 10px; height:30px; background: url(<?php echo $tag['path.skin'];?>res/images/line.gif) repeat-x; padding:10px 0; position:relative;}
#comindex a{ color:#1E50A2; text-decoration:underline; font:16px "微软雅黑";}
#comindex a:hover{ color:#c00;}
#comindex a span{ color:#BA2636;}
</style>
<a name="commentPosition"></a>
<div id="comindex">
	<a href="<?php echo sys_href($request['p'],'comment',$request['r'])?>" target="_blank"> 【有<span><?php echo $counts?></span>人参与评论】【点击评论】</a>
    <div id="jiathis_style_32x32" style="float:right; padding-right:25px;">
        <a target="_blank" class="jiathis jiathis_txt jtico jtico_jiathis" href="http://www.jiathis.com/share/?uid=1509517" style=""></a>
        <a class="jiathis_button_qzone" title="分享到QQ空间"><span class="jiathis_txt jtico jtico_qzone"></span></a>
        <a class="jiathis_button_tsina" title="分享到新浪微博"><span class="jiathis_txt jtico jtico_tsina"></span></a>
        <a class="jiathis_button_tqq" title="分享到腾讯微博"><span class="jiathis_txt jtico jtico_tqq"></span></a>
        <a class="jiathis_button_kaixin001" title="分享到开心网"><span class="jiathis_txt jtico jtico_kaixin001"></span></a>
        <a class="jiathis_button_renren" title="分享到人人网"><span class="jiathis_txt jtico jtico_renren"></span></a>
    </div>
</div>
<script charset="utf-8" src="http://v1.jiathis.com/code/jia.js?uid=1509517" type="text/javascript"></script>
<script charset="utf-8" src="http://v1.jiathis.com/code/plugin.client.js" type="text/javascript"></script>