Ext.define('HDCWS.controller.Comment', {

    extend : 'Ext.app.Controller',

	initEvents : function(){

		if(this.initFlag) return;

		this.initFlag = true;

        this.control({

            'button[action=commentDel]' : {

                click : this.listDel

            },

            'button[action=commentSearch]' : {

                click : this.listSearch

            }

		});
	
	},

	showList : function(){

		this.comList = Ext.create('HDCWS.view.Comment.List', {
		
			controller : this
		
		});

		this.initEvents();

	},

	extraParams : {},

	listSearch : function(button){

		button = button || Ext.ComponentQuery.query('button[action=commentSearch]')[0];

		var prev = button.previousNode(),
			
			key = prev.getValue().trim();

		this.extraParams = {key : key};

		this.comList.down('grid').getStore().load({params : this.extraParams});
	
	},

	showComment : function(record){
	
		Ext.create('HDCWS.view.Comment.View', {
		
			commentData : record.getData()
		
		});
	
	},

	listDel : function(){

		var grid = this.comList.down('grid'),

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
			
			Ext.Msg.alert('提示信息', '请至少选中一条留言再进行操作');
		
			return;
		
		}
		
		Ext.Msg.confirm('提示信息', '确定删除吗', function(flag){
		
			if(flag == 'yes'){

				Ext.Msg.wait('请稍候...', '提示信息');
			
				Ext.Ajax.request({
				
					url : APP + '/Comment/del',

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