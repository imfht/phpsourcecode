Ext.define('HDCWS.view.Web.TableList', {

    extend : 'Ext.window.Window',

	width : 800,

	height : 500,

	minWidth : 750,

	minHeight : 400,

	title : '数据列表',

	iconCls : 'list-icon',

	autoShow : true,

	resizable : true,

	maximizable : true,

	modal : true,

	layout : 'fit',

	tbar : [
		
		{xtype : 'button', text: '备份', iconCls : 'list-store', action : 'addStore'}
	
	],

	items : {

		xtype : 'gridpanel',

		selModel : Ext.create('Ext.selection.CheckboxModel'),

		store : Ext.create('HDCWS.store.Table'),

		columns : {

			items : [

				{text : '表名', dataIndex : 'Name', width : 130, menuDisabled : true, sortable : false},

				{text : '表用途', dataIndex : 'Comment', width : 150, menuDisabled : true, sortable : false},

				{text : '记录行数', dataIndex : 'Rows', width : 80, menuDisabled : true, sortable : false},

				{text : '引擎', dataIndex : 'Engine', width : 80, menuDisabled : true, sortable : false},

				{text : '字符集', dataIndex : 'Collation', width : 120, menuDisabled : true, sortable : false},

				{text : '表大小', dataIndex : 'size', width : 100, menuDisabled : true, sortable : false},

				{

					xtype : 'actioncolumn', text : '操作',

					items: [

						{

							iconCls : 'list-col-optimize',

							tooltip: '优化',

							handler: function(grid, rowIndex, colIndex){

								this.up('window').optimize(grid, rowIndex, colIndex);

							}

						},
							
						{

							iconCls : 'list-col-repair',

							tooltip: '修复',

							handler: function(grid, rowIndex, colIndex){

								this.up('window').repair(grid, rowIndex, colIndex);

							}

						}

					],
					
					width : 100, align : 'center', menuDisabled : true, sortable : false
						
				}
			
			]
		
		}
	
	},

	optimize : function(grid, rowIndex, colIndex){
	
		this.controller.optimize(grid.getStore().getAt(rowIndex), grid);
	
	},

	repair : function(grid, rowIndex, colIndex){
	
		this.controller.repair(grid.getStore().getAt(rowIndex), grid);
	
	}

});