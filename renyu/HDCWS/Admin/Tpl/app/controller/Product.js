Ext.define('HDCWS.controller.Product', {

    extend : 'Ext.app.Controller',

	initEvents : function(){

		if(this.initFlag) return;

		this.initFlag = true;

        this.control({

            'button[action=proAdd]' : {

                click : this.showAdd

            },

            'button[action=proDel]' : {

                click : this.listDel

            },

            'button[action=productSearch]' : {

                click : this.listSearch

            },

			'button[action=proCatManage]' : {

                click : this.showCatList

            },

            'button[action=proAddSure]' : {

                click : this.add

            },

			'button[action=proEditSure]' : {

                click : this.edit

            },

            'button[action=proCatAddSure]' : {

                click : this.addCat

            },

			'button[action=proCatEditSure]' : {

                click : this.editCat

            }

		});
	
	},

	showList : function(){

		this.proList = Ext.create('HDCWS.view.Product.List', {
		
			controller : this
		
		});

		this.initEvents();

	},

	extraParams : {},

	listSearch : function(button){

		button = button || Ext.ComponentQuery.query('button[action=productSearch]')[0];

		var prev = button.previousNode(),
			
			key = prev.getValue().trim(),

			cid = prev.previousNode().getValue();

		this.extraParams = {key : key, cid : cid};

		this.proList.down('grid').getStore().load({params : this.extraParams});
	
	},

	showAdd : function(){
	
		Ext.create('HDCWS.view.Product.Add', {
		
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
			
				url : APP + '/Product/add',

				success : function(form, action){

					Ext.MessageBox.alert('提示信息', '添加成功');
					
					win.close();

					me.proList.down('pagingtoolbar').getStore().reload();
				
				},

				failure : function(form, action){
					
					Ext.MessageBox.alert('提示信息', '添加失败');
				
				}
			
			});

		}
	
	},

	showEdit : function(record){
	
		Ext.create('HDCWS.view.Product.Edit', {
		
			proData : record.getData()
		
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
			
				url : APP + '/Product/edit',

				success : function(form, action){

					Ext.MessageBox.alert('提示信息', '修改成功');
					
					win.close();

					me.proList.down('pagingtoolbar').getStore().reload();
				
				},

				failure : function(form, action){
					
					Ext.MessageBox.alert('提示信息', '修改失败');
				
				}
			
			});

		}
	
	},

	listDel : function(){

		var grid = this.proList.down('grid'),

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
			
			Ext.Msg.alert('提示信息', '请至少选中一个产品再进行操作');
		
			return;
		
		}
		
		Ext.Msg.confirm('提示信息', '确定删除吗', function(flag){
		
			if(flag == 'yes'){

				Ext.Msg.wait('请稍候...', '提示信息');
			
				Ext.Ajax.request({
				
					url : APP + '/Product/del',

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

	showCatList : function(){
	
		this.catList = Ext.create('HDCWS.view.Product.CatList', {
		
			controller : this
		
		});
	
	},

	showAddCat : function(grid, record){

		var catData;

		if(typeof record != 'undefined'){

			catData = record.getData();

		}

		Ext.create('HDCWS.view.Product.AddCat', {

			catData : catData
		
		});

	},

	addCat : function(button){

		var me = this,
			
			win = button.up('window'),
				
			form = win.down('form'),
				
			data = form.getValues();

		if(form.isValid()){
		
			form.submit({
			
				url : APP + '/Product/addCat',

				waitMsg : '请稍候...',

				success : function(form, action){
				
					Ext.Msg.alert('提示信息', '添加成功');

					win.close();

					data.id = action.result.id;

					me.catList.addCatNode(data);
				
				},

				failure : function(){
				
					Ext.Msg.alert('提示信息', '添加失败');
				
				}
			
			});
		
		}
	
	},

	showEditCat : function(grid, record){

		var data, parentData;

		if(typeof record != 'undefined'){

			data = record.getData();

			var treeStore = grid.getTreeStore(),
				
				parentNode = treeStore.getNodeById(data.id).parentNode;
					
			parentData = parentNode.getData();

		}

		Ext.create('HDCWS.view.Product.EditCat', {
		
			catData : data,

			parentData : parentData
		
		});
	
	},

	editCat : function(button){
	
		var me = this,
			
			win = button.up('window'),
				
			form = win.down('form'),
				
			data = form.getValues();

		if(form.isValid()){
		
			form.submit({
			
				url : APP + '/Product/editCat',

				waitMsg : '请稍候...',

				success : function(form, action){
				
					Ext.Msg.alert('提示信息', '修改成功');

					win.close();

					me.catList.editCatNode(data);
				
				},

				failure : function(){
				
					Ext.Msg.alert('提示信息', '修改失败');
				
				}
			
			});
		
		}
	
	},

	delCat : function(grid, record){
	
		var me = this,
				
			data = record.getData();

		Ext.Msg.confirm('提示信息', '确定删除吗?', function(flag){

			if(flag == 'yes'){

				Ext.Msg.wait('请稍候...', '提示信息');

				Ext.Ajax.request({

					url : APP + '/Product/delCat',
				
					params : {id : data.id},

					success : function(response, opts){

						Ext.Msg.hide();
					
						var result = Ext.decode(response.responseText);

						if(result && result.success){
						
							me.catList.delCatNode(data.id, data.cid);
						
						}else{
						
							Ext.Msg.alert('提示信息', '<span style="color:red">此类型含有子类或产品</span>,删除失败');
						
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