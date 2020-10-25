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
 * 侧边栏
 */
if(!defined('EMLOG_ROOT')) {exit('error!');} 
?>
<ul class="sidebar">
<?php 
$widgets = !empty($options_cache['widgets1']) ? unserialize($options_cache['widgets1']) : array();
doAction('diff_side');
foreach ($widgets as $val)
{
	$widget_title = @unserialize($options_cache['widget_title']);
	$custom_widget = @unserialize($options_cache['custom_widget']);
	if(strpos($val, 'custom_wg_') === 0)
	{
		$callback = 'widget_custom_text';
		if(function_exists($callback))
		{
			call_user_func($callback, htmlspecialchars($custom_widget[$val]['title']), $custom_widget[$val]['content']);
		}
	}else{
		$callback = 'widget_'.$val;
		if(function_exists($callback))
		{
			preg_match("/^.*\s\((.*)\)/", $widget_title[$val], $matchs);
			$wgTitle = isset($matchs[1]) ? $matchs[1] : $widget_title[$val];
			call_user_func($callback, htmlspecialchars($wgTitle));
		}
	}
}
?>
<?php if (Option::get('rss_output_num')):?>
<li style="padding-left:10;background:none;">
<div class="rss">
<a href="<?php echo BLOG_URL; ?>rss.php" title="RSS订阅"><img src="<?php echo TEMPLATE_URL; ?>images/rss.gif" alt="订阅Rss"/></a>
</div>
</li>
<?php endif;?>
</ul><!--end #siderbar-->
