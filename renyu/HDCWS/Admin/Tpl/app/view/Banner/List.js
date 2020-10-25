Ext.define('HDCWS.view.Banner.List', {

    extend : 'Ext.window.Window',

	width : 1000,

	height : 500,

	minWidth : 1000,

	minHeight : 500,

	title : 'Banner列表',

	iconCls : 'list-icon',

	autoShow : true,

	resizable : true,

	maximizable : true,

	modal : true,

	layout : 'fit',

	tbar : [
	
		{xtype : 'button', text: '添加Banner', iconCls : 'list-add', action : 'bannerAdd'}
	
	],

	items : {
	
		xtype : 'gridpanel',

		store : Ext.create('HDCWS.store.Banner'),

		columns : {
		
			items : [

				{text : 'Banner标题', dataIndex : 'title', width : 250, menuDisabled : true, sortable : false},

				{

					xtype : 'templatecolumn', text : '图片展示', dataIndex : 'src',

					tpl : new Ext.XTemplate(

						'<tpl if="this.isEmpty(src)">',

							'<span>无图片</span>',

						'<tpl else>',

							'<span><img width="200" src="' + APPDATA.root + '{src}" /></span>',

						'</tpl>',

						{
							
							isEmpty : function(name){

								return !((typeof name == 'string' || typeof name == 'number') && !/^\s*$/.test(name)) ? true : false;

							}
						
						}

					),

					width : 220, menuDisabled : true, sortable : false},

				{text : '链接地址', dataIndex : 'url', width : 260, align : 'left', menuDisabled : true},

				{

					xtype : 'templatecolumn', text : '打开方式', dataIndex : 'target',

					tpl : new Ext.XTemplate(

						'<tpl if="this.isNew(target)">',

							'<span>新窗口</span>',

						'<tpl else>',

							'<span>本页</span>',

						'</tpl>',

						{
							
							isNew : function(a){

								return a == '_blank';

							}
						
						}

					),

					width : 80, align : 'center', menuDisabled : true, sortable : false},

				{text : '排序', dataIndex : 'sort', width : 50, align : 'center', menuDisabled : true},

				{

					xtype : 'actioncolumn', text : '操作',
						
					items: [
	
						{

							iconCls : 'list-col-edit',

							tooltip: '查看/编辑',

							handler: function(grid, rowIndex, colIndex){

								this.up('window').edit(grid, rowIndex, colIndex);

							}

						},
							
						{

							iconCls : 'list-col-del',

							tooltip: '删除',

							handler: function(grid, rowIndex, colIndex){

								this.up('window').del(grid, rowIndex, colIndex);

							}

						}

					],
					
					width : 105, align : 'center', menuDisabled : true, sortable : false
						
				}
			
			]
		
		}
	
	},

	edit : function(grid, rowIndex, colIndex){

		var record = grid.getStore().getAt(rowIndex);
	
		this.controller.showEdit(record);
	
	},
	
	del : function(grid, rowIndex, colIndex){

		var record = grid.getStore().getAt(rowIndex);
	
		this.controller.del(record, grid);
	
	}

});

Ext.define('HDCWS.view.Banner.Img', {

	extend : 'Ext.panel.Panel',

	width : 200,

	height : 140,

	padding : '5 5 5 5',

	cls : 'banner-img-panel loading',

	bodyCls : 'banner-img-panel-body',

	header : false,

	layout : 'absolute',

	items : [
		
		{
	
			xtype : 'button',

			border : 0,

			width : 18,

			height : 18,

			x : 170,

			y : 3,

			hidden : true,

			cls : 'banner-img-remove',

			handler : function(){

				var me = this;
			
				Ext.MessageBox.confirm('提示信息', '确定删除吗', function(flag){

					if(flag == 'yes') me.up('panel').delImg();

				});
			
			}
	
		},

		{
		
			xtype : 'progressbar',

			cls : 'banner-img-progressbar',

			width : 200,
			
			height : 8,

			x : 0,

			y : 65
		
		}
	
	],

	showDel : true,

	showProcss : function(file, bytes, total){

		var val = bytes / total; 

		this.down('progressbar').updateProgress(val);
	
	},

	showImg : function(file, data, response){
	
		if(data != 0){

			this.removeCls('loading');

			this.imgUrl = data;

			this.add(Ext.create('Ext.Img', {
			
				src : APPDATA.root + data,

				width : 190,

				height : 133
			
			}));

		}else this.showFail();
	
	},

	showFail : function(){
	
		Ext.MessageBox.alert('提示信息', '图片上传失败或图片不存在');

		this.delSelf();
	
	},

	showError : function(){

		Ext.MessageBox.alert('提示信息', '图片上传出错或图片不存在');

		this.delSelf();
	
	},

	delSelf : function(){

		this.hide();

		var me = this;

		setTimeout(function(){

			var upPanel = me.up('panel');
		
			if(upPanel) upPanel.remove(me);
		
		}, 1500);
	
	},

	delImg : function(){

		var me = this;

		Ext.Ajax.request({
		
			url : APP + '/Banner/delImg',

			params : {
			
				imgUrl : me.imgUrl
			
			},

			callback : function(opt, success, response){

				var result = response.responseText;

				if(result == 1){
				
					me.formPanel.delImg(me.fileId);

					me.delSelf();
				
				}else{
				
					Ext.MessageBox.alert('提示信息', '删除失败');
				
				}
			
			}

		});
	
	},

	initComponent : function(){

		this.callParent();

		var me = this;

		this.on('render', function(){

			if(me.showDel){
	
				me.getEl().on({
				
					mouseover : function(){
					
						me.down('button').show();
					
					},

					mouseleave : function(){
					
						me.down('button').hide();
					
					}
				
				});

			}

		});

		if(this.initShow) this.showImg(null, this.imgUrl);
	
	}

});