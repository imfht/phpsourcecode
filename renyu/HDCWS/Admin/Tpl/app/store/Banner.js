Ext.define('HDCWS.store.Banner', {

	extend : 'Ext.data.Store',

	model : 'HDCWS.model.Banner',

	autoLoad : true,

	proxy : {
	
		type : 'ajax',

		url : APP + '/Banner/getList',

		reader : {
		
			type : 'json',

			root : 'list'
		
		}
	
	}

});