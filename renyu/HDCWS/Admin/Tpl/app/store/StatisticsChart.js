Ext.define('HDCWS.store.StatisticsChart', {

	extend : 'Ext.data.Store',

	fields : ['num', 'date'],

	autoLoad : true,

	proxy : {
	
		url : APP + '/Statistics/getChartList',

		type : 'ajax',

		reader : {
		
			type : 'json',

            root : 'list',

            successProperty : 'success'
		
		}
	
	},

	load : function(){

		arguments[0] = arguments[0] || {};

		arguments[0].params = Ext.merge({}, this.controller.extraParams);

		this.callParent(arguments);
	
	}

});