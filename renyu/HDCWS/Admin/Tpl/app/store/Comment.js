Ext.define('HDCWS.store.Comment', {

	extend : 'Ext.data.Store',

	model : 'HDCWS.model.Comment',

	autoLoad : true,

	pageSize : 20,

	proxy : {
	
		type : 'ajax',

		api : {
		
			read : APP + '/Comment/getList'
		
		},

		reader : {
		
			type : 'json',

			root: 'list',

            successProperty : 'success',

			totalProperty : 'total'
		
		}
	
	},

	load : function(){

		arguments[0] = typeof arguments[0] == 'function' ? {callback : arguments[0]} : typeof arguments[0] == 'undefined' ? {} : arguments[0]; 

		arguments[0].limit = 20;

		arguments[0].params = Ext.merge({}, this.controller.extraParams);

		this.callParent(arguments);
	
	}

});