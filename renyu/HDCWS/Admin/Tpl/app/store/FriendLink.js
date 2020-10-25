Ext.define('HDCWS.store.FriendLink', {

	extend : 'Ext.data.Store',

	model : 'HDCWS.model.FriendLink',

	autoLoad : true,

	pageSize : 20,

	proxy : {
	
		type : 'ajax',

		api : {
		
			read : APP + '/FriendLink/getList'
		
		},

		reader : {
		
			type : 'json',

			root: 'list',

            successProperty : 'success',

			totalProperty : 'total'
		
		}
	
	},

	load : function(){

		arguments[0] = {limit : 20};

		this.callParent(arguments);
	
	}

});