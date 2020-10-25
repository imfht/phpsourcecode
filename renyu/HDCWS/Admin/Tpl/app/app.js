Ext.Loader.setConfig({enabled : true});

Ext.application({

	name : 'HDCWS',

	appFolder : AP,

	controllers : ['HDCWS.controller.User'],

	//autoCreateViewport : true,

	init : function(){

		HDCWSAPP = this;

		if(/\{.*\}/.test(APPDATA)) APPDATA = eval('(' + APPDATA + ')');
		
		else APPDATA = {};
	
	},

	showDeskTop : function(user){

		var me = this;

		this.sessionUser = user;

		if(this.viewport){

			me.deskTop.resetUser();

			me.taskBar.resetUser();

			this.viewport.down('panel').show();
		
			this.viewport.down('toolbar').show();
		
		}else{

			this.viewport = Ext.create('HDCWS.view.Viewport', {
				
				listeners : {
				
					afterrender : {
					
						fn : function(){

							me.deskTop = Ext.create('HDCWS.controller.DeskTop', {application : me});
							
							me.deskTop.init();

							me.taskBar = Ext.create('HDCWS.controller.TaskBar', {application : me});
							
							me.taskBar.init();
						
						}
					
					}
				
				}
				
			});
		
		}

		//桌面初始化的一些控制
		me.deskTop.setDeskTopBg(APPDATA.desktop);
	
	},

	showLogout : function(){

		this.viewport.down('panel').hide();
	
		this.viewport.down('toolbar').hide();

		this.getController('HDCWS.controller.User').showLogin();

	}

});