Ext.define('HDCWS.view.Banner.Edit', {

    extend : 'Ext.window.Window',

	width : 800,

	height : 500,

	modal : true,

	title : 'Banner编辑',

	iconCls : 'win-add-icon',

	autoShow : true,

	maximizable : true,

	padding : '10',

	border : 0,

	overflowY : 'scroll',

	items : {
	
		xtype : 'form',

		border : 0,

		layout : 'form',

		items : [
			
			{
				
				xtype : 'textfield', 
					
				name : 'title', 
					
				fieldLabel : '标题', 
					
				allowBlank : false,

				blankText : '请输入标题',
					
				labelAlign : 'right', 
					
				height : 30, 
					
				labelWidth : 70
					
			},

			{
			
				xtype : 'panel',

				cls : 'banner-up-panel',

				border : 0,

				items : [

					{
						
						xtype : 'hidden', 
							
						name : 'src', 
							
						value : ''
							
					},
					
					{
						
						xtype : 'label',

						text : '缩略图:',

						margin : '0 5 0 28'
						
						
					},
				
					{
					
						xtype : 'button',

						text : '点击上传',

						width : 100,

						height : 25,

						id : 'banner_img'
					
					},

					{
					
						xtype : 'button',

						text : '点击上传',

						width : 100,

						height : 25
					
					},

					{
					
						xtype : 'panel',

						id : 'banner_img_panel',

						border : 0,

						padding : '5 0 5 75'
					
					}
				
				]
			
			},

			{
				
				height : 20,

				border : 0,

				padding : '0 0 0 75',
					
				html : '最好上传<span style="color:red;">1200*360</span>的图片'
				
			},

			{
				
				xtype : 'textfield', 
					
				name : 'url', 
					
				fieldLabel : '链接地址', 
					
				allowBlank : false,

				blankText : '请输入链接地址',
					
				labelAlign : 'right', 
					
				height : 30, 
					
				labelWidth : 70
					
			},

			{
				
				xtype : 'radiogroup',
					
				name : 'target',
					
				fieldLabel : '打开方式',
					
				labelAlign : 'right',

				height : 30,

				fieldBodyCls : 'art-radio-cls',
					
				labelWidth : 70,

				items : [
				
					{boxLabel : '本页', name : 'target', inputValue : '_self'},

					{boxLabel : '新窗口', name : 'target', inputValue : '_blank'}
				
				]
					
			},

			{
				
				xtype : 'textfield', 
					
				name : 'sort', 
					
				fieldLabel : '排序', 
					
				labelAlign : 'right', 
					
				height : 30, 
					
				labelWidth : 70,

				allowBlank : false,

				blankText : '请输入排序',

				value : 1,
					
				regex : /^\d+$/,
					
				regexText : '请填写数字'
					
			},

			{
				
				xtype : 'hidden', 
					
				name : 'id', 
					
				value : ''
					
			}
		
		]

	},

	bbar : [

		'->',
		
		{xtype : 'button', width : 120, height : 40, text: '取消', action : 'bannerEditCancel', handler : function(){this.up('window').close();}},

		{xtype : 'button', width : 120, height : 40, text: '确定', action : 'bannerEditSure', cls : 'add-sure', overCls : 'add-sure-hover'}
	
	],

	initComponent : function(){
		
		this.callParent();

		var me = this,

			bannerData = this.bannerData;

		Ext.Loader.loadScript({
			
			url : AP + '/resources/js/swfupload/swfupload.js',

			onLoad : function(){

				var config = {
						
					flash_url : AP + '/resources/js/swfupload/swfupload.swf',

					file_size_limit : '10MB',

					file_types : "*.jpg;*.jpeg;*.png;*.bmp;*.gif",

					button_width : 100,

					button_height : 25,

					button_cursor : -2,

					button_window_mode : 'transparent',

					post_params : {

						s : AS
							
					},

					file_dialog_complete_handler : function(select, queue, total){

						if(queue > 0){

							this.startUpload();

						}

					},

					upload_complete_handler : function(file){

						this.startUpload();

					}
				
				};

				new SWFUpload(Ext.merge({}, config, {

					upload_url : APP + '/Banner/upload',

					button_placeholder_id : 'banner_img',
				
					file_post_name : 'img',

					button_action : -100,

					swfupload_loaded_handler : function(){

						var showCom = me.down('#banner_img_panel'),

							img = Ext.create('HDCWS.view.Banner.Img', {
							
								showDel : false,

								initShow : true,

								fileId : parseInt(Math.random() * 10000),

								imgUrl : bannerData.src
							
							});

						me.bannerImg = img;

						showCom.add(img);
					 
					 },

					upload_start_handler : function(file){

						if(me.bannerImg){
						
							me.bannerImg.delSelf();
						
						}

						var showCom = me.down('#banner_img_panel'),

							img = Ext.create('HDCWS.view.Banner.Img', {

								fileId : file.id,
							
								showDel : false
							
							});

						me.bannerImg = img;

						showCom.add(img);
					
					},

					upload_progress_handler : function(file, bytes, total){

						me.bannerImg.showProcss(file, bytes, total);
							
					},

					upload_success_handler : function(file, data, response){

						me.bannerImg.showImg(file, data, response);
						
					},

					upload_error_handler : function(file, error, message){

						this.cancelUpload();

						this.stopUpload();

						me.bannerImg.showError(file, error, message);
							
					}
				
				}));

				config = null;

			}

		});

		this.down('textfield[name=title]').setValue(bannerData.title);

		this.down('textfield[name=url]').setValue(bannerData.url);

		this.down('textfield[name=sort]').setValue(bannerData.sort);

		this.down('radiogroup[name=target]').setValue({target : bannerData.target});

		this.down('hidden[name=id]').setValue(bannerData.id);

	},

	setHideValue : function(){

		if(this.bannerImg) this.down('hidden[name=src]').setValue(this.bannerImg.imgUrl);

		else{
			
			Ext.MessageBox.alert('提示信息', '未上传banner图');

			return false;

		}

		return true;

	}

});