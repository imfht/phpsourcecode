Ext.define('HDCWS.view.System.Window', {
	
	extend : 'Ext.window.Window',
	
	width : 800,

	height : 500,

	minWidth : 800,

	minHeight : 500,

	title : '系统设置',

	iconCls : 'list-icon',

	autoShow : true,

	modal : true,

	resizable : true,

	maximizable : true,

	layout : 'column',
	
	items : [
	         
		{
			
			xtype : 'panel',

			bodyStyle : 'padding:0',
				
			columnWidth : 0.25,

			defaults : {
			
				height : 450
			
			},

			layout : 'accordion',

			items : [

				{

					xtype : 'treepanel',

					name : 'desktoppanel',

					title : '桌面背景',

					rootVisible : false,

					store : Ext.create('HDCWS.store.DeskTopBg'),

					dockedItems : [
						
						{
							
							xtype : 'toolbar',
								
							dock : 'bottom',

							layout : 'absolute',

							cls : 'desktop-upload',
							
							items : [
								
								{xtype : 'button', text : '上传背景', x : 30, y : 0, width : 130, height : 30, cls : 'edit-sure', overCls : 'edit-sure-hover', style : 'z-index : 1'},

								{xtype : 'button', text : '上传背景', x : 30, y : 0, id : 'desktop_upload', width : 130, height : 30, cls : 'edit-sure', overCls : 'edit-sure-hover', style : 'z-index : 2'}
							
							]
							
						}
					
					],

					listeners : {
					
						itemclick : {
							
							fn : function(me, record, item, index, e, eOpts){
							
								this.up('window').preview(record, item, index);
							
							}

						},

						itemcontextmenu : {
						
							fn : function(me, record, item, index, e, eOpts){

								e.preventDefault();

								e.stopEvent();
							
								var menu = Ext.widget('menu', {
								
									items : [

										{text : '预览', iconCls : 'list-edit', handler : function(){me.up('window').preview(record, item, index)}},
									
										{text : '删除', iconCls : 'list-del', handler : function(){me.up('window').delDeskTopBg(record, item, index)}}
									
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

									}
								
								});

								menu.showAt(e.getXY());
							
							}
						
						},

						render : {
						
							fn : function(){
							
								var me = this;

								Ext.Loader.loadScript({
									
									url : AP + '/resources/js/swfupload/swfupload.js',

									onLoad : function(){

										var config = {
												
											flash_url : AP + '/resources/js/swfupload/swfupload.swf',

											file_size_limit : '10MB',

											file_types : "*.jpg;*.jpeg;*.png;*.bmp;*.gif",

											button_width : 130,

											button_height : 30,

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

											upload_url : APP + '/System/uploadDeskTop',

											button_placeholder_id : 'desktop_upload',
										
											file_post_name : 'desktop',

											button_action : -100,

											upload_start_handler : function(file){

												me.desktopProgressBar = Ext.create('Ext.ProgressBar', {
												
													width : 400,

													height : 15,

													renderTo : Ext.getBody()
												
												});
											
											},

											upload_progress_handler : function(file, bytes, total){

												if(total > 0) me.desktopProgressBar.updateProgress(bytes / total);
													
											},

											upload_success_handler : function(file, data, response){

												me.desktopProgressBar.hide();

												me.addDeskTopNode(data);

											},

											upload_error_handler : function(file, error, message){

												this.cancelUpload();

												this.stopUpload();

												me.desktopProgressBar.hide();

												Ext.Msg.alert('提示信息', '上传出错');
													
											}
										
										}));

										config = null;

									}

								});
							
							}
						
						}
					
					},

					addDeskTopNode : function(name){

						if(/\w/.test(name)){

							var root = this.getRootNode(), text = name.split('/');

							root.appendChild({dir : name, text : text[text.length - 1], leaf : true});

						}
					
					}

				}
			
			]

		},

		{
			
			xtype : 'panel',

			name : 'viewpanel',
				
			columnWidth : 0.75,
				
			header : false,

			layout : 'fit',

			defaults : {
			
				height : 455,

				border : 0
			
			}
			
		}
	         
	],

	delDeskTopBg : function(record, item, index){

		this.controller.delDeskTopBg(record, item, index, this);

	},

	delDeskTopNode : function(record, item, index){
	
		var desktopPanel = this.down('treepanel[name=desktoppanel]'),

			viewpanel = this.down('panel[name=viewpanel]'),
			
			root = desktopPanel.getRootNode();

		root.removeChild(root.getChildAt(index));

		viewpanel.removeAll();
	
	},

	preview : function(record, item, index){
	
		var me = this,
			
			viewpanel = this.down('panel[name=viewpanel]'),
			
			data = record.getData();

		this.deskTopDir = data.dir,

		viewpanel.removeAll();

		viewpanel.add(Ext.widget('panel', {

			header : {
			
				title : '桌面背景预览'
			
			},

			tools : [
				
				{

					type : 'save',

					tooltip : '保存为默认背景',

					handler : function(event, toolEl, panel){

						me.controller.saveDeskTopBg(me.deskTopDir);
					
					}

				},

				{

					type : 'close',

					tooltip : '删除',

					handler : function(event, toolEl, panel){

						me.controller.delDeskTopBg(record, item, index, me);
					
					}

				}
			
			],

			bodyStyle : 'background:url(' + data.dir + ')'
		
		}));
	
	}

});