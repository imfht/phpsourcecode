Ext.define('HDCWS.view.Main.TaskBar', {

	extend : 'Ext.toolbar.Toolbar',

	anchor : '100% 40',

	cls : 'taskbar',

	layout : 'absolute',

	margin : 0,

	padding : 0,

	border : 0,

	items : [

		{

			xtype : 'button',

			cls : 'start-btn',

			iconCls : 'start-btn-img',

			overCls : 'start-btn-hover',

			text : '开始',
				
			width : 85,

			height : 40,
			
			x : 0,

			y : 0,

			margin : 0,

			padding : 0,

			border : 0,

			menuAlign : 'bl?',

			menu : Ext.create('HDCWS.view.Main.StartMenu')

		},

		{
			
			xtype : 'menuseparator',

			cls : 'taskbar-separator',

			x : 90,

			y : 5
			
		},

		{
		
			xtype : 'panel',

			border : 0,

			x : 130,

			y : 10,

			layout : {
				
				type : 'hbox',

				pack : 'end'

			},

			items : [
				
				{xtype : 'displayfield', fieldLabel : '本次登陆时间', labelWidth : 90, labelAlign : 'right', name : 'thislogin', value : ''},

				{xtype : 'displayfield', fieldLabel : '本次登陆IP', labelWidth : 90, labelAlign : 'right', name : 'thisip', value : ''},

				{xtype : 'displayfield', fieldLabel : '上次登陆时间', labelWidth : 90, labelAlign : 'right', name : 'lastlogin', value : ''},

				{xtype : 'displayfield', fieldLabel : '上次登陆IP', labelWidth : 90, labelAlign : 'right', name : 'lastip', value : ''}
			
			]
		
		}
	
	],

	listeners : {
	
		afterrender : {

			el : 'comp',
		
			fn : function(){
			
				this.el.on('contextmenu', function(e, el, eOpts){
				
					e.preventDefault();
					
					e.stopEvent();

					Ext.create('HDCWS.view.Main.ContextMenu').showMe(e);
				
				});
			
			}
		
		}
	
	}

});