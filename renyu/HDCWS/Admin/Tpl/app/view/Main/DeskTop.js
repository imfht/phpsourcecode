Ext.define('HDCWS.view.Main.DeskTop', {

    extend : 'Ext.panel.Panel',

	anchor : '100% -40',

	border : 0,

	bodyCls : 'desk-bg',

	layout : {

		type : 'absolute'

	},

	items : [

		Ext.create('HDCWS.view.Main.DeskIcon', {html : '产品管理', bodyCls : 'icon-product', action : 'manageProduct'}),
		
		Ext.create('HDCWS.view.Main.DeskIcon', {html : '文章管理', bodyCls : 'icon-aticle', action : 'manageArticle'}),

		Ext.create('HDCWS.view.Main.DeskIcon', {html : '访客留言', bodyCls : 'icon-comment', action : 'manageComment'}),

		Ext.create('HDCWS.view.Main.DeskIcon', {html : '友情链接', bodyCls : 'icon-links', action : 'manageLinks'}),

		Ext.create('HDCWS.view.Main.DeskIcon', {html : '导航设置', bodyCls : 'icon-nav', action : 'manageNav'}),

		Ext.create('HDCWS.view.Main.DeskIcon', {html : 'Banner', bodyCls : 'icon-banner', action : 'manageBanner'}),

		Ext.create('HDCWS.view.Main.DeskIcon', {html : '用户管理', bodyCls : 'icon-user', action : 'manageUser', hidden : true}),

		Ext.create('HDCWS.view.Main.DeskIcon', {html : '网站管理', bodyCls : 'icon-web', action : 'manageWeb', hidden : true})
	
	],

	listeners : {
	
		afterrender : {
		
			fn : function(){
			
				this.el.on('contextmenu', function(e, el, eOpts){
				
					e.preventDefault();
					
					e.stopEvent();

					Ext.create('HDCWS.view.Main.ContextMenu').showMe(e);
				
				});
			
			}
		
		},

		resize : {
		
			fn : function(){

				this.resetIcons();
			
			}
		
		}
	
	},

	resetIcons : function(){
	
		var size = this.getSize(),
			
			width = size.width,
				
			height = size.height - 80,

			perW = 70 + 10,

			perH = 80,
				
			icons = this.query('panel'),

			yMax = Math.floor(height / perH),
				
			xN = 0, yN = 0;

		Ext.Array.forEach(icons, function(item){

			if(yN > yMax){

				xN++;

				yN = 0;
			
			}

			item.setX(xN * perW);

			item.setY(yN * perH);

			yN++;
		
		});
	
	}

});

Ext.define('HDCWS.view.Img', {

	extend : 'Ext.panel.Panel',

	width : 200,

	height : 140,

	padding : '5 5 5 5',

	cls : 'img-panel loading',

	bodyCls : 'img-panel-body',

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

			cls : 'img-remove',

			handler : function(){

				var me = this;
			
				Ext.MessageBox.confirm('提示信息', '确定删除吗', function(flag){

					if(flag == 'yes') me.up('panel').delImg();

				});
			
			}
	
		},

		{
		
			xtype : 'progressbar',

			cls : 'img-progressbar',

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