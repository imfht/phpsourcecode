<?php 
$tag=self::getPairTag(self::DATAVIEW);
$id=$tag['tagId'];
$atts=$tag['atts'];
$xtype=self::DATAVIEW;
?>
<?php ##注册事件监听器 ?>
<?php require dirname(__FILE__).'/subvm/listeners.php';?>
<?php ##View使用的Store ?>
<?php if (isset($atts['url']) && $atts['url']){?>
    var <?php echo $id?>_store = Ext.create('Ext.data.Store', {
        fields: <?php echo $atts['fields']?>,
        autoLoad: <?php echo $atts['autoLoad']?>,
        proxy : {
	        type : 'ajax',
	        url:'<?php echo $atts['url']?>',
	        reader : {
		        type : 'json'
	           }
        }
    });
    <?php ##绑定Store的事件 ?>
    <?php if(isset($atts['onload'])){?>
    <?php echo $id?>_store.on('load', <?php echo $atts['onload']?>);
    <?php }?>
<?php }?>
	
<?php ##View配置项定义?>
var <?php echo $id?>_cfg = {
<?php require dirname(__FILE__).'/common/componentTagSupport.php';?>
     plugins : [
     <?php if (isset($atts['animated']) && $atts['animated']=="true"){?>
 		Ext.create('Ext.ux.DataView.Animated', {
 		<?php if (isset($atts['idProperty'])){?>
 		idProperty: '<?php echo $atts['idProperty']?>',
 		<?php }?>
        duration  : <?php echo $atts['duration']?>
        })
     <?php }?>
    ], 
    <?php if(isset($atts['url']) && $atts['url']){?>
    store: <?php echo $id?>_store,
    <?php }?>		
    <?php if(isset($atts['itemSelector'])){?>
    itemSelector: '<?php echo $atts['itemSelector']?>',
    <?php }?>	
    <?php if(isset($atts['overItemCls'])){?>
    overItemCls: '<?php echo $atts['overItemCls']?>',
    <?php }?>		
	multiSelect: <?php echo $atts['multiSelect']?>,
	app: 169
};
<?php ##View实例化?>
var <?php echo $id?> = Ext.create('Ext.view.View', <?php echo $id?>_cfg);
<?php ##组件常用事件绑定?>
<?php require dirname(__FILE__).'/subvm/events.php';
