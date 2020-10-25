Ext.define('HDCWS.controller.Banner', {

	extend : 'Ext.app.Controller',

	initEvents : function(){

		if(this.initFlag) return;

		this.initFlag = true;

        this.control({

            'button[action=bannerAdd]' : {

                click : this.showAdd

            },

            'button[action=bannerAddSure]' : {

                click : this.add

            },

            'button[action=bannerEditSure]' : {

                click : this.edit

            }

		});
	
	},

	showList : function(){

		this.bannerList = Ext.create('HDCWS.view.Banner.List', {
		
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

	del : function(record, grid){

		var id= record.getData().id,

			record = [record];

		if(!/\d/.test(id)){
			
			Ext.Msg.alert('提示信息', '请至少选中一篇文章再进行操作');
		
			return;
		
		}
		
		Ext.Msg.confirm('提示信息', '确定删除吗', function(flag){
		
			if(flag == 'yes'){

				Ext.Msg.wait('请稍候...', '提示信息');

				Ext.Ajax.request({
				
					url : APP + '/Banner/del',

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
	
	}

});