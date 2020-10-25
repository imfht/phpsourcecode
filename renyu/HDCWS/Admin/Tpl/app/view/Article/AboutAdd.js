Ext.define('HDCWS.view.Article.AboutAdd', {

    extend : 'Ext.window.Window',

	width : 800,

	height : 500,

	modal : true,

	title : '文章添加',

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

				cls : 'article-up-panel',

				border : 0,

				items : [

					{
						
						xtype : 'hidden', 
							
						name : 'thumburl', 
							
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

						id : 'article_thumb_img'
					
					},

					{
					
						xtype : 'button',

						text : '点击上传',

						width : 100,

						height : 25
					
					},

					{
					
						xtype : 'panel',

						id : 'article_thumb_img_panel',

						border : 0,

						padding : '5 0 5 75'
					
					}
				
				]
			
			},

			{
				
				xtype : 'textfield', 
					
				name : 'keywords', 
					
				fieldLabel : '关键字', 
					
				labelAlign : 'right', 
					
				height : 30, 
					
				labelWidth : 70
					
			},

			{
				
				height : 20,

				border : 0,

				padding : '0 0 0 75',
					
				html : '关键字以<span style="color:red">空格</span>或<span style="color:red">逗号</span>或<span style="color:red">分号</span>隔开'
				
			},

			{
				
				xtype : 'textareafield', 
					
				name : 'description',
					
				fieldLabel : '描述',
					
				blankText : '请输入描述',
					
				allowBlank : false,
					
				labelAlign : 'right', 
					
				height : 100, 
					
				labelWidth : 70

			},

			{
			
				xtype : 'panel',

				cls : 'kepanel',

				height : 570,

				border : 0,

				items : [

					{
					
						xtype : 'label',

						cls : 'edit-label',

						text : '文章内容:',

						margin : '0 0 0 12'
					
					},
					
					{
					
						xtype : 'textareafield',
							
						name : 'content',

						id : 'article_content',
							
						height : 250,
							
						labelWidth : 70, 
							
						width : 700

					}
				
				]
			
			},

			{
				
				height : 20,

				border : 0,

				padding : '0 0 0 75'
				
			},

			{xtype : 'hidden', name : 'cid', value : 0},

			{xtype : 'hidden', name : 'tid', value : 3},

			{xtype : 'hidden', name : 'mid', value : 2},

			{xtype : 'hidden', name : 'status', value : 1}
		
		]

	},

	bbar : [

		'->',
		
		{xtype : 'button', width : 120, height : 40, text: '取消', action : 'artAddCancel', handler : function(){this.up('window').close();}},

		{xtype : 'button', width : 120, height : 40, text: '确定', action : 'artAddSure', cls : 'add-sure', overCls : 'add-sure-hover'}
	
	],

	initComponent : function(){
		
		this.callParent();

		var me = this;

		Ext.Loader.loadScript({
			
			url : AP + '/resources/js/kindeditor/kindeditor-min.js', 
				
			onLoad : function(){

				me.keditor = KindEditor.create('#article_content', {
				
					width : '670px',

					height : '500px',

					resizeType : 0,
					
					uploadJson : APP + '/Article/uploadJson',
					
					fileManagerJson : APP + '/Article/fileManagerJson',
					
					extraFileUploadParams : {

						s : AS

					}
				
				});

			}

		});

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

					upload_url : APP + '/Article/uploadThumbImg',

					button_placeholder_id : 'article_thumb_img',
				
					file_post_name : 'thumbImg',

					button_action : -100,

					upload_start_handler : function(file){

						if(me.thumbImg){
						
							me.thumbImg.delSelf();
						
						}

						var showCom = me.down('#article_thumb_img_panel'),

							img = Ext.create('HDCWS.view.Article.Img', {

								fileId : file.id,
							
								showDel : false
							
							});

						me.thumbImg = img;

						showCom.add(img);
					
					},

					upload_progress_handler : function(file, bytes, total){

						me.thumbImg.showProcss(file, bytes, total);
							
					},

					upload_success_handler : function(file, data, response){

						me.thumbImg.showImg(file, data, response);
						
					},

					upload_error_handler : function(file, error, message){

						this.cancelUpload();

						this.stopUpload();

						me.thumbImg.showError(file, error, message);
							
					}
				
				}));

				config = null;

			}

		});

	},

	delImg : function(fileId){

		var me = this;

		for(var i in me.imgList){
		
			if(i == fileId){

				delete me.imgList[fileId];
			
				break;

			}
		
		}
	
	},

	setHideValue : function(){

		Ext.query('.kepanel textarea#article_content-inputEl')[0].value = this.keditor.html();

		if(this.thumbImg) this.down('hidden[name=thumburl]').setValue(this.thumbImg.imgUrl);

		else{
			
			Ext.MessageBox.alert('提示信息', '未上传缩略图');

			return false;

		}

		return true;

	}

});