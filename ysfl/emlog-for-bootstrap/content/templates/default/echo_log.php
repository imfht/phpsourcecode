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
 * 阅读文章页面
 */
if(!defined('EMLOG_ROOT')) {exit('error!');} 
?>
<div class="contentleft">
	<div class="log_content panel panel-default">

	<div class="panel-heading text-center">

		<h2 class="log_title"><?php topflg($top); #置顶?><?php echo $log_title; #文章标题?></h2>

	</div>
	<div class="btn-group log_info">
		<button class="btn btn-default">发布时间：<?php echo gmdate('Y-n-j', $date); #发布时间?></button>
		<button class="btn btn-default">作者：<?php blog_author($author); #作者?></button>
		<button class="btn btn-default">分类：<?php blog_sort($logid); #文章分类?></button>
		<div class="btn-group">
		<button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button">标签：<span class="caret"></span></button>
		<ul class="dropdown-menu"><?php blog_tag($logid); #标签?></ul>
		</div>
		<?php editflg($logid,$author); #编辑按钮?>
	</div>

	<div class="panel-body"><?php echo $log_content; #文章内容?></div>
	
	<div class="panel-footer">
		<?php doAction('log_related', $logData); #相关文章?>

		<ul class="pager"><?php neighbor_log($neighborLog); #相邻文章?></ul>
	</div>
	<div class="comment-list-main"><?php blog_comments($comments,$allow_remark/*加入评论开关参数,后面需要传参*/); #评论列表?></div>
	<?php blog_comments_post($logid,$ckname,$ckmail,$ckurl,$verifyCode,$allow_remark); #发表评论?>
	
	<div style="clear:both;"></div>

	</div>
</div><!--end #contentleft-->
<?php
 include View::getView('side');
 include View::getView('footer');
?>