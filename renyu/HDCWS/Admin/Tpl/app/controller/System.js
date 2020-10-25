Ext.define('HDCWS.controller.System', {

	extend : 'Ext.app.Controller',

	initEvents : function(){

		if(this.initFlag) return;

		this.initFlag = true;

        this.control({

			

		});
	
	},

	showWindow : function(){

		this.systemWindow = Ext.create('HDCWS.view.System.Window', {
		
			controller : this
		
		});

		this.initEvents();

	},

	showAdd : function(){

		Ext.create('HDCWS.view.Banner.Add', {
		
			controller : this
		
		});
	
	},

	add : function(button){

		var me = this,

			win = button.up('window'),
			
			form = win.down('form');

		if(form.isValid()){

			if(!win.setHideValue()) return;

			form.submit({

				waitMsg : '请稍候...',
			
				url : APP + '/Banner/add',

				success : function(form, action){

					Ext.MessageBox.alert('提示信息', '添加成功');
					
					win.close();

					me.bannerList.down('gridpanel').getStore().reload();
				
				},

				failure : function(form, action){
					
					Ext.MessageBox.alert('提示信息', '添加失败');
				
				}
			
			});

		}
	
	},

	showEdit : function(record){

		var data = record.getData();
		
		Ext.create('HDCWS.view.Banner.Edit', {
		
			bannerData : data
		
		});
	
	},
	
	edit : function(button){

		var me = this,

			win = button.up('window'),
			
			form = win.down('form');

		if(form.isValid()){

			if(!win.setHideValue()) return;

			form.submit({

				waitMsg : '请稍候...',
			
				url : APP + '/Banner/edit',

				success : function(form, action){

					Ext.MessageBox.alert('提示信息', '修改成功');
					
					win.close();

					me.bannerList.down('gridpanel').getStore().reload();
				
				},

				failure : function(form, action){
					
					Ext.MessageBox.alert('提示信息', '修改失败');
				
				}
			
			});

		}
	
	},

	saveDeskTopBg : function(dir){

		var me = this;

		Ext.Msg.wait('请稍候...', '提示信息');

		Ext.Ajax.request({

			url : APP + '/System/saveDeskTop',

			params : {dir : dir},

			callback : function(opts, suc, res){

				Ext.Msg.hide();

				var msg = '保存失败';
				
				if(res.responseText == 1){

					msg = '保存成功';

					HDCWSAPP.getController('HDCWS.controller.DeskTop').setDeskTopBg(dir);
				
				}

				Ext.Msg.alert('提示信息', msg);
			
			}
		
		});
	
	},

	delDeskTopBg : function(record, item, index, win){

		var url = record.getData().dir;
		
		Ext.Msg.confirm('提示信息', '确定删除吗', function(flag){

			if(flag == 'yes'){

				Ext.Msg.wait('请稍候...', '提示信息');

				Ext.Ajax.request({

					url : APP + '/System/delImg',

					params : {url : url},

					callback : function(opts, suc, res){

						Ext.Msg.hide();

						var msg = '删除失败,图片不存在或当前图片为桌面背景';
						
						if(res.responseText == 1){
						
							msg = '删除成功';

							win.delDeskTopNode(record, item, index);
						
						}

						Ext.Msg.alert('提示信息', msg);
					
					}
				
				});
			
			}
		
		});
	
	}

});