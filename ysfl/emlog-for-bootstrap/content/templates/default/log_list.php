<?php 
/*
 * This file is part of the emlog for bootstrap Project. See CREDITS and LICENSE files
 *
 * emlog for bootstrap Project URL:https://git.oschina.net/ysfl/emlog-for-bootstrap
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
/**
 * 站点首页模板
 */
if(!defined('EMLOG_ROOT')) {exit('error!');} 
?>
<div class="contentleft">
<?php doAction('index_loglist_top'); ?>

<?php 
if (!empty($logs)):
foreach($logs as $value): 
?>
<div class="post">

	<div class="post-time"><?php echo gmdate('Y / n / j', $value['date']); # 文章时间?></div>

	<h2 class="post-title"><?php editflg($value['logid'],$value['author']); # 文章编辑链接?><?php topflg($value['top'], $value['sortop'], isset($sortid)?$sortid:''); ?><a href="<?php echo $value['log_url']; ?>"><?php echo $value['log_title']; ?></a></h2>
	
	<div class="post-info">
	
		<span class="glyphicon glyphicon-user"> </span> <span>作者：<?php blog_author($value['author']); # 文章作者?></span></br>
	
		<span class="glyphicon glyphicon-folder-open"> </span> <span>分类：<?php blog_sort($value['logid']); # 分类ID?></span></br>

		<span class="glyphicon glyphicon-comment"> </span> <span>评论：<a href="<?php echo $value['log_url']; ?>#comments"><?php comunm_check($value);?></a></span></br>

		<span class="glyphicon glyphicon-eye-open"> </span> <span>查看：<a href="<?php echo $value['log_url']; ?>"><?php echo $value['views']; # 浏览数量?></a></span>
	
	</div>
</div>
	<div style="clear:both;"></div>
<?php 
endforeach;
else:
?>
	<h2>未找到</h2>
	<p>抱歉，没有符合您查询条件的结果。</p>
<?php endif;?>
<nav>
	<ul class="pagination"><li><?php echo $page_url;?></li></ul>
</nav>

</div><!-- end #contentleft-->
<?php
 include View::getView('side'); # 调用侧边栏
 include View::getView('footer'); # 调用页尾
?>