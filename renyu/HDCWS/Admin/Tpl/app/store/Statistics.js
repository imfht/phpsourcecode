Ext.define('HDCWS.store.Statistics', {

	extend : 'Ext.data.Store',

	model : 'HDCWS.model.Statistics',

	autoLoad : true,

	proxy : {
	
		url : APP + '/Statistics/getList',

		type : 'ajax',

		reader : {
		
			type : 'json',

            root : 'list',

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