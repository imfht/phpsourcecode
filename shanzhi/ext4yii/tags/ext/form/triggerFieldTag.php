<?php 
$atts=self::resolveAtts($atts);
$id=self::getUUID4Tag(isset($atts['id'])?$atts['id']:null,true,self::TRIGGERFIELD); 
$xtype=self::TRIGGERFIELD;
?>
<?php ##注册事件监听器 ?>
<?php require dirname(__FILE__).'/../subvm/listeners.php';?>
<?php ##TextField定义?>
var <?php echo $id?>_cfg = {
<?php require dirname(__FILE__).'/../common/formItemTagSupport.php';?>
<?php if(isset($atts['maxLength'])){?>
    maxLength:<?php echo $atts['maxLength']?>,
<?php }?>
<?php if(isset($atts['minLength'])){?>
    minLength:<?php echo $atts['minLength']?>,
<?php }?>
<?php if(isset($atts['trigger1Cls'])){?>
    trigger1Cls:'<?php echo $atts['trigger1Cls']?>',
<?php }?>
<?php if(isset($atts['trigger2Cls'])){?>
    trigger2Cls:'<?php echo $atts['trigger2Cls']?>',
<?php }?>
<?php if(isset($atts['trigger3Cls'])){?>
    trigger3Cls:'<?php echo $atts['trigger3Cls']?>',
<?php }?>
<?php if(isset($atts['onTrigger1Click'])){?>
    onTrigger1Click:<?php echo $atts['onTrigger1Click']?>,
<?php }?>
<?php if(isset($atts['onTrigger2Click'])){?>
    onTrigger2Click:<?php echo $atts['onTrigger2Click']?>,
<?php }?>
<?php if(isset($atts['onTrigger3Click'])){?>
    onTrigger3Click:<?php echo $atts['onTrigger3Click']?>,
<?php }?>
    app:169	
};
<?php ##实例化?>
<?php $instance=isset($attr['instance'])?$attr['instance']:"true"; ?>
<?php if($instance == "true"){?>
var <?php echo $id?> = Ext.create('Ext.form.field.Trigger',<?php echo $id?>_cfg);
<?php }?>
<?php ##组件常用事件绑定?>
<?php require dirname(__FILE__).'/../subvm/events.php';?>
<?php ##表单元素组件实例后设置?>
<?php require dirname(__FILE__).'/../subvm/afterFormFieldCreated.php';?>