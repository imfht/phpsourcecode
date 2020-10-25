<?php 
$atts['labelAlign']=isset($atts['labelAlign'])?$atts['labelAlign']:'right';
$atts['itemAnchor']=isset($atts['itemAnchor'])?$atts['itemAnchor']:'100%';
$atts['defaultType']=isset($atts['defaultType'])?$atts['defaultType']:'textfield';
?>
<?php require dirname(__FILE__).'/panelTagSupport.php';?>
    defaults : {
    <?php if(isset($atts['itemAnchor'])){?>
    anchor : '<?php echo $atts['itemAnchor']?>',
    <?php }?>
    },
	fieldDefaults: {
	<?php if(isset($atts['labelWidth'])){?>
    labelWidth : <?php echo $atts['labelWidth']?>,
    <?php }?>
	<?php if(isset($atts['labelAlign'])){?>
    labelAlign : '<?php echo $atts['labelAlign']?>',
    <?php }?>
	<?php if(isset($atts['labelSeparator'])){?>
    labelSeparator : '<?php echo $atts['labelSeparator']?>',
    <?php }?>
	<?php if(isset($atts['labelPad'])){?>
    labelPad : <?php echo $atts['labelPad']?>,
    <?php }?>
	<?php if(isset($atts['msgTarget'])){?>
    msgTarget : '<?php echo $atts['msgTarget']?>',
    <?php }?>
    },
    <?php if(isset($atts['defaultType'])){?>
    defaultType : '<?php echo $atts['defaultType']?>',
    <?php }?>