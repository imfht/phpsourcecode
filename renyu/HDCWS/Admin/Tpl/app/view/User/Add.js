Ext.define('HDCWS.view.User.Add', {

	extend : 'Ext.window.Window',

	width : 400,

	height : 300,

	minWidth : 400,

	minHeight : 300,

	autoShow : true,

	resizable : true,

	maximizable : true,

	modal : true,

	title : '用户添加',

	iconCls : 'win-add-icon',

	padding : 10,

	border : 0,

	layout : 'fit',

	items : {

		xtype : 'form',

		layout : 'form',

		border : 0,
		
		items : [
		
			{xtype : 'textfield', name : 'name', labelWidth : 70, height : 30, fieldLabel : '用户名', labelAlign : 'right', allowBlank : false, blankText : '用户名不能为空'},

			{xtype : 'textfield', name : 'password', labelWidth : 70, height : 30, fieldLabel : '密码', labelAlign : 'right', inputType : 'password', allowBlank : false, blankText : '密码不能为空'},

			{xtype : 'textfield', name : 'reppassword', labelWidth : 70, height : 30, fieldLabel : '确认密码', labelAlign : 'right', inputType : 'password', allowBlank : false, blankText : '确认密码不能为空', 
				
				validator : function(value){
					
					var pwd = this.up('form').down('textfield[name=password]').getValue();

					return (pwd == value) ? true : '两次密码输入不一致';
					
				}
				
			},

			{xtype : 'textfield', name : 'email', fieldLabel : '邮箱', labelAlign : 'right', height : 30, labelWidth : 70, allowBlank : false, blankText : '邮箱不能为空', vtype : 'email', vtypeText : '邮箱格式不正确'},

			{xtype : 'hidden', name : 'status', value : 1}

		]

	},

	bbar : [
		
		'->',

		{xtype : 'button', width : 120, height : 30, text : '取消', action : 'userAddCancel', handler : function(){this.up('window').close()}},

		{xtype : 'button', width : 120, height : 30, text : '提交', action : 'userAddSure', cls : 'add-sure', overCls : 'add-sure-hover'}
	
	]

});