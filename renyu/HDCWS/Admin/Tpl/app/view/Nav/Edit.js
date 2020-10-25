Ext.define('HDCWS.view.Nav.Edit', {

	extend : 'Ext.window.Window',

	width : 500,

	height : 350,

	minWidth : 500,

	minHeight : 350,

	autoShow : true,

	resizable : true,

	maximizable : true,

	modal : true,

	title : '导航编辑',

	iconCls : 'win-edit-icon',

	padding : 10,

	border : 0,

	layout : 'fit',

	items : {

		xtype : 'form',

		layout : 'form',

		border : 0,
		
		items : [
		
			{xtype : 'textfield', name : 'name', labelWidth : 70, height : 30, fieldLabel : '导航名称', labelAlign : 'right', allowBlank : false, blankText : '名称不能为空'},

			{xtype : 'textfield', name : 'url', labelWidth : 70, height : 30, fieldLabel : '链接地址', labelAlign : 'right', allowBlank : false, blankText : '链接地址不能为空'},
			
			{xtype : 'panel', padding : '0 0 0 75', height : 40, border : 0, html : '内部地址格式:<span style="color:red">类名</span>/<span style="color:red">方法名</span>?<span style="color:red">参数名=参数值</span>,例:product/list/;<br/>外部地址以http开头,例:http://www.hidoger.com'},
			
			{xtype : 'textfield', name : 'sort', labelWidth : 70, height : 30, fieldLabel : '排序', labelAlign : 'right', value : 1, regex : /^\d+$/, regexText : '请填写数字'},

			{xtype : 'displayfield', name : 'nname', labelWidth : 70, height : 30, fieldLabel : '父类导航', labelAlign : 'right', value : '顶层导航'},

			{xtype : 'hidden', name : 'id', value : 0},
			
			{xtype : 'hidden', name : 'nid', value : 0},

			{xtype : 'hidden', name : 'tid', value : 1},

			{xtype : 'hidden', name : 'leaf', value : true},

			{xtype : 'hidden', name : 'expanded', value : true}

		]

	},

	bbar : [
		
		'->',

		{xtype : 'button', width : 120, height : 30, text : '取消', action : 'navEditCancel', handler : function(){this.up('window').close()}},

		{xtype : 'button', width : 120, height : 30, text : '提交', action : 'navEditSure', cls : 'edit-sure', overCls : 'edit-sure-hover'}
	
	],

	initComponent : function(){

		this.callParent();

		var data = this.navData,
			
			parentData = this.parentData;

		if(data){

			this.down('hidden[name=id]').setValue(data.id);
		
			this.down('textfield[name=name]').setValue(data.name);
			
			this.down('textfield[name=url]').setValue(data.url);

			this.down('textfield[name=sort]').setValue(data.sort);
		
		}

		if(parentData && parentData.id &&  parentData.name){
			
			this.down('hidden[name=nid]').setValue(parentData.id);
			
			this.down('displayfield[name=nname]').setValue(parentData.name);

		}
	
	}

});