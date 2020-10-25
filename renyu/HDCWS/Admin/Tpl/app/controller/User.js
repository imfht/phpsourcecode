Ext.define('HDCWS.controller.User', {

    extend : 'Ext.app.Controller',

	init : function(){

		this.checkUser();

		this.initEvents();
	
	},

	statics : {
	
		userList : null
	
	},

	checkUser : function(){

		var me = this;

		Ext.Msg.wait('验证中,请稍候...', '提示信息');

		Ext.Ajax.request({

			url : APP + '/Pub/checkLogin',

			callback : function(opt, success, response){
			
				Ext.Msg.hide();
				
				if(success){
				
					var result = Ext.decode(response.responseText);

					if(Ext.typeOf(result) == 'object' && result.user) me.showViewPort(result.user);

					else me.showLogin();
				
				}else Ext.Msg.alert('提示信息', '连接服务器失败,请刷新页面');
			
			}
		
		});
	
	},

	initEvents : function(){

		if(this.initFlag) return;

		this.initFlag = true;

        this.control({

            'button[action=userAdd]' : {

                click : this.showAdd

            },

            'button[action=userDel]' : {

                click : this.listDel

            },
			
            'button[action=userAddSure]' : {

                click : this.add

            },

			'button[action=userEditSure]' : {

                click : this.edit

            },

			'button[action=userModpwdSure]' : {

                click : this.modPwd

            },

            'button[action=userSearch]' : {

                click : this.listSearch

            }

		});
	
	},

	showList : function(){

		Ext.getClass(this).userList = Ext.create('HDCWS.view.User.List', {
		
			controller : this
		
		});

	},

	extraParams : {},

	listSearch : function(button){
		
		button = button || Ext.ComponentQuery.query('button[action=userSearch]')[0];

		var prev = button.previousNode(),
			
			key = prev.getValue().trim();

		this.extraParams = {key : key};

		button.up('window').down('grid').getStore().load({params : this.extraParams});
	
	},

	showAdd : function(){

		Ext.create('HDCWS.view.User.Add', {
		
			controller : this
		
		});

	},

	add : function(button){

		var me = this,

			win = button.up('window'),
			
			form = win.down('form'),
				
			userList = Ext.getClass(this).userList;

		if(form.isValid()){

			Ext.Msg.wait('请稍候...', '提示信息');

			Ext.Ajax.request({
			
				url : APP + '/User/checkName',

				params : form.getForm().getFieldValues(),

				callback : function(opts, suc, res){

					Ext.Msg.hide();

					if(res.responseText == 1){
					
						form.submit({

							waitMsg : '请稍候...',
						
							url : APP + '/User/add',

							success : function(form, action){

								Ext.MessageBox.alert('提示信息', '添加成功');
								
								win.close();

								userList.down('pagingtoolbar').getStore().reload();
							
							},

							failure : function(form, action){
								
								Ext.MessageBox.alert('提示信息', '添加失败');
							
							}
						
						});

					}else Ext.MessageBox.alert('提示信息', '用户名已经存在');

				}

			});

		}

	},

	showEdit : function(record){
	
		Ext.create('HDCWS.view.User.Edit', {
		
			userData : record.getData()
		
		});
	
	},
	
	edit : function(button){

		var me = this,

			win = button.up('window'),
			
			form = win.down('form'),

			userList = Ext.getClass(this).userList;

		if(form.isValid()){

			Ext.Msg.wait('请稍候...', '提示信息');

			Ext.Ajax.request({
			
				url : APP + '/User/checkName',

				params : form.getForm().getFieldValues(),

				callback : function(opts, suc, res){

					Ext.Msg.hide();

					if(res.responseText == 1){

						form.submit({

							waitMsg : '请稍候...',
						
							url : APP + '/User/edit',

							success : function(form, action){

								Ext.MessageBox.alert('提示信息', '修改成功');
								
								win.close();

								userList.down('pagingtoolbar').getStore().reload();
							
							},

							failure : function(form, action){
								
								Ext.MessageBox.alert('提示信息', '修改失败');
							
							}
						
						});

					}else Ext.MessageBox.alert('提示信息', '用户名已经存在');

				}

			});

		}
	
	},

	modPwd : function(button){
	
		var me = this,

			win = button.up('window'),
			
			form = win.down('form');

		if(form.isValid()){
					
			form.submit({

				waitMsg : '请稍候...',
			
				url : APP + '/User/modPwd',

				success : function(form, action){

					Ext.MessageBox.alert('提示信息', '修改成功');
					
					win.close();
				
				},

				failure : function(form, action){
					
					Ext.MessageBox.alert('提示信息', '修改失败');
				
				}
			
			});

		}
	
	},

	listDel : function(){

		var userList = Ext.getClass(this).userList,
			
			grid = userList.down('grid'),

			record = grid.getSelectionModel().getSelection();

		this.del(record, grid);
	
	},

	del : function(record, grid){

		var id;
		
		if(Ext.typeOf(record) == 'array'){

			var ids = [];

			record.forEach(function(item, i){

				ids.push(item.getData().id);
			
			});

			id = ids.join(',');

		}else{
			
			id = record.getData().id;

			record = [record];

		}

		if(!/\d/.test(id)){
			
			Ext.Msg.alert('提示信息', '请至少选中一个用户再进行操作');
		
			return;
		
		}
		
		Ext.Msg.confirm('提示信息', '确定删除吗', function(flag){
		
			if(flag == 'yes'){

				Ext.Msg.wait('请稍候...', '提示信息');
			
				Ext.Ajax.request({
				
					url : APP + '/User/del',

					params : {id : id},

					callback : function(opts, suc, res){

						Ext.Msg.hide();

						var msg = '删除失败';
						
						if(res.responseText == 1){
						
							msg = '删除成功';
							
							grid.getStore().remove(record);
						
						}

						Ext.Msg.alert('提示信息', msg);
					
					}
				
				});
			
			}
		
		});
	
	},

	showViewPort : function(user){

		this.getApplication().showDeskTop(user);
	
	},

	showLogin : function(){

		Ext.create('HDCWS.view.User.Login', {
		
			controller : this
		
		});
	
	},

	login : function(form, win){

		var me = this;

		if(form.isValid()){
			
			form.submit({
			
				url : APP + '/Pub/login',

				params : form.getFieldValues(),

				waitTitle : '提示信息',

				waitMsg : '登陆中,请稍候...',

				success : function(form, action){

					var result = action.result.user;

					win.close();

					me.showViewPort(result);

				},

				failure : function(form, action){

					var result = action.result.user, msg;

					if(result == 0){
					
						msg = '用户名或密码错误';
					
					}else{
					
						msg = '验证码错误';
					
					}

					Ext.Msg.alert('提示信息', msg);
				
				}
			
			});
		
		}

	},

	logout : function(){

		var me = this;

		Ext.Msg.wait('退出中...', '提示信息');

		Ext.Ajax.request({

			url : APP + '/Pub/logout',

			callback : function(opt, success, response){
			
				Ext.Msg.hide();
				
				if(success){
				
					me.getApplication().showLogout();
				
				}else Ext.Msg.alert('提示信息', '连接服务器失败,请刷新页面');
			
			}
		
		});
	
	}

});