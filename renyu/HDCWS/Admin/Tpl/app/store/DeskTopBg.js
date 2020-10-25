Ext.define('HDCWS.store.DeskTopBg', {

	extend : 'Ext.data.TreeStore',

	fields : ['dir', 'text'],

	proxy : {
	
		type : 'ajax',

		url : APP + '/System/getDeskTopBgList',

		reader : {
		
			type : 'json'

		}
	
	},

	root : {

		expanded : true
	
	}

});