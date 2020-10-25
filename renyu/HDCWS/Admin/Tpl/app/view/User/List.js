Ext.define('HDCWS.view.User.List', {

    extend : 'Ext.window.Window',

	width : 950,

	height : 500,

	minWidth : 600,

	minHeight : 400,

	title : '用户列表',

	iconCls : 'list-icon',

	autoShow : true,

	resizable : true,

	maximizable : true,

	modal : true,

	layout : 'fit',

	tbar : [
	
		{xtype : 'button', text: '添加', iconCls : 'list-add', action : 'userAdd'},

		{xtype : 'button', text: '删除', iconCls : 'list-del', action : 'userDel'},

		{xtype : 'textfield', labelWidth : 0, labelAlign : 'right', width : 150, enableKeyEvents : true,
			
			listeners : {

				keyup : {
				
					fn : function(me, e, eOpts){

						if(e.getKey() == 13) this.nextSibling().fireEvent('click')
					
					}
				
				}

			}

		},

		{xtype : 'button', text: '搜索', iconCls : 'list-search', action : 'userSearch'}
	
	],
	
	initComponent : function(){

		this.callParent();

		var me = this;

		this.myStore = Ext.create('HDCWS.store.User', {controller : this.controller});

		var selectModel = Ext.create('Ext.selection.CheckboxModel');

		this.add({

			xtype : 'gridpanel',

			store : me.myStore,

			selModel : selectModel,

			columns : {
			
				items : [

					{text : '用户名', dataIndex : 'name', width : 100, menuDisabled : true, sortable : false},

					{text : '邮箱', dataIndex : 'email', width : 200, menuDisabled : true},

					{text : '添加时间', dataIndex : 'addtime', width : 150, menuDisabled : true},

					{text : '最后登陆时间', dataIndex : 'lastlogin', width : 150, menuDisabled : true},

					{text : '最后登陆IP', dataIndex : 'lastip', width : 120, menuDisabled : true},

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

					{

						xtype : 'actioncolumn', text : '操作', 
							
						items: [
		
							{

								iconCls : 'list-col-edit',

								tooltip: '查看/编辑',

								handler: function(grid, rowIndex, colIndex){

									this.up('window').editUser(grid, rowIndex, colIndex);

								}

							},
								
							{

								iconCls : 'list-col-del',

								tooltip: '删除',

								handler: function(grid, rowIndex, colIndex){

									this.up('window').delUser(grid, rowIndex, colIndex);

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

	editUser : function(grid, rowIndex, colIndex){
	
		var record = grid.getStore().getAt(rowIndex);
	
		this.controller.showEdit(record);
	
	},

	delUser : function(grid, rowIndex, colIndex){
	
		var record = grid.getStore().getAt(rowIndex);
	
		this.controller.del(record, grid);
	
	}

});