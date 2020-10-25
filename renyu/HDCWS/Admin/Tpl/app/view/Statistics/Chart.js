Ext.define('HDCWS.view.Statistics.Chart', {

	extend : 'Ext.window.Window',

	width : 1200,

	height : 500,

	minWidth : 1000,

	minHeight : 500,

	title : '流量图表',

	iconCls : 'list-chart',

	cls : 'prolist-grid',

	autoShow : true,

	modal : true,

	resizable : true,

	maximizable : true,

	layout : 'fit',

	items : {
	
		xtype : 'tabpanel',

		defaults : {

			dockedItems : {
				
				xtype : 'toolbar',

				dock : 'top',

				items : [
					
					{xtype : 'button', text : '今天', cls : 'button-active', actionKey : 0},

					{xtype : 'button', text : '昨天', actionKey : 1},
					
					{xtype : 'button', text : '最近一周', actionKey : 2},

					{xtype : 'button', text : '最近一月', actionKey : 3},

					{xtype : 'button', text : '最近一年', actionKey : 4}
				
				]
			
			},
		
			extraParams : {key : 0},

			showChart : function(button){
			
				var key = button.actionKey;

				if(key == this.extraParams.key) return;

				button.up('toolbar').query('button[actionKey=' + this.extraParams.key + ']')[0].removeCls('button-active');

				this.extraParams = {key : key};

				this.down('chart').getStore().reload();

				button.addCls('button-active');
			
			}
		
		},

		items : [
			
			{
				
				title : '柱状图',

				action : 'columnChart',

				layout : 'fit',

				listeners : {
				
					render : {
					
						fn : function(){
						
							var me = this, colors = ['url(#v-1)', 'url(#v-2)', 'url(#v-3)', 'url(#v-4)', 'url(#v-5)'];

							this.add({

								xtype : 'chart',

								theme : 'Sky',

								store : Ext.create('HDCWS.store.StatisticsChart', {controller : this}),

								animate : {
								
									easing : 'bounceOut',

									duration : 500
								
								},

								gradients : this.getGradients(),

								axes : [
									
									{
									
										type : 'Numeric',

										position : 'left',

										fields : ['num'],

										minimum : 0,

										title : '访问量',

										grid : {
										
											odd : {
											
												opacity : 0.5,

												stroke : '#666'
											
											},

											even : {
											
												opacity : 0.5,

												stroke : '#666'
											
											}
										
										}
									
									},

									{
									
										type : 'Category',

										position : 'bottom',

										fields : ['date'],

										title : '日期/时间',
											
										label : {

											rotate : {

												degrees : 315

											}

										}
									
									}
								
								],

								series : [{
								
									type : 'column',

									axis : 'left',

									highlight : true,

									label : {
									
										display : 'insideEnd',

										'text-anchor' : 'middle',

										field : 'num',

										contrast : true
									
									},

									renderer : function(sprite, record, attributes, index, store){
									
										attributes.fill = colors[index % colors.length];
										
										return attributes;
									
									},

									style : {
									
										opacity : 0.8
									
									},

									xField : 'date',

									yField : 'num'
								
								}]
							
							});
						
						}
					
					}
				
				},

				getGradients : function(){
				
					return [

						{

							id : 'v-1',

							angle : 0,

							stops : {

								0 : {

									color : 'rgb(212, 40, 40)'

								},

								100 : {

									color : 'rgb(117, 14, 14)'

								}

							}

						},

						{
							id : 'v-2',

							angle : 0,

							stops : {

								0 : {

									color : 'rgb(180, 216, 42)'

								},

								100 : {

									color : 'rgb(94, 114, 13)'

								}

							}

						},

						{

							id : 'v-3',

							angle : 0,

							stops : {

								0 : {

									color : 'rgb(43, 221, 115)'

								},

								100 : {

									color : 'rgb(14, 117, 56)'

								}

							}

						},

						{

							id : 'v-4',

							angle : 0,

							stops : {

								0 : {

									color : 'rgb(45, 117, 226)'

								},

								100 : {

									color : 'rgb(14, 56, 117)'

								}

							}

						},

						{

							id : 'v-5',

							angle : 0,

							stops : {

								0 : {

									color : 'rgb(187, 45, 222)'

								},

								100 : {

									color : 'rgb(85, 10, 103)'

								}

							}

						}
						
					]
				
				}

			},

			{
				
				title : '波形图',

				layout : 'fit',

				listeners : {

					render : {
					
						fn : function(){

							this.add({

								xtype : 'chart',

								theme : 'Sky',

								style : 'background:#fff',

								animate : true,

								theme : 'Category1',

								store : Ext.create('HDCWS.store.StatisticsChart', {controller : this}),

								axes: [{

									type : 'Numeric',

									position : 'left',

									fields : ['num'],

									title : '访问量',

									grid : true

								}, {

									type : 'Category',

									position : 'bottom',

									fields : ['date'],

									title : '日期/时间'

								}],

								series : [{

									type : 'column',

									axis : 'left',

									xField : 'date',

									yField : 'num',

									markerConfig : {

										type : 'cross',

										size : 3

									},

									tips : {

										trackMouse : true,

										width : 140,

										height : 28,

										renderer : function(storeItem, item) {

											this.setTitle(storeItem.get('date') + '(点击量):' + storeItem.get('num'));

										}

									}

								}, {

									type : 'scatter',

									axis : 'left',

									xField : 'date',

									yField : 'num',

									markerConfig : {

										type : 'circle',

										size : 5

									}

								},{

									type : 'line',

									axis : 'left',

									smooth : true,

									fill : true,

									fillOpacity : 0.5,

									xField : 'date',

									yField : 'num'

								}]

							});
						
						}
					
					}
				
				}
				
			},

			{

				title : '饼图',

				layout : 'fit',

				listeners : {
				
					render : {
					
						fn : function(){

							this.add({

								xtype : 'chart',

								theme : 'Base:gradients',

								animate : true,

								store : Ext.create('HDCWS.store.StatisticsChart', {controller : this}),

								shadow : true,

								legend : {

									position : 'bottom'

								},

								insetPadding : 60,

								series : [{

									type : 'pie',

									field : 'num',

									showInLegend : true,

									donut : false,

									tips : {

										trackMouse : true,

										width : 140,

										height : 28,

										renderer : function(storeItem, item) {

											this.setTitle(storeItem.get('date') + '(点击量):' + storeItem.get('num'));

										}

									},

									highlight : {

										segment : {

											margin : 20

										}

									},

									label : {

										field : 'date',

										display : 'rotate',

										contrast : true,

										font : '18px Arial'

									}

								}]

							});
						
						}
					
					}
				
				}
				
			}
		
		],

		listeners : {
		
			render : {
			
				fn : function(){

					Ext.Array.each(this.query('toolbar button'), function(btn){
					
						btn.on('click', function(){

							this.up('panel').showChart(this);
						
						});
					
					});
				
				}
			
			}
		
		}
	
	}

});