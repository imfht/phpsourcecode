<?php
$atts=self::resolveAtts($atts);
$id=self::getUUID4Tag(isset($atts['id'])?$atts['id']:null,true,self::NUMBERFIELD);

$xtype=self::NUMBERFIELD;
?>
<?php ##注册事件监听器 ?>
<?php require dirname(__FILE__).'/../subvm/listeners.php';?>

<?php ##NumberField定义?>
var <?php echo $id?>_cfg = {
<?php require dirname(__FILE__).'/../common/formItemTagSupport.php';?>
<?php if(isset($atts['maxValue'])){?>
maxValue:<?php echo $atts['maxValue']?>,
<?php }?>
<?php if(isset($atts['minValue'])){?>
minValue:<?php echo $atts['minValue']?>,
<?php }?>
<?php if(isset($atts['step'])){?>
step:<?php echo $atts['step']?>,
<?php }?>
<?php if(isset($atts['hideTrigger'])){?>
hideTrigger:<?php echo $atts['hideTrigger']?>,
<?php }?>
<?php if(isset($atts['keyNavEnabled'])){?>
keyNavEnabled:<?php echo $atts['keyNavEnabled']?>,
<?php }?>
<?php if(isset($atts['mouseWheelEnabled'])){?>
mouseWheelEnabled:<?php echo $atts['mouseWheelEnabled']?>,
<?php }?>
<?php if(isset($atts['allowDecimals'])){?>
allowDecimals:<?php echo $atts['allowDecimals']?>,
<?php }?>
<?php if(isset($atts['decimalPrecision'])){?>
decimalPrecision:<?php echo $atts['decimalPrecision']?>,
<?php }?>
    app:169	
};
<?php ##NumberField实例化?>
<?php if($atts['instance']=="true"){?>
var <?php echo $id?> = Ext.create('Ext.form.field.Number',<?php echo $id?>_cfg);
<?php }?>
<?php ##组件常用事件绑定 ?>
<?php require dirname(__FILE__).'/../subvm/events.php';?>
<?php ##表单元素组件实例后设置 ?>
<?php require dirname(__FILE__).'/../subvm/afterFormFieldCreated.php';?>