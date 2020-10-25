Ext.define('HDCWS.view.Main.StartMenu', {

	extend : 'Ext.menu.Menu',

	plain : true,

	width : 250,

	height : 300,

	title : '欢迎登陆',

	cls : 'menu-head',

	layout : 'hbox',

	shadow : true,

	items : [

		{

			xtype : 'panel',

			width : 150,

			items : [

				{
					
					xtype : 'menu',
					
					floating : false,

					border : 0,

					style : {color : '#666'},

					items : [
						
						{text : '修改资料', height : 30, style : 'line-height:30px', action : 'editself'}
					
					]

				}

			]

		},

		{

			xtype : 'toolbar',

			width : 100,

			layout : 'absolute',

			border : 0,

			items : [
				
				{

					xtype : 'button',

					action : 'edit-system',
					
					cls : 'menu-system',

					x : 0,

					y : 5,

					width : '90%',

					height : 35,

					text : '系统设置'
			
				},

				{

					xtype : 'button',

					action : 'logout',

					cls : 'menu-exit',

					x : 0,

					y : 220,
						
					width : '90%',

					height : 35,
			
					text : '退出'
			
				}
			
			]

		}
	
	]

});