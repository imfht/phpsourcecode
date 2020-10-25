Ext.define('HDCWS.view.Statistics.View', {

	extend : 'Ext.window.Window',

	width : 600,

	height : 500,

	minWidth : 500,

	minHeight : 500,

	autoShow : true,

	resizable : true,

	maximizable : true,

	modal : true,

	title : '访客信息查看',

	iconCls : 'win-edit-icon',

	padding : 10,

	border : 0,

	items : [
		
		{xtype : 'displayfield', name : 'ip', labelWidth : 70, width : 550, height : 30, fieldLabel : 'IP', labelAlign : 'right', readOnly : true},

		{xtype : 'displayfield', name : 'host', labelWidth : 70, width : 550, height : 30, fieldLabel : '主机', labelAlign : 'right', readOnly : true},

		{xtype : 'displayfield', name : 'port', labelWidth : 70, width : 550, height : 30, fieldLabel : '端口', labelAlign : 'right', readOnly : true},

		{xtype : 'displayfield', name : 'brower', labelWidth : 70, width : 550, height : 30, fieldLabel : '浏览器类型', labelAlign : 'right', readOnly : true},

		{xtype : 'displayfield', name : 'charset', labelWidth : 70, width : 550, height : 30, fieldLabel : '字符集', labelAlign : 'right', readOnly : true},

		{xtype : 'displayfield', name : 'encoding', labelWidth : 70, width : 550, height : 30, fieldLabel : '编码', labelAlign : 'right', readOnly : true},

		{xtype : 'displayfield', name : 'language', labelWidth : 70, width : 550, height : 30, fieldLabel : '语言', labelAlign : 'right', readOnly : true},

		{xtype : 'displayfield', name : 'method', labelWidth : 70, width : 550, height : 30, fieldLabel : '请求方式', labelAlign : 'right', readOnly : true},

		{xtype : 'displayfield', name : 'go', labelWidth : 70, width : 550, height : 30, fieldLabel : '访问地址', labelAlign : 'right', readOnly : true},

		{xtype : 'displayfield', name : 'from', labelWidth : 70, width : 550, height : 30, fieldLabel : '来源地址', labelAlign : 'right', readOnly : true},

		{xtype : 'displayfield', name : 'time', labelWidth : 70, width : 550, height : 30, fieldLabel : '访问时间', labelAlign : 'right', readOnly : true},
	
	],

	initComponent : function(){
	
		this.callParent();

		var data = this.staData;

		this.down('displayfield[name=ip]').setValue(data.ip);

		this.down('displayfield[name=host]').setValue(data.host);

		this.down('displayfield[name=port]').setValue(data.port);

		this.down('displayfield[name=brower]').setValue(HDCWS.view.Statistics.List.getBrowerInfo(data.brower));

		this.down('displayfield[name=charset]').setValue(data.charset);

		this.down('displayfield[name=encoding]').setValue(data.encoding);

		this.down('displayfield[name=language]').setValue(data.language);

		this.down('displayfield[name=method]').setValue(data.method);

		this.down('displayfield[name=go]').setValue(data.go);

		this.down('displayfield[name=from]').setValue(data.from);

		this.down('displayfield[name=time]').setValue(data.time);
	
	}

});