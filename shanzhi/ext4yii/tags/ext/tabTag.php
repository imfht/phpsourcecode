<?php 
$tag=self::getPairTag(self::TAB);
$id=$tag['tagId'];
$atts=$tag['atts'];
$xtype=self::TAB;
?>
<?php ##注册事件监听器 ?>
<?php require dirname(__FILE__).'/subvm/listeners.php';?>
<?php ##Panel定义 ?>
var <?php echo $id;?>_cfg = {
<?php require dirname(__FILE__).'/common/panelTagSupport.php';?>
	app: 169
};

<?php ##Panel实例化 ?>
var <?php echo $id;?> = Ext.create('Ext.panel.Panel',<?php echo $id;?>_cfg);
<?php ##注册Items子组件?>
<?php require dirname(__FILE__).'/subvm/items.php';?>
<?php ##组件常用事件绑定?>
<?php require dirname(__FILE__).'/subvm/events.php';?>
<?php ##处理Border?>
<?php require dirname(__FILE__).'/subvm/borders.php';?>