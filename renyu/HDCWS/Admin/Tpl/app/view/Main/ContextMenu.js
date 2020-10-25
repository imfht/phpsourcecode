Ext.define('HDCWS.view.Main.ContextMenu', {

	extend : 'Ext.menu.Menu',

	items : [
		
		{text : '产品管理', iconCls : 'contextmenu-product', handler : function(){
		
			HDCWSAPP.deskTop.manageProduct();
		
		}},

		{text : '文章管理', iconCls : 'contextmenu-article', handler : function(){
		
			HDCWSAPP.deskTop.manageArticle();
		
		}},

		{text : '访客留言', iconCls : 'contextmenu-comment', handler : function(){
		
			HDCWSAPP.deskTop.manageComment();
		
		}},

		{text : '友情链接', iconCls : 'contextmenu-link', handler : function(){
		
			HDCWSAPP.deskTop.manageLinks();
		
		}}
	
	],

	listeners : {
	
		hide : {
		
			fn : function(){
			
				var me = this;
				
				setTimeout(function(){
				
					me.destroy();
				
				}, 1000);
			
			}
		
		}
	
	},

	showMe : function(e){

		this.showAt(e.getXY());
	
	}

});