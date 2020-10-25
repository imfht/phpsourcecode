/*
 * Ext JS Library 2.2
 * Copyright(c) 2006-2008, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */

Ext.onReady(function(){
	Ext.menu.RangeMenu.prototype.icons = {
	  gt: 'img/greater_then.png', 
	  lt: 'img/less_then.png',
	  eq: 'img/equals.png'
	};
	Ext.grid.filter.StringFilter.prototype.icon = 'img/find.png';
    
    // NOTE: This is an example showing simple state management. During development,
    // it is generally best to disable state management as dynamically-generated ids
    // can change across page loads, leading to unpredictable results.  The developer
    // should ensure that stable state ids are set for stateful components in real apps.
	Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
	  
	 ds = new Ext.data.JsonStore({
	  url:'grid-filter.php',
    id: 'id',
    totalProperty: 'total',
    root: 'data',
    fields: [
      {name:'id'}, 
      {name:'product_name'}, 
      {name:'product_sell_price'}, 
      {name:'product_updatetime'}, 
      {name:'product_purchasing_price'}, 
    ],
	  sortInfo: {field: 'id', direction: 'desc'},
	  remoteSort: true
	});
  
	var filters = new Ext.grid.GridFilters({
	  filters:[
	    {type: 'numeric',  dataIndex: 'id'},
	    {type: 'string',  dataIndex: 'product_name'},
	    {type: 'numeric', dataIndex: 'product_sell_price'},
	    {type: 'date',  dataIndex: 'product_updatetime'},
	    {type: 'numeric', dataIndex: 'product_purchasing_price'}
	]});
	
	var cm = new Ext.grid.ColumnModel([
	  {dataIndex: 'id', header: '编号Id'},
	  {dataIndex: 'product_name', header: '产品名称', id: 'product_name'},
	  {dataIndex: 'product_sell_price', header: '市场价'},
	  {dataIndex: 'product_updatetime',header: '日期'}, 
	  {dataIndex: 'product_purchasing_price',header: '采购价'},
	  {header:'操作',width:130,dataIndex:'',align:'center',menuDisabled :true,renderer:function(v, params, record){
			var rowid=record.data.id;
			var html = "  <span class='grid-edit' onclick='onedit("+rowid+")'> 编辑</span>";
			html += "  <span class='grid-remove' onclick='onremove("+rowid+")' > 删除</span>";	
			return html;
                }}
	]);
	cm.defaultSortable = true;

			
	var originaltoolbar = [{
                //id: 'addButton',
                text: '添加',
                iconCls: 'add',
               // tooltip: this.addtooltip,
                handler:function(){create.show()} ,
                scope: this
            }];	
	var grid = new Ext.grid.GridPanel({
	  id: 'example',
	  title: '商品进货列表',
	  ds: ds,
	  cm: cm,
	  enableColLock: false,
	  loadMask: true,
	  plugins: filters,
	  height:400,
	  width:700,        
	  el: 'grid-example',
	  autoExpandColumn: 'product_name',
	  tbar: [
          originaltoolbar
      ],
	  /*items: [
         new Ext.Button(action)       // <-- Add the action as a button
      ],*/
	  bbar: new Ext.PagingToolbar({
	    store: ds,
	    pageSize: 15,
	    plugins: filters
	  })
	});
	grid.render();

	ds.load({params:{start: 0, limit: 15}});
	
	//添加数据
	var creatForm = new Ext.form.FormPanel({
        baseCls: 'x-plain',
        layout:'absolute',
        //url:'grid-filter.php',
        defaultType: 'textfield',

        items: [{
            x: 0,
            y: 5,
            xtype:'label',
            text: '产品名:'
        },{
            x: 60,
            y: 0,
            name: 'product_name',
            anchor:'100%'  // anchor width by percentage
        },{
            x: 0,
            y: 35,
            xtype:'label',
            text: '市场价:'
        },{
            x: 60,
            y: 30,
            name: 'product_sell',
            anchor: '30%'  // anchor width by percentage
        },{
            x: 0,
            y: 65,
            xtype:'label',
            text: '采购价:'
        },{
            x: 60,
            y: 60,
            name: 'product_purchas',
            anchor: '30%'  // anchor width by percentage
        }
		],
		buttons: [{
            text: '保存',
				handler: function(){
                if(creatForm.getForm().isValid()){
	                creatForm.getForm().submit({
	                    url: 'grid-filter.php?action=save',
	                    waitMsg: '正在保存...',
	                    success: function(creatForm, o){
	                        Ext.Msg.alert('成功', '保存数据： "'+o.result.file+'" 到数据库中');
							ds.reload();
							creatForm.reset();
							create.hide();
	                    }
	                });
                }
            }
        },{
            text: '取消',
			handler: function(){
                creatForm.getForm().reset();
				create.hide();
            }
        }
        ]
    });
	var create = new Ext.Window({
        title: '添加数据',
        width: 500,
        height:200,
        minWidth: 300,
        minHeight: 200,
        layout: 'fit',
        plain:true,
        bodyStyle:'padding:5px;',
        buttonAlign:'center',
        items: creatForm,
		
    });
	
 });

 //操作 删除
	var onremove =  function(id){
			Ext.Msg.confirm('','确认要删除这条记录？',function(btn){
				if (btn=="yes")
			{
				Ext.Ajax.request({
					url: 'grid-filter.php?action=del',
					params: { id: id},
					success: function(response,options ) {	
							Ext.Msg.alert('', '删除成功');
							ds.reload();
					},
					failure: function(response) {
						Ext.Msg.alert('', '删除失败');
					},
					scope :this
				});
			}
		},this);		
 }
 //操作编辑
 var onedit = function(id){
	Ext.onReady(function(){
	  var fs = new Ext.form.FormPanel({
        baseCls: 'x-plain',
        layout:'absolute',
        //url:'grid-filter.php',
        defaultType: 'textfield',
			

        // configure how to read the json Data
        reader : new Ext.data.JsonReader({
            root : 'data',
            successProperty: 'true',			
        },[	    
            'product_name',
		  {name:'product_sell',mapping:'product_sell_price'},
		  {name:'product_purchas',mapping:'product_purchasing_price'}
        ]),

        // reusable eror reader class defined at the end of this file
        //errorReader: new Ext.form.JsonErrorReader(),

        items: [
            new Ext.form.FieldSet({
                title: '产品信息',
                autoHeight: true,
                defaultType: 'textfield',
                items: [
					{
                        fieldLabel: '产品名',
                        name: 'product_name',
                        width:190
                    }, {
                        fieldLabel: '市场价',
                        name: 'product_sell',
                        width:190
                    }, {
                        fieldLabel: '采购价',
                        name: 'product_purchas',
                        width:190
                    }
                ]
            })
        ]
    });

    // explicit add
    var submit = fs.addButton({
        text: '保存',
        handler: function(){
			fs.getForm().submit({
				url:'grid-filter.php?action=update',
				params:{id: id},
				waitMsg:'保存数据中...',
				success:function(){
						Ext.Msg.alert('成功', '修改成功');
						create.hide();
						ds.reload();						
				}
			});
        }
    });
		var create = new Ext.Window({
			
			title: '编辑数据',
			width: 500,
			height:200,
			minWidth: 300,
			minHeight: 200,
			layout: 'fit',
			plain:true,
			bodyStyle:'padding:5px;',
			buttonAlign:'center',
			items: fs,
			
		});
		fs.getForm().load({url:'grid-filter.php?action=edit',params:{id: id},waitMsg:'Loading'});
		create.show();
	});

}