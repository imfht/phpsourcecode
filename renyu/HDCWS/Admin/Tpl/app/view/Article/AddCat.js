Ext.define('HDCWS.view.Article.AddCat', {

	extend : 'Ext.window.Window',

	width : 500,

	height : 350,

	minWidth : 500,

	minHeight : 350,

	autoShow : true,

	resizable : true,

	maximizable : true,

	modal : true,

	title : '文章类型添加',

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

			{xtype : 'textfield', name : 'keywords', labelWidth : 70, height : 30, fieldLabel : '关键字', labelAlign : 'right', allowBlank : false, blankText : '关键字不能为空'},

			{xtype : 'panel', padding : '0 0 0 75', height : 20, border : 0, html : '关键字以<span style="color:red">空格</span>或<span style="color:red">逗号</span>或<span style="color:red">分号</span>隔开'},

			{xtype : 'textarea', name : 'description', labelWidth : 70, height : 80, fieldLabel : '描述', labelAlign : 'right', allowBlank : false, blankText : '描述不能为空'},

			{xtype : 'textfield', name : 'sort', labelWidth : 70, height : 30, fieldLabel : '排序', labelAlign : 'right', value : 1, regex : /^\d+$/, regexText : '请填写数字'},

			{xtype : 'displayfield', name : 'cname', labelWidth : 70, height : 30, fieldLabel : '父类名称', labelAlign : 'right', value : '顶层类'},

			{xtype : 'hidden', name : 'cid', value : 0},

			{xtype : 'hidden', name : 'status', value : 1},

			{xtype : 'hidden', name : 'leaf', value : true},

			{xtype : 'hidden', name : 'expanded', value : true}

		]

	},

	bbar : [
		
		'->',

		{xtype : 'button', width : 120, height : 30, text : '取消', action : 'artCatAddCancel', handler : function(){this.up('window').close()}},

		{xtype : 'button', width : 120, height : 30, text : '提交', action : 'artCatAddSure', cls : 'add-sure', overCls : 'add-sure-hover'}
	
	],

	initComponent : function(){

		this.callParent();

		var catData = this.catData;

		if(catData && catData.id){
			
			this.down('hidden[name=cid]').setValue(catData.id);
			
			this.down('displayfield[name=cname]').setValue(catData.name);

		}
	
	}

});