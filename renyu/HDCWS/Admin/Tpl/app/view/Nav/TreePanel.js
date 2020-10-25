Ext.define('HDCWS.view.Nav.TreePanel', {
	
	extend : 'Ext.tree.Panel',
	
	title : '导航',

	useArrows : true,

	rootVisible : false,

	animate : true,

	columns : {

		items : [
			
			{
				
				xtype : 'treecolumn',
				
				text : '名称',
					
				dataIndex : 'name',

				draggable : true,
					
				width : 250,
					
				menuDisabled : true,
					
				sortable : false
			
			},

			{
				
				xtype : 'gridcolumn',
				
				text : '链接地址',
					
				dataIndex : 'url',

				draggable : true,
					
				width : 350,
					
				menuDisabled : true,
					
				sortable : false
			
			},

			{
				
				xtype : 'gridcolumn',
				
				text : '排序',
					
				dataIndex : 'sort',

				draggable : true,
					
				width : 50,
					
				menuDisabled : true,
					
				sortable : false,

				align : 'center'
			
			},

			{

				xtype : 'actioncolumn', text : '操作', 
					
				items: [

					{

						iconCls : 'list-col-edit',

						tooltip : '查看/编辑',

						handler : function(grid, rowIndex, colIndex, item, e, record){

							this.up('window').editNav(grid, record);

						}

					},

					{

						iconCls : 'list-col-add',

						tooltip : '添加子链',

						handler : function(grid, rowIndex, colIndex, item, e, record){

							this.up('window').addNav(grid, record);

						}

					},
						
					{

						iconCls : 'list-col-del',

						tooltip : '删除',

						handler : function(grid, rowIndex, colIndex, item, e, record){

							this.up('window').delNav(grid, record);

						},

						isDisabled : function(grid, rowIndex, colIndex, item, record){

							var data = record.getData();

							return !data.leaf;
						
						}

					}

				],
				
				width : 100, align : 'center', menuDisabled : true, sortable : false
					
			}
		
		]
	
	},

	listeners : {
	
		itemcontextmenu : {
		
			fn : function(me, record, htmlEl, index, e){

				e.preventDefault();
				
				e.stopEvent();

				var data = record.getData(),

					treeStore = me.getTreeStore(),
			
					node = treeStore.getNodeById(data.id),

					bol = node.isLeaf(),
					
					items = [
						
						{
							
							text : '编辑', 
								
							iconCls : 'list-edit', 
								
							handler : function(){
								
								me.up('window').editNav(me, record);
									
							}
							
						},

						{
						
							text : '添加子链',

							iconCls : 'list-add',

							handler : function(){
							
								me.up('window').addNav(me, record);
							
							}
						
						}
					
					];

				if(bol){
					
					items.push({
						
						text : '删除', 
							
						iconCls : 'list-del', 
							
						handler : function(){
							
							me.up('window').delNav(me, record);
							
						}
						
					});

				}
			
				Ext.create('Ext.menu.Menu', {

					width : 80,
				
					items : items,

					listeners : {
					
						hide : {
						
							fn : function(){

								var me = this;
							
								setTimeout(function(){me.close();}, 1000);
							
							}
						
						}
					
					}
				
				}).showAt(e.getXY());

				return false;
			
			}
		
		}
	
	},

	initComponent : function(){

		this.store = Ext.create('HDCWS.store.Nav', {
			
			tid : this.tid
			
		});
		
		this.callParent();

	}
	
});