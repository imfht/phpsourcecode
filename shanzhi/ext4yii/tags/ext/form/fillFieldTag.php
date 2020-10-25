<?php
$atts=self::resolveAtts($atts);
$id=self::getUUID4Tag(isset($atts['id'])?$atts['id']:null,true,self::FILLFIELD);

$xtype=self::FILLFIELD;
?>
<?php ##FillField定义?>
var <?php echo $id?>_cfg = {
<?php require dirname(__FILE__).'/../common/formItemTagSupport.php';?>
    app:169	
};
<?php ##FillField实例化(使用DisplayFiled作为FillField的实例类)?>
var <?php echo $id?> = Ext.create('Ext.form.field.Display',<?php echo $id?>_cfg);

