Ext.define('HDCWS.controller.Nav', {

	extend : 'Ext.app.Controller',

	initEvents : function(){

		if(this.initFlag) return;

		this.initFlag = true;

        this.control({
			
            'button[action=navAddSure]' : {

                click : this.add

            },

			'button[action=navEditSure]' : {

                click : this.edit

            }

		});
	
	},

	showList : function(){

		this.navList = Ext.create('HDCWS.view.Nav.List', {
		
			controller : this
		
		});

		this.initEvents();

	},

	showAddNav : function(grid, record){

		var navData, tid;

		if(typeof record != 'undefined'){

			navData = record.getData();

		}

		if(typeof grid == 'number') tid = grid;

		else tid = navData.tid;

		Ext.create('HDCWS.view.Nav.Add', {

			navData : navData,

			tid : tid
		
		});

	},

	add : function(button){

		var me = this,
			
			win = button.up('window'),
				
			form = win.down('form'),
				
			data = form.getValues();

		if(form.isValid()){
		
			form.submit({
			
				url : APP + '/Nav/add',

				waitMsg : '请稍候...',

				success : function(form, action){
				
					Ext.Msg.alert('提示信息', '添加成功');

					win.close();

					data.id = action.result.id;

					me.navList.addNavNode(data);
				
				},

				failure : function(){
				
					Ext.Msg.alert('提示信息', '添加失败');
				
				}
			
			});
		
		}
	
	},

	showEditNav : function(grid, record){

		var data, parentData;

		if(typeof record != 'undefined'){

			data = record.getData();

			var treeStore = grid.getTreeStore(),
				
				parentNode = treeStore.getNodeById(data.id).parentNode;
					
			parentData = parentNode.getData();

		}

		Ext.create('HDCWS.view.Nav.Edit', {
		
			navData : data,

			parentData : parentData
		
		});
	
	},

	edit : function(button){

		var me = this,
			
			win = button.up('window'),
				
			form = win.down('form'),
				
			data = form.getValues();

		if(form.isValid()){
		
			form.submit({
			
				url : APP + '/Nav/edit',

				waitMsg : '请稍候...',

				success : function(form, action){

					Ext.Msg.alert('提示信息', '修改成功');

					win.close();

					me.navList.editNavNode(data);
				
				},

				failure : function(){

					Ext.Msg.alert('提示信息', '修改失败');
				
				}
			
			});
		
		}
	
	},

	del : function(grid, record){
	
		var me = this,
				
			data = record.getData();

		Ext.Msg.confirm('提示信息', '确定删除吗?', function(flag){

			if(flag == 'yes'){

				Ext.Msg.wait('请稍候...', '提示信息');

				Ext.Ajax.request({

					url : APP + '/Nav/del',
				
					params : {id : data.id},

					success : function(response, opts){

						Ext.Msg.hide();
					
						var result = Ext.decode(response.responseText);

						if(result && result.success){
						
							me.navList.delNavNode(data.id, data.nid);
						
						}else{
						
							Ext.Msg.alert('提示信息', '<span style="color:red">此导航下含有子链接</span>,删除失败');
						
						}
					
					},

					failure : function(response, opts){

						Ext.Msg.hide();
					
						Ext.Msg.alert('提示信息', '删除失败');
					
					}

				});

			}

		});

	}

});