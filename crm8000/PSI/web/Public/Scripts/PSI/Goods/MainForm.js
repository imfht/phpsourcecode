/**
 * 物料 - 主界面
 * 
 * @author 李静波
 */
Ext.define("PSI.Goods.MainForm", {
  extend: "PSI.AFX.BaseMainExForm",

  config: {
    pAddCategory: null,
    pEditCategory: null,
    pDeleteCategory: null,
    pAddGoods: null,
    pEditGoods: null,
    pDeleteGoods: null,
    pImportGoods: null,
    pGoodsSI: null,
    pAddBOM: null,
    pEditBOM: null,
    pDeleteBOM: null,
    pPriceSystem: null,
    pExcel: null
  },

	/**
	 * 初始化组件
	 */
  initComponent: function () {
    var me = this;

    Ext.apply(me, {
      tbar: me.getToolbarCmp(),
      items: [{
        id: "panelQueryCmp",
        region: "north",
        border: 0,
        height: 65,
        header: false,
        collapsible: true,
        collapseMode: "mini",
        layout: {
          type: "table",
          columns: 4
        },
        items: me.getQueryCmp()
      }, {
        region: "center",
        layout: "border",
        border: 0,
        items: [{
          region: "center",
          xtype: "panel",
          layout: "border",
          border: 0,
          items: [{
            region: "center",
            layout: "fit",
            border: 0,
            items: [me.getMainGrid()]
          }, {
            region: "south",
            layout: "fit",
            height: 200,
            split: true,
            collapsible: true,
            collapseMode: "mini",
            header: false,
            xtype: "tabpanel",
            border: 0,
            items: [me.getSIGrid(),
            me.getGoodsBOMGrid(),
            me.getGoodsPriceGrid()]
          }]
        }, {
          id: "panelCategory",
          xtype: "panel",
          region: "west",
          layout: "fit",
          width: 430,
          split: true,
          collapsible: true,
          header: false,
          border: 0,
          items: [me.getCategoryGrid()]
        }]
      }]
    });

    me.callParent(arguments);

    me.queryTotalGoodsCount();

    me.__queryEditNameList = ["editQueryCode", "editQueryName",
      "editQuerySpec", "editQueryBarCode", "editQueryBrand"];
  },

  getToolbarCmp: function () {
    var me = this;

    return [{
      text: "新增物料分类",
      disabled: me.getPAddCategory() == "0",
      handler: me.onAddCategory,
      scope: me
    }, {
      text: "编辑物料分类",
      disabled: me.getPEditCategory() == "0",
      handler: me.onEditCategory,
      scope: me
    }, {
      text: "删除物料分类",
      disabled: me.getPDeleteCategory() == "0",
      handler: me.onDeleteCategory,
      scope: me
    }, "-", {
      text: "新增物料",
      disabled: me.getPAddGoods() == "0",
      handler: me.onAddGoods,
      scope: me
    }, {
      text: "导入物料",
      disabled: me.getPImportGoods() == "0",
      handler: me.onImportGoods,
      scope: me
    }, "-", {
      text: "编辑物料",
      disabled: me.getPEditGoods() == "0",
      handler: me.onEditGoods,
      scope: me
    }, {
      text: "删除物料",
      disabled: me.getPDeleteGoods() == "0",
      handler: me.onDeleteGoods,
      scope: me
    }, "-", {
      text: "导出Excel",
      disabled: me.getPExcel() == "0",
      handler: me.onExcel,
      scope: me
    }, "-", {
      text: "帮助",
      handler: function () {
        window.open(me.URL("Home/Help/index?t=goods"));
      }
    }, "-", {
      text: "关闭",
      handler: function () {
        me.closeWindow();
      }
    }];
  },

  getQueryCmp: function () {
    var me = this;
    return [{
      id: "editQueryCode",
      labelWidth: 60,
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "物料编码",
      margin: "5, 0, 0, 0",
      xtype: "textfield",
      listeners: {
        specialkey: {
          fn: me.onQueryEditSpecialKey,
          scope: me
        }
      }
    }, {
      id: "editQueryName",
      labelWidth: 60,
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "品名",
      margin: "5, 0, 0, 0",
      xtype: "textfield",
      listeners: {
        specialkey: {
          fn: me.onQueryEditSpecialKey,
          scope: me
        }
      }
    }, {
      id: "editQuerySpec",
      labelWidth: 60,
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "规格型号",
      margin: "5, 0, 0, 0",
      xtype: "textfield",
      listeners: {
        specialkey: {
          fn: me.onQueryEditSpecialKey,
          scope: me
        }
      }
    }, {
      xtype: "container",
      items: [{
        xtype: "button",
        text: "查询",
        width: 100,
        height: 26,
        margin: "5, 0, 0, 20",
        handler: me.onQuery,
        scope: me
      }, {
        xtype: "button",
        text: "清空查询条件",
        width: 100,
        height: 26,
        margin: "5, 0, 0, 5",
        handler: me.onClearQuery,
        scope: me
      }, {
        xtype: "button",
        text: "隐藏查询条件栏",
        width: 130,
        height: 26,
        iconCls: "PSI-button-hide",
        margin: "5 0 0 10",
        handler: function () {
          Ext.getCmp("panelQueryCmp").collapse();
        },
        scope: me
      }]
    }, {
      id: "editQueryBarCode",
      labelWidth: 60,
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "条形码",
      margin: "5, 0, 0, 0",
      xtype: "textfield",
      listeners: {
        specialkey: {
          fn: me.onQueryEditSpecialKey,
          scope: me
        }
      }
    }, {
      id: "editQueryBrand",
      labelWidth: 60,
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "品牌",
      margin: "5, 0, 0, 0",
      xtype: "PSI_goods_brand_field",
      showModal: true,
      listeners: {
        specialkey: {
          fn: me.onLastQueryEditSpecialKey,
          scope: me
        }
      }
    }];
  },

  getMainGrid: function () {
    var me = this;
    if (me.__mainGrid) {
      return me.__mainGrid;
    }
    var modelName = "PSIGoods";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "code", "name", "spec", "unitId",
        "unitName", "categoryId", "salePrice",
        "purchasePrice", "barCode", "memo", "dataOrg",
        "brandFullName", "recordStatus", "taxRate", "mType"]
    });

    var store = Ext.create("Ext.data.Store", {
      autoLoad: false,
      model: modelName,
      data: [],
      pageSize: 20,
      proxy: {
        type: "ajax",
        actionMethods: {
          read: "POST"
        },
        url: me.URL("Home/Goods/goodsList"),
        reader: {
          root: 'goodsList',
          totalProperty: 'totalCount'
        }
      }
    });

    store.on("beforeload", function () {
      store.proxy.extraParams = me.getQueryParam();
    });
    store.on("load", function (e, records, successful) {
      if (successful) {
        me.refreshCategoryCount();
        me.gotoGoodsGridRecord(me.__lastId);
      }
    });

    me.__mainGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      header: {
        height: 30,
        title: me.formatGridHeaderTitle("物料列表")
      },
      bbar: ["->", {
        id: "pagingToolbar",
        border: 0,
        xtype: "pagingtoolbar",
        store: store
      }, "-", {
          xtype: "displayfield",
          value: "每页显示"
        }, {
          id: "comboCountPerPage",
          xtype: "combobox",
          editable: false,
          width: 60,
          store: Ext.create("Ext.data.ArrayStore", {
            fields: ["text"],
            data: [["20"], ["50"], ["100"], ["300"],
            ["1000"]]
          }),
          value: 20,
          listeners: {
            change: {
              fn: function () {
                store.pageSize = Ext
                  .getCmp("comboCountPerPage")
                  .getValue();
                store.currentPage = 1;
                Ext.getCmp("pagingToolbar").doRefresh();
              },
              scope: me
            }
          }
        }, {
          xtype: "displayfield",
          value: "条记录"
        }],
      columnLines: true,
      columns: [Ext.create("Ext.grid.RowNumberer", {
        text: "序号",
        width: 40
      }), {
        header: "编码",
        dataIndex: "code",
        menuDisabled: true,
        sortable: false,
        renderer: function (value, metaData, record) {
          if (parseInt(record.get("recordStatus")) == 1000) {
            return value;
          } else {
            return "<span style='color:gray;text-decoration:line-through;'>"
              + value + "</span>";
          }
        }
      }, {
        header: "物料类型",
        dataIndex: "mType",
        menuDisabled: true,
        sortable: false,
        width: 70
      }, {
        header: "品名",
        dataIndex: "name",
        menuDisabled: true,
        sortable: false,
        width: 300
      }, {
        header: "规格型号",
        dataIndex: "spec",
        menuDisabled: true,
        sortable: false,
        width: 200
      }, {
        header: "计量单位",
        dataIndex: "unitName",
        menuDisabled: true,
        sortable: false,
        width: 80
      }, {
        header: "品牌",
        dataIndex: "brandFullName",
        menuDisabled: true,
        sortable: false
      }, {
        header: "销售基准价",
        dataIndex: "salePrice",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn"
      }, {
        header: "建议采购价",
        dataIndex: "purchasePrice",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn"
      }, {
        header: "税率",
        dataIndex: "taxRate",
        menuDisabled: true,
        sortable: false,
        align: "right"
      }, {
        header: "条形码",
        dataIndex: "barCode",
        menuDisabled: true,
        sortable: false
      }, {
        header: "备注",
        dataIndex: "memo",
        menuDisabled: true,
        sortable: false,
        width: 300
      }, {
        header: "数据域",
        dataIndex: "dataOrg",
        menuDisabled: true,
        sortable: false
      }, {
        header: "状态",
        dataIndex: "recordStatus",
        menuDisabled: true,
        sortable: false,
        renderer: function (value) {
          if (parseInt(value) == 1000) {
            return "启用";
          } else {
            return "<span style='color:red'>停用</span>";
          }
        }
      }],
      store: store,
      listeners: {
        itemdblclick: {
          fn: me.onEditGoods,
          scope: me
        },
        select: {
          fn: me.onGoodsSelect,
          scope: me
        }
      }
    });

    return me.__mainGrid;
  },

	/**
	 * 新增商品分类
	 */
  onAddCategory: function () {
    var me = this;

    var form = Ext.create("PSI.Goods.CategoryEditForm", {
      parentForm: me
    });

    form.show();
  },

	/**
	 * 编辑商品分类
	 */
  onEditCategory: function () {
    var me = this;

    var item = this.categoryGrid.getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要编辑的物料分类");
      return;
    }

    var category = item[0];

    var form = Ext.create("PSI.Goods.CategoryEditForm", {
      parentForm: me,
      entity: category
    });

    form.show();
  },

	/**
	 * 删除商品分类
	 */
  onDeleteCategory: function () {
    var me = this;
    var item = me.categoryGrid.getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要删除的物料分类");
      return;
    }

    var category = item[0];

    var store = me.categoryGrid.getStore();

    var info = "请确认是否删除物料分类: <span style='color:red'>"
      + category.get("text") + "</span>";

    me.confirm(info, function () {
      var el = Ext.getBody();
      el.mask("正在删除中...");
      me.ajax({
        url: me.URL("Home/Goods/deleteCategory"),
        params: {
          id: category.get("id")
        },
        callback: function (options, success, response) {
          el.unmask();

          if (success) {
            var data = me.decodeJSON(response.responseText);
            if (data.success) {
              me.tip("成功完成删除操作")
              me.freshCategoryGrid();
            } else {
              me.showInfo(data.msg);
            }
          } else {
            me.showInfo("网络错误");
          }
        }

      });
    });
  },

	/**
	 * 刷新商品分类Grid
	 */
  freshCategoryGrid: function (id) {
    var me = this;
    var store = me.getCategoryGrid().getStore();
    store.load();
  },

	/**
	 * 刷新商品Grid
	 */
  freshGoodsGrid: function () {
    var me = this;
    var item = me.getCategoryGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.getMainGrid().setTitle(me.formatGridHeaderTitle("物料列表"));
      return;
    }

    Ext.getCmp("pagingToolbar").doRefresh()
  },

  onCategoryGridSelect: function () {
    var me = this;
    me.getSIGrid().setTitle("物料安全库存");
    me.getSIGrid().getStore().removeAll();
    me.getGoodsBOMGrid().getStore().removeAll();
    me.getGoodsPriceGrid().getStore().removeAll();

    me.getMainGrid().getStore().currentPage = 1;

    me.freshGoodsGrid();
  },

	/**
	 * 新增商品
	 */
  onAddGoods: function () {
    var me = this;

    if (me.getCategoryGrid().getStore().getCount() == 0) {
      me.showInfo("没有物料分类，请先新增物料分类");
      return;
    }

    var form = Ext.create("PSI.Goods.GoodsEditForm", {
      parentForm: me
    });

    form.show();
  },

	/**
	 * 编辑商品
	 */
  onEditGoods: function () {
    var me = this;
    if (me.getPEditGoods() == "0") {
      return;
    }

    var item = me.getCategoryGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择物料分类");
      return;
    }

    var category = item[0];

    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要编辑的物料");
      return;
    }

    var goods = item[0];
    goods.set("categoryId", category.get("id"));
    var form = Ext.create("PSI.Goods.GoodsEditForm", {
      parentForm: me,
      entity: goods
    });

    form.show();
  },

	/**
	 * 删除商品
	 */
  onDeleteGoods: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要删除的物料");
      return;
    }

    var goods = item[0];

    var store = me.getMainGrid().getStore();
    var index = store.findExact("id", goods.get("id"));
    index--;
    var preItem = store.getAt(index);
    if (preItem) {
      me.__lastId = preItem.get("id");
    }

    var info = "请确认是否删除物料: <span style='color:red'>" + goods.get("name")
      + " " + goods.get("spec") + "</span>";

    me.confirm(info, function () {
      var el = Ext.getBody();
      el.mask("正在删除中...");
      me.ajax({
        url: me.URL("Home/Goods/deleteGoods"),
        params: {
          id: goods.get("id")
        },
        callback: function (options, success, response) {
          el.unmask();

          if (success) {
            var data = me.decodeJSON(response.responseText);
            if (data.success) {
              me.tip("成功完成删除操作");
              me.freshGoodsGrid();
            } else {
              me.showInfo(data.msg);
            }
          } else {
            me.showInfo("网络错误");
          }
        }

      });
    });
  },

  gotoCategoryGridRecord: function (id) {
    var me = this;
    var grid = me.getCategoryGrid();
    var store = grid.getStore();
    if (id) {
      var r = store.findExact("id", id);
      if (r != -1) {
        grid.getSelectionModel().select(r);
      } else {
        grid.getSelectionModel().select(0);
      }
    }
  },

  gotoGoodsGridRecord: function (id) {
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
    }
  },

  refreshCategoryCount: function () {
    var me = this;
    var item = me.getCategoryGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      return;
    }
  },

  onQueryEditSpecialKey: function (field, e) {
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

  onLastQueryEditSpecialKey: function (field, e) {
    if (e.getKey() === e.ENTER) {
      this.onQuery();
    }
  },

  getQueryParamForCategory: function () {
    var me = this;
    var result = {};

    if (Ext.getCmp("editQueryCode") == null) {
      return result;
    }

    var code = Ext.getCmp("editQueryCode").getValue();
    if (code) {
      result.code = code;
    }

    var name = Ext.getCmp("editQueryName").getValue();
    if (name) {
      result.name = name;
    }

    var spec = Ext.getCmp("editQuerySpec").getValue();
    if (spec) {
      result.spec = spec;
    }

    var barCode = Ext.getCmp("editQueryBarCode").getValue();
    if (barCode) {
      result.barCode = barCode;
    }

    var brandId = Ext.getCmp("editQueryBrand").getIdValue();
    if (brandId) {
      result.brandId = brandId;
    }

    return result;
  },

  getQueryParam: function () {
    var me = this;
    var item = me.getCategoryGrid().getSelectionModel().getSelection();
    var categoryId;
    if (item == null || item.length != 1) {
      categoryId = null;
    } else {
      categoryId = item[0].get("id");
    }

    var result = {
      categoryId: categoryId
    };

    var code = Ext.getCmp("editQueryCode").getValue();
    if (code) {
      result.code = code;
    }

    var name = Ext.getCmp("editQueryName").getValue();
    if (name) {
      result.name = name;
    }

    var spec = Ext.getCmp("editQuerySpec").getValue();
    if (spec) {
      result.spec = spec;
    }

    var barCode = Ext.getCmp("editQueryBarCode").getValue();
    if (barCode) {
      result.barCode = barCode;
    }

    var brandId = Ext.getCmp("editQueryBrand").getIdValue();
    if (brandId) {
      result.brandId = brandId;
    }

    return result;
  },

	/**
	 * 查询
	 */
  onQuery: function () {
    var me = this;

    me.getMainGrid().getStore().removeAll();
    me.getSIGrid().setTitle("物料安全库存");
    me.getSIGrid().getStore().removeAll();
    me.getGoodsBOMGrid().getStore().removeAll();
    me.getGoodsPriceGrid().getStore().removeAll();

    me.queryTotalGoodsCount();

    me.freshCategoryGrid();
  },

	/**
	 * 清除查询条件
	 */
  onClearQuery: function () {
    var me = this;
    var nameList = me.__queryEditNameList;
    for (var i = 0; i < nameList.length; i++) {
      var name = nameList[i];
      var edit = Ext.getCmp(name);
      if (edit) {
        edit.setValue(null);
      }
    }

    Ext.getCmp("editQueryBrand").clearIdValue();

    me.onQuery();
  },

	/**
	 * 安全库存Grid
	 */
  getSIGrid: function () {
    var me = this;
    if (me.__siGrid) {
      return me.__siGrid;
    }

    var modelName = "PSIGoodsSafetyInventory";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "warehouseCode", "warehouseName",
        "safetyInventory", {
          name: "inventoryCount",
          type: "float"
        }, "unitName", "inventoryUpper"]
    });

    me.__siGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      features: [{
        ftype: "summary",
        dock: "bottom"
      }],
      title: "物料安全库存",
      tbar: [{
        text: "设置物料安全库存",
        disabled: me.getPGoodsSI() == "0",
        iconCls: "PSI-button-commit",
        handler: me.onSafetyInventory,
        scope: me
      }],
      columnLines: false,
      columns: [{
        header: "仓库编码",
        dataIndex: "warehouseCode",
        width: 80,
        menuDisabled: true,
        sortable: false
      }, {
        header: "仓库名称",
        dataIndex: "warehouseName",
        width: 100,
        menuDisabled: true,
        sortable: false
      }, {
        header: "库存上限",
        dataIndex: "inventoryUpper",
        width: 120,
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        format: "0"
      }, {
        header: "安全库存量",
        dataIndex: "safetyInventory",
        width: 120,
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        format: "0",
        summaryRenderer: function () {
          return "当前库存合计";
        }
      }, {
        header: "当前库存",
        dataIndex: "inventoryCount",
        width: 120,
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        summaryType: "sum",
        format: "0"
      }, {
        header: "计量单位",
        dataIndex: "unitName",
        width: 80,
        menuDisabled: true,
        sortable: false
      }],
      store: Ext.create("Ext.data.Store", {
        model: modelName,
        autoLoad: false,
        data: []
      }),
      listeners: {
        itemdblclick: {
          fn: me.onSafetyInventory,
          scope: me
        }
      }
    });

    return me.__siGrid;
  },

  onGoodsSelect: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.getSIGrid().setTitle("物料安全库存");
      me.getGoodsBOMGrid().setTitle("BOM");
      return;
    }

    me.refreshGoodsSI();

    me.refreshGoodsBOM();

    me.refreshGoodsPriceSystem();
  },

  refreshGoodsSI: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      return;
    }

    var goods = item[0];
    var info = goods.get("code") + " " + goods.get("name") + " "
      + goods.get("spec");

    var grid = me.getSIGrid();
    grid.setTitle("物料[" + info + "]的安全库存");

    var el = grid.getEl() || Ext.getBody();
    el.mask(PSI.Const.LOADING);
    me.ajax({
      url: me.URL("Home/Goods/goodsSafetyInventoryList"),
      method: "POST",
      params: {
        id: goods.get("id")
      },
      callback: function (options, success, response) {
        var store = grid.getStore();

        store.removeAll();

        if (success) {
          var data = me.decodeJSON(response.responseText);
          store.add(data);
        }

        el.unmask();
      }
    });
  },

  refreshGoodsBOM: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      return;
    }

    var goods = item[0];

    var gridBOM = me.getGoodsBOMGrid();
    var elBOM = gridBOM.getEl();
    if (elBOM) {
      elBOM.mask(PSI.Const.LOADING);
    }

    me.ajax({
      url: me.URL("Home/Goods/goodsBOMList"),
      method: "POST",
      params: {
        id: goods.get("id")
      },
      callback: function (options, success, response) {
        var store = gridBOM.getStore();

        store.removeAll();

        if (success) {
          var data = me.decodeJSON(response.responseText);
          store.add(data);
        }

        if (elBOM) {
          elBOM.unmask();
        }
      }
    });
  },

	/**
	 * 设置安全库存
	 */
  onSafetyInventory: function () {
    var me = this;
    if (me.getPGoodsSI() == "0") {
      return;
    }

    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要设置安全库存的物料");
      return;
    }

    var goods = item[0];

    var form = Ext.create("PSI.Goods.SafetyInventoryEditForm", {
      parentForm: me,
      entity: goods
    });

    form.show();
  },

	/**
	 * 导入商品资料
	 */
  onImportGoods: function () {
    var form = Ext.create("PSI.Goods.GoodsImportForm", {
      parentForm: this
    });

    form.show();
  },

	/**
	 * 商品分类Grid
	 */
  getCategoryGrid: function () {
    var me = this;
    if (me.__categoryGrid) {
      return me.__categoryGrid;
    }

    var modelName = "PSIGoodsCategory";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "text", "fullName", "code", "cnt", "leaf",
        "children", "taxRate", "mType"]
    });

    var store = Ext.create("Ext.data.TreeStore", {
      model: modelName,
      proxy: {
        type: "ajax",
        actionMethods: {
          read: "POST"
        },
        url: me.URL("Home/Goods/allCategories")
      },
      listeners: {
        beforeload: {
          fn: function () {
            store.proxy.extraParams = me.getQueryParamForCategory();
          },
          scope: me
        }
      }

    });

    store.on("load", me.onCategoryStoreLoad, me);

    me.__categoryGrid = Ext.create("Ext.tree.Panel", {
      cls: "PSI",
      header: {
        height: 30,
        title: me.formatGridHeaderTitle("物料分类")
      },
      store: store,
      rootVisible: false,
      useArrows: true,
      viewConfig: {
        loadMask: true
      },
      tools: [{
        type: "close",
        handler: function () {
          Ext.getCmp("panelCategory").collapse();
        }
      }],
      bbar: [{
        id: "fieldTotalGoodsCount",
        xtype: "displayfield",
        value: "共有物料0种"
      }],
      columns: {
        defaults: {
          sortable: false,
          menuDisabled: true,
          draggable: false
        },
        items: [{
          xtype: "treecolumn",
          text: "分类",
          dataIndex: "text",
          width: 220
        }, {
          text: "编码",
          dataIndex: "code",
          width: 100
        }, {
          text: "物料种类数",
          dataIndex: "cnt",
          align: "right",
          width: 90,
          renderer: function (value) {
            return value == 0 ? "" : value;
          }
        }, {
          text: "默认税率",
          dataIndex: "taxRate",
          align: "center",
          width: 80
        }, {
          text: "物料类型",
          dataIndex: "mType",
          width: 150
        }]
      },
      listeners: {
        select: {
          fn: function (rowModel, record) {
            me.onCategoryTreeNodeSelect(record);
          },
          scope: me
        }
      }
    });

    me.categoryGrid = me.__categoryGrid;

    return me.__categoryGrid;
  },

  onCategoryStoreLoad: function () {
    var me = this;
    var tree = me.getCategoryGrid();
    var root = tree.getRootNode();
    if (root) {
      var node = root.firstChild;
      if (node) {
        var m = tree.getSelectionModel();
        if (!m.hasSelection()) {
          m.select(node);
        }
      }
    }
  },

  onCategoryTreeNodeSelect: function (record) {
    if (!record) {
      me.getMainGrid().setTitle(me.formatGridHeaderTitle("物料列表"));
      return;
    }

    var me = this;

    var title = "属于分类 [" + record.get("fullName") + "] 的物料列表";
    me.getMainGrid().setTitle(me.formatGridHeaderTitle(title));

    me.onCategoryGridSelect();
  },

  queryTotalGoodsCount: function () {
    var me = this;
    me.ajax({
      url: me.URL("Home/Goods/getTotalGoodsCount"),
      params: me.getQueryParamForCategory(),
      callback: function (options, success, response) {

        if (success) {
          var data = me.decodeJSON(response.responseText);
          Ext.getCmp("fieldTotalGoodsCount").setValue("共有物料"
            + data.cnt + "种");
        }
      }
    });
  },

	/**
	 * 商品构成Grid
	 */
  getGoodsBOMGrid: function () {
    var me = this;
    if (me.__bomGrid) {
      return me.__bomGrid;
    }

    var modelName = "PSIGoodsBOM";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "goodsId", "goodsCode", "goodsName",
        "goodsCount", "goodsSpec", "unitName",
        "costWeight", "costWeightNote"]
    });

    me.__bomGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      title: "BOM",
      tbar: [{
        text: "新增子物料",
        scope: me,
        iconCls: "PSI-button-add",
        disabled: me.getPAddBOM() == "0",
        handler: me.onAddBOM
      }, "-", {
        text: "编辑子物料",
        scope: me,
        iconCls: "PSI-button-edit",
        disabled: me.getPEditBOM() == "0",
        handler: me.onEditBOM
      }, "-", {
        text: "删除子物料",
        scope: me,
        iconCls: "PSI-button-delete",
        disabled: me.getPDeleteBOM() == "0",
        handler: me.onDeleteBOM
      }],
      columnLines: true,
      columns: {
        defaults: {
          menuDisabled: true,
          sortable: false
        },
        items: [{
          header: "子物料编码",
          dataIndex: "goodsCode"
        }, {
          header: "子物料名称",
          dataIndex: "goodsName",
          width: 300
        }, {
          header: "子物料规格型号",
          dataIndex: "goodsSpec",
          width: 200
        }, {
          header: "子物料数量",
          dataIndex: "goodsCount",
          width: 90,
          align: "right"
        }, {
          header: "子物料单位",
          dataIndex: "unitName",
          width: 90
        }, {
          header: "成本分摊权重",
          dataIndex: "costWeight",
          width: 100,
          align: "right"
        }, {
          header: "成本分摊占比",
          dataIndex: "costWeightNote",
          width: 200
        }]
      },
      store: Ext.create("Ext.data.Store", {
        model: modelName,
        autoLoad: false,
        data: []
      })
    });

    return me.__bomGrid;
  },

	/**
	 * 新增子商品
	 */
  onAddBOM: function () {
    var me = this;

    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择一个物料");
      return;
    }

    var goods = item[0];

    var form = Ext.create("PSI.Goods.GoodsBOMEditForm", {
      parentForm: me,
      goods: goods
    });
    form.show();
  },

	/**
	 * 编辑子商品
	 */
  onEditBOM: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择一个物料");
      return;
    }

    var goods = item[0];

    var item = me.getGoodsBOMGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要编辑的子物料");
      return;
    }
    var subGoods = item[0];

    var form = Ext.create("PSI.Goods.GoodsBOMEditForm", {
      parentForm: me,
      goods: goods,
      entity: subGoods
    });
    form.show();
  },

	/**
	 * 删除子商品
	 */
  onDeleteBOM: function () {
    var me = this;

    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择一个物料");
      return;
    }

    var goods = item[0];

    var item = me.getGoodsBOMGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要删除的子物料");
      return;
    }
    var subGoods = item[0];

    var info = "请确认是否删除子物料: <span style='color:red'>"
      + subGoods.get("goodsName") + " " + subGoods.get("goodsSpec")
      + "</span>?";

    var confirmFunc = function () {
      var el = Ext.getBody();
      el.mask("正在删除中...");

      var r = {
        url: me.URL("Home/Goods/deleteGoodsBOM"),
        params: {
          id: subGoods.get("id")
        },
        callback: function (options, success, response) {
          el.unmask();

          if (success) {
            var data = me.decodeJSON(response.responseText);
            if (data.success) {
              me.tip("成功完成删除操作");
              me.refreshGoodsBOM();
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

    me.confirm(info, confirmFunc);
  },

	/**
	 * 价格体系Grid
	 */
  getGoodsPriceGrid: function () {
    var me = this;
    if (me.__priceGrid) {
      return me.__priceGrid;
    }

    var modelName = "PSIGoodsPriceSystem";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "name", "price"]
    });

    me.__priceGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      title: "价格体系",
      columnLines: true,
      columns: [{
        header: "名称",
        dataIndex: "name",
        width: 150,
        menuDisabled: true,
        sortable: false
      }, {
        header: "价格",
        dataIndex: "price",
        width: 100,
        menuDisabled: true,
        sortable: false,
        xtype: "numbercolumn",
        align: "right"
      }],
      store: Ext.create("Ext.data.Store", {
        model: modelName,
        autoLoad: false,
        data: []
      }),
      tbar: [{
        text: "设置商品价格体系",
        disabled: me.getPPriceSystem() == "0",
        iconCls: "PSI-button-commit",
        handler: me.onGoodsPriceSystem,
        scope: me
      }]
    });

    return me.__priceGrid;
  },

  refreshGoodsPriceSystem: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      return;
    }

    var goods = item[0];

    var grid = me.getGoodsPriceGrid();
    var el = grid.getEl();
    if (el) {
      el.mask(PSI.Const.LOADING);
    }

    me.ajax({
      url: me.URL("Home/Goods/goodsPriceSystemList"),
      method: "POST",
      params: {
        id: goods.get("id")
      },
      callback: function (options, success, response) {
        var store = grid.getStore();

        store.removeAll();

        if (success) {
          var data = me.decodeJSON(response.responseText);
          store.add(data);
        }

        if (el) {
          el.unmask();
        }
      }
    });
  },

	/**
	 * 设置商品价格
	 */
  onGoodsPriceSystem: function () {
    var me = this;

    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      PSI.MsgBox.showInfo("请选择要设置价格的商品");
      return;
    }

    var goods = item[0];

    var form = Ext.create("PSI.Goods.GoodsPriceSystemEditForm", {
      parentForm: me,
      entity: goods
    });

    form.show();
  },

  // 导出Excel
  onExcel: function () {
    var me = this;

    me.confirm("请确认是否把物料资料导出为Excel文件？", function () {
      var url = "Home/Goods/exportExcel";

      window.open(me.URL(url));
    });
  }
});
