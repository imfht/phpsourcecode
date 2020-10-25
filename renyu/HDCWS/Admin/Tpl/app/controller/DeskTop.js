Ext.define('HDCWS.controller.DeskTop', {

    extend : 'Ext.app.Controller',

	id : 'desktopController',

    init : function(){

		if(this.initFlag) return;

		this.initFlag = true;

		var me = this;

		var productPanel = Ext.ComponentQuery.query('panel[action=manageProduct]')[0];
	
		productPanel.getEl().on({
		
			click : me.manageProduct
		
		});

		var articlePanel = Ext.ComponentQuery.query('panel[action=manageArticle]')[0];
	
		articlePanel.getEl().on({
		
			click : me.manageArticle
		
		});

		var commentPanel = Ext.ComponentQuery.query('panel[action=manageComment]')[0];
	
		commentPanel.getEl().on({
		
			click : me.manageComment
		
		});

		var linksPanel = Ext.ComponentQuery.query('panel[action=manageLinks]')[0];
	
		linksPanel.getEl().on({
		
			click : me.manageLinks
		
		});

		var userPanel = Ext.ComponentQuery.query('panel[action=manageUser]')[0];
	
		userPanel.getEl().on({
		
			click : me.manageUser
		
		});

		var navPanel = Ext.ComponentQuery.query('panel[action=manageNav]')[0];
	
		navPanel.getEl().on({
		
			click : me.manageNav
		
		});

		var bannerPanel = Ext.ComponentQuery.query('panel[action=manageBanner]')[0];
	
		bannerPanel.getEl().on({
		
			click : me.manageBanner
		
		});

		var webPanel = Ext.ComponentQuery.query('panel[action=manageWeb]')[0];
	
		webPanel.getEl().on({
		
			click : me.manageWeb
		
		});

		this.resetUser();

    },

	manageProduct : function(){

		var me = this;

		if(this.pro){
			
			this.pro.showList();
	
			return;
		
		}
		
		Ext.require('HDCWS.controller.Product', function(){

			var pro = Ext.create('HDCWS.controller.Product');

			pro.showList();

			me.pro = pro;
		
		});
	
	},

	manageArticle : function(){
	
		var me = this;

		if(this.article){
			
			this.article.showList();
	
			return;
		
		}
		
		Ext.require('HDCWS.controller.Article', function(){

			var article = Ext.create('HDCWS.controller.Article');

			article.showList();

			me.article = article;
		
		});
	
	},

	manageComment : function(){
	
		var me = this;

		if(this.comment){
			
			this.comment.showList();
	
			return;
		
		}
		
		Ext.require('HDCWS.controller.Comment', function(){

			var comment = Ext.create('HDCWS.controller.Comment');

			comment.showList();

			me.comment = comment;
		
		});
	
	},

	manageLinks : function(){

		var me = this;

		if(this.friendLink){
			
			this.friendLink.showList();
	
			return;
		
		}
		
		Ext.require('HDCWS.controller.FriendLink', function(){

			var friendLink = Ext.create('HDCWS.controller.FriendLink');

			friendLink.showList();

			me.friendLink = friendLink;
		
		});
	
	},

	manageUser : function(){
	
		var me = this;

		if(this.user){
			
			this.user.showList();
	
			return;
		
		}
		
		Ext.require('HDCWS.controller.User', function(){

			var user = Ext.create('HDCWS.controller.User');

			user.showList();

			me.user = user;
		
		});
	
	},

	manageNav : function(){
	
		var me = this;

		if(this.nav){
			
			this.nav.showList();
	
			return;
		
		}
		
		Ext.require('HDCWS.controller.Nav', function(){

			var nav = Ext.create('HDCWS.controller.Nav');

			nav.showList();

			me.nav = nav;
		
		});
	
	},

	manageBanner : function(){
	
		var me = this;

		if(this.banner){
			
			this.banner.showList();
	
			return;
		
		}
		
		Ext.require('HDCWS.controller.Banner', function(){

			var banner = Ext.create('HDCWS.controller.Banner');

			banner.showList();

			me.banner = banner;
		
		});
	
	},

	manageWeb : function(){
	
		var me = this;

		if(this.web){
			
			this.web.showList();
	
			return;
		
		}
		
		Ext.require('HDCWS.controller.Web', function(){

			var web = Ext.create('HDCWS.controller.Web');

			web.showList();

			me.web = web;
		
		});
	
	},

	setDeskTopBg : function(dir){

		Ext.getCmp('desktop').setBodyStyle('background-image', 'url(' + dir + ')');
	
	},

	resetUser : function(){
	
		var user = this.application.sessionUser;

		if(user.isSuper){

			Ext.ComponentQuery.query('panel[action=manageUser]')[0].show();

			Ext.ComponentQuery.query('panel[action=manageWeb]')[0].show();
		
		}else{
		
			Ext.ComponentQuery.query('panel[action=manageUser]')[0].hide();

			Ext.ComponentQuery.query('panel[action=manageWeb]')[0].hide();
		
		}
	
	}

});