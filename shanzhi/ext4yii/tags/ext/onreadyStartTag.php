<?php 
	$extPath=self::getExtPath();
?>
<?php ##导入相关插件依赖的CSS资源?>
<?php if(isset($atts['toggle']) && $atts['toggle']="true"){?>
<link rel="stylesheet" type="text/css" href="<?php echo $extPath?>/weblib/ext/ux/toggleslide/css/style.css" />
<?php }?>

<script type="text/javascript">
<?php ##导入Ext扩展依赖资源 ?>
Ext.Loader.setConfig({  
   enabled: true,  
   paths : { 
      'Ext.ux' : '<?php echo $extPath?>/weblib/ext/ux',
      'App.ux' : '<?php echo $extPath?>/weblib/myux'
   }  
});
<?php ##本来可以不需要显式导入这些依赖资源，Ext会依赖加载。但还是建议显式导入更好。细节不赘述，我说好就是好 : )。?>
Ext.require('App.ux.Notification');
<?php if(isset($atts['statusBar']) && $atts['statusBar']== "true"){?>
Ext.require('Ext.ux.statusbar.StatusBar');
<?php }?>
<?php if(isset($atts['pagingMemoryProxy']) && $atts['pagingMemoryProxy']== "true"){?>
Ext.require('Ext.ux.data.PagingMemoryProxy');
<?php }?>
<?php if(isset($atts['iframe']) && $atts['iframe']== "true"){?>
Ext.require('App.ux.IFrame');
<?php }?>
<?php if(isset($atts['treePicker']) && $atts['treePicker']== "true"){?>
Ext.require('App.ux.TreePicker');
<?php }?>
<?php if(isset($atts['dataView']) && $atts['dataView']== "true"){?>
Ext.require('Ext.ux.DataView.Animated');
<?php }?>
<?php if(isset($atts['toggle']) && $atts['toggle']="true"){?>
Ext.require([ 'Ext.ux.form.field.ToggleSlide']);
Ext.require([ 'Ext.ux.toggleslide.ToggleSlide']);
<?php }?>

<?php 
##=====Ajax的全局设定开始
##将缺省超时时间设置为120s
?>
Ext.Ajax.timeout = 120000;

<?php ##监听请求异常回调 ?>
Ext.Ajax.on('requestexception', function(conn, response, options, eopts) {
	if (response.status === 500) {
		<?php ##服务器异常 ?>
		Ext.Msg.alert('提示', response.responseText);
	} else if (response.status === 0) {
		<?php ##网络异常或服务器关闭 ?>
		Ext.Msg.alert('提示', response.responseText);
	} else {
	    <?php ##其它异常 ?>
		Ext.Msg.alert('提示', response.responseText);
	}
});
<?php  ##=====Ajax的全局设定结束?>

<?php ##表单必录项的高亮提示 ?>
var x_field_required = '<span class="mustinput-label">*</span>';

<?php ##Ext组件开始实例化?>
Ext.onReady(function(){
Ext.QuickTips.init();

<?php ##TODO 此属性改为可配置 ?>
Ext.form.Field.prototype.msgTarget = 'under';

<?php ##屏蔽浏览器右键 ?>
<?php if(isset($atts['preventDefault']) && $atts['preventDefault'] == "true"){?>
Ext.getDoc().on("contextmenu", function(e){
    e.preventDefault();
});
<?php }?>
