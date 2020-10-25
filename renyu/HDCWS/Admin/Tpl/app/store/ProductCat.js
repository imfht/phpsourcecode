Ext.define('HDCWS.store.ProductCat', {

    extend : 'Ext.data.Store',

	model : 'HDCWS.model.ProductCat',

    autoLoad : false,

	params : {},

    proxy : {

        type : 'ajax',

		api : {

            read : APP + '/Product/getCatList'

        },
        
		reader : {

            type : 'json',

            root : 'list',

            successProperty : 'success'

        }

    },

	load : function(){

		arguments[0] = typeof arguments[0] == 'function' ? {callback : arguments[0]} : typeof arguments[0] == 'undefined' ? {} : arguments[0]; 

		arguments[0].params = this.params;

		this.callParent(arguments);
	
	}

});