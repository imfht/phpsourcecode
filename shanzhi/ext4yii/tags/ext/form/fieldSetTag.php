<?php 
$tag=self::getPairTag(self::FIELDSET);
$id=$tag['tagId'];
$atts=$tag['atts'];
$xtype=self::FIELDSET;
?>
<?php ##注册事件监听器 ?>
<?php require dirname(__FILE__).'/../subvm/listeners.php';?>

<?php ##FieldSet定义 ?>
var <?php echo $id ?>_cfg = {
<?php require dirname(__FILE__).'/../common/formPanelTagSupport.php';?>
<?php if (isset($atts['checkboxToggle'])){?>
checkboxToggle : <?php echo $atts['checkboxToggle']?>,
<?php }?>
<?php if (isset($atts['checkboxName'])){?>
checkboxName : <?php echo $atts['checkboxName']?>,
<?php }?>

<?php ##如果没设置padding，则为FIeldSet设置一个固定的下边距。和上边距匹配对称。缺省情况下，上下边距不对称。?>
<?php if(!isset($atts['padding'])){?>
padding : '0 0 3 0',
<?php }?>
	app: 169
};
<?php ##FieldSet实例化?>
var <?php echo $id ?> = Ext.create('Ext.form.FieldSet',<?php echo $id ?>_cfg);
<?php ##注册Items子组件?>
<?php require dirname(__FILE__).'/../subvm/items.php';?>
<?php ##组件常用事件绑定?>
<?php require dirname(__FILE__).'/../subvm/events.php';?>
<?php ##处理Border?>
<?php require dirname(__FILE__).'/../subvm/borders.php';?>