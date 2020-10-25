Ext.define('HDCWS.view.FriendLink.Edit', {

	extend : 'Ext.window.Window',

	width : 600,

	height : 360,

	minWidth : 500,

	minHeight : 360,

	autoShow : true,

	resizable : true,

	maximizable : true,

	modal : true,

	title : '友情链接添加',

	iconCls : 'win-edit-icon',

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

			{xtype : 'textfield', name : 'sort', labelWidth : 70, height : 30, fieldLabel : '排序', labelAlign : 'right', value : 1, regex : /^\d+$/, regexText : '请填写数字'},

			{xtype : 'radiogroup', name : 'status', labelWidth : 70, height : 30, fieldLabel : '状态', labelAlign : 'right', fieldBodyCls : 'cat-radio-cls',

				items : [
					
					{xtype : 'radio', boxLabel : '启用', name : 'status', inputValue : 1, style : 'color:#45BA6C;'},

					{xtype : 'radio', boxLabel : '禁用', name : 'status', inputValue : 0, style : 'color:#F75557;'}
				
				]
					
			},

			{xtype : 'hidden', name : 'id'}

		]

	},

	bbar : [
		
		'->',

		{xtype : 'button', width : 120, height : 30, text : '取消', action : 'linkEditCancel', handler : function(){this.up('window').close()}},

		{xtype : 'button', width : 120, height : 30, text : '提交', action : 'linkEditSure', cls : 'edit-sure', overCls : 'edit-sure-hover'}
	
	],

	initComponent : function(){
	
		this.callParent();

		var data = this.linkData;

		if(typeof data == 'object' && data != null){

			this.down('hidden[name=id]').setValue(data.id);

			this.down('textfield[name=name]').setValue(data.name);

			this.down('textfield[name=url]').setValue(data.url);

			this.down('textarea[name=description]').setValue(data.description);

			this.down('textfield[name=sort]').setValue(data.sort);

			this.down('radiogroup[name=status]').setValue({status : data.status});
		
		}
	
	}

});