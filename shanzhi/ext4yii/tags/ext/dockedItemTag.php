<?php 
$atts=self::resolveAtts($atts);
$id=self::getUUID4Tag(isset($atts['id'])?$atts['id']:null,true,self::DOCKEDITEM);

?>
<?php 
$xtype=isset($atts['xtype'])?$atts['xtype']:'button';
?>
<?php ##注册事件监听器?>
<?php require dirname(__FILE__).'/subvm/listeners.php';?>
<?php ##BarItem定义?>
var <?php echo $id?> = {
<?php require dirname(__FILE__).'/common/buttonTagSupport.php';?>
    app:169	
};