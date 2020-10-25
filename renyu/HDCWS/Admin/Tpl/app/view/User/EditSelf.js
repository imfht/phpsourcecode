Ext.define('HDCWS.view.User.EditSelf', {

	extend : 'Ext.window.Window',

	width : 400,

	height : 270,

	minWidth : 400,

	minHeight : 270,

	autoShow : true,

	resizable : true,

	maximizable : true,

	modal : true,

	title : '资料修改',

	iconCls : 'win-edit-icon',

	padding : 10,

	border : 0,

	layout : 'fit',

	items : {

		xtype : 'form',

		layout : 'form',

		border : 0,
		
		items : [
		         
		    {xtype : 'textfield', name : 'email', fieldLabel : '邮箱', labelAlign : 'right', height : 30, labelWidth : 90, allowBlank : false, blankText : '邮箱不能为空', vtype : 'email', vtypeText : '邮箱格式不正确'},
		
			{height : 20, border : 0, padding : '0 0 0 95', html : '密码如不需要修改,置空即可'},
			
		    {xtype : 'textfield', name : 'password', labelWidth : 90, height : 30, fieldLabel : '原密码', inputType : 'password', labelAlign : 'right'},

			{xtype : 'textfield', name : 'newpassword', labelWidth : 90, height : 30, fieldLabel : '新密码', inputType : 'password', labelAlign : 'right'},

			{xtype : 'textfield', name : 'renewpassword', labelWidth : 90, height : 30, fieldLabel : '确认新密码', inputType : 'password', labelAlign : 'right',

				validator : function(value){

					var pwd = this.up('form').down('textfield[name=newpassword]').getValue();

					return (pwd == value) ? true : '两次新密码输入不一致';
			
					return false;
			
				}
			},

			{xtype : 'hidden', name : 'id'}

		]

	},

	bbar : [

		'->',

		{xtype : 'button', width : 120, height : 30, text : '取消', action : 'userEditSelfCancel', handler : function(){this.up('window').close()}},

		{xtype : 'button', width : 120, height : 30, text : '提交', action : 'userEditSelfSure', cls : 'edit-sure', overCls : 'edit-sure-hover'}
	
	],

	initComponent : function(){
	
		this.callParent();

		var data = this.userData;

		if(typeof data == 'object' && data != null){
			
			this.down('textfield[name=email]').setValue(data.email);

			this.down('hidden[name=id]').setValue(data.id);
		
		}
	
	}

});