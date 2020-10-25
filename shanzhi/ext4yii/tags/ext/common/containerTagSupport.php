<?php require 'componentTagSupport.php';?>
<?php if(isset(self::$layoutconfigs) && is_array(self::$layoutconfigs) && count(self::$layoutconfigs)){?>
    layout:{
    <?php foreach (self::$layoutconfigs as $config){?>
    	<?php echo $config['key']?>: <?php echo $config['value']?>,
    <?php }?>
	<?php if(isset($atts['layout']) && $atts['layout']=="table"){?>
		tableAttrs : {
						style : {
							width : '100%'
						}
					},
	<?php }?>
	app: 169
	},
<?php }elseif(isset($atts['layout'])){?>
	 layout:'<?php echo $atts['layout']?>',
<?php }?>
<?php if (isset($atts['split'])){?>
	 split: <?php echo $atts['split']?>,
<?php }?>
<?php if (isset($atts['anchor'])){?>
	 anchor: '<?php echo $atts['anchor']?>',
<?php }?>
<?php if (isset($atts['x'])){?>
	 x: <?php echo $atts['x']?>,
<?php }?>
<?php if (isset($atts['y'])){?>
	 y: <?php echo $atts['y']?>,
<?php }?>
<?php if (isset($atts['rowspan'])){?>
	 rowspan: <?php echo $atts['rowspan']?>,
<?php }?>
<?php if (isset($atts['colspan'])){?>
	 colspan: <?php echo $atts['colspan']?>,
<?php }?>
<?php if (isset($atts['flex'])){?>
	 flex: <?php echo $atts['flex']?>,
<?php }?>
<?php if (isset($atts['constrain'])){?>
	 constrain: <?php echo $atts['constrain']?>,
<?php }?>
<?php if (isset($atts['loaderInit'])){?>
	 loaderInit: {},
<?php }?>
<?php if (isset($atts['autoShow']) && $atts['autoShow']=="true"){?>
	 autoRender:true,
	 autoShow:true,
<?php }?>