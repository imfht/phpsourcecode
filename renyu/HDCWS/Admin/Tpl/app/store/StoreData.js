Ext.define('HDCWS.store.StoreData', {

	extend : 'Ext.data.Store',

	fields : ['name', 'pre', 'time', 'size', 'number'],

	autoLoad : true,

	proxy : {

		type : 'ajax',
	
		api : {
		
			read : APP + '/Web/getRestoreList'
		
		},

		reader : {
		
			type : 'json',

			root : 'list'
		
		}
	
	}

});