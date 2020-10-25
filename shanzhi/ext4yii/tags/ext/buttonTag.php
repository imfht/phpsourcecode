<?php
$atts=self::resolveAtts($atts);
$id=self::getUUID4Tag(isset($atts['id'])?$atts['id']:null,true,self::BUTTON);
$xtype=isset($atts['type'])?$atts['type']:'button';
$classtype=strcasecmp($xtype, "splitbutton")==0?"Ext.button.Split":"Ext.button.Button";
?>
<?php ##注册事件监听器 ?>
<?php require dirname(__FILE__).'/subvm/listeners.php';?>

<?php ##Button定义 ?>
var <?php echo $id?>_cfg = {
<?php require dirname(__FILE__).'/common/buttonTagSupport.php';?>
<?php if(isset($atts['iconAlign'])){?>
iconAlign: '<?php echo $atts['iconAlign']?>',
<?php }?>
<?php if(isset($atts['arrowAlign'])){?>
arrowAlign: '<?php echo $atts['arrowAlign']?>',
<?php }?>
<?php if(isset($atts['textAlign'])){?>
textAlign: '<?php echo $atts['textAlign']?>',
<?php }?>
    app:169	
};
<?php ##Button实例化 ?>
var <?php echo $id?> = Ext.create('<?php echo $classtype?>', <?php echo $id?>_cfg);
<?php ##组件常用事件绑定 ?>
<?php require dirname(__FILE__).'/../subvm/events.php';?>