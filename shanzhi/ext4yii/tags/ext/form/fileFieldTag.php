<?php
$atts=self::resolveAtts($atts);
$id=self::getUUID4Tag(isset($atts['id'])?$atts['id']:null,true,self::FILEFIELD);

$xtype=self::FILEFIELD;
?>
<?php ##注册事件监听器 ?>
<?php require dirname(__FILE__).'/../subvm/listeners.php';?>
<?php ##FileField定义 ?>
var <?php echo $id?>_cfg = {
<?php require dirname(__FILE__).'/../common/formItemTagSupport.php';?>
<?php if(isset($atts['buttonText'])){?>
buttonText:'<?php echo $atts['buttonText']?>',
<?php }?>
    app:169	
};
<?php ##FileField实例化 ?>
var <?php echo $id?> = Ext.create('Ext.form.field.File',<?php echo $id?>_cfg);
<?php ##组件常用事件绑定 ?>
<?php require dirname(__FILE__).'/../subvm/events.php';?>
<?php ##表单元素组件实例后设置 ?>
<?php require dirname(__FILE__).'/../subvm/afterFormFieldCreated.php';?>