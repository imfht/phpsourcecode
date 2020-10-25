<?php 
$atts=self::resolveAtts($atts);
$id=self::getUUID4Tag(isset($atts['id'])?$atts['id']:null,true,self::COMPONENT);
$xtype=self::COMPONENT;
?>
<?php ##注册事件监听器 ?>
<?php require dirname(__FILE__).'/subvm/listeners.php';?>

<?php ##Component定义?>
var <?php echo $id?>_cfg = {
<?php require dirname(__FILE__).'/common/componentTagSupport.php';?>
	app: 169
};
<?php ##Component实例化 ?>
var <?php echo $id?> = Ext.create('Ext.Component', <?php echo $id?>_cfg);
<?php ##组件常用事件绑定 ?>
<?php require dirname(__FILE__).'/../subvm/events.php';?>