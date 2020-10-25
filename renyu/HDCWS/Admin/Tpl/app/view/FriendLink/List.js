Ext.define('HDCWS.view.FriendLink.List', {

    extend : 'Ext.window.Window',

	width : 800,

	height : 500,

	minWidth : 600,

	minHeight : 400,

	title : '友情链接列表',

	iconCls : 'list-icon',

	autoShow : true,

	resizable : true,

	maximizable : true,

	modal : true,

	layout : 'fit',

	tbar : [
	
		{xtype : 'button', text: '添加', iconCls : 'list-add', action : 'linkAdd'},

		{xtype : 'button', text: '删除', iconCls : 'list-del', action : 'linkDel'}
	
	],
	
	initComponent : function(){

		this.callParent();

		var me = this;

		this.myStore = Ext.create('HDCWS.store.FriendLink');

		var selectModel = Ext.create('Ext.selection.CheckboxModel');

		this.add({

			xtype : 'gridpanel',

			store : me.myStore,

			selModel : selectModel,

			columns : {
			
				items : [

					{text : '名称', dataIndex : 'name', width : 200, menuDisabled : true, sortable : false},

					{text : '地址', dataIndex : 'url', width : 330, menuDisabled : true},

					{

						xtype : 'templatecolumn', text : '状态', dataIndex : 'status', 
							
						tpl : new Ext.XTemplate(

							'<tpl if="status == 1">',

								'<span style="color:#45BA6C;">启用</span>',

							'<tpl else>',

								'<span style="color:#F75557;">禁用</span>',

							'</tpl>'

						),

						width : 60, align : 'center', menuDisabled : true, sortable : false},

					{text : '排序', dataIndex : 'sort', width : 50, menuDisabled : true, align : 'center'},

					{

						xtype : 'actioncolumn', text : '操作', 
							
						items: [
		
							{

								iconCls : 'list-col-edit',

								tooltip: '查看/编辑',

								handler: function(grid, rowIndex, colIndex){

									this.up('window').editLink(grid, rowIndex, colIndex);

								}

							},
								
							{

								iconCls : 'list-col-del',

								tooltip: '删除',

								handler: function(grid, rowIndex, colIndex){

									this.up('window').delLink(grid, rowIndex, colIndex);

								}

							}

						],
						
						width : 100, align : 'center', menuDisabled : true, sortable : false}
				
				]
			
			},

			bbar : {

				xtype : 'pagingtoolbar',

				store : me.myStore,

				prevText : '上一页',

				nextText : '上一页',

				firstText : '首页',

				lastText : '尾页',

				refreshText : '刷新',

				beforePageText : '页',

				afterPageText : '/ {0}',
				
				displayInfo : true

			}
		
		});

	},

	editLink : function(grid, rowIndex, colIndex){
	
		var record = grid.getStore().getAt(rowIndex);
	
		this.controller.showEdit(record);
	
	},

	delLink : function(grid, rowIndex, colIndex){
	
		var record = grid.getStore().getAt(rowIndex);
	
		this.controller.del(record, grid);
	
	}

});