Ext.define('HDCWS.view.Product.Add', {

    extend : 'Ext.window.Window',

	width : 800,

	height : 500,

	modal : true,

	title : '产品添加',

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
					
				name : 'name', 
					
				fieldLabel : '名称', 
					
				allowBlank : false,

				blankText : '请输入名称',
					
				labelAlign : 'right', 
					
				height : 30, 
					
				labelWidth : 70
					
			},
			
			{
				
				xtype : 'textfield', 
					
				name : 'price', 
					
				fieldLabel : '价格(分)',

				blankText : '请输入价格',
					
				allowBlank : false, 
					
				labelAlign : 'right', 
					
				height : 30, 
					
				labelWidth : 70,

				regex : /^\d+$/,

				regexText : '请输入整数'
					
			},

			{
				
				height : 20,

				border : 0,

				padding : '0 0 0 75',
					
				html : '请输入整数,单位(<span style="color:red">分</span>),如价格为5.12元,可输入512'
				
			},			

			{
				
				xtype : 'combobox', 
					
				name : 'cid', 
					
				fieldLabel : '类型', 
					
				allowBlank : false, 
					
				labelAlign : 'right', 
					
				height : 30, 
					
				labelWidth : 70,

				editable : false,

				emptyText : '请选择类型',

				blankText : '请选择类型',

				store : Ext.create('HDCWS.store.ProductCat'),

				displayField : 'name',

				valueField : 'id'
					
			},

			{
				
				height : 20,

				border : 0,

				padding : '0 0 0 75',
					
				html : '如果没有产品类型,<span class="span-add-cat" style="color:#3892D3;cursor:pointer;font-weight:700">点击此处添加</span>'
				
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
					
				fieldLabel : '简介',
					
				blankText : '请输入简介',
					
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

						text : '详细描述:',

						margin : '0 0 0 12'
					
					},
					
					{
					
						xtype : 'textareafield',
							
						name : 'content',

						id : 'product_content',
							
						height : 250,
							
						labelWidth : 70, 
							
						width : 700

					}
				
				]
			
			},

			{
			
				xtype : 'panel',

				cls : 'product-up-panel',

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

						id : 'product_thumb_img'
					
					},

					{
					
						xtype : 'button',

						text : '点击上传',

						width : 100,

						height : 25
					
					},

					{
					
						xtype : 'panel',

						id : 'product_thumb_img_panel',

						border : 0,

						padding : '5 0 5 75'
					
					}
				
				]
			
			},

			{

				height : 20,

				border : 0,

				padding : '0 0 0 75',
					
				html : '产品列表中显示的图片(单张)'
				
			},

			{
			
				xtype : 'panel',

				cls : 'product-up-panel',

				border : 0,

				items : [

					{
						
						xtype : 'hidden', 
							
						name : 'imgurl',
							
						value : ''

					},
					
					{
						
						xtype : 'label',

						text : '展示大图:',

						margin : '0 5 0 16'

					},
				
					{
					
						xtype : 'button',

						text : '点击上传',

						width : 100,

						height : 25,

						id : 'product_img'
					
					},

					{
					
						xtype : 'button',

						text : '点击上传',

						width : 100,

						height : 25
					
					},

					{
					
						xtype : 'panel',

						id : 'product_img_panel',

						border : 0,

						padding : '5 0 5 75'

					}
				
				]
			
			},

			{
				
				height : 20,

				border : 0,

				padding : '0 0 0 75',
					
				html : '以特效的形式展示(多张)'
				
			},

			{
				
				xtype : 'textfield', 
					
				name : 'buypurl',
					
				fieldLabel : '购买链接',
					
				labelAlign : 'right', 
					
				height : 30, 
					
				labelWidth : 70,

				regex : /^https?\:\/\//,

				regexText : '链接格式不正确'
					
			},

			{
				
				height : 20,

				border : 0,

				padding : '0 0 0 75',
					
				html : '<span style="color:red">可以不填</span>,若填写(以http或https开头),将在<span style="color:red">产品详情页</span>显示'
				
			}
		
		],

		listeners : {
		
			render : {
			
				fn : function(){

					var win = this.up('window');
				
					this.getEl().query('span.span-add-cat')[0].onclick = function(){
						
						win.controller.showCatList();
							
					}
				
				}
			
			}
		
		}

	},

	bbar : [

		'->',
		
		{xtype : 'button', width : 120, height : 40, text: '取消', action : 'proAddCancel', handler : function(){this.up('window').close();}},

		{xtype : 'button', width : 120, height : 40, text: '确定', action : 'proAddSure', cls : 'add-sure', overCls : 'add-sure-hover'}
	
	],

	initComponent : function(){
		
		this.callParent();

		var me = this;

		Ext.Loader.loadScript({
			
			url : AP + '/resources/js/kindeditor/kindeditor-min.js', 
				
			onLoad : function(){

				me.keditor = KindEditor.create('#product_content', {
				
					width : '670px',

					height : '500px',

					resizeType : 0,
					
					uploadJson : APP + '/Product/uploadJson',
					
					fileManagerJson : APP + '/Product/fileManagerJson',
					
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

					upload_url : APP + '/Product/uploadThumbImg',

					button_placeholder_id : 'product_thumb_img',
				
					file_post_name : 'thumbImg',

					button_action : -100,

					upload_start_handler : function(file){

						if(me.thumbImg){
						
							me.thumbImg.delSelf();
						
						}

						var showCom = me.down('#product_thumb_img_panel'),

							img = Ext.create('HDCWS.view.Product.Img', {

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

				new SWFUpload(Ext.merge({}, config, {

					upload_url : APP + '/Product/uploadImg',

					button_placeholder_id : 'product_img',
				
					file_post_name : 'img',

					upload_start_handler : function(file){

						if(!me.imgList) me.imgList = {};

						var showCom = me.down('#product_img_panel'),

							img = Ext.create('HDCWS.view.Product.Img', {
							
								fileId : file.id,

								formPanel : me
							
							});

						me.imgList[file.id] = img;

						showCom.add(img);
					
					},

					upload_progress_handler : function(file, bytes, total){

						me.imgList[file.id].showProcss(file, bytes, total);
							
					},

					upload_success_handler : function(file, data, response){

						me.imgList[file.id].showImg(file, data, response);
						
					},

					upload_error_handler : function(file, error, message){

						this.cancelUpload();

						this.stopUpload();

						me.imgList[file.id].showError(file, error, message);
							
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

		Ext.query('.kepanel textarea#product_content-inputEl')[0].value = this.keditor.html();

		if(this.thumbImg) this.down('hidden[name=thumburl]').setValue(this.thumbImg.imgUrl);

		else{
			
			Ext.MessageBox.alert('提示信息', '未上传缩略图');

			return false;

		}
		
		if(this.imgList){

			var arr = [], list = this.imgList;

			for(var i in list){
			
				if(list[i]) arr.push(list[i].imgUrl);
			
			}

			this.down('hidden[name=imgurl]').setValue(arr.join(';'));
		
		}else{
			
			Ext.MessageBox.alert('提示信息', '未上传产品大图');

			return false;

		}

		return true;

	}

});