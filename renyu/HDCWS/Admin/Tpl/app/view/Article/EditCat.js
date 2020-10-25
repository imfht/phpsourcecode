Ext.define('HDCWS.view.Article.EditCat', {

	extend : 'Ext.window.Window',

	width : 500,

	height : 400,

	minWidth : 500,

	minHeight : 400,

	autoShow : true,

	resizable : true,

	maximizable : true,

	modal : true,

	title : '文章类型编辑',

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

			{xtype : 'textfield', name : 'keywords', labelWidth : 70, height : 30, fieldLabel : '关键字', labelAlign : 'right', allowBlank : false, blankText : '关键字不能为空'},

			{xtype : 'panel', padding : '0 0 0 75', height : 20, border : 0, html : '关键字以<span style="color:red">空格</span>或<span style="color:red">逗号</span>或<span style="color:red">分号</span>隔开'},

			{xtype : 'textarea', name : 'description', labelWidth : 70, height : 80, fieldLabel : '描述', labelAlign : 'right', allowBlank : false, blankText : '描述不能为空'},

			{xtype : 'textfield', name : 'sort', labelWidth : 70, height : 30, fieldLabel : '排序', labelAlign : 'right', value : 1, regex : /^\d+$/, regexText : '请填写数字'},

			{xtype : 'displayfield', name : 'cname', labelWidth : 70, height : 30, fieldLabel : '父类名称', labelAlign : 'right', value : '顶层类'},

			{xtype : 'radiogroup', name : 'status', labelWidth : 70, height : 30, fieldLabel : '状态', labelAlign : 'right', fieldBodyCls : 'cat-radio-cls',

				items : [
					
					{xtype : 'radio', boxLabel : '启用', name : 'status', inputValue : 1, style : 'color:#45BA6C;'},

					{xtype : 'radio', boxLabel : '禁用', name : 'status', inputValue : 0, style : 'color:#F75557;'}
				
				]
					
			},

			{xtype : 'hidden', name : 'id', value : 0},

			{xtype : 'hidden', name : 'cid', value : 0}

		]

	},

	bbar : [
		
		'->',

		{xtype : 'button', width : 120, height : 30, text : '取消', action : 'artCatEditCancel', handler : function(){this.up('window').close()}},

		{xtype : 'button', width : 120, height : 30, text : '提交', action : 'artCatEditSure', cls : 'edit-sure', overCls : 'edit-sure-hover'}
	
	],

	initComponent : function(){

		this.callParent();

		var data = this.catData,
			
			parentData = this.parentData;

		if(data){

			this.down('hidden[name=id]').setValue(data.id);
		
			this.down('textfield[name=name]').setValue(data.name);
			
			this.down('textfield[name=keywords]').setValue(data.keywords);

			this.down('textarea[name=description]').setValue(data.description);

			this.down('textfield[name=sort]').setValue(data.sort);

			this.down('radiogroup[name=status]').setValue({status : data.status});
		
		}

		if(parentData && parentData.id &&  parentData.name){
			
			this.down('hidden[name=cid]').setValue(parentData.id);
			
			this.down('displayfield[name=cname]').setValue(parentData.name);

		}
	
	}

});