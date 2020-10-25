/**
 * 原材料 - 主界面
 * 
 * @author 李静波
 */
Ext.define("PSI.RawMaterial.MainForm", {
  extend: "PSI.AFX.BaseMainExForm",

  config: {
    pAddCategory: null,
    pEditCategory: null,
    pDeleteCategory: null,
    pAddRawMaterial: null,
    pEditRawMaterial: null,
    pDeleteRawMaterial: null
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
        height: 35,
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

    me.queryTotalRawMaterialCount();

    me.__queryEditNameList = ["editQueryCode", "editQueryName",
      "editQuerySpec"];
  },

  getToolbarCmp: function () {
    var me = this;

    return [{
      text: "新增原材料分类",
      disabled: me.getPAddCategory() == "0",
      handler: me.onAddCategory,
      scope: me
    }, {
      text: "编辑原材料分类",
      disabled: me.getPEditCategory() == "0",
      handler: me.onEditCategory,
      scope: me
    }, {
      text: "删除原材料分类",
      disabled: me.getPDeleteCategory() == "0",
      handler: me.onDeleteCategory,
      scope: me
    }, "-", {
      text: "新增原材料",
      disabled: me.getPAddRawMaterial() == "0",
      handler: me.onAddRawMaterial,
      scope: me
    }, {
      text: "编辑原材料",
      disabled: me.getPEditRawMaterial() == "0",
      handler: me.onEditRawMaterial,
      scope: me
    }, {
      text: "删除原材料",
      disabled: me.getPDeleteRawMaterial() == "0",
      handler: me.onDeleteRawMaterial,
      scope: me
    }, "-", {
      text: "帮助",
      handler: function () {
        me.showInfo("TODO");
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
    }];
  },

  getMainGrid: function () {
    var me = this;
    if (me.__mainGrid) {
      return me.__mainGrid;
    }
    var modelName = "PSIRawMaterial";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "code", "name", "spec", "unitId",
        "unitName", "categoryId",
        "purchasePrice", "memo", "dataOrg",
        "recordStatus", "taxRate"]
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
        url: me.URL("Home/Material/rawMaterialList"),
        reader: {
          root: 'dataList',
          totalProperty: 'totalCount'
        }
      }
    });

    store.on("beforeload", function () {
      store.proxy.extraParams = me.getQueryParam();
    });
    store.on("load", function (e, records, successful) {
      if (successful) {
        me.gotoRawMaterialGridRecord(me.__lastId);
      }
    });

    me.__mainGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      header: {
        height: 30,
        title: me.formatGridHeaderTitle("原材料列表")
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
                store.pageSize = Ext.getCmp("comboCountPerPage").getValue();
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
        header: "物料编码",
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
        header: "名称",
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
        header: "单位",
        dataIndex: "unitName",
        menuDisabled: true,
        sortable: false,
        width: 80
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
          fn: me.onEditRawMaterial,
          scope: me
        },
        select: {
          fn: me.onRawMaterialSelect,
          scope: me
        }
      }
    });

    return me.__mainGrid;
  },

	/**
	 * 新增原材料分类
	 */
  onAddCategory: function () {
    var me = this;

    var form = Ext.create("PSI.RawMaterial.CategoryEditForm", {
      parentForm: me
    });

    form.show();
  },

	/**
	 * 编辑原材料分类
	 */
  onEditCategory: function () {
    var me = this;

    var item = me.getCategoryGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要编辑的原材料分类");
      return;
    }

    var category = item[0];

    var form = Ext.create("PSI.RawMaterial.CategoryEditForm", {
      parentForm: me,
      entity: category
    });

    form.show();
  },

	/**
	 * 删除原材料分类
	 */
  onDeleteCategory: function () {
    var me = this;
    var item = me.getCategoryGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要删除的原材料分类");
      return;
    }

    var category = item[0];

    var info = "请确认是否删除原材料分类: <span style='color:red'>"
      + category.get("text") + "</span>";

    me.confirm(info, function () {
      var el = Ext.getBody();
      el.mask("正在删除中...");
      me.ajax({
        url: me.URL("Home/Material/deleteRawMaterialCategory"),
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
	 * 刷新原材料分类Grid
	 */
  freshCategoryGrid: function (id) {
    var me = this;
    var store = me.getCategoryGrid().getStore();
    store.load();
  },

	/**
	 * 刷新原材料Grid
	 */
  freshRawMaterialGrid: function () {
    var me = this;
    var item = me.getCategoryGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.getMainGrid().setTitle(me.formatGridHeaderTitle("原材料列表"));
      return;
    }

    Ext.getCmp("pagingToolbar").doRefresh()
  },

  onCategoryGridSelect: function () {
    var me = this;
    me.getMainGrid().getStore().currentPage = 1;

    me.freshRawMaterialGrid();
  },

	/**
	 * 新增原材料
	 */
  onAddRawMaterial: function () {
    var me = this;

    var item = me.getCategoryGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请先选择原材料分类");
      return;
    }

    var form = Ext.create("PSI.RawMaterial.RawMaterialEditForm", {
      parentForm: me
    });

    form.show();
  },

	/**
	 * 编辑原材料
	 */
  onEditRawMaterial: function () {
    var me = this;
    if (me.getPEditRawMaterial() == "0") {
      return;
    }

    var item = me.getCategoryGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择原材料分类");
      return;
    }

    var category = item[0];

    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要编辑的原材料");
      return;
    }

    var rm = item[0];
    rm.set("categoryId", category.get("id"));
    var form = Ext.create("PSI.RawMaterial.RawMaterialEditForm", {
      parentForm: me,
      entity: rm
    });

    form.show();
  },

	/**
	 * 删除原材料
	 */
  onDeleteRawMaterial: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要删除的原材料");
      return;
    }

    var rm = item[0];

    var store = me.getMainGrid().getStore();
    var index = store.findExact("id", rm.get("id"));
    index--;
    var preItem = store.getAt(index);
    if (preItem) {
      me.__lastId = preItem.get("id");
    }

    var info = "请确认是否删除原材料: <span style='color:red'>" + rm.get("name")
      + " " + rm.get("spec") + "</span>";

    me.confirm(info, function () {
      var el = Ext.getBody();
      el.mask("正在删除中...");
      me.ajax({
        url: me.URL("Home/Material/deleteRawMaterial"),
        params: {
          id: rm.get("id")
        },
        callback: function (options, success, response) {
          el.unmask();

          if (success) {
            var data = me.decodeJSON(response.responseText);
            if (data.success) {
              me.tip("成功完成删除操作");
              me.freshRawMaterialGrid();
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

  gotoRawMaterialGridRecord: function (id) {
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

    return result;
  },

	/**
	 * 查询
	 */
  onQuery: function () {
    var me = this;

    me.getMainGrid().getStore().removeAll();

    me.queryTotalRawMaterialCount();

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

    me.onQuery();
  },

  onRawMaterialSelect: function () {
  },

  getCategoryGrid: function () {
    var me = this;
    if (me.__categoryGrid) {
      return me.__categoryGrid;
    }

    var modelName = "PSIRawMaterialCategory";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "text", "fullName", "code", "cnt", "leaf",
        "children", "taxRate"]
    });

    var store = Ext.create("Ext.data.TreeStore", {
      model: modelName,
      proxy: {
        type: "ajax",
        actionMethods: {
          read: "POST"
        },
        url: me.URL("Home/Material/allRawMaterialCategories")
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
        title: me.formatGridHeaderTitle("原材料分类")
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
        id: "fieldTotalRawMaterialCount",
        xtype: "displayfield",
        value: "共有原材料0种"
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
          text: "原材料种类数",
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
      me.getMainGrid().setTitle(me.formatGridHeaderTitle("原材料列表"));
      return;
    }

    var me = this;

    var title = "属于原材料分类 [" + record.get("fullName") + "] 的原材料列表";
    me.getMainGrid().setTitle(me.formatGridHeaderTitle(title));

    me.onCategoryGridSelect();
  },

  queryTotalRawMaterialCount: function () {
    var me = this;
    me.ajax({
      url: me.URL("Home/Material/getTotalRawMaterialCount"),
      params: me.getQueryParamForCategory(),
      callback: function (options, success, response) {

        if (success) {
          var data = me.decodeJSON(response.responseText);
          Ext.getCmp("fieldTotalRawMaterialCount").setValue("共有原材料" + data.cnt + "种");
        }
      }
    });
  }
});
