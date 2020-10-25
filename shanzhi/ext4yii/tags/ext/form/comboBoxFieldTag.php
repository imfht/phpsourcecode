<?php
$atts=self::resolveAtts($atts);
$id=self::getUUID4Tag(isset($atts['id'])?$atts['id']:null,true,self::COMBOBOXFIELD);

$xtype=self::COMBOBOXFIELD;
$atts['forceSelection']=isset($atts['forceSelection'])?$atts['forceSelection']:"true";
$atts['editable']=isset($atts['editable'])?$atts['editable']:"false";
$atts['queryMode']=isset($atts['queryMode'])?$atts['queryMode']:"local";
$atts['displayField']=isset($atts['displayField'])?$atts['displayField']:"display";
$atts['valueField']=isset($atts['valueField'])?$atts['valueField']:"value";
$atts['fields']=isset($atts['fields'])?$atts['fields']:"[ 'value', 'display']";

$atts['autoLoad']=isset($atts['autoLoad'])?$atts['autoLoad']:"true";

if(isset($atts['url'])){
	$atts['queryMode']="remote";
}

?>
<?php ##注册事件监听器 ?>
<?php require dirname(__FILE__).'/../subvm/listeners.php';?>

<?php ##ComboBoxStore定义?>
<?php if($atts['queryMode'] == "local" && isset($atts['data'])){?>
var <?php echo $id?>_store = Ext.create('Ext.data.Store', {
	 fields : <?php echo $atts['fields']?>,
	 proxy : {
			type : 'memory',
			reader : {
				type : 'json'
			},
		},
	 data : <?php echo $atts['data']?>
	 
});
<?php }?>
<?php if($atts['queryMode'] == "remote"){?>
var <?php echo $id?>_store = Ext.create('Ext.data.Store', {
	 fields :  <?php echo $atts['fields']?>,
	 autoLoad: <?php echo $atts['autoLoad']?>,
	 proxy : {
			type : 'ajax',
			url:'<?php echo $atts['url']?>',
			reader : {
				type : 'json'
			}
		}
});
<?php }?>

<?php ##ComboBoxField定义?>
var <?php echo $id?>_cfg = {
<?php require dirname(__FILE__).'/../common/formItemTagSupport.php';?>
<?php if(isset($atts['hiddenName'])){?>
hiddenName:'<?php echo $atts['hiddenName']?>',
<?php }?>
<?php if(isset($atts['multiSelect'])){?>
multiSelect:<?php echo $atts['multiSelect']?>,
<?php }?>
<?php if(isset($atts['forceSelection'])){?>
forceSelection:<?php echo $atts['forceSelection']?>,
<?php }?>
<?php if(isset($atts['editable'])){?>
editable:<?php echo $atts['editable']?>,
<?php }?>
<?php if(isset($atts['typeAhead'])){?>
typeAhead:<?php echo $atts['typeAhead']?>,
typeAheadDelay:2000,
<?php }?>
<?php if(isset($atts['queryMode'])){?>
queryMode:'<?php echo $atts['queryMode']?>',
<?php }?>
<?php if(isset($atts['displayField'])){?>
displayField:'<?php echo $atts['displayField']?>',
<?php }?>
<?php if(isset($atts['valueField'])){?>
valueField:'<?php echo $atts['valueField']?>',
<?php }?>
    store:<?php echo $id?>_store,
    app:169	
};
<?php ##ComboBoxField实例化?>
<?php if($atts['instance'] == "true"){?>
var <?php echo $id?> = Ext.create('Ext.form.field.ComboBox',<?php echo $id?>_cfg);
<?php }?>
<?php ##组件常用事件绑定 ?>
<?php require dirname(__FILE__).'/../subvm/events.php';?>
<?php ##表单元素组件实例后设置 ?>
<?php require dirname(__FILE__).'/../subvm/afterFormFieldCreated.php';?>