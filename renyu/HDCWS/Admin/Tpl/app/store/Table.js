Ext.define('HDCWS.store.Table', {

	extend : 'Ext.data.Store',

	fields : ['Name', 'Comment', 'Rows', 'Engine', 'Collation', 'size'],

	autoLoad : true,

	proxy : {

		type : 'ajax',
	
		api : {
		
			read : APP + '/Web/getTableList'
		
		},

		reader : {
		
			type : 'json',

			root : 'list'
		
		}
	
	}

});