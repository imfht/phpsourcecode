<?php
$atts=self::resolveAtts($atts);
$id=self::getUUID4Tag(isset($atts['id'])?$atts['id']:null,true,self::DATEFIELD);

$xtype=self::DATEFIELD;
?>
<?php ##注册事件监听器 ?>
<?php require dirname(__FILE__).'/../subvm/listeners.php';?>

<?php ##DateField定义?>
var <?php echo $id?>_cfg = {
<?php require dirname(__FILE__).'/../common/formItemTagSupport.php';?>
<?php if(isset($atts['format'])){?>
format:'<?php echo $atts['format']?>',
<?php }?>
<?php if(isset($atts['disabledDays'])){?>
disabledDays:'<?php echo $atts['disabledDays']?>',
<?php }?>
<?php if(isset($atts['disabledDaysText'])){?>
disabledDaysText:'<?php echo $atts['disabledDaysText']?>',
<?php }?>
<?php if(isset($atts['maxValue'])){?>
maxValue:<?php echo $atts['maxValue']?>,
<?php }?>
<?php if(isset($atts['minValue'])){?>
minValue:<?php echo $atts['minValue']?>,
<?php }?>
    app:169	
};
<?php ##DateField实例化?>
<?php if($atts['instance']=="true"){?>
var <?php echo $id?> = Ext.create('Ext.form.field.Date',<?php echo $id?>_cfg);
<?php }?>
<?php ##组件常用事件绑定 ?>
<?php require dirname(__FILE__).'/../subvm/events.php';?>
<?php ##表单元素组件实例后设置 ?>
<?php require dirname(__FILE__).'/../subvm/afterFormFieldCreated.php';?>