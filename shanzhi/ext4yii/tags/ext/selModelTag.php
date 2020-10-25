<?php 
$atts=self::resolveAtts($atts);
$id=self::getUUID4Tag(isset($atts['id'])?$atts['id']:null,true,self::SELMODEL);

?>
<?php ##注册事件监听器?>
<?php require 'subvm/listeners.php';?>
<?php ##Panel实例化?>
<?php 
	$type=isset($atts['type'])?$atts['type']:'row';
	$parentTag=self::getParentTag($id);
	if($parentTag['type']==self::GRIDPANEL){
		self::addTagAttribute($parentTag['tagId'],'selModel',$id);
	}
	$classname="";
	switch ($type){
		case "row":
			$type="rowmodel";$classname="Ext.selection.RowModel";break;
		case "cell":
			$type="cellmodel";$classname="Ext.selection.CellModel";break;
		case "checkbox":
			$type="checkboxmodel";$classname="Ext.selection.CheckboxModel";break;
		default:
			$type="rowmodel";$classname="Ext.selection.RowModel";break;
	}
?>
var <?php echo $id?> = Ext.create('<?php echo $classname?>',{
<?php require 'common/extTagSupport.php';?>
<?php if(isset($atts['mode'])){?>
	mode:'<?php echo $atts['mode']?>',
<?php }?>
<?php if(isset($atts['injectCheckbox'])){?>
	injectCheckbox:'<?php echo $atts['injectCheckbox']?>',
<?php }?>
<?php ##总是允许反选?>
    allowDeselect:true,
	app: 169
});
