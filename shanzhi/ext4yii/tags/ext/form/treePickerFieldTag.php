<?php
$atts=self::resolveAtts($atts);
$id=self::getUUID4Tag(isset($atts['id'])?$atts['id']:null,true,self::TREEPICKERFIELD);

$xtype='apptreepicker';
$atts['rootVisible']=isset($atts['rootVisible'])?$atts['rootVisible']:"false";
$atts['rootText']=isset($atts['rootText'])?$atts['rootText']:"根节点";
if(isset($atts['rootAttribute'])){
	$atts['rootAttribute']=trim($atts['rootAttribute'],',');
}
?>
<?php ##注册事件监听器 ?>
<?php require dirname(__FILE__).'/../subvm/listeners.php';?>
<?php ##远程异步数据源 ?>
<?php if(isset($atts['url'])){?>
var <?php echo $id?>_store = Ext.create('Ext.data.TreeStore', {
<?php if(isset($atts['nodeParam'])){?>
	nodeParam : '<?php echo $atts['nodeParam']?>',
<?php }?>
	proxy : {
		type : 'ajax',
		url : '<?php echo $atts['url']?>'
	},
	root : {
	<?php if(isset($atts['rootAttribute'])){?>
	<?php ##根节点不会将请求load回来的节点附加属性作为根节点的附加属性，如果需要让根节点有附加属性则需要这里强制附加。?>
		<?php echo $atts['rootAttribute']?>,
    <?php }?>
    <?php ##只能为null，否则表单重置时候会将根节点的值重置到文本显示区域带来表单校验不该通过却通过的bug。?>
	<?php if($atts['rootVisible']=="false"){?>	
		text : null,
	<?php }else{?>
		text : '<?php echo $atts['rootText']?>',
	<?php }?>
	<?php if(isset($atts['rootIcon'])){?>
		 icon : '<?php echo self::getIcon($atts['rootIcon'])?>',
	<?php }?>
		id : '<?php echo $atts['rootId']?>',
	<?php ##必须为true,否则下拉层滚动条有问题?>
		expanded : true
	}
});	
<?php }?>

<?php ##TextField定义?>
var <?php echo $id?>_cfg = {
<?php require dirname(__FILE__).'/../common/formItemTagSupport.php';?>
<?php if(isset($atts['useArrows'])){?>
useArrows:<?php echo $atts['useArrows']?>,
<?php }?>
<?php if(isset($atts['nodeParam'])){?>
nodeParam:'<?php echo $atts['nodeParam']?>',
<?php }?>
<?php if(isset($atts['singleExpand'])){?>
singleExpand:<?php echo $atts['singleExpand']?>,
<?php }?>
<?php if(isset($atts['maxPickerHeight'])){?>
maxPickerHeight:<?php echo $atts['maxPickerHeight']?>,
<?php }?>
<?php if(isset($atts['displayField'])){?>
displayField:'<?php echo $atts['displayField']?>',
<?php }?>
<?php if(isset($atts['rootVisible'])){?>
rootVisible:<?php echo $atts['rootVisible']?>,
<?php }?>
    store:<?php echo $id?>_store,
    app:169	
};
<?php ##实例化?>
<?php if($atts['instance'] == "true"){?>
var <?php echo $id?> = Ext.create('App.ux.TreePicker',<?php echo $id?>_cfg);
<?php }?>
<?php ##组件常用事件绑定 ?>
<?php require dirname(__FILE__).'/../subvm/events.php';?>
<?php ##表单元素组件实例后设置 ?>
<?php require dirname(__FILE__).'/../subvm/afterFormFieldCreated.php';?>

