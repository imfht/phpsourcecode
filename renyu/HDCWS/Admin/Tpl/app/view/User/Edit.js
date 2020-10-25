Ext.define('HDCWS.view.User.Edit', {

	extend : 'Ext.window.Window',

	width : 400,

	height : 330,

	minWidth : 400,

	minHeight : 330,

	autoShow : true,

	resizable : true,

	maximizable : true,

	modal : true,

	title : '用户编辑',

	iconCls : 'win-edit-icon',

	padding : '0 10 10 10',

	border : 0,

	layout : 'fit',

	tbar : [
		
		{xtype : 'button', text : '修改密码', iconCls : 'list-edit', action : 'modpwd'}
	
	],

	items : {

		xtype : 'form',

		layout : 'form',

		border : 0,
		
		items : [
		
			{xtype : 'textfield', name : 'name', labelWidth : 90, height : 30, fieldLabel : '用户名', labelAlign : 'right', allowBlank : false, blankText : '用户名不能为空'},

			{xtype : 'textfield', name : 'email', fieldLabel : '邮箱', labelAlign : 'right', height : 30, labelWidth : 90, allowBlank : false, blankText : '邮箱不能为空', vtype : 'email', vtypeText : '邮箱格式不正确'},

			{xtype : 'radiogroup', name : 'status', fieldLabel : '状态', labelAlign : 'right', height : 30, labelWidth : 90, fieldBodyCls : 'cat-radio-cls', items : [

				{xtype : 'radio', name : 'status', boxLabel : '启用', inputValue : 1, style : 'color:#45BA6C;'},

				{xtype : 'radio', name : 'status', boxLabel : '禁用', inputValue : 0, style : 'color:#F75557;'},

			]},

			{xtype : 'displayfield', name : 'displayaddtime', fieldLabel : '添加时间', labelAlign : 'right', height : 30, labelWidth : 90},

			{xtype : 'displayfield', name : 'displaylastlogin', fieldLabel : '最近登陆时间', labelAlign : 'right', height : 30, labelWidth : 90},

			{xtype : 'displayfield', name : 'displaylastip', fieldLabel : '最近登陆IP', height : 30, labelAlign : 'right', labelWidth : 90},

			{xtype : 'hidden', name : 'id'}

		]

	},

	bbar : [
		
		'->',

		{xtype : 'button', width : 120, height : 30, text : '取消', action : 'userEditCancel', handler : function(){this.up('window').close()}},

		{xtype : 'button', width : 120, height : 30, text : '提交', action : 'userEditSure', cls : 'edit-sure', overCls : 'edit-sure-hover'}
	
	],

	initComponent : function(){
	
		this.callParent();

		var data = this.userData;

		if(typeof data == 'object' && data != null){

			this.down('hidden[name=id]').setValue(data.id);

			this.down('textfield[name=name]').setValue(data.name);

			this.down('textfield[name=email]').setValue(data.email);

			this.down('displayfield[name=displayaddtime]').setValue(data.addtime);

			this.down('displayfield[name=displaylastlogin]').setValue(data.lastlogin);

			this.down('displayfield[name=displaylastip]').setValue(data.lastip);

			this.down('radiogroup[name=status]').setValue({status : data.status});
		
		}

		this.down('button[action=modpwd]').on('click', function(){
		
			Ext.create('HDCWS.view.User.ModPwd', {
			
				userData : data
			
			});
		
		});
	
	}

});