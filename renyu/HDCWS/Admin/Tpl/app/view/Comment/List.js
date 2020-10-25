Ext.define('HDCWS.view.Comment.List', {

	extend : 'Ext.window.Window',

	width : 650,

	height : 500,

	minWidth : 650,

	minHeight : 500,

	title : '留言列表',

	iconCls : 'list-icon',

	autoShow : true,

	resizable : true,

	maximizable : true,

	modal : true,

	layout : 'fit',

	tbar : [

		{xtype : 'button', text: '删除', iconCls : 'list-del', action : 'commentDel'},

		{xtype : 'textfield', labelWidth : 0, labelAlign : 'right', width : 150, enableKeyEvents : true,
			
			listeners : {

				keyup : {
				
					fn : function(me, e, eOpts){

						if(e.getKey() == 13) this.nextSibling().fireEvent('click')
					
					}
				
				}

			}},

		{xtype : 'button', text: '搜索', iconCls : 'list-search', action : 'commentSearch'}
	
	],
	
	initComponent : function(){

		this.callParent();

		var me = this;

		this.myStore = Ext.create('HDCWS.store.Comment', {controller : this.controller});

		var selectModel = Ext.create('Ext.selection.CheckboxModel');

		this.add({

			xtype : 'gridpanel',

			store : me.myStore,

			selModel : selectModel,

			columns : {
			
				items : [

					{text : '标题', dataIndex : 'title', width : 350, menuDisabled : true, sortable : false},

					{text : '留言时间', dataIndex : 'time', width : 140, menuDisabled : true, sortable : false, align : 'center'},

					{

						xtype : 'actioncolumn', text : '操作', 
							
						items: [
		
							{

								iconCls : 'list-col-view',

								tooltip: '查看',

								handler: function(grid, rowIndex, colIndex){

									this.up('window').showComment(grid, rowIndex, colIndex);

								}

							},
								
							{

								iconCls : 'list-col-del',

								tooltip: '删除',

								handler: function(grid, rowIndex, colIndex){

									this.up('window').delComment(grid, rowIndex, colIndex);

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

	showComment : function(grid, rowIndex, colIndex){
	
		var record = grid.getStore().getAt(rowIndex);
	
		this.controller.showComment(record);
	
	},

	delComment : function(grid, rowIndex, colIndex){
	
		var record = grid.getStore().getAt(rowIndex);
	
		this.controller.del(record, grid);
	
	}

});