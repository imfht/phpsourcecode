<?php 
$tag=self::getPairTag(self::FORMPANEL);
$id=$tag['tagId'];
$atts=$tag['atts'];
$xtype=self::FORMPANEL;
?>
<?php ##注册事件监听器 ?>
<?php require dirname(__FILE__).'/../subvm/listeners.php';?>
<?php ##FormPanel定义 ?>
var <?php echo $id?>_cfg = {
<?php require dirname(__FILE__).'/../common/formPanelTagSupport.php';?>
	app: 169
};
<?php ##FormPanel实例化?>
var <?php echo $id?> = Ext.create('Ext.form.Panel',<?php echo $id?>_cfg);
<?php ##注册Items子组件?>
<?php require dirname(__FILE__).'/../subvm/items.php';?>
<?php ##组件常用事件绑定?>
<?php require dirname(__FILE__).'/../subvm/events.php';?>
<?php ##处理Border?>
<?php require dirname(__FILE__).'/../subvm/borders.php';?>
