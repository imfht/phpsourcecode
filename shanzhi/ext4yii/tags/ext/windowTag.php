<?php 
$tag=self::getPairTag(self::WINDOW);
$id=$tag['tagId'];
$atts=$tag['atts'];
$xtype=self::WINDOW;
?>
<?php ##注册事件监听器 ?>
<?php require dirname(__FILE__).'/subvm/listeners.php';?>
<?php ##Window定义 ?>
var <?php echo $id?>_cfg = {
<?php require dirname(__FILE__).'/common/panelTagSupport.php';?>
<?php if(isset($atts['maximizable'])){?>
maximizable: <?php echo $atts['maximizable']?>,
<?php }?>
<?php if(isset($atts['maximized'])){?>
maximized: <?php echo $atts['maximized']?>,
<?php }?>
<?php if(isset($atts['minimizable'])){?>
minimizable: <?php echo $atts['minimizable']?>,
<?php }?>
<?php if(isset($atts['headerPosition'])){?>
headerPosition: <?php echo $atts['headerPosition']?>,
<?php }?>
<?php if(isset($atts['modal'])){?>
modal: <?php echo $atts['modal']?>,
<?php }?>
<?php if(isset($atts['draggable'])){?>
draggable: <?php echo $atts['draggable']?>,
<?php }?>
<?php if(isset($atts['animateTarget'])){?>
animateTarget: Ext.get('<?php echo $atts['animateTarget']?>'),
<?php }?>
	app: 169
};
<?php ##Window实例化?>
var <?php echo $id?> = Ext.create('Ext.window.Window',<?php echo $id?>_cfg);
<?php ##注册Items子组件?>
<?php require dirname(__FILE__).'/subvm/items.php';?>
<?php ##组件常用事件绑定?>
<?php require dirname(__FILE__).'/subvm/events.php';?>
<?php ##处理Border?>
<?php require dirname(__FILE__).'/subvm/borders.php';?>
