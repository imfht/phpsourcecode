<?php 
$atts=self::resolveAtts($atts);
$id=self::getUUID4Tag(isset($atts['id'])?$atts['id']:null,true,self::TOOL);
$xtype=isset($atts['type'])?$atts['type']:'';
$atts['tooltip']=isset($atts['tooltip'])?$atts['tooltip']:'';
$atts['onclick']=self::getMyOnclick(isset($atts['onclick'])?$atts['onclick']:'');

?>
<?php ##Tool定义?>
var <?php echo $id?> = {
<?php require dirname(__FILE__).'/common/extTagSupport.php';?>
<?php if(isset($atts['onclick']) && $atts['onclick']){?>
callback: <?php echo $atts['onclick']?>,
<?php }?>
    tooltip: '<?php echo $atts['tooltip']?>',
    type: '<?php echo $atts['type']?>',
    app:169
};