Ext.define('HDCWS.controller.FriendLink', {

    extend : 'Ext.app.Controller',

	initEvents : function(){

		if(this.initFlag) return;

		this.initFlag = true;

        this.control({

            'button[action=linkAdd]' : {

                click : this.showAdd

            },

            'button[action=linkDel]' : {

                click : this.listDel

            },
			
            'button[action=linkAddSure]' : {

                click : this.add

            },

			'button[action=linkEditSure]' : {

                click : this.edit

            }

		});
	
	},

	showList : function(){

		this.linkList = Ext.create('HDCWS.view.FriendLink.List', {
		
			controller : this
		
		});

		this.initEvents();

	},

	showAdd : function(){
	
		Ext.create('HDCWS.view.FriendLink.Add', {
		
			controller : this
		
		});
	
	},

	add : function(button){

		var me = this,

			win = button.up('window'),
			
			form = win.down('form');

		if(form.isValid()){

			form.submit({

				waitMsg : '请稍候...',
			
				url : APP + '/FriendLink/add',

				success : function(form, action){

					Ext.MessageBox.alert('提示信息', '添加成功');
					
					win.close();

					me.linkList.down('pagingtoolbar').getStore().reload();
				
				},

				failure : function(form, action){
					
					Ext.MessageBox.alert('提示信息', '添加失败');
				
				}
			
			});

		}
	
	},

	showEdit : function(record){
	
		Ext.create('HDCWS.view.FriendLink.Edit', {
		
			linkData : record.getData()
		
		});
	
	},
	
	edit : function(button){

		var me = this,

			win = button.up('window'),
			
			form = win.down('form');

		if(form.isValid()){

			form.submit({

				waitMsg : '请稍候...',
			
				url : APP + '/FriendLink/edit',

				success : function(form, action){

					Ext.MessageBox.alert('提示信息', '修改成功');
					
					win.close();

					me.linkList.down('pagingtoolbar').getStore().reload();
				
				},

				failure : function(form, action){
					
					Ext.MessageBox.alert('提示信息', '修改失败');
				
				}
			
			});

		}
	
	},

	listDel : function(){

		var grid = this.linkList.down('grid'),

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
			
			Ext.Msg.alert('提示信息', '请至少选中一个链接再进行操作');
		
			return;
		
		}
		
		Ext.Msg.confirm('提示信息', '确定删除吗', function(flag){
		
			if(flag == 'yes'){

				Ext.Msg.wait('请稍候...', '提示信息');
			
				Ext.Ajax.request({
				
					url : APP + '/FriendLink/del',

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