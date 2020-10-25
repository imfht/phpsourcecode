Ext.define('HDCWS.view.Article.List', {

    extend : 'Ext.window.Window',

	width : 1200,

	height : 500,

	minWidth : 800,

	minHeight : 500,

	title : '文章列表',

	iconCls : 'list-icon',

	autoShow : true,

	modal : true,

	resizable : true,

	maximizable : true,

	layout : 'fit',

	tbar : [

		{xtype : 'button', text: '添加', iconCls : 'list-add', menu : {

			xtype : 'menu', width : 100, items : [
				
				{text : '普通文章', iconCls : 'menu-ico-1', handler : function(){this.up('window').addArticle(1)}},

				{text : '公司团队', iconCls : 'menu-ico-1', handler : function(){this.up('window').addArticle(2)}},

				{text : '关于公司', iconCls : 'menu-ico-1', handler : function(){this.up('window').addArticle(3)}}
			
			]
		
		}},

		{xtype : 'button', text: '删除', iconCls : 'list-del', action : 'artDel'},

		{xtype : 'button', text: '文章类型管理', iconCls : 'cat-manage', action : 'artCatManage'},

		{xtype : 'combobox', fieldLabel : '类型', labelWidth : 35, displayField : 'name', valueField : 'id', value : '全部', width : 185, editable : false, 

			store : Ext.create('HDCWS.store.ArticleCat', {
				
				autoLoad : true,

				params : {status : 'all'},
			
				listeners : {

					load : {
					
						fn : function(store, records, opts){ 

							store.insert(0, [{id : '', name : '全部'}]); 
						
						}
					
					}

				}
				
			})
				
		},

		{xtype : 'textfield', labelWidth : 0, labelAlign : 'right', width : 150, enableKeyEvents : true,
			
			listeners : {

				keyup : {
				
					fn : function(me, e, eOpts){

						if(e.getKey() == 13) this.nextSibling().fireEvent('click')
					
					}
				
				}

			}},

		{xtype : 'button', text: '搜索', iconCls : 'list-search', action : 'artSearch'}
	
	],

	initComponent : function(){
		
		this.callParent();

		var me = this;

		this.myStore = Ext.create('HDCWS.store.Article', {controller : this.controller});

		var selectModel = Ext.create('Ext.selection.CheckboxModel');

		this.add({

			xtype : 'gridpanel',

			store : me.myStore,

			selModel : selectModel,

			columns : {
			
				items : [

					{text : '文章标题', dataIndex : 'title', width : 250, menuDisabled : true, sortable : false},

					{

						xtype : 'templatecolumn', text : '文章类型', dataIndex : 'cname',

						tpl : new Ext.XTemplate(

							'<tpl if="this.isEmpty(cname)">',

								'<span>无</span>',

							'<tpl else>',

								'<span>{cname}</span>',

							'</tpl>',

							{
								
								isEmpty : function(name){

									return !((typeof name == 'string' || typeof name == 'number') && !/^\s*$/.test(name)) ? true : false;

								}
							
							}

						),

						width : 180, menuDisabled : true, sortable : false},

					{

						xtype : 'templatecolumn', text : '版块类型', dataIndex : 'tid',
							
						tpl : new Ext.XTemplate(

							'<tpl if="tid == 3">',

								'<span>关于公司</span>',

							'<tpl elseif="tid == 2">',

								'<span>公司团队</span>',

							'<tpl else>',

								'<span>普通</span>',

							'</tpl>'

						),

						width : 100, menuDisabled : true, sortable : false},

					{

						xtype : 'templatecolumn', text : '文章模型', dataIndex : 'mid',
							
						tpl : new Ext.XTemplate(

							'<tpl if="mid == 3">',

								'<span>特殊图片</span>',

							'<tpl elseif="mid == 2">',

								'<span>单页</span>',

							'<tpl else>',

								'<span>普通</span>',

							'</tpl>'

						),

						width : 100, menuDisabled : true, sortable : false},

					{
						
						xtype : 'templatecolumn', text : '导航链接', width : 200, align : 'center', menuDisabled : true,
							
						tpl : new Ext.XTemplate(

							'<tpl>',

								'<input type="text" value="Article/v?tid={tid}&cid={cid}&id={id}" style="width:115px;" />&nbsp;<a href="' + ROOT + '/Article/v?tid={tid}&cid={cid}&id={id}" target="_blank" style="color:#45BA6C;">前台访问</a>',

							'</tpl>'

						)
						
					},

					{text : '添加时间', dataIndex : 'time', width : 150, align : 'center', menuDisabled : true},

					{

						xtype : 'templatecolumn', text : '状态', dataIndex : 'status', 
							
						tpl : new Ext.XTemplate(

							'<tpl if="status == 1">',

								'<span style="color:#45BA6C;">启用</span>',

							'<tpl else>',

								'<span style="color:#F75557;">禁用</span>',

							'</tpl>'

						),

						width : 60, align : 'center', menuDisabled : true, sortable : false},

					{

						xtype : 'actioncolumn', text : '操作', 
							
						items: [
		
							{

								iconCls : 'list-col-edit',

								tooltip: '查看/编辑',

								handler: function(grid, rowIndex, colIndex){

									this.up('window').editArticle(grid, rowIndex, colIndex);

								}

							},
								
							{

								iconCls : 'list-col-del',

								tooltip: '删除',

								handler: function(grid, rowIndex, colIndex){

									this.up('window').delArticle(grid, rowIndex, colIndex);

								}

							}

						],
						
						width : 100, align : 'center', menuDisabled : true, sortable : false}
				
				]
			
			},

			bbar : {

				xtype : 'pagingtoolbar',

				store : me.myStore,

				prevText : '上一页',

				nextText : '上一页',

				firstText : '首页',

				lastText : '尾页',

				refreshText : '刷新',

				beforePageText : '页',

				afterPageText : '/ {0}',

				displayInfo : true

			}
		
		});

	},

	addArticle : function(tid){
	
		this.controller.showAdd(tid);
	
	},

	editArticle : function(grid, rowIndex, colIndex){

		var record = grid.getStore().getAt(rowIndex);
	
		this.controller.showEdit(record);
	
	},
	
	delArticle : function(grid, rowIndex, colIndex){

		var record = grid.getStore().getAt(rowIndex);
	
		this.controller.del(record, grid);
	
	}

});

Ext.define('HDCWS.view.Article.Img', {

	extend : 'Ext.panel.Panel',

	width : 200,

	height : 140,

	padding : '5 5 5 5',

	cls : 'article-img-panel loading',

	bodyCls : 'article-img-panel-body',

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

			cls : 'article-img-remove',

			handler : function(){

				var me = this;
			
				Ext.MessageBox.confirm('提示信息', '确定删除吗', function(flag){

					if(flag == 'yes') me.up('panel').delImg();

				});
			
			}
	
		},

		{
		
			xtype : 'progressbar',

			cls : 'article-img-progressbar',

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
		
			url : APP + '/Article/delImg',

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