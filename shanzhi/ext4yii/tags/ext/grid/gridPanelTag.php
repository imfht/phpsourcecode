<?php 
$tag=self::getPairTag(self::GRIDPANEL);
$id=$tag['tagId'];
$fields=isset($tag['fields'])?$tag['fields']:array();
$columns=isset($tag['columns'])?$tag['columns']:array();
$atts=$tag['atts'];
$xtype=self::GRIDPANEL;
?>
<?php ##注册事件监听器 ?>
<?php require dirname(__FILE__).'/../subvm/listeners.php';?>

<?php ##Store.Fields ?>
var <?php echo $id?>_store_fields = [
<?php foreach($fields as $velocityCount=> $fielddto){?>
<?php if(isset($fielddto['dataIndex'])){?>
{
<?php if(isset($fielddto['dataIndex'])){?>
name: '<?php echo $fielddto['dataIndex']?>',
<?php }?>
<?php ##date列要对在此进行一次时分秒格式化，然后再在column里格式化成自己想要的格式。?>
<?php ##如果不设置，则在FF和IE下进行column格式化时，将被错误的格式化为：0NaN-NaN-NaN。或者在服务器格式化后以文本列输出也是可以的。?>
<?php if(isset($fielddto['type']) && $fielddto['type'] == "date"){?>
type:'date',
dateFormat : 'Y-m-d H:i:s',
<?php }else{?>
type:'auto',
<?php }?>
<?php if(isset($fielddto['defaultValue'])){?>
defaultValue:'<?php echo $fielddto['defaultValue']?>',
<?php }?>
app:169
}<?php if($velocityCount+1 != count($fields)){?>,<?php }?>
<?php }?>
<?php }?>
];

<?php ##Store定义?>
var <?php echo $id?>_store = Ext.create('Ext.data.Store', {
 fields : <?php echo $id?>_store_fields,
 pageSize:<?php echo isset($atts['pageSize'])?$atts['pageSize']:'25'?>,
 <?php if(isset($atts['autoLoad'])){?>
 autoLoad: <?php echo $atts['autoLoad']?>,
 <?php }?>
 <?php if(isset($atts['pageType']) && $atts['pageType'] == "client"){?>
 proxy : {
type : 'pagingmemory'
}
<?php }else{?>
proxy : {
    type : 'ajax',
    url:'<?php echo isset($atts['url'])?$atts['url']:''?>',
    reader : {
    type : 'json',
    totalProperty: '<?php echo isset($atts['totalProperty'])?$atts['totalProperty']:'total'?>', 
    <?php if(isset($atts['idProperty'])){?>
idProperty: '<?php echo $atts['idProperty']?>',
             <?php }?>
root: '<?php echo isset($atts['root'])?$atts['root']:'rows'?>'
    }
}
    <?php }?>
});

<?php ##绑定Store的事件?>
<?php if(isset($atts['onload'])){ ?>
<?php echo $id?>_store.on('load', <?php echo $atts['onload']?>);
<?php }?>

<?php ##Grid.Columns?>
var <?php echo $id?>_grid_columns = [
<?php foreach ($columns as $velocityCount=> $columndto){?>
{
id:'<?php echo $columndto['id']?>',
stopSelection:false,
<?php if(isset($columndto['width'])){?>
width:<?php echo self::getMyWidth($columndto['width'])?>,
<?php }?>
<?php if(isset($columndto['header'])){?>
header:'<span class="app-grid-column-header"><?php echo $columndto['header']?></span>',
<?php }?>
<?php if(isset($columndto['dataIndex'])){?>
dataIndex:'<?php echo $columndto['dataIndex']?>',
<?php }?>
<?php if(isset($columndto['hidden'])){?>
hidden:<?php echo $columndto['hidden']?>,
<?php }?>
<?php if(isset($columndto['hideable'])){?>
hideable:<?php echo $columndto['hideable']?>,
<?php }?>
<?php if(isset($columndto['sortable'])){?>
sortable:<?php echo $columndto['sortable']?>,
<?php }?>
<?php if(isset($columndto['flex'])){?>
flex:<?php echo $columndto['flex']?>,
<?php }?>
<?php if(isset($columndto['rendererField']) && $columndto['rendererField']){?>
<?php $columndto['rendererField']=json_decode($columndto['rendererField'],true);?>
renderer:function(value,metaData,record,rowIndex ,colIndex ,store){
if(Ext.isEmpty(value)) return '';
<?php if(count($columndto['rendererField'])){?>
<?php if(isset($columndto['dataType']) && $columndto['dataType'] == "number"){?>
	<?php foreach ($columndto['rendererField'] as $key=>$val){?>
	if(value === <?php echo $key?>) return '<?php echo $val?>';
	<?php }?>
<?php }else{?>
	<?php foreach ($columndto['rendererField'] as $key=>$val){?>
	if(value === '<?php echo $key?>') return '<?php echo $val?>';
	<?php }?>
<?php }?>
<?php }?>
return value;
},
<?php }?>
<?php if((isset($columndto['rendererFn']) || isset($columndto['celltip'])) && !isset($columndto['rendererField'])){?>
renderer:function(value,metaData,record,rowIndex ,colIndex ,store){
<?php if(isset($columndto['celltip'])){?>
metaData.tdAttr = 'data-qtip="'+value+'"';
<?php }?>
<?php if(isset($columndto['rendererFn'])){?>
return <?php echo $columndto['rendererFn']?>(value,metaData,record,rowIndex ,colIndex ,store);
<?php }else{?>
return value;
<?php }?>
},
<?php }?>
<?php if(isset($columndto['format'])){?>
format:'<?php echo $columndto['format']?>',
<?php }?>
<?php if(isset($columndto['align'])){?>
align:'<?php echo $columndto['align']?>',
<?php }?>
<?php if(isset($columndto['editor'])&& !isset($columndto['editor2'])){?>
editor:'<?php echo $columndto['editor']?>',
<?php }?>
<?php if(isset($columndto['editor2'])){?>
editor:<?php echo $columndto['editor2']?>,
<?php }?>
<?php if(isset($columndto['lockable'])){?>
lockable:<?php echo $columndto['lockable']?>,
<?php }?>
<?php if(isset($columndto['locked'])){?>
locked:<?php echo $columndto['locked']?>,
<?php }?>
<?php if(isset($columndto['maxWidth'])){?>
maxWidth:<?php echo $columndto['maxWidth']?>,
<?php }?>
<?php if(isset($columndto['minWidth'])){?>
minWidth:<?php echo $columndto['minWidth']?>,
<?php }?>
<?php if(isset($columndto['any'])){?>
<?php echo $columndto['any']?>,
<?php }?>
<?php if(isset($columndto['type'])&& $columndto['type'] == "action"){?>
<?php $actionDtos=$columndto['actionDtos'];?>
items: [
 <?php foreach($actionDtos as $i=> $actionDto){?>
<?php if($i+1 != "1"){?>
  '-',
<?php }?>
{
<?php if(isset($actionDto['icon'])){?>
icon: '<?php echo self::getIcon($actionDto['icon'])?>',
<?php }?>
<?php if(isset($actionDto['tooltip'])){?>
tooltip: '<?php echo $actionDto['tooltip']?>',
<?php }?>
<?php if(isset($actionDto['handler'])){?>
handler: <?php echo $actionDto['handler']?>,
<?php }?>
 app:169
}<?php if($i+1 != count($actionDtos)){?>,<?php }?>
 <?php }?>
],
<?php }?>
 xtype: '<?php echo isset($columndto['xtype'])?$columndto['xtype']:'gridcolumn'?>'
<?php if(isset($columndto['tpl'])){?>
,tpl:'<?php echo $columndto['tpl']?>'
<?php }?>
}<?php if($velocityCount+1 != count($columns)){?>,<?php }?>
    <?php }?>
];
<?php ##Grid定义?>
var <?php echo $id?>_cfg = {
<?php require dirname(__FILE__).'/../common/panelTagSupport.php';?>
<?php if(isset($atts['enableLocking'])){?>
enableLocking:<?php echo $atts['enableLocking']?>,
<?php }?>
<?php if(isset($atts['disableSelection'])){?>
disableSelection:<?php echo $atts['disableSelection']?>,
<?php }?>
<?php if(isset($atts['forceFit'])){?>
forceFit:<?php echo $atts['forceFit']?>,
<?php }?>
<?php if(isset($atts['columnLines'])){?>
columnLines:<?php echo $atts['columnLines']?>,
<?php }?>
<?php if(isset($atts['rowLines'])){?>
rowLines:<?php echo $atts['rowLines']?>,
<?php }?>
<?php if(isset($atts['selModel'])){?>
selModel:<?php echo $atts['selModel']?>,
<?php }?>
store: <?php echo $id?>_store,
columns:<?php echo $id?>_grid_columns,
viewConfig: {
<?php if(isset($atts['stripeRows'])){?>
stripeRows: <?php echo $atts['stripeRows']?>
<?php }?>
},<?php if(!isset($atts['hidePagebar']) ||  $atts['hidePagebar'] != "true"){?>
dockedItems: [{
xtype: 'pagingtoolbar',
store: <?php echo $id?>_store,
dock: '<?php echo isset($atts['pagebardock'])?$atts['pagebardock']:'bottom'?>',
displayInfo: true
}],
<?php }?>
app: 169
};

<?php ##Grid实例化?>
var <?php echo $id?> = Ext.create('Ext.grid.Panel',<?php echo $id?>_cfg);

<?php ##组件常用事件绑定?>
<?php require dirname(__FILE__).'/../subvm/events.php';?>

<?php ##处理Border?>
<?php require dirname(__FILE__).'/../subvm/borders.php';?>