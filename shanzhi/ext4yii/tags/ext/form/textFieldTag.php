<?php
$atts=self::resolveAtts($atts);
$id=self::getUUID4Tag(isset($atts['id'])?$atts['id']:null,true,self::TEXTFIELD);

$xtype=self::TEXTFIELD;
?>
<?php ##注册事件监听器 ?>
<?php require dirname(__FILE__).'/../subvm/listeners.php';?>
<?php ##TextField定义 ?>
var <?php echo $id?>_cfg = {
<?php require dirname(__FILE__).'/../common/formItemTagSupport.php';?>
<?php if(isset($atts['maxLength']) && $atts['maxLength']){?>
maxLength:<?php echo $atts['maxLength']?>,
<?php }?>
<?php if(isset($atts['minLength']) && $atts['minLength']){?>
minLength:<?php echo $atts['minLength']?>,
<?php }?>
    app:169	
};
<?php ##实例化 ?>
<?php if($atts['instance']=="true"){?>
var <?php echo $id?> = Ext.create('Ext.form.field.Text',<?php echo $id?>_cfg);
<?php }?>
<?php ##组件常用事件绑定 ?>
<?php require dirname(__FILE__).'/../subvm/events.php';?>
<?php ##表单元素组件实例后设置 ?>
<?php require dirname(__FILE__).'/../subvm/afterFormFieldCreated.php';?>