Ext.define('HDCWS.view.Statistics.List', {

	extend : 'Ext.window.Window',

	width : 850,

	height : 500,

	minWidth : 800,

	minHeight : 500,

	title : '流量统计列表',

	iconCls : 'list-icon',

	autoShow : true,

	resizable : true,

	maximizable : true,

	modal : true,

	layout : 'fit',

	tbar : [

		{xtype : 'button', text: '删除', iconCls : 'list-del', action : 'staDel'},

		{xtype : 'button', text: '图表显示', iconCls : 'list-chart', action : 'staChart'}

	],

	initComponent : function(){
	
		this.callParent();

		var me = this, 
			
			store = Ext.create('HDCWS.store.Statistics', {controller : this.controller});

		this.add({

			xtype : 'grid',

			store : store,

			selModel : new Ext.selection.CheckboxModel(),

			columns : [
				
				{text : 'IP', dataIndex : 'ip', width : 140, menuDisabled : true, sortable : false},

				{xtype : 'templatecolumn', text : '浏览器类型', dataIndex : 'brower', width : 140, menuDisabled : true, sortable : false,
					
					tpl : new Ext.XTemplate(

						'<tpl>',

							'{[this.getValue(values.brower)]}',

						'</tpl>',
						
						{
							
							disableFormats : true,

							getValue : function(brower){

								return HDCWS.view.Statistics.List.getBrowerInfo(brower);

							}

						}
					
					)
				
				},

				{text : '访问地址', dataIndex : 'go', width : 140, menuDisabled : true, sortable : false},

				{text : '来源地址', dataIndex : 'from', width : 140, menuDisabled : true, sortable : false},

				{text : '时间', dataIndex : 'time', width : 140, menuDisabled : true, sortable : false},

				{xtype : 'actioncolumn', text : '操作', width : 80, align : 'center', menuDisabled : true, sortable : false, items : [

					{

						iconCls : 'list-col-view',

						tooltip : '查看',

						handler : function(grid, rowIndex, colIndex){

							var record = grid.getStore().getAt(rowIndex);

							this.up('window').viewSta(record);

						}
					
					},

					{
					
						iconCls : 'list-col-del',

						tooltip : '删除',

						handler : function(grid, rowIndex, colIndex){

							var record = grid.getStore().getAt(rowIndex);

							this.up('window').delSta(grid, record);

						}
					
					}
				
				]}
			
			],

			bbar : {

				xtype : 'pagingtoolbar',

				store : store,

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

	viewSta : function(record){
	
		this.controller.viewSta(record);
	
	},

	delSta : function(grid, record){
	
		this.controller.delSta(record, grid);
	
	}

});

HDCWS.view.Statistics.List.addStatics({

	getBrowerInfo : function(brower){

		function check(regex){

			return regex.test(brower);

		}

		function version(is, regex) {
		
			var m;

			return (is && (m = regex.exec(brower))) ? (parseFloat(m[1]) || '') : '';

		}

		var isOpera = check(/opera/i),

			isChrome = check(/\bchrome\b/i),

			isWebKit = check(/webkit/i),

			isSafari = !isChrome && check(/safari/i),

			isTrident = check(/trident/i),

			isIE = !isOpera && (check(/msie/i) || isTrident),

			isGecko = !isWebKit && check(/gecko/i),

			isGecko3 = isGecko && check(/rv:\d+\.*\d*/i),

			isFF = isGecko3 && check(/rv:\d+\.*\d*/i),

			chromeVersion = version(true, /\bchrome\/(\d+\.\d+)/i),

			firefoxVersion = version(true, /\bfirefox\/(\d+\.\d+)/i),

			ieVersion = (isTrident && isIE) ? version(isIE, /rv:(\d+\.*\d*)/i) : version(isIE, /msie (\d+\.\d+)/i),

			operaVersion = version(isOpera, /version\/(\d+\.\d+)/i),

			safariVersion = version(isSafari, /version\/(\d+\.\d+)/i);

	   return isOpera ? '欧朋' + operaVersion : isChrome ? '谷歌' + chromeVersion : isSafari ? '苹果' + safariVersion : isIE ? 'IE' + ieVersion : isFF ? '火狐' + firefoxVersion : '搜索引擎或其它';

	}

});