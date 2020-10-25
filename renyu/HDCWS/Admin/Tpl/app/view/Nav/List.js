Ext.define('HDCWS.view.Nav.List', {

    extend : 'Ext.window.Window',

	width : 800,

	height : 500,

	minWidth : 600,

	minHeight : 400,

	title : '导航列表',

	iconCls : 'list-icon',

	autoShow : true,

	resizable : true,

	maximizable : true,

	modal : true,

	tbar : [
	
		{
			
			xtype : 'button', text: '添加导航', iconCls : 'list-add',
		
			menu : [
				
				{text : '顶部导航', iconCls : 'icon-nav-top', handler : function(){this.up('window').addTopNav()}},
				
				{text : '底部导航', iconCls : 'icon-nav-bottom', handler : function(){this.up('window').addBottomNav()}}
			
			]
		
		}
	
	],

    layout : {

        type : 'accordion',

        titleCollapse : false,

        animate : true,

        activeOnTop : true

    },

    items : [

		Ext.create('HDCWS.view.Nav.TreePanel', {title : '顶部导航', tid : 1}),
			
		Ext.create('HDCWS.view.Nav.TreePanel', {title : '底部导航', tid : 2})
		
	],

	addTopNav : function(){
	
		this.addNav(1);
	
	},

	addBottomNav : function(){
	
		this.addNav(2);
	
	},

	addNav : function(grid, record){
	
		this.controller.showAddNav(grid, record);
	
	},

	addNavNode : function(data){

		var panel = this.down('treepanel').getView(),
			
			treeStore = panel.getTreeStore(),
				
			nid = parseInt(data.nid),
				
			parentNode;

		if(nid){
		
			parentNode = treeStore.getNodeById(nid);
		
		}else parentNode = treeStore.getRootNode();

		parentNode.set('leaf', false);

		parentNode.set('expanded', true);

		parentNode.appendChild(data);
	
	},

	editNav : function(grid, record){

		this.controller.showEditNav(grid, record);
	
	},

	editNavNode : function(data){

		var panel = this.down('treepanel').getView(),
			
			treeStore = panel.getTreeStore(),

			node = treeStore.getById(data.id);

		node.set('name', data.name);

		node.set('url', data.url);

		node.set('sort', data.sort);

		node.commit();

	},
	
	delNav : function(grid, record){

		this.controller.del(grid, record);
	
	},

	delNavNode : function(id, nid){

		var panel = this.down('treepanel').getView(),
			
			treeStore = panel.getTreeStore(),

			node = treeStore.getNodeById(id),
				
			parentNode;

		node.remove();

		if(nid){
		
			parentNode = treeStore.getNodeById(nid);
		
		}else parentNode = treeStore.getRootNode();

		if(!parentNode.hasChildNodes()) parentNode.set('leaf', true);
	
	}

});