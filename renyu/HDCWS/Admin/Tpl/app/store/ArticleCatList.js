Ext.define('HDCWS.store.ArticleCatList', {

    extend : 'Ext.data.TreeStore',

	model : 'HDCWS.model.ArticleCat',

    proxy : {

        type : 'ajax',

		api : {

            read : APP + '/Article/getCatListTree'

        },
        
		reader : {

            type : 'json'

        }

    },

	nodeParam : 'id',

    root: {

		expanded : true,

		id : 0

    }

});