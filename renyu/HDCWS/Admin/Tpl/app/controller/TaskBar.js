Ext.define('HDCWS.controller.TaskBar', {

    extend : 'Ext.app.Controller',

	id : 'taskBarController',

    init : function(){

		this.control({
		
			'button[action=logout]' : {
			
				click : this.logout
			
			},

			'menuitem[action=editself]' : {
			
				click : this.editSelf
			
			},

			'button[action=edit-system]' : {
			
				click : this.editSystem
			
			},
			
			'button[action=userEditSelfSure]' : {
			
				click : this.editSelfForm
			
			}
		
		});

		this.resetUser();

    },

	resetUser : function(){
	
		var user = this.application.sessionUser,

			toolbar = Ext.ComponentQuery.query('toolbar[cls=taskbar]')[0],
			
			startmenu = toolbar.down('menu');

		startmenu.title = '欢迎登陆,' + user.name;

		toolbar.down('displayfield[name=thislogin]').setValue(user.thislogin);

		toolbar.down('displayfield[name=thisip]').setValue(user.thisip);

		toolbar.down('displayfield[name=lastlogin]').setValue(user.lastlogin);

		toolbar.down('displayfield[name=lastip]').setValue(user.lastip);
	
	},

	editSelf : function(){
	
		Ext.create('HDCWS.view.User.EditSelf', {
		
			userData : this.application.sessionUser
		
		});
	
	},
	
	editSelfForm : function(button){
		
		var me = this,

		win = button.up('window'),
		
		form = win.down('form'),
		
		values = form.getForm().getFieldValues();

		if(form.isValid()){
			
			form.submit({
	
				waitMsg : '请稍候...',
			
				url : APP + '/Pub/editSelf',
	
				success : function(form, action){
					
					me.application.sessionUser.email = values['email'];
	
					Ext.MessageBox.alert('提示信息', action.result.msg);
					
					win.close();
				
				},
	
				failure : function(form, action){
					
					Ext.MessageBox.alert('提示信息', action.result.msg);
				
				}
			
			});
	
		}
		
		
	},

	editSystem : function(){

		var me = this;

		if(this.system){
			
			this.system.showWindow();
	
			return;
		
		}
		
		Ext.require('HDCWS.controller.System', function(){

			var system = Ext.create('HDCWS.controller.System');

			system.showWindow();

			me.system = system;
		
		});
	
	},

	logout : function(){

		var me = this;

		Ext.Msg.confirm('提示信息', '确定退出吗?', function(flag){
		
			if(flag == 'yes') me.getController('HDCWS.controller.User').logout();
		
		});
	
	}

});