<?php ##停靠边栏定义?>
<?php 
$tag=self::getPairTag(self::DOCKED);
$id=$tag['tagId'];
$atts=$tag['atts'];
$xtype=isset($atts['xtype'])?$atts['xtype']:'toolbar';
$atts['height']=isset($atts['height'])?$atts['height']:'27';
?>
<?php //$id=isset($atts['id'])?$atts['id']:self::getUUID4Tag();?>
var <?php echo $id?>_cfg = {
<?php require dirname(__FILE__).'/common/containerTagSupport.php';?>
<?php if(isset($atts['dock'])){?>
	dock: '<?php echo $atts['dock']?>',
<?php }?>
<?php if(isset($atts['text'])){?>
	text: '<?php echo $atts['text']?>',
<?php }?>
<?php if(isset($atts['defaultText'])){?>
	defaultText: '<?php echo $atts['defaultText']?>',
<?php }?>
<?php if(isset($atts['ui'])){?>
	ui: '<?php echo $atts['ui']?>',
<?php }?>
    enableOverflow: true,
	app: 169
};
<?php ##停靠边栏实例化?>
<?php 
$barType = "Ext.toolbar.Toolbar";
if("pagingtoolbar"==$xtype){
	$barType = "Ext.toolbar.Paging";
}else if("statusbar" == $xtype){
	$barType = "Ext.ux.statusbar.StatusBar";
}
?>
var <?php echo $id?> = Ext.create('<?php echo $barType?>', <?php echo $id?>_cfg);
<?php ##注册Items子组件?>
<?php require dirname(__FILE__).'/subvm/items.php';?>
<?php ##组件常用事件绑定?>
<?php require dirname(__FILE__).'/subvm/events.php';?>
<?php
## 强行设置各边框。docked在容器里的时候，边框会被各种组合造成被随机覆盖。
##和其它的boders.vm机制不一样，所以这里特殊处理
?>
<?php 
$forceBorders=array();
if(isset($atts['forceBoder'])){
	$borders=explode(" ", $atts['forceBoder']);
	foreach ($borders as $i=> $size){
		if ($i==0){
			$align="top";
		}else if($i == 1){
			$align="right";
		}else if($i == 2){
			$align="bottom";
		}else if($i == 3){
			$align="left";
		}
		$forceBorders[]=array(
			"id"=>self::getUUID4Tag(),
			"size"=>$size,
			"align"=>$align
		);
	}
}
?>
<?php foreach ($forceBorders as $border){?>
	 Ext.util.CSS.createStyleSheet('#<?php echo $id?> {border-<?php echo $border['align']?>-width: <?php echo $border['size']?>px !important;}','<?php echo $border['id']?>');
<?php }?>
<?php 
if(!isset($atts['renderTo'])){
	self::addAfterRenderRegisterList2Parent($id,'dock');
	//self::$afterRenderRegisterList[]=array("id"=>$id,"type"=>'dock');
}
?>