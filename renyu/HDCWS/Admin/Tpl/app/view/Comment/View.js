Ext.define('HDCWS.view.Comment.View', {

	extend : 'Ext.window.Window',

	width : 600,

	height : 400,

	minWidth : 500,

	minHeight : 400,

	autoShow : true,

	resizable : true,

	maximizable : true,

	modal : true,

	title : '留言查看',

	iconCls : 'win-edit-icon',

	padding : 10,

	border : 0,

	items : [
		
		{xtype : 'displayfield', name : 'title', labelWidth : 70, width : 550, height : 30, fieldLabel : '标题', labelAlign : 'right', readOnly : true},

		{xtype : 'textarea', name : 'content', labelWidth : 70, width : 550, height : 250, fieldLabel : '内容', labelAlign : 'right', readOnly : true},

		{xtype : 'displayfield', name : 'time', labelWidth : 70, width : 550, height : 30, fieldLabel : '留言时间', labelAlign : 'right', readOnly : true},
	
	],

	initComponent : function(){
	
		this.callParent();

		var data = this.commentData;

		this.down('displayfield[name=title]').setValue(data.title);

		this.down('textarea[name=content]').setValue(data.content);

		this.down('displayfield[name=time]').setValue(data.time);
	
	}

});