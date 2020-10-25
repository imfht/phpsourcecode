<?php 
add_action('admin_menu', 'ets_page');
function ets_page (){
	if ( count($_POST) > 0 && isset($_POST['ets_settings']) ){
		$options = array ('keywords','description','analytics','power','copyright');
		foreach ( $options as $opt ){
			delete_option ( 'ets_'.$opt, $_POST[$opt] );
			add_option ( 'ets_'.$opt, $_POST[$opt] );	
		}
	}
	add_theme_page(__('主题选项'), __('主题选项'), 'edit_themes', basename(__FILE__), 'ets_settings');
}
function ets_settings(){?>
<style>
	.wrap,textarea,em{font-family:'Century Gothic','Microsoft YaHei',Verdana;}
	fieldset{width:100%;border:1px solid #aaa;padding-bottom:20px;margin-top:20px;-webkit-box-shadow:rgba(0,0,0,.2) 0px 0px 5px;-moz-box-shadow:rgba(0,0,0,.2) 0px 0px 5px;box-shadow:rgba(0,0,0,.2) 0px 0px 5px;}
	legend{margin-left:5px;padding:0 5px;color:#2481C6;background:#F9F9F9;cursor:pointer;}
	textarea{width:100%;font-size:11px;border:1px solid #aaa;background:none;-webkit-box-shadow:rgba(0,0,0,.2) 1px 1px 2px inset;-moz-box-shadow:rgba(0,0,0,.2) 1px 1px 2px inset;box-shadow:rgba(0,0,0,.2) 1px 1px 2px inset;-webkit-transition:all .4s ease-out;-moz-transition:all .4s ease-out;}
	textarea:focus{-webkit-box-shadow:rgba(0,0,0,.2) 0px 0px 8px;-moz-box-shadow:rgba(0,0,0,.2) 0px 0px 8px;box-shadow:rgba(0,0,0,.2) 0px 0px 8px;outline:none;}
</style>
<div class="wrap">
<h2>主题选项</h2>
<form method="post" action="">
	<fieldset>
	<legend><strong>SEO 代码添加</strong></legend>
		<table class="form-table">
			<tr><td>
				<strong>网站关键字</strong>
				<textarea name="keywords" id="keywords" rows="1" cols="70"><?php echo get_option('ets_keywords'); ?></textarea><br />
			</td></tr>
			<tr><td>
				<strong>网站描述</strong>
				<textarea name="description" id="description" rows="3" cols="70"><?php echo get_option('ets_description'); ?></textarea>
				<em>网站描述（Meta Description），针对搜索引擎设置的网页描述。</em>
			</td></tr>
		</table>
	</fieldset>

	<fieldset>
	<legend><strong>底部设置</strong></legend>
		<table class="form-table">
			<tr><td>
				<strong>power</strong>
				<textarea name="power" id="power" rows="3" cols="70"><?php echo get_option('ets_power'); ?></textarea><br />
				<em>power by</em>
			</td></tr>
			<tr><td>
				<strong>版权</strong>
				<textarea name="copyright" id="description" rows="3" cols="70"><?php echo get_option('ets_copyright'); ?></textarea>
				<em>底部版权信息</em>
			</td></tr>
		</table>
	</fieldset>
 
	<fieldset>
	<legend><strong>统计代码添加</strong></legend>
		<table class="form-table">
			<tr><td>
				<textarea name="analytics" id="analytics" rows="5" cols="70"><?php echo stripslashes(get_option('ets_analytics')); ?></textarea>
			</td></tr>
		</table>
	</fieldset>
 
	<p class="submit">
		<input type="submit" name="Submit" class="button-primary" value="保存设置" />
		<input type="hidden" name="ets_settings" value="save" style="display:none;" />
	</p>
</form>
</div>
<?php }