<?php 
$tag=self::getPairTag(self::VIEWPORT);
$id=$tag['tagId'];
$atts=$tag['atts'];
$xtype=self::VIEWPORT;
?>
<?php //$id=isset($atts['id'])?$atts['id']:self::getUUID4Tag();?>
<?php ##注册事件监听器 ?>
<?php require dirname(__FILE__).'/subvm/listeners.php';?>
<?php ##Viewport定义?>
var <?php echo $id?>_cfg = {
<?php require dirname(__FILE__).'/common/containerTagSupport.php';?>
	app: 169
};
<?php ##Viewport实例化?>
var <?php echo $id?> = Ext.create('Ext.container.Viewport',<?php echo $id?>_cfg);
<?php ##注册Items子组件?>
<?php require dirname(__FILE__).'/subvm/items.php';?>
<?php ##组件常用事件绑定?>
<?php require dirname(__FILE__).'/subvm/events.php';?>
