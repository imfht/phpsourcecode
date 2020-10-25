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
 * 页面底部信息
 */
if(!defined('EMLOG_ROOT')) {exit('error!');} 
?>
<div style="clear:both;"></div>
<div class="footer">
<div class="git">
	<script src='http://git.oschina.net/ysfl/emlog-for-bootstrap/widget_preview'></script></div>
	Powered by <a href="http://www.emlog.net" title="采用emlog系统">emlog</a> 
	theme by <a href="https://git.oschina.net/ysfl/emlog-for-bootstrap.git" title="emlog for bootstrap">emlog for bootstrap</a> 
	<a href="http://www.miibeian.gov.cn" target="_blank"><?php echo $icp; ?></a> <?php echo $footer_info; ?>
	<?php doAction('index_footer'); ?>
</div><!--end #footer-->
</div><!--end #wrap-->
<script>prettyPrint();</script>
</body>
</html>