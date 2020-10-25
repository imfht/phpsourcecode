Ext.define('HDCWS.store.User', {

	extend : 'Ext.data.Store',

	model : 'HDCWS.model.User',

	autoLoad : true,

	pageSize : 20,

	proxy : {
	
		type : 'ajax',

		api : {
		
			read : APP + '/User/getList'
		
		},

		reader : {
		
			type : 'json',

			root: 'list',

            successProperty : 'success',

			totalProperty : 'total'
		
		}
	
	},

	load : function(){

		arguments[0] = arguments[0] || {};

		arguments[0].limit = 20;

		arguments[0].params = Ext.merge({}, this.controller.extraParams);

		this.callParent(arguments);
	
	}

});