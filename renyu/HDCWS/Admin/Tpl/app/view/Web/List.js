Ext.define('HDCWS.view.Web.List', {

    extend : 'Ext.window.Window',

	width : 800,

	height : 500,

	minWidth : 600,

	minHeight : 400,

	title : '网站管理',

	iconCls : 'list-icon',

	autoShow : true,

	resizable : true,

	maximizable : true,

	modal : true,

    layout : {

        type : 'accordion',

        titleCollapse : false,

        animate : true,

        activeOnTop : true

    },

    items : [

		{

			xtype : 'form',

			name : 'webinfoPanel',

			title : '网站信息设置',

			layout : 'form',

			autoScroll : true,

			items : [
				
				{xtype : 'textfield', name : 'webname', fieldLabel : '网站名称', labelAlign : 'right', height : 30, labelWidth : 80},

				{
				
					xtype : 'panel',

					cls : 'img-up-panel',

					border : 0,

					items : [

						{
							
							xtype : 'hidden', 
								
							name : 'logourl', 
								
							value : ''
								
						},
						
						{
							
							xtype : 'label',

							text : '网站Logo:',

							margin : '0 5 0 22'
							
							
						},
					
						{
						
							xtype : 'button',

							text : '点击上传',

							width : 100,

							height : 25,

							id : 'img_btn'
						
						},

						{
						
							xtype : 'button',

							text : '点击上传',

							width : 100,

							height : 25
						
						},

						{
						
							xtype : 'panel',

							id : 'img_panel',

							border : 0,

							padding : '5 0 5 80'
						
						}
					
					]
				
				},

				{height : 20, border : 0, padding : '0 0 0 80', html : '上传尺寸为<span style="color:red;">204*45</span>最佳'},
				
				{xtype : 'textfield', name : 'weburl', fieldLabel : '网站地址', labelAlign : 'right', height : 30, labelWidth : 80},

				{height : 20, border : 0, padding : '0 0 0 80', html : '即购买的域名,例:<span style="color:red;">http://www.hidoger.com</span>'},

				{xtype : 'textfield', name : 'webtitle', fieldLabel : '网站标题', labelAlign : 'right', height : 30, labelWidth : 80},

				{xtype : 'textfield', name : 'keywords', fieldLabel : '网站关键字', labelAlign : 'right', height : 30, labelWidth : 80},

				{xtype : 'textareafield', name : 'description', fieldLabel : '网站描述', labelAlign : 'right', height : 80, labelWidth : 80},

				{xtype : 'textfield', name : 'email', fieldLabel : '网站邮箱', labelAlign : 'right', height : 30, labelWidth : 80},

				{xtype : 'textfield', name : 'fox', fieldLabel : '传真', labelAlign : 'right', height : 30, labelWidth : 80},

				{xtype : 'textfield', name : 'phone', fieldLabel : '电话', labelAlign : 'right', height : 30, labelWidth : 80},

				{xtype : 'textfield', name : 'address', fieldLabel : '公司地址', labelAlign : 'right', height : 30, labelWidth : 80},

				{xtype : 'textfield', name : 'beian', fieldLabel : '备案号', labelAlign : 'right', height : 30, labelWidth : 80},

				{xtype : 'textfield', name : 'copyright', fieldLabel : '版权文字', labelAlign : 'right', height : 30, labelWidth : 80}
			
			],

			dockedItems: [{

				xtype: 'toolbar',

				dock: 'bottom',
					
				border : 0,

				items: [

					'->',

					{xtype : 'button', text : '流量统计', width : 120, height : 40, cls : 'edit-cancel', action : 'webSta'},
					
					{xtype : 'button', text : '确定', width : 120, height : 40, cls : 'edit-sure', action : 'webinfoSure'}

				]

			}],

			setHideValue : function(){
			
				if(this.logo) this.down('hidden[name=logourl]').setValue(this.logo.imgUrl);
			
			}
			
		},
			
		{
			
			xtype : 'form',

			name : 'cachePanel',
				
			title : '缓存设置',

			layout : 'form',

			items : [

				{
					
					xtype : 'radiogroup',
						
					name : 'HTML_CACHE_ON',
						
					fieldLabel : '是否开启',
						
					labelAlign : 'right',

					height : 30,

					fieldBodyCls : 'pro-radio-cls',
						
					labelWidth : 100,

					items : [
					
						{boxLabel : '是', name : 'HTML_CACHE_ON', inputValue : 1},

						{boxLabel : '否', name : 'HTML_CACHE_ON', inputValue : 0}
					
					]
						
				},
				
				{xtype : 'textfield', name : 'HTML_FILE_SUFFIX', fieldLabel : '静态文件后缀', labelAlign : 'right', height : 30, labelWidth : 100, allowBlank : false, blankText : '请输入静态文件后缀', regex : /^\.\w+$/, regexText : '例:.html'},

				{xtype : 'textfield', name : 'HTML_CACHE_TIME', fieldLabel : '缓存有效期', labelAlign : 'right', height : 30, labelWidth : 100, allowBlank : false, blankText : '请输入缓存有效期', regex : /^\d+$/, regexText : '请输入数字'},

				{height : 20, border : 0, padding : '0 0 0 100', html : '<span style="color:red;">单位:秒</span>,例如,缓存一小时:3600'},
			
			],

			dockedItems: [{

				xtype: 'toolbar',

				dock: 'bottom',
					
				border : 0,

				items: [

					'->',

					{xtype : 'button', text : '清除缓存', width : 120, height : 40, action : 'clearcacheSure'},
					
					{xtype : 'button', text : '确定', width : 120, height : 40, cls : 'edit-sure', action : 'cacheSure'}

				]

			}]
			
		},

		{
			
			xtype : 'form',

			name : 'dbPanel',
				
			title : '数据库设置',

			layout : 'form',

			items : [

				{height : 20, border : 0, padding : '0 0 0 80', html : '<span style="color:red">数据库配置为网站核心配置,请仔细检查后再进行提交</span>'},
				
				{xtype : 'textfield', name : 'DB_TYPE', fieldLabel : '数据库类型', labelAlign : 'right', height : 30, labelWidth : 80, allowBlank : false, blankText : '请输入数据库类型'},

				{xtype : 'textfield', name : 'DB_HOST', fieldLabel : '数据库地址', labelAlign : 'right', height : 30, labelWidth : 80, allowBlank : false, blankText : '请输入数据库地址'},

				{xtype : 'textfield', name : 'DB_NAME', fieldLabel : '数据库名称', labelAlign : 'right', height : 30, labelWidth : 80, allowBlank : false, blankText : '请输入数据库名称'},

				{xtype : 'textfield', name : 'DB_USER', fieldLabel : '用户名', labelAlign : 'right', height : 30, labelWidth : 80, allowBlank : false, blankText : '请输入用户名'},

				{xtype : 'textfield', name : 'DB_PWD', fieldLabel : '密码', labelAlign : 'right', height : 30, labelWidth : 80, allowBlank : false, blankText : '请输入密码'}
			
			],

			dockedItems : [{

				xtype: 'toolbar',

				dock: 'bottom',
				
				border : 0,

				items: [

					'->',

					{xtype : 'button', text : '数据列表', width : 120, height : 40, action : 'tablelist'},

					{xtype : 'button', text : '数据还原', width : 120, height : 40, action : 'storedatalist'},
					
					{xtype : 'button', text : '确定', width : 120, height : 40, cls : 'edit-sure', action : 'dbSure'}

				]

			}]
			
		}

	],

	initComponent : function(){

		this.callParent();

		var me = this,
			
			data = this.configData,
				
			webinfoPanel = this.down('form[name=webinfoPanel]'),
				
			cachePanel = this.down('form[name=cachePanel]'),
				
			dbPanel = this.down('form[name=dbPanel]');
	
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

					upload_url : APP + '/Web/uploadLogo',

					button_placeholder_id : 'img_btn',
				
					file_post_name : 'logo',

					button_action : -100,

					swfupload_loaded_handler : function(){

						if(webinfoPanel.logo) return;

						var showCom = webinfoPanel.down('#img_panel'),

							img = Ext.create('HDCWS.view.Img', {
							
								showDel : false,

								initShow : true,

								fileId : parseInt(Math.random() * 10000),

								imgUrl : data.logourl
							
							});

						webinfoPanel.logo = img;

						showCom.add(img);
					 
					 },

					upload_start_handler : function(file){

						if(webinfoPanel.logo){
						
							webinfoPanel.logo.delSelf();
						
						}

						var showCom = webinfoPanel.down('#img_panel'),

							img = Ext.create('HDCWS.view.Img', {

								fileId : file.id,
							
								showDel : false
							
							});

						webinfoPanel.logo = img;

						showCom.add(img);
					
					},

					upload_progress_handler : function(file, bytes, total){

						webinfoPanel.logo.showProcss(file, bytes, total);
							
					},

					upload_success_handler : function(file, data, response){

						webinfoPanel.logo.showImg(file, data, response);
						
					},

					upload_error_handler : function(file, error, message){

						this.cancelUpload();

						this.stopUpload();

						webinfoPanel.logo.showError(file, error, message);
							
					}
				
				}));

				config = null;

			}

		});

		webinfoPanel.down('textfield[name=webname]').setValue(data.webname);

		webinfoPanel.down('textfield[name=weburl]').setValue(data.weburl);

		webinfoPanel.down('textfield[name=webtitle]').setValue(data.webtitle);

		webinfoPanel.down('textfield[name=keywords]').setValue(data.keywords);

		webinfoPanel.down('textareafield[name=description]').setValue(data.description);

		webinfoPanel.down('textfield[name=email]').setValue(data.email);

		webinfoPanel.down('textfield[name=fox]').setValue(data.fox);

		webinfoPanel.down('textfield[name=phone]').setValue(data.phone);

		webinfoPanel.down('textfield[name=address]').setValue(data.address);

		webinfoPanel.down('textfield[name=beian]').setValue(data.beian);

		webinfoPanel.down('textfield[name=copyright]').setValue(data.copyright);

		cachePanel.down('radiogroup[name=HTML_CACHE_ON]').setValue({HTML_CACHE_ON : data.HTML_CACHE_ON});

		cachePanel.down('textfield[name=HTML_FILE_SUFFIX]').setValue(data.HTML_FILE_SUFFIX);

		cachePanel.down('textfield[name=HTML_CACHE_TIME]').setValue(data.HTML_CACHE_TIME);

		dbPanel.down('textfield[name=DB_TYPE]').setValue(data.DB_TYPE);

		dbPanel.down('textfield[name=DB_HOST]').setValue(data.DB_HOST);

		dbPanel.down('textfield[name=DB_NAME]').setValue(data.DB_NAME);

		dbPanel.down('textfield[name=DB_USER]').setValue(data.DB_USER);

		dbPanel.down('textfield[name=DB_PWD]').setValue(data.DB_PWD);
	
	}

});