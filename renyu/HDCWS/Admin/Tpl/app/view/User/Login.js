Ext.define('HDCWS.view.User.Login', {

	extend : 'Ext.window.Window',

	width : 350,

	height : 220,

	autoShow : true,

	maximizable : true,
	
	closable : false,

	modal : true,

	title : '用户登陆',

	iconCls : 'win-login-icon',

	padding : 10,

	border : 0,

	layout : 'fit',

	items : {

		xtype : 'form',

		layout : 'form',

		border : 0,
		
		items : [
		
			{xtype : 'textfield', name : 'name', labelWidth : 70, height : 30, fieldLabel : '用户名', labelAlign : 'right', allowBlank : false, blankText : '用户名不能为空'},

			{xtype : 'textfield', name : 'password', labelWidth : 70, height : 30, fieldLabel : '密码', labelAlign : 'right', inputType : 'password', allowBlank : false, blankText : '密码不能为空'},

			{
				xtype : 'panel', 
			
				height : 30,

				border : 0,

				padding : 0,

				layout : 'hbox',

				items : [
					
					{xtype : 'textfield', name : 'verify', labelWidth : 70, height : 30, fieldLabel : '验证码', labelAlign : 'right', allowBlank : false, blankText : '验证码不能为空',
						
						enableKeyEvents : true,
						
						listeners : {
							
							keyup : {
								
								fn : function(env, e, eOpts){
								 
									if(e.getKey() == 13) this.up('window').login();
								
								}
						
							}
					
						}

					},

					{xtype : 'image', width : 50, height : 20, src : APP + '/Pub/verify', margin : '5 0 0 5', style : 'cursor:pointer;', 
						
						listeners : {
						
							render : {
							
								fn : function(){

									var me = this;
								
									this.getEl().on('click', function(){
										
										me.setSrc(me.src + '?t=' + Math.random())
											
									});
								
								}
							
							}
						
						}
						
					}
				
				]

			}

		]

	},

	bbar : [
		
		'->',

		{xtype : 'button', width : 120, height : 30, text : '登陆', cls : 'add-sure', overCls : 'login-sure-hover', handler : function(){this.up('window').login();}}
	
	],

	login : function(){
	
		this.controller.login(this.down('form').getForm(), this);
	
	}

});