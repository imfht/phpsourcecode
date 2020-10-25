<?php
$atts=self::resolveAtts($atts);
$id=self::getUUID4Tag(isset($atts['id'])?$atts['id']:null,true,self::HIDDENFIELD);

$xtype=self::HIDDENFIELD;
?>
<?php ##注册事件监听器 ?>
<?php require dirname(__FILE__).'/../subvm/listeners.php';?>
<?php ##HiddenField定义?>
var <?php echo $id?>_cfg = {
<?php require dirname(__FILE__).'/../common/formItemTagSupport.php';?>
    app:169	
};
<?php ##HiddenField实例化?>
var <?php echo $id?> = Ext.create('Ext.form.field.Hidden',<?php echo $id?>_cfg);