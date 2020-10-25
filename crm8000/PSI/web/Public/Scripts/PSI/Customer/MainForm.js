/**
 * 客户资料 - 主界面
 * 
 * @author 李静波
 */
Ext.define("PSI.Customer.MainForm", {
	extend : "PSI.AFX.BaseMainExForm",
	border : 0,

	config : {
		pAddCategory : null,
		pEditCategory : null,
		pDeleteCategory : null,
		pAddCustomer : null,
		pEditCustomer : null,
		pDeleteCustomer : null,
		pImportCustomer : null
	},

	/**
	 * 初始化组件
	 */
	initComponent : function() {
		var me = this;

		Ext.apply(me, {
					tbar : me.getToolbarCmp(),
					layout : "border",
					items : [{
								id : "panelQueryCmp",
								region : "north",
								height : 65,
								border : 0,
								collapsible : true,
								collapseMode : "mini",
								header : false,
								layout : {
									type : "table",
									columns : 5
								},
								items : me.getQueryCmp()
							}, {
								region : "center",
								layout : "border",
								border : 0,
								items : [{
											region : "center",
											xtype : "panel",
											layout : "fit",
											border : 0,
											items : [me.getMainGrid()]
										}, {
											id : "panelCategory",
											xtype : "panel",
											region : "west",
											layout : "fit",
											width : 430,
											split : true,
											collapsible : true,
											header : false,
											border : 0,
											items : [me.getCategoryGrid()]
										}]
							}]
				});

		me.callParent(arguments);

		me.categoryGrid = me.getCategoryGrid();
		me.customerGrid = me.getMainGrid();

		me.__queryEditNameList = ["editQueryCode", "editQueryName",
				"editQueryAddress", "editQueryContact", "editQueryMobile",
				"editQueryTel", "editQueryQQ"];

		me.freshCategoryGrid();
	},

	getToolbarCmp : function() {
		var me = this;

		return [{
					text : "新增客户分类",
					disabled : me.getPAddCategory() == "0",
					handler : me.onAddCategory,
					scope : me
				}, {
					text : "编辑客户分类",
					disabled : me.getPEditCategory() == "0",
					handler : me.onEditCategory,
					scope : me
				}, {
					text : "删除客户分类",
					disabled : me.getPDeleteCategory() == "0",
					handler : me.onDeleteCategory,
					scope : me
				}, "-", {
					text : "新增客户",
					disabled : me.getPAddCustomer() == "0",
					handler : me.onAddCustomer,
					scope : me
				}, {
					text : "导入客户",
					disabled : me.getPImportCustomer() == "0",
					handler : me.onImportCustomer,
					scope : me
				}, {
					text : "编辑客户",
					disabled : me.getPEditCustomer() == "0",
					handler : me.onEditCustomer,
					scope : me
				}, {
					text : "删除客户",
					disabled : me.getPDeleteCustomer() == "0",
					handler : me.onDeleteCustomer,
					scope : me
				}, "-", {
					text : "帮助",
					handler : function() {
						window.open(me.URL("Home/Help/index?t=customer"));
					}
				}, "-", {
					text : "关闭",
					handler : function() {
						me.closeWindow();
					}
				}];
	},

	getQueryCmp : function() {
		var me = this;

		return [{
					id : "editQueryCode",
					labelWidth : 60,
					labelAlign : "right",
					labelSeparator : "",
					fieldLabel : "客户编码",
					margin : "5, 0, 0, 0",
					xtype : "textfield",
					listeners : {
						specialkey : {
							fn : me.onQueryEditSpecialKey,
							scope : me
						}
					}
				}, {
					id : "editQueryName",
					labelWidth : 60,
					labelAlign : "right",
					labelSeparator : "",
					fieldLabel : "客户名称",
					margin : "5, 0, 0, 0",
					xtype : "textfield",
					listeners : {
						specialkey : {
							fn : me.onQueryEditSpecialKey,
							scope : me
						}
					}
				}, {
					id : "editQueryAddress",
					labelWidth : 60,
					labelAlign : "right",
					labelSeparator : "",
					fieldLabel : "地址",
					margin : "5, 0, 0, 0",
					xtype : "textfield",
					listeners : {
						specialkey : {
							fn : me.onQueryEditSpecialKey,
							scope : me
						}
					}
				}, {
					id : "editQueryContact",
					labelWidth : 60,
					labelAlign : "right",
					labelSeparator : "",
					fieldLabel : "联系人",
					margin : "5, 0, 0, 0",
					xtype : "textfield",
					listeners : {
						specialkey : {
							fn : me.onQueryEditSpecialKey,
							scope : me
						}
					}
				}, {
					id : "editQueryMobile",
					labelWidth : 60,
					labelAlign : "right",
					labelSeparator : "",
					fieldLabel : "手机",
					margin : "5, 0, 0, 0",
					xtype : "textfield",
					listeners : {
						specialkey : {
							fn : me.onQueryEditSpecialKey,
							scope : me
						}
					}
				}, {
					id : "editQueryTel",
					labelWidth : 60,
					labelAlign : "right",
					labelSeparator : "",
					fieldLabel : "固话",
					margin : "5, 0, 0, 0",
					xtype : "textfield",
					listeners : {
						specialkey : {
							fn : me.onQueryEditSpecialKey,
							scope : me
						}
					}
				}, {
					id : "editQueryQQ",
					labelWidth : 60,
					labelAlign : "right",
					labelSeparator : "",
					fieldLabel : "QQ",
					margin : "5, 0, 0, 0",
					xtype : "textfield",
					listeners : {
						specialkey : {
							fn : me.onLastQueryEditSpecialKey,
							scope : me
						}
					}
				}, {
					id : "editQueryRecordStatus",
					xtype : "combo",
					queryMode : "local",
					editable : false,
					valueField : "id",
					labelWidth : 60,
					labelAlign : "right",
					labelSeparator : "",
					fieldLabel : "状态",
					margin : "5, 0, 0, 0",
					store : Ext.create("Ext.data.ArrayStore", {
								fields : ["id", "text"],
								data : [[-1, "全部"], [1000, "启用"], [0, "停用"]]
							}),
					value : -1
				}, {
					xtype : "container",
					items : [{
								xtype : "button",
								text : "查询",
								width : 100,
								height : 26,
								margin : "5, 0, 0, 20",
								handler : me.onQuery,
								scope : me
							}, {
								xtype : "button",
								text : "清空查询条件",
								width : 100,
								height : 26,
								margin : "5, 0, 0, 15",
								handler : me.onClearQuery,
								scope : me
							}]
				}, {
					xtype : "container",
					items : [{
								xtype : "button",
								text : "隐藏查询条件栏",
								width : 130,
								height : 26,
								iconCls : "PSI-button-hide",
								margin : "5 0 0 10",
								handler : function() {
									Ext.getCmp("panelQueryCmp").collapse();
								},
								scope : me
							}]
				}];
	},

	getCategoryGrid : function() {
		var me = this;

		if (me.__categoryGrid) {
			return me.__categoryGrid;
		}

		var modelName = "PSICustomerCategory";

		Ext.define(modelName, {
					extend : "Ext.data.Model",
					fields : ["id", "code", "name", {
								name : "cnt",
								type : "int"
							}, "priceSystem"]
				});

		me.__categoryGrid = Ext.create("Ext.grid.Panel", {
					cls : "PSI",
					viewConfig : {
						enableTextSelection : true
					},
					header : {
						height : 30,
						title : me.formatGridHeaderTitle("客户分类")
					},
					tools : [{
								type : "close",
								handler : function() {
									Ext.getCmp("panelCategory").collapse();
								}
							}],
					features : [{
								ftype : "summary"
							}],
					columnLines : true,
					columns : [{
								header : "类别编码",
								dataIndex : "code",
								width : 80,
								menuDisabled : true,
								sortable : false
							}, {
								header : "类别",
								dataIndex : "name",
								width : 160,
								menuDisabled : true,
								sortable : false,
								summaryRenderer : function() {
									return "客户数合计";
								}
							}, {
								header : "价格体系",
								dataIndex : "priceSystem",
								width : 80,
								menuDisabled : true,
								sortable : false
							}, {
								header : "客户数",
								dataIndex : "cnt",
								width : 80,
								menuDisabled : true,
								sortable : false,
								summaryType : "sum",
								align : "right"
							}],
					store : Ext.create("Ext.data.Store", {
								model : modelName,
								autoLoad : false,
								data : []
							}),
					listeners : {
						select : {
							fn : me.onCategoryGridSelect,
							scope : me
						},
						itemdblclick : {
							fn : me.onEditCategory,
							scope : me
						}
					}
				});

		return me.__categoryGrid;
	},

	getMainGrid : function() {
		var me = this;
		if (me.__mainGrid) {
			return me.__mainGrid;
		}

		var modelName = "PSICustomer";

		Ext.define(modelName, {
					extend : "Ext.data.Model",
					fields : ["id", "code", "name", "contact01", "tel01",
							"mobile01", "qq01", "contact02", "tel02",
							"mobile02", "qq02", "categoryId",
							"initReceivables", "initReceivablesDT", "address",
							"addressReceipt", "bankName", "bankAccount", "tax",
							"fax", "note", "dataOrg", "warehouseName",
							"recordStatus"]
				});

		var store = Ext.create("Ext.data.Store", {
					autoLoad : false,
					model : modelName,
					data : [],
					pageSize : 20,
					proxy : {
						type : "ajax",
						actionMethods : {
							read : "POST"
						},
						url : me.URL("Home/Customer/customerList"),
						reader : {
							root : 'customerList',
							totalProperty : 'totalCount'
						}
					},
					listeners : {
						beforeload : {
							fn : function() {
								store.proxy.extraParams = me.getQueryParam();
							},
							scope : me
						},
						load : {
							fn : function(e, records, successful) {
								if (successful) {
									me.refreshCategoryCount();
									me.gotoCustomerGridRecord(me.__lastId);
								}
							},
							scope : me
						}
					}
				});

		me.__mainGrid = Ext.create("Ext.grid.Panel", {
			cls : "PSI",
			viewConfig : {
				enableTextSelection : true
			},
			header : {
				height : 30,
				title : me.formatGridHeaderTitle("客户列表")
			},
			columnLines : true,
			columns : {
				defaults : {
					menuDisabled : true,
					sortable : false
				},
				items : [Ext.create("Ext.grid.RowNumberer", {
									text : "序号",
									width : 40
								}), {
							header : "客户编码",
							dataIndex : "code",
							locked : true,
							renderer : function(value, metaData, record) {
								if (parseInt(record.get("recordStatus")) == 1000) {
									return value;
								} else {
									return "<span style='color:gray;text-decoration:line-through;'>"
											+ value + "</span>";
								}
							}
						}, {
							header : "客户名称",
							dataIndex : "name",
							locked : true,
							width : 300
						}, {
							header : "地址",
							dataIndex : "address",
							width : 300
						}, {
							header : "联系人",
							dataIndex : "contact01"
						}, {
							header : "手机",
							dataIndex : "mobile01"
						}, {
							header : "固话",
							dataIndex : "tel01"
						}, {
							header : "QQ",
							dataIndex : "qq01"
						}, {
							header : "备用联系人",
							dataIndex : "contact02"
						}, {
							header : "备用联系人手机",
							dataIndex : "mobile02",
							width : 150
						}, {
							header : "备用联系人固话",
							dataIndex : "tel02",
							width : 150
						}, {
							header : "备用联系人QQ",
							dataIndex : "qq02",
							width : 150
						}, {
							header : "收货地址",
							dataIndex : "addressReceipt",
							width : 300
						}, {
							header : "开户行",
							dataIndex : "bankName"
						}, {
							header : "开户行账号",
							dataIndex : "bankAccount"
						}, {
							header : "税号",
							dataIndex : "tax"
						}, {
							header : "传真",
							dataIndex : "fax"
						}, {
							header : "应收期初余额",
							dataIndex : "initReceivables",
							align : "right",
							xtype : "numbercolumn"
						}, {
							header : "应收期初余额日期",
							dataIndex : "initReceivablesDT",
							width : 150
						}, {
							header : "销售出库仓库",
							dataIndex : "warehouseName",
							width : 200
						}, {
							header : "备注",
							dataIndex : "note",
							width : 400
						}, {
							header : "数据域",
							dataIndex : "dataOrg"
						}, {
							header : "状态",
							dataIndex : "recordStatus",
							renderer : function(value) {
								if (parseInt(value) == 1000) {
									return "启用";
								} else {
									return "<span style='color:red'>停用</span>";
								}
							}
						}]
			},
			store : store,
			bbar : ["->", {
						id : "pagingToolbar",
						border : 0,
						xtype : "pagingtoolbar",
						store : store
					}, "-", {
						xtype : "displayfield",
						value : "每页显示"
					}, {
						id : "comboCountPerPage",
						xtype : "combobox",
						editable : false,
						width : 60,
						store : Ext.create("Ext.data.ArrayStore", {
									fields : ["text"],
									data : [["20"], ["50"], ["100"], ["300"],
											["1000"]]
								}),
						value : 20,
						listeners : {
							change : {
								fn : function() {
									store.pageSize = Ext
											.getCmp("comboCountPerPage")
											.getValue();
									store.currentPage = 1;
									Ext.getCmp("pagingToolbar").doRefresh();
								},
								scope : me
							}
						}
					}, {
						xtype : "displayfield",
						value : "条记录"
					}],
			listeners : {
				itemdblclick : {
					fn : me.onEditCustomer,
					scope : me
				}
			}
		});

		return me.__mainGrid;
	},

	/**
	 * 新增客户分类
	 */
	onAddCategory : function() {
		var me = this;

		var form = Ext.create("PSI.Customer.CategoryEditForm", {
					parentForm : me
				});

		form.show();
	},

	/**
	 * 编辑客户分类
	 */
	onEditCategory : function() {
		var me = this;
		if (me.getPEditCategory() == "0") {
			return;
		}

		var item = me.getCategoryGrid().getSelectionModel().getSelection();
		if (item == null || item.length != 1) {
			me.showInfo("请选择要编辑的客户分类");
			return;
		}

		var category = item[0];

		var form = Ext.create("PSI.Customer.CategoryEditForm", {
					parentForm : me,
					entity : category
				});

		form.show();
	},

	/**
	 * 删除客户分类
	 */
	onDeleteCategory : function() {
		var me = this;
		var item = me.categoryGrid.getSelectionModel().getSelection();
		if (item == null || item.length != 1) {
			me.showInfo("请选择要删除的客户分类");
			return;
		}

		var category = item[0];

		var store = me.getCategoryGrid().getStore();
		var index = store.findExact("id", category.get("id"));
		index--;
		var preIndex = null;
		var preItem = store.getAt(index);
		if (preItem) {
			preIndex = preItem.get("id");
		}

		var info = "请确认是否删除客户分类: <span style='color:red'>"
				+ category.get("name") + "</span>";

		var funcConfirm = function() {
			var el = Ext.getBody();
			el.mask("正在删除中...");

			var r = {
				url : me.URL("Home/Customer/deleteCategory"),
				params : {
					id : category.get("id")
				},
				callback : function(options, success, response) {
					el.unmask();

					if (success) {
						var data = me.decodeJSON(response.responseText);
						if (data.success) {
							me.tip("成功完成删除操作");
							me.freshCategoryGrid(preIndex);
						} else {
							me.showInfo(data.msg);
						}
					} else {
						me.showInfo("网络错误");
					}
				}
			};

			me.ajax(r);
		};

		me.confirm(info, funcConfirm);
	},

	/**
	 * 刷新客户分类Grid
	 */
	freshCategoryGrid : function(id) {
		var me = this;
		var grid = me.getCategoryGrid();
		var el = grid.getEl() || Ext.getBody();
		el.mask(PSI.Const.LOADING);
		me.ajax({
					url : me.URL("Home/Customer/categoryList"),
					params : me.getQueryParam(),
					callback : function(options, success, response) {
						var store = grid.getStore();

						store.removeAll();

						if (success) {
							var data = me.decodeJSON(response.responseText);
							store.add(data);

							if (store.getCount() > 0) {
								if (id) {
									var r = store.findExact("id", id);
									if (r != -1) {
										grid.getSelectionModel().select(r);
									}
								} else {
									grid.getSelectionModel().select(0);
								}
							}
						}

						el.unmask();
					}
				});
	},

	/**
	 * 刷新客户资料Grid
	 */
	freshCustomerGrid : function(id) {
		var me = this;

		var item = me.getCategoryGrid().getSelectionModel().getSelection();
		if (item == null || item.length != 1) {
			var grid = me.getMainGrid();
			grid.setTitle(me.formatGridHeaderTitle("客户列表"));
			return;
		}

		var category = item[0];

		var grid = me.getMainGrid();
		grid.setTitle(me.formatGridHeaderTitle("属于分类 [" + category.get("name")
				+ "] 的客户"));

		me.__lastId = id;
		Ext.getCmp("pagingToolbar").doRefresh()
	},

	onCategoryGridSelect : function() {
		var me = this;
		me.getMainGrid().getStore().currentPage = 1;
		me.freshCustomerGrid();
	},

	/**
	 * 新增客户资料
	 */
	onAddCustomer : function() {
		var me = this;

		if (me.getCategoryGrid().getStore().getCount() == 0) {
			me.showInfo("没有客户分类，请先新增客户分类");
			return;
		}

		var form = Ext.create("PSI.Customer.CustomerEditForm", {
					parentForm : me
				});

		form.show();
	},

	/**
	 * 导入客户资料
	 */
	onImportCustomer : function() {
		var form = Ext.create("PSI.Customer.CustomerImportForm", {
					parentForm : this
				});

		form.show();
	},

	/**
	 * 编辑客户资料
	 */
	onEditCustomer : function() {
		var me = this;
		if (me.getPEditCustomer() == "0") {
			return;
		}

		var item = me.getCategoryGrid().getSelectionModel().getSelection();
		if (item == null || item.length != 1) {
			me.showInfo("没有选择客户分类");
			return;
		}
		var category = item[0];

		var item = me.getMainGrid().getSelectionModel().getSelection();
		if (item == null || item.length != 1) {
			me.showInfo("请选择要编辑的客户");
			return;
		}

		var customer = item[0];
		customer.set("categoryId", category.get("id"));
		var form = Ext.create("PSI.Customer.CustomerEditForm", {
					parentForm : me,
					entity : customer
				});

		form.show();
	},

	/**
	 * 删除客户资料
	 */
	onDeleteCustomer : function() {
		var me = this;
		var item = me.getMainGrid().getSelectionModel().getSelection();
		if (item == null || item.length != 1) {
			me.showInfo("请选择要删除的客户");
			return;
		}

		var customer = item[0];

		var store = me.getMainGrid().getStore();
		var index = store.findExact("id", customer.get("id"));
		index--;
		var preIndex = null;
		var preItem = store.getAt(index);
		if (preItem) {
			preIndex = preItem.get("id");
		}

		var info = "请确认是否删除客户: <span style='color:red'>" + customer.get("name")
				+ "</span>";

		var funcConfirm = function() {
			var el = Ext.getBody();
			el.mask("正在删除中...");

			var r = {
				url : me.URL("Home/Customer/deleteCustomer"),
				params : {
					id : customer.get("id")
				},
				callback : function(options, success, response) {
					el.unmask();

					if (success) {
						var data = me.decodeJSON(response.responseText);
						if (data.success) {
							me.tip("成功完成删除操作");
							me.freshCustomerGrid(preIndex);
						} else {
							me.showInfo(data.msg);
						}
					}
				}

			};

			me.ajax(r);
		};

		me.confirm(info, funcConfirm);
	},

	gotoCustomerGridRecord : function(id) {
		var me = this;
		var grid = me.getMainGrid();
		var store = grid.getStore();
		if (id) {
			var r = store.findExact("id", id);
			if (r != -1) {
				grid.getSelectionModel().select(r);
			} else {
				grid.getSelectionModel().select(0);
			}
		} else {
			grid.getSelectionModel().select(0);
		}
	},

	refreshCategoryCount : function() {
		var me = this;
		var item = me.getCategoryGrid().getSelectionModel().getSelection();
		if (item == null || item.length != 1) {
			return;
		}

		var category = item[0];
		category.set("cnt", me.getMainGrid().getStore().getTotalCount());
		me.getCategoryGrid().getStore().commitChanges();
	},

	onQueryEditSpecialKey : function(field, e) {
		if (e.getKey() === e.ENTER) {
			var me = this;
			var id = field.getId();
			for (var i = 0; i < me.__queryEditNameList.length - 1; i++) {
				var editorId = me.__queryEditNameList[i];
				if (id === editorId) {
					var edit = Ext.getCmp(me.__queryEditNameList[i + 1]);
					edit.focus();
					edit.setValue(edit.getValue());
				}
			}
		}
	},

	onLastQueryEditSpecialKey : function(field, e) {
		if (e.getKey() === e.ENTER) {
			this.onQuery();
		}
	},

	getQueryParam : function() {
		var me = this;
		var item = me.getCategoryGrid().getSelectionModel().getSelection();
		var categoryId;
		if (item == null || item.length != 1) {
			categoryId = null;
		} else {
			categoryId = item[0].get("id");
		}

		var result = {
			categoryId : categoryId
		};

		var code = Ext.getCmp("editQueryCode").getValue();
		if (code) {
			result.code = code;
		}

		var address = Ext.getCmp("editQueryAddress").getValue();
		if (address) {
			result.address = address;
		}

		var name = Ext.getCmp("editQueryName").getValue();
		if (name) {
			result.name = name;
		}

		var contact = Ext.getCmp("editQueryContact").getValue();
		if (contact) {
			result.contact = contact;
		}

		var mobile = Ext.getCmp("editQueryMobile").getValue();
		if (mobile) {
			result.mobile = mobile;
		}

		var tel = Ext.getCmp("editQueryTel").getValue();
		if (tel) {
			result.tel = tel;
		}

		var qq = Ext.getCmp("editQueryQQ").getValue();
		if (qq) {
			result.qq = qq;
		}

		result.recordStatus = Ext.getCmp("editQueryRecordStatus").getValue();

		return result;
	},

	/**
	 * 查询
	 */
	onQuery : function() {
		var me = this;

		me.getMainGrid().getStore().removeAll();
		me.freshCategoryGrid();
	},

	/**
	 * 清除查询条件
	 */
	onClearQuery : function() {
		var me = this;

		var nameList = me.__queryEditNameList;
		for (var i = 0; i < nameList.length; i++) {
			var name = nameList[i];
			var edit = Ext.getCmp(name);
			if (edit) {
				edit.setValue(null);
			}
		}

		Ext.getCmp("editQueryRecordStatus").setValue(-1);

		me.onQuery();
	}
});
