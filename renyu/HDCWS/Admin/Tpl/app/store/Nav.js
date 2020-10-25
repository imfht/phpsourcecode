Ext.define('HDCWS.store.Nav', {

	extend : 'Ext.data.TreeStore',

	model : 'HDCWS.model.Nav',

	autoLoad : true,

	proxy : {
	
		type : 'ajax',

		api : {

            read : APP + '/Nav/getListTree'

        },

		//url : APP + '/Nav/getListTree',

		reader : {

            type : 'json'

        }
	
	},

    root : {

		expanded : true,

		id : 0

    },

	load : function(){

		arguments[0].params = {tid : this.tid};

		this.callParent(arguments);
	
	}

});