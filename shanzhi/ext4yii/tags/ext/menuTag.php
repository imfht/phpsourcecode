<?php 
$tag=self::getPairTag(self::MENU);
$id=$tag['tagId'];
$atts=$tag['atts'];
$xtype=self::MENU;
?>
<?php ##注册事件监听器?>
<?php require 'subvm/listeners.php';?>
<?php ##菜单定义?>
var <?php echo $id?>_cfg = {
<?php require 'common/panelTagSupport.php';?>
<?php if(isset($atts['plain'])){?>
plain : <?php echo $atts['plain']?>,
<?php }?>
<?php if(isset($atts['floating'])){?>
floating : <?php echo $atts['floating']?>,
<?php }?>
	app:169
};
<?php ##菜单实例化?>
var <?php echo $id?> = Ext.create('Ext.menu.Menu', <?php echo $id?>_cfg);
<?php ##组件常用事件绑定?>
<?php require 'subvm/events.php';?>
<?php if(isset($tag['menus']) && is_array($tag['menus']) && count($tag['menus'])){?>
<?php foreach ($tag['menus'] as $mid){?>
<?php echo $id?>.add(<?php echo $mid?>_cfg);
<?php }?>
<?php }?>
<?php if(!isset($tag['renderTo'])){
	$parentTag=self::getParentTag($id);
	if((isset($atts['floating']) && $atts['floating']=="false")){
		self::addItem2Parent($tag);
	}elseif(strtolower(substr($parentTag['type'],-5))=="panel"){
		self::addTagAttribute($parentTag['tagId'],'contextMenuID',$id);
	}
}?>