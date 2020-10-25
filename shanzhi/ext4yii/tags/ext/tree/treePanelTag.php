<?php 
$tag=self::getPairTag(self::TREEPANEL);
$id=$tag['tagId'];
$fields=isset($tag['fields'])?$tag['fields']:array();
$columns=isset($tag['columns'])?$tag['columns']:array();
$atts=$tag['atts'];
$xtype=self::TREEPANEL;
$fields=isset($tag['fields'])?$tag['fields']:array();
$columns=isset($tag['columns'])?$tag['columns']:array();
?>
<?php ##注册事件监听器 ?>
<?php require dirname(__FILE__).'/../subvm/listeners.php';?>

<?php ##客户端数据源?>
<?php if(!isset($atts['url'])){?>
var <?php echo $id?>_store = Ext.create('Ext.data.TreeStore', {
  <?php ##从node标签生成的Json?>
  <?php if(isset($atts['rootJson'])){?>
      root: <?php echo $atts['rootJson']?>
  <?php }?>
});
<?php }?>

<?php ##TreeGrid.Store.Fields?>
<?php if (isset($atts['treegrid'])=="true"){?>
var <?php echo $id?>_treegrid_store_fields = [
<?php foreach ($fields as $i=> $fielddto){?>
        {
	<?php if(isset($fielddto['name'])){?>
	name: '<?php echo $fielddto['name']?>',
	<?php }?>
	<?php ##date列要对在此进行一次时分秒格式化，然后再在column里格式化成自己想要的格式。?>
	<?php ##如果不设置，则在FF和IE下进行column格式化时，将被错误的格式化为：0NaN-NaN-NaN。或者在服务器格式化后以文本列输出也是可以的。?>
	<?php if($fielddto['type']=="date"){?>
	type:'date',
	dateFormat : 'Y-m-d H:i:s',
	<?php }else{?>
	type:'auto',
	<?php }?>
	<?php if(isset($fielddto['defaultValue'])){?>
	defaultValue:'<?php echo $fielddto['defaultValue']?>',
	<?php }?>
	app:169
}
<?php if($i+1 != count($fields)){?>,<?php }?>
<?php }?>
];
<?php }?>

<?php ##远程异步数据源?>
<?php if(isset($atts['url'])){?>
var <?php echo $id?>_store = Ext.create('Ext.data.TreeStore', {
<?php if($atts['treegrid']=="true"){?>
	fields: <?php echo $id?>_treegrid_store_fields,
<?php }?>
<?php if(isset($atts['nodeParam'])){?>
nodeParam : '<?php echo $atts['nodeParam']?>',
<?php }?>
	proxy : {
		type : 'ajax',
		url : '<?php echo $atts['url']?>'
	},
	root : {
		text : '<?php echo $atts['rootText']?>',
		id : '<?php echo $atts['rootId']?>',
<?php if(isset($atts['rootAttribute'])){?>
<?php ##根节点不会将请求load回来的节点附加属性作为根节点的附加属性，如果需要让根节点有附加属性则需要这里强制附加。?>
	   <?php echo $atts['rootAttribute']?>,
<?php }?>
<?php if(isset($atts['rootIcon'])){?>
		icon : '<?php echo $atts['rootIcon']?>',
<?php }?>
<?php if(isset($atts['rootChecked'])){?>
<?php ##级联时候有bug，根节点不能参与级联选择。因为根节点不会触发checkchange事件。如果要解决此问题，可以考虑在一级节点上虚拟一个根节点出来。?>
		checked : <?php echo $atts['rootChecked']?>,
<?php }?>
		expanded : <?php echo $atts['rootExpanded']?>
	}
});	
<?php }?>

<?php ##TreeGrid.Columns?>
<?php if($atts['treegrid']=="true"){?>
var <?php echo $id?>_treegrid_columns = [
<?php foreach ($columns as $ci=> $columndto){?>
        {
			id:'<?php echo $columndto['id']?>',
	<?php if(isset($columndto['width'])){?>
	width:<?php echo $columndto['width']?>,
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
	<?php if(isset($columndto['rendererField'])){?>
			renderer:function(value,metaData,record,rowIndex ,colIndex ,store){
			<?php $dicList=$columndto['dicList'];?>
			 if(Ext.isEmpty(value)) return '';
			<?php ##返回的JSON中的数值类型?>
				<?php if($columndto['dataType'] == "number"){?>
					<?php foreach ($dicList as $dic){?>
						 if(value === <?php echo $dic['code_']?>) return '<?php echo $dic['desc_']?>';
					<?php }?>
				<?php }else{?>
					<?php foreach ($dicList as $dic){?>
						 if(value === '<?php echo $dic['code_']?>') return '<?php echo $dic['desc_']?>';
					<?php }?>					
			<?php }?>
			 return value;
			},
	  <?php }?>
	  <?php if(isset($columndto['rendererFn']) || isset($columndto['celltip']) || isset($columndto['rendererField'])){?>
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
	   <?php if(isset($columndto['editor'])){?>
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
		<?php if($columndto['type']=="action"){?>
		<?php $actionDtos=$columndto['actionDtos'];?>
    		items: [
    		<?php foreach ($actionDtos as $i=>$actionDto){?>
    			<?php if($i+1 !="1"){?>
				  '-',
				<?php }?>
				{
				<?php if(isset($actionDto['icon'])){?>
			        icon: '<?php echo $actionDto['icon']?>',
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
	   	    xtype: '<?php echo $columndto['xtype']?>'
		<?php if(isset($columndto['tpl'])){?>
		,tpl:'<?php echo $columndto['tpl']?>'
		<?php }?>
		}
	<?php if($ci+1 != count($columns)){?>,<?php }?>
    <?php }?>
];
<?php }?>

<?php ##TreePanel实例化?>
var <?php echo $id?> = Ext.create('Ext.tree.Panel',{
<?php require dirname(__FILE__).'/../common/panelTagSupport.php';?>
<?php if(isset($atts['treegrid']) && $atts['treegrid']=="true"){?>
	columns:<?php echo $id?>_treegrid_columns,
	<?php if(isset($atts['enableLocking'])){?>
	enableLocking:<?php echo $atts['enableLocking']?>,
	<?php }?>
<?php }?>
<?php if(isset($atts['columnLines'])){?>
columnLines: <?php echo $atts['columnLines']?>,
<?php }?>
<?php if(isset($atts['rowLines'])){?>
rowLines: <?php echo $atts['rowLines']?>,
<?php }?>
<?php if(isset($atts['rootVisible'])){?>
rootVisible: <?php echo $atts['rootVisible']?>,
<?php }?>
<?php if(isset($atts['useArrows'])){?>
useArrows: <?php echo $atts['useArrows']?>,
<?php }?>
<?php if(isset($atts['singleExpand'])){?>
singleExpand: <?php echo $atts['singleExpand']?>,
<?php }?>
<?php if(isset($atts['lines'])){?>
lines: <?php echo $atts['lines']?>,
<?php }?>
    store: <?php echo $id?>_store,
	app: 169
});

<?php ##展开所有节点?>
<?php if(isset($atts['expandAll']) && $atts['expandAll'] == "true"){?>
<?php echo $id?>.expandAll();
<?php }?>

<?php ##展开指定路径的节点?>
<?php if(isset($atts['expandPath'])){?>
<?php echo $id?>.expandPath('<?php echo $atts['expandPath']?>');
<?php }?>

<?php ##处理单击、双击进行节点的展开和收缩操作?>
<?php if(isset($atts['singleClick']) && $atts['singleClick'] == "true"){?>
<?php echo $id?>.on('itemclick', function (view, record, item, index, e) {
				if (record.isExpanded()) {
					record.collapse();
				} else {
					record.expand();
				}
			}); 
<?php }?>

<?php ##配合复选框级联选择?>
<?php if(isset($atts['cascade']) && $atts['cascade'] == "true"){?>
	//递归选中、反选父节点
	var <?php echo $id?>_eachParent = function(node, checked) {
		if (!node.isRoot() && checked == true) {
			if (!Ext.isEmpty(node.get('checked'))) {
				node.set('checked', checked);
				node.commit();
			}
			<?php echo $id?>_eachParent(node.parentNode, checked);
		} else if (!node.isRoot()) {
			if (!Ext.isEmpty(node.get('checked'))) {
				var flag = false;
				node.eachChild(function(n) {
					if (n.get("checked")) {
						flag = true;
					}
				});
				if (!flag) {
					node.set('checked', checked);
					node.commit();
					<?php echo $id?>_eachParent(node.parentNode, checked);
				}
			}
		}
	};
	//递归选中、反选孩子节点 
	var <?php echo $id?>_eachChild = function(node, checked) {
		node.eachChild(function(n) {
			if (!Ext.isEmpty(n.get('checked'))) {
				n.set('checked', checked);
				n.commit();
			}
			<?php echo $id?>_eachChild(n, checked);
		});
	};
	//监听
	<?php echo $id?>.on('checkchange', function(node, checked, eOpts) {
		<?php echo $id?>_eachParent(node.parentNode, checked);
		<?php echo $id?>_eachChild(node, checked);
	});	
<?php }?>

<?php ##组件常用事件绑定?>
<?php require dirname(__FILE__).'/../subvm/events.php';?>

<?php ##处理Border?>
<?php require dirname(__FILE__).'/../subvm/borders.php';?>

<?php ## treegrid的标题列的左右边框设置，防止边框线重叠?>
<?php if(isset($atts['treegrid']) && $atts['treegrid'] == "true"){?>
Ext.util.CSS.createStyleSheet('.x-grid-header-ct {border-left-width: 0px !important;border-right-width: 0px !important;}','treegrid_<?php echo $id?>');
<?php }?>