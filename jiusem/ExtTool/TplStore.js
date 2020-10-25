Ext.define('MyApp.store.{NAME}Store',{
	extend:'Ext.data.Store',
	model:'MyApp.model.{MODEL}Model',
    proxy: {
        type: 'ajax',
        url: MyApp.url('{NAME}','index'),
        reader: {
            type: 'json',
            rootProperty: 'root'
        }
    }
});