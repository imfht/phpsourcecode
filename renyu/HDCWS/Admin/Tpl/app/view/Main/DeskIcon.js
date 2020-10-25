Ext.define('HDCWS.view.Main.DeskIcon', {

	extend : 'Ext.panel.Panel',

	layout : 'fit',

	cls : 'icon-style',

	width : 70,

	height : 80,

	border : 0,

	overCls : 'icon-hover',

	autoShow : false,

	constructor : function(opt){

		this.callParent(arguments);
		
		this.html = '<div class="icon-name">' + opt.html + '</div>';

		this.bodyCls = (this.bodyCls || '') + ' desktop-icon';

	}

});