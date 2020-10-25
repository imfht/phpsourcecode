<?php
$atts=self::resolveAtts($atts);
$id=self::getUUID4Tag(isset($atts['id'])?$atts['id']:null,true,self::DISPLAYFIELD);

$xtype=self::DISPLAYFIELD;
if(isset($atts['value'])){
	$atts['value']="<div style=\"line-height:18px;\">{$atts['value']}</div>";
}
?>
<?php ##注册事件监听器 ?>
<?php require dirname(__FILE__).'/../subvm/listeners.php';?>
<?php ##DisplayField定义 ?>
var <?php echo $id?>_cfg = {
<?php require dirname(__FILE__).'/../common/formItemTagSupport.php';?>
    app:169	
};
<?php ##DisplayField实例化 ?>
var <?php echo $id?> = Ext.create('Ext.form.field.Display',<?php echo $id?>_cfg);
<?php ##组件常用事件绑定 ?>
<?php require dirname(__FILE__).'/../subvm/events.php';?>
