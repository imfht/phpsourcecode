<?php 
$atts=self::resolveAtts($atts);
$id=self::getUUID4Tag(isset($atts['id'])?$atts['id']:null,true,self::IFRAME);
$xtype=self::IFRAME;
?>
<?php ##注册事件监听器 ?>
<?php require dirname(__FILE__).'/subvm/listeners.php';?>
<?php ##Panel定义 ?>
var <?php echo $id?>_cfg = {
<?php require dirname(__FILE__).'/common/panelTagSupport.php';?>
<?php if(isset($atts['loadMask'])){?>
loadMask: '<?php echo $atts['loadMask']?>',
<?php }?>
<?php if(isset($atts['src'])){?>
src: '<?php echo $atts['src']?>',
<?php }?>
<?php if(isset($atts['mask'])){?>
mask: <?php echo $atts['mask']?>,
<?php }?>
app: 169
};
<?php ##IFrame实例化 ?>
var <?php echo $id?> = Ext.create('App.ux.IFrame',<?php echo $id?>_cfg);
<?php ##组件常用事件绑定 ?>
<?php require dirname(__FILE__).'/subvm/events.php';?>
<?php ##处理Border ?>
<?php require dirname(__FILE__).'/subvm/borders.php';?>