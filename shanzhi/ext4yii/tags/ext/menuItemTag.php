<?php ## MenuItem定义 ---- 普通menu---不带子菜单?>
<?php 
$atts=self::resolveAtts($atts);
$id=self::getUUID4Tag(isset($atts['id'])?$atts['id']:null,true,self::MENUITEM);
$xtype=isset($atts['xtype'])?$atts['xtype']:self::MENUITEM;
$parentTag=self::getParentTag($id);
if($parentTag['type']==self::MENU){
	$parentXalias="menu";
}
if($parentTag['type']==self::MENUITEM){
	self::addTagAttribute($parentTag['tagId'],'subMenuItemId');
	$subMenuId = $parentTag['tagId'] + "_sub";
}
?>
var <?php echo $id?>_cfg =  {
<?php require 'common/buttonTagSupport.php';?>
<?php if(isset($atts['plain'])){?>
plain : <?php echo $atts['plain']?>,
<?php }?>
    app:169	
};
<?php if($parentXalias == "menu"){
self::addTagParams($parentTag['tagId'],'menus',$id);
}?>