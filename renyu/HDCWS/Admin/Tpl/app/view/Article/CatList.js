Ext.define('HDCWS.view.Article.CatList', {

    extend : 'Ext.window.Window',

	width : 900,

	height : 500,

	minWidth : 600,

	minHeight : 400,

	title : '类型列表',

	iconCls : 'list-icon',

	autoShow : true,

	resizable : true,

	maximizable : true,

	modal : true,

	layout : 'fit',

	tbar : [
	
		{xtype : 'button', text: '添加文章类型', iconCls : 'list-add', action : 'catAdd'}
	
	],

	items : {

		xtype : 'treepanel',

		store : Ext.create('HDCWS.store.ArticleCatList'),

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
						
					width : 450,
						
					menuDisabled : true,
						
					sortable : false,

					edit : {xtype : 'textfield'}
						
				},

				{
						
					text : '排序',
						
					dataIndex : 'sort',
						
					width : 50,
						
					menuDisabled : true,
						
					sortable : false,

					align : 'center'
						
				},

				{
					
					xtype : 'templatecolumn', text : '导航链接', width : 200, align : 'center', menuDisabled : true,
						
					tpl : new Ext.XTemplate(

						'<tpl>',

							'<input type="text" value="Article/lists?tid={tid}&cid={id}" style="width:120px;" />&nbsp;<a href="' + ROOT + '/Article/lists?tid={tid}&cid={id}" target="_blank" style="color:#45BA6C;">前台访问</a>',

						'</tpl>'

					)
					
				},

				{

					xtype : 'templatecolumn', text : '状态', dataIndex : 'status', 

					tpl : new Ext.XTemplate(

						'<tpl if="status == 1">',

							'<span style="color:#45BA6C;">启用</span>',

						'<tpl else>',

							'<span style="color:#F75557;">禁用</span>',

						'</tpl>'

					),

					width : 60, align : 'center', menuDisabled : true, sortable : false
						
				},

				{

					xtype : 'actioncolumn', text : '操作', 
						
					items: [
	
						{

							iconCls : 'list-col-edit',

							tooltip : '查看/编辑',

							handler : function(grid, rowIndex, colIndex, item, e, record){

								this.up('window').editCat(grid, record);

							}

						},

						{

							iconCls : 'list-col-add',

							tooltip : '添加分类',

							handler : function(grid, rowIndex, colIndex, item, e, record){

								this.up('window').addCat(grid, record);

							}

						},
							
						{

							iconCls : 'list-col-del',

							tooltip : '删除',

							handler : function(grid, rowIndex, colIndex, item, e, record){

								this.up('window').delCat(grid, record);

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
									
									me.up('window').editCat(me, record);
										
								}
								
							},

							{
							
								text : '添加文章类型',

								iconCls : 'list-add',

								handler : function(){
								
									me.up('window').addCat(me, record);
								
								}
							
							}
						
						];

					if(bol){
						
						items.push({
							
							text : '删除', 
								
							iconCls : 'list-del', 
								
							handler : function(){
								
								me.up('window').delCat(me, record);
								
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
		
		}
	
	},

	initComponent : function(){
	
		this.callParent();

		var me = this, grid = this.down('treepanel');

		this.down('button[action=catAdd]').on('click', function(){
		
			me.addCat(grid);
		
		});
	
	},

	addCat : function(grid, record){
	
		this.controller.showAddCat(grid, record);
	
	},

	addCatNode : function(data){

		var panel = this.down('treepanel').getView(),
			
			treeStore = panel.getTreeStore(),
				
			cid = parseInt(data.cid),
				
			parentNode;

		if(cid){
		
			parentNode = treeStore.getNodeById(cid);
		
		}else parentNode = treeStore.getRootNode();

		parentNode.set('leaf', false);

		parentNode.set('expanded', true);

		parentNode.appendChild(data);
	
	},

	editCat : function(grid, record){
	
		this.controller.showEditCat(grid, record);
	
	},

	editCatNode : function(data){

		var panel = this.down('treepanel').getView(),
			
			treeStore = panel.getTreeStore(),

			node = treeStore.getById(data.id);

		node.set('name', data.name);

		node.set('keywords', data.keywords);

		node.set('description', data.description);

		node.set('status', data.status);

		node.set('sort', data.sort);

		node.commit();

	},
	
	delCat : function(grid, record){

		this.controller.delCat(grid, record);
	
	},

	delCatNode : function(id, cid){

		var panel = this.down('treepanel').getView(),
			
			treeStore = panel.getTreeStore(),

			node = treeStore.getNodeById(id),
				
			parentNode;

		node.remove();

		if(cid){
		
			parentNode = treeStore.getNodeById(cid);
		
		}else parentNode = treeStore.getRootNode();

		if(!parentNode.hasChildNodes()) parentNode.set('leaf', true);
	
	}

});