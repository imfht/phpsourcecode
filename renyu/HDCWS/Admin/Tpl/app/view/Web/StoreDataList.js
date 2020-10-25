Ext.define('HDCWS.view.Web.StoreDataList', {

    extend : 'Ext.window.Window',

	width : 750,

	height : 500,

	minWidth : 750,

	minHeight : 400,

	title : '数据还原',

	iconCls : 'list-icon',

	autoShow : true,

	resizable : true,

	maximizable : true,

	modal : true,

	layout : 'fit',

	tbar : [
		
		{xtype : 'button', text: '删除', iconCls : 'list-del', action : 'delStore'}
	
	],

	items : {

		xtype : 'gridpanel',

		selModel : Ext.create('Ext.selection.CheckboxModel'),

		store : Ext.create('HDCWS.store.StoreData'),

		columns : {

			items : [

				{text : '文件名称', dataIndex : 'name', width : 260, menuDisabled : true, sortable : false},

				{text : '文件大小', dataIndex : 'size', width : 150, menuDisabled : true, sortable : false},

				{text : '备份时间', dataIndex : 'time', width : 140, menuDisabled : true, sortable : false, align : 'center'},

				{text : '卷号', dataIndex : 'number', width : 60, menuDisabled : true, sortable : false},

				{

					xtype : 'actioncolumn', text : '操作',

					items: [

						{

							iconCls : 'list-col-restore',

							tooltip: '还原',

							handler: function(grid, rowIndex, colIndex){

								this.up('window').restore(grid, rowIndex, colIndex);

							}

						},
							
						{

							iconCls : 'list-col-del',

							tooltip: '删除',

							handler: function(grid, rowIndex, colIndex){

								this.up('window').del(grid, rowIndex, colIndex);

							}

						}

					],
					
					width : 100, align : 'center', menuDisabled : true, sortable : false
						
				}
			
			]
		
		}
	
	},

	restore : function(grid, rowIndex, colIndex){
	
		this.controller.restore(grid.getStore().getAt(rowIndex), grid);
	
	},

	del : function(grid, rowIndex, colIndex){
	
		this.controller.delStoreData(grid.getStore().getAt(rowIndex).getData().id, grid);
	
	}

});