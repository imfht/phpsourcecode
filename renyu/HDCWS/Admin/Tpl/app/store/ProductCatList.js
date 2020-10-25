Ext.define('HDCWS.store.ProductCatList', {

    extend : 'Ext.data.TreeStore',

	model : 'HDCWS.model.ProductCat',

    proxy : {

        type : 'ajax',

		api : {

            read : APP + '/Product/getCatListTree'

        },
        
		reader : {

            type : 'json'

        }

    },

	nodeParam : 'id',

    root : {

		expanded : true,

		id : 0

    }

});