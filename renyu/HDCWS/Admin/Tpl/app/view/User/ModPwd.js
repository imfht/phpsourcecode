Ext.define('HDCWS.view.User.ModPwd', {

	extend : 'Ext.window.Window',

	width : 400,

	height : 210,

	minWidth : 400,

	minHeight : 210,

	autoShow : true,

	resizable : true,

	maximizable : true,

	modal : true,

	title : '密码修改',

	iconCls : 'win-edit-icon',

	padding : 10,

	border : 0,

	layout : 'fit',

	items : {

		xtype : 'form',

		layout : 'form',

		border : 0,
		
		items : [
		
			{xtype : 'textfield', name : 'old', labelWidth : 90, height : 30, fieldLabel : '原密码', inputType : 'password', labelAlign : 'right', allowBlank : false, blankText : '输入原密码'},

			{xtype : 'textfield', name : 'password', labelWidth : 90, height : 30, fieldLabel : '新密码', inputType : 'password', labelAlign : 'right', allowBlank : false, blankText : '新密码不能为空'},

			{xtype : 'textfield', name : 'repassword', labelWidth : 90, height : 30, fieldLabel : '确认新密码', inputType : 'password', labelAlign : 'right', allowBlank : false, blankText : '确认新密码不能为空',

				validator : function(value){

					var pwd = this.up('form').down('textfield[name=password]').getValue();

					return (pwd == value) ? true : '两次新密码输入不一致';
			
					return false;
			
				}
			},

			{xtype : 'hidden', name : 'id'}

		]

	},

	bbar : [

		'->',

		{xtype : 'button', width : 120, height : 30, text : '取消', action : 'userModpwdCancel', handler : function(){this.up('window').close()}},

		{xtype : 'button', width : 120, height : 30, text : '提交', action : 'userModpwdSure', cls : 'edit-sure', overCls : 'edit-sure-hover'}
	
	],

	initComponent : function(){
	
		this.callParent();

		var data = this.userData;

		if(typeof data == 'object' && data != null){

			this.down('hidden[name=id]').setValue(data.id);
		
		}
	
	}

});