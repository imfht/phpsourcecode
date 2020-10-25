Ext.define('HDCWS.Controller.Statistics', {

	extend : 'Ext.app.Controller',

	initEvents : function(){
	
		if(this.initFlag) return;

		this.initFlag = true;

		this.control({
		
			'button[action=staDel]' : {
			
				click : this.listDel
			
			},

			'button[action=staChart]' : {
			
				click : this.showChart
			
			}
		
		});
	
	},

	extraParams : {},

	showList : function(){

		this.staList = Ext.create('HDCWS.view.Statistics.List', {
		
			controller : this
		
		});

		this.initEvents();

	},

	viewSta : function(record){
	
		Ext.create('HDCWS.view.Statistics.View', {
			
			staData : record.getData()
				
		});
	
	},

	listDel : function(){

		var grid = this.staList.down('grid'),

			record = grid.getSelectionModel().getSelection();

		this.delSta(record, grid);
	
	},

	delSta : function(record, grid){

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
			
			Ext.Msg.alert('提示信息', '请至少选中一条记录再进行操作');
		
			return;
		
		}
		
		Ext.Msg.confirm('提示信息', '确定删除吗', function(flag){
		
			if(flag == 'yes'){

				Ext.Msg.wait('请稍候...', '提示信息');
			
				Ext.Ajax.request({
				
					url : APP + '/Statistics/del',

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

	showChart : function(){
	
		Ext.create('HDCWS.view.Statistics.Chart');
	
	}

});