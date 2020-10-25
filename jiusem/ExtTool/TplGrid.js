Ext.define('MyApp.view.{NAME}', {
	extend:'Ext.grid.Panel',
    title: '标题',
	xtype:'{XTYPE}',
    store:'',
	winType:'',
    columns: [
		{xtype: 'rownumberer'},
        { text: 'ID',  dataIndex: 'id' }
    ],
	selModel:{
		selType:'checkboxmodel'
	},
	dockedItems: [
		{
			xtype: 'pagingtoolbar',
			store:'',  
			dock: 'bottom',
			displayInfo: true
		},
		{
			xtype:'toolbar',
			items:[
				{
					text:'添加',
					name:'add',
					scale:'medium',
					iconCls:'icon-add',
					submitUrl:''
				},
				{
					text:'编辑',
					name:'edit',
					scale:'medium',
					iconCls:'icon-edit',
					submitUrl:''
				},
				{
					text:'删除',
					name:'delete',
					scale:'medium',
					iconCls:'icon-delete',
					submitUrl:''
				}
			]
		}
	]
});