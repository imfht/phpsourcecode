Ext.define('HDCWS.controller.Web', {

	extend : 'Ext.app.Controller',

	initEvents : function(){

		if(this.initFlag) return;

		this.initFlag = true;

        this.control({
			
            'button[action=webinfoSure]' : {

                click : this.saveWebInfo

            },

            'button[action=webSta]' : {
            	
            	click : this.showStatistics
            	
            },

			'button[action=cacheSure]' : {

                click : this.saveCache

            },

			'button[action=clearcacheSure]' : {

                click : this.clearCache

            },

			'button[action=dbSure]' : {

                click : this.saveDb

            },
				
			'button[action=tablelist]' : {

                click : this.showTableList

            },

			'button[action=addStore]' : {

                click : this.addStore

            },

			'button[action=delStore]' : {

                click : this.delStoreList

            },

			'button[action=storedatalist]' : {

                click : this.showStoredDataList

            }

		});
	
	},

	showList : function(){

		var me = this;

		Ext.Ajax.request({
		
			url : APP + '/Web/getConfig',

			callback : function(opt, success, res){

				if(success){

					var data = Ext.decode(res.responseText);

					me.configList = Ext.create('HDCWS.view.Web.List', {
					
						controller : me,

						configData : data.list
					
					});

					me.initEvents();

				}else{
				
					Ext.Msg.alert('提示信息', '获取数据失败');
				
				}
			
			},

			watiMsg : '获取数据中...'
		
		});

	},

	showStatistics : function(){
		
		Ext.create('HDCWS.Controller.Statistics').showList();
		
	},

	saveWebInfo : function(button){
	
		var formPanel = button.up('form'),
			
			form = formPanel.getForm();

		formPanel.setHideValue();

		if(form.isValid()){

			form.submit({

				waitMsg : '请稍候...',

				url : APP + '/Web/saveWebInfo',

				success : function(form, action){

					Ext.MessageBox.alert('提示信息', '编辑成功');
				
				},

				failure : function(form, action){
					
					Ext.MessageBox.alert('提示信息', '编辑失败');
				
				}
			
			});
		
		}
	
	},

	saveCache : function(button){
	
		var formPanel = button.up('form'),
			
			form = formPanel.getForm();

		if(form.isValid()){

			form.submit({

				waitMsg : '请稍候...',

				url : APP + '/Web/saveCache',

				success : function(form, action){

					Ext.MessageBox.alert('提示信息', '编辑成功');
				
				},

				failure : function(form, action){
					
					Ext.MessageBox.alert('提示信息', '编辑失败');
				
				}
			
			});
		
		}
	
	},

	clearCache : function(){
	
		Ext.Msg.confirm('提示信息', '您确定清除吗?', function(flag){

			if(flag != 'yes') return;
		
			Ext.Msg.wait('请稍候...', '提示信息');

			Ext.Ajax.request({
			
				url : APP + '/Web/clearCache',

				callback : function(opts, suc, res){

					Ext.Msg.hide();

					var msg = '清除失败';
					
					if(res.responseText == 1){
					
						msg = '清除成功';
					
					}

					Ext.Msg.alert('提示信息', msg);
				
				}
			
			});
		
		});
	
	},

	saveDb : function(button){
	
		var formPanel = button.up('form'),
			
			form = formPanel.getForm();

		if(form.isValid()){

			Ext.Msg.confirm('<span style="color:red">警告</span>', '您确定修改数据库配置吗?', function(flag){

				if(flag != 'yes') return; 

				form.submit({

					waitMsg : '请稍候...',

					url : APP + '/Web/saveDb',

					success : function(form, action){

						Ext.MessageBox.alert('提示信息', '编辑成功');
					
					},

					failure : function(form, action){
						
						Ext.MessageBox.alert('提示信息', '编辑失败');
					
					}
				
				});

			});
		
		}
	
	},

    showTableList : function(){
	
		Ext.create('HDCWS.view.Web.TableList', {
			
			controller : this
				
		});
	
	},

	addStore : function(button){
	
		var grid = button.up('window').down('grid'),
			
			records = grid.getSelectionModel().getSelection(),
				
			arr = [];

		Ext.Array.forEach(records, function(item, i){
		
			arr.push(item.getData().id);

		});

		if(arr.length < 1){
		
			Ext.Msg.alert('提示信息', '请选择至少一张表再进行操作');
		
		}else{
		
			Ext.Msg.wait('请稍候...', '提示信息');

			Ext.Ajax.request({
			
				url : APP + '/Web/store',

				params : {name : arr.join(',')},

				callback : function(opts, suc, res){

					Ext.Msg.hide();

					var msg = '备份失败';
					
					if(res.responseText == 1){

						msg = '备份成功';
					
					}

					Ext.Msg.alert('提示信息', msg);
				
				}
			
			});
		
		}
	
	},

	optimize : function(record, grid){
	
		Ext.Msg.wait('请稍候...', '提示信息');

		Ext.Ajax.request({
		
			url : APP + '/Web/optimize',

			params : {tablename : record.getData().id},

			callback : function(opts, suc, res){

				Ext.Msg.hide();

				var msg = '优化失败';
				
				if(res.responseText == 1){

					msg = '优化成功';
				
				}

				Ext.Msg.alert('提示信息', msg);
			
			}
		
		});	
	
	},

	repair : function(record, grid){
	
		Ext.Msg.wait('请稍候...', '提示信息');

		Ext.Ajax.request({
		
			url : APP + '/Web/repair',

			params : {tablename : record.getData().id},

			callback : function(opts, suc, res){

				Ext.Msg.hide();

				var msg = '修复失败';
				
				if(res.responseText == 1){

					msg = '修复成功';
				
				}

				Ext.Msg.alert('提示信息', msg);
			
			}
		
		});	
	
	},

	showStoredDataList : function(){
	
		Ext.create('HDCWS.view.Web.StoreDataList', {
			
			controller : this
				
		});
	
	},

	restore : function(record, grid){

		Ext.Msg.confirm('提示信息', '确定还原吗?', function(flag){

			if(flag != 'yes') return;

			Ext.Msg.wait('请稍候...', '提示信息');

			Ext.Ajax.request({
			
				url : APP + '/Web/backdata',

				params : {name : record.getData().id},

				callback : function(opts, suc, res){

					Ext.Msg.hide();

					var msg = '还原失败';
					
					if(res.responseText == 1){

						msg = '还原成功';
					
					}

					Ext.Msg.alert('提示信息', msg);
				
				}
			
			});

		});
	
	},

	delStoreList : function(button){

		var me = this,

			grid = button.up('window').down('grid'),
			
			records = grid.getSelectionModel().getSelection();

		if(records.length < 1){
		
			Ext.Msg.alert('提示信息', '请至少选择一条记录再进行操作');

			return;
		
		}

		var arr = [];

		Ext.each(records, function(item){
		
			arr.push(item.getData().id);
		
		});

		me.delStoreData(arr.join(','), grid);
	
	},

	delStoreData : function(name, grid){

		Ext.Msg.confirm('提示信息', '确定删除吗?', function(flag){

			if(flag != 'yes') return;

			Ext.Msg.wait('请稍候...', '提示信息');

			Ext.Ajax.request({
			
				url : APP + '/Web/delsql',

				params : {name : name},

				callback : function(opts, suc, res){

					Ext.Msg.hide();

					var msg = '删除失败';
					
					if(res.responseText == 1){

						msg = '删除成功';

						grid.getStore().reload();
					
					}

					Ext.Msg.alert('提示信息', msg);
				
				}
			
			});

		});
	
	}

});