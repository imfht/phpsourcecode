Ext.define('HDCWS.view.FriendLink.Add', {

	extend : 'Ext.window.Window',

	width : 600,

	height : 300,

	minWidth : 500,

	minHeight : 300,

	autoShow : true,

	resizable : true,

	maximizable : true,

	modal : true,

	title : '友情链接添加',

	iconCls : 'win-add-icon',

	padding : 10,

	border : 0,

	layout : 'fit',

	items : {

		xtype : 'form',

		layout : 'form',

		border : 0,
		
		items : [
		
			{xtype : 'textfield', name : 'name', labelWidth : 70, height : 30, fieldLabel : '类型名称', labelAlign : 'right', allowBlank : false, blankText : '名称不能为空'},

			{xtype : 'textfield', name : 'url', fieldLabel : '链接地址', labelAlign : 'right', height : 30, labelWidth : 70, allowBlank : false, blankText : '链接地址不能为空', regex : /^https?\:\/\//, regexText : '链接格式不正确'},

			{xtype : 'panel', padding : '0 0 0 75', height : 20, border : 0, html : '请填写以<span style="color:red;">http</span>或<span style="color:red;">https</span>开头的网络地址'},

			{xtype : 'textarea', name : 'description', labelWidth : 70, height : 80, fieldLabel : '描述', labelAlign : 'right', allowBlank : false, blankText : '描述不能为空'},

			{xtype : 'hidden', name : 'status', value : 1}
		]

	},

	bbar : [
		
		'->',

		{xtype : 'button', width : 120, height : 30, text : '取消', action : 'linkAddCancel', handler : function(){this.up('window').close()}},

		{xtype : 'button', width : 120, height : 30, text : '提交', action : 'linkAddSure', cls : 'add-sure', overCls : 'add-sure-hover'}
	
	]

});