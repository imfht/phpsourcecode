<?php require 'extTagSupport.php';?>
<?php if (isset($atts['region'])){?>
	 region: '<?php echo $atts['region']?>',
<?php }?>
<?php if (isset($atts['renderTo'])){?>
	 renderTo: <?php echo self::getRenderTo($atts['renderTo'])?>,
<?php }?>
<?php if (isset($atts['width']) && !empty($atts['width'])){?>
	 width: <?php echo self::getMyWidth($atts['width'])?>,
<?php }?>
<?php if (isset($atts['height']) && !empty($atts['height'])){?>
	 height: <?php echo self::getMyHeight($atts['height'])?>,
<?php }?>
<?php if (isset($atts['frame'])){?>
	 frame: <?php echo $atts['frame']?>,
<?php }?>
<?php if (isset($atts['border'])){?>
	 border: <?php echo $atts['border']?>,
<?php }?>
<?php if (isset($atts['autoScroll'])){?>
	 autoScroll: <?php echo $atts['autoScroll']?>,
<?php }?>
<?php if (isset($atts['margin'])){?>
	 margin: '<?php echo $atts['margin']?>',
<?php }?>
<?php if (isset($atts['contentEl'])){?>
	 contentEl: <?php echo $atts['contentEl']?>,
<?php }?>
<?php if (isset($atts['html'])){?>
	 html: '<?php echo $atts['html']?>',
<?php }?>
<?php if (isset($atts['iconCls'])){?>
	 iconCls: '<?php echo $atts['iconCls']?>',
<?php }?>
<?php if (isset($atts['icon'])){?>
	 icon: '<?php echo self::getIcon($atts['icon'])?>',
<?php }?>
<?php if (isset($atts['style']) && $atts['style']){?>
	 style: <?php echo $atts['style']?>,
<?php }?>
<?php if (isset($atts['padding'])){?>
	 padding: '<?php echo $atts['padding']?>',
<?php }?>
<?php if (isset($atts['disabled'])){?>
	 disabled: <?php echo $atts['disabled']?>,
<?php }?>
<?php if (isset($atts['tooltip'])){?>
	 tooltip: '<?php echo $atts['tooltip']?>',
<?php }?>
<?php if (isset($atts['columnWidth'])){?>
	 columnWidth: <?php echo $atts['columnWidth']?>,
<?php }?>
<?php if (isset($atts['animate'])){?>
	 animate: <?php echo $atts['animate']?>,
<?php }?>
<?php if (isset($atts['maxWidth'])){?>
	 maxWidth: <?php echo self::getMyWidth($atts['maxWidth'])?>,
<?php }?>
<?php if (isset($atts['minWidth'])){?>
	 minWidth: <?php echo $atts['minWidth']?>,
<?php }?>
<?php if (isset($atts['maxHeight'])){?>
	 maxHeight: <?php echo self::getMyHeight($atts['maxHeight'])?>,
<?php }?>
<?php if (isset($atts['minHeight'])){?>
	 minHeight: <?php echo $atts['minHeight']?>,
<?php }?>
<?php if (isset($atts['resizable'])){?>
	 resizable: <?php echo $atts['resizable']?>,
<?php }?>
<?php if (isset($atts['tpl'])){?>
	 tpl: <?php echo $atts['tpl']?>,
<?php }?>
<?php if (isset($atts['plugins'])){?>
	 plugins: <?php echo $atts['plugins']?>,
<?php }?>