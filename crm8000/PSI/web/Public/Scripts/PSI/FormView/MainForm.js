/**
 * 视图开发助手 - 主页面
 */
Ext.define("PSI.FormView.MainForm", {
  extend: "PSI.AFX.BaseMainExForm",

  initComponent: function () {
    var me = this;

    Ext.apply(me, {
      tbar: me.getToolbarCmp(),
      items: [{
        id: "panelCategory",
        border: 0,
        split: true,
        region: "west",
        width: 370,
        layout: "fit",
        items: me.getCategoryGrid()
      }, {
        border: 0,
        region: "center",
        layout: "border",
        items: [{
          region: "center",
          border: 0,
          layout: "fit",
          items: [me.getMainGrid()]
        }, {
          region: "south",
          border: 0,
          height: "60%",
          split: true,
          layout: "fit",
          xtype: "tabpanel",
          items: [me.getColsGrid(), me.getQcGrid(), me.getButtonGrid(), {
            title: "排序"
          }]
        }]
      }]
    });

    me.callParent();

    me.refreshCategoryGrid();
  },

  getToolbarCmp: function () {
    var me = this;
    return [{
      text: "新增视图分类",
      handler: me.onAddCategory,
      scope: me
    }, {
      text: "编辑视图分类",
      handler: me.onEditCategory,
      scope: me
    }, {
      text: "删除视图分类",
      handler: me.onDeleteCategory,
      scope: me
    }, "-", {
      text: "新增视图",
      handler: me.onAddFv,
      scope: me
    }, {
      text: "编辑视图",
      handler: me.onEditFv,
      scope: me
    }, {
      text: "删除视图",
      handler: me.onDeleteFv,
      scope: me
    }, "-", {
      text: "帮助",
      handler: function () {
        window.open(me.URL("Home/Help/index?t=formview"));
      }
    }, "-", {
      text: "关闭",
      handler: function () {
        me.closeWindow();
      },
      scope: me
    }];
  },

  getCategoryGrid: function () {
    var me = this;

    if (me.__categoryGrid) {
      return me.__categoryGrid;
    }

    var modelName = "PSIFvCategory";

    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "code", "name", "isSystem", "isSystemCaption"]
    });

    me.__categoryGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      header: {
        height: 30,
        title: me.formatGridHeaderTitle("视图分类")
      },
      tools: [{
        type: "close",
        handler: function () {
          Ext.getCmp("panelCategory").collapse();
        }
      }],
      columnLines: true,
      columns: [{
        header: "分类编码",
        dataIndex: "code",
        width: 80,
        menuDisabled: true,
        sortable: false
      }, {
        header: "视图分类",
        dataIndex: "name",
        width: 200,
        menuDisabled: true,
        sortable: false
      }, {
        header: "系统固有",
        dataIndex: "isSystemCaption",
        menuDisabled: true,
        width: 80,
        align: "center",
        sortable: false
      }],
      store: Ext.create("Ext.data.Store", {
        model: modelName,
        autoLoad: false,
        data: []
      }),
      listeners: {
        select: {
          fn: me.onCategoryGridSelect,
          scope: me
        }
      }
    });

    return me.__categoryGrid;
  },

  getColsGrid: function () {
    var me = this;

    if (me.__colsGrid) {
      return me.__colsGrid;
    }

    var modelName = "PSIFvCols";

    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "caption", "showOrder", "width", "valueFromTableName", "valueFromColName",
        "displayFormat"]
    });

    me.__colsGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      title: "列",
      tbar: [{
        text: "新增列",
        handler: me.onAddCol,
        scope: me
      }, "-", {
        text: "编辑列",
        handler: me.onEditCol,
        scope: me
      }, "-", {
        text: "删除列",
        handler: me.onDeleteCol,
        scope: me
      }],
      columnLines: true,
      columns: {
        defaults: {
          menuDisabled: true,
          sortable: false
        },
        items: [{
          header: "显示次序",
          dataIndex: "showOrder",
          width: 80
        }, {
          header: "标题",
          dataIndex: "caption",
          width: 200
        }, {
          header: "宽度(px)",
          dataIndex: "width",
          width: 100
        }, {
          header: "取值表名",
          dataIndex: "valueFromTableName",
          width: 200
        }, {
          header: "取值列名",
          dataIndex: "valueFromColName",
          width: 200
        }, {
          header: "显示格式",
          dataIndex: "displayFormat",
          width: 200
        }]
      },
      store: Ext.create("Ext.data.Store", {
        model: modelName,
        autoLoad: false,
        data: []
      })
    });

    return me.__colsGrid;
  },

  getButtonGrid: function () {
    var me = this;

    if (me.__buttonGrid) {
      return me.__buttonGrid;
    }

    var modelName = "PSIFvButtons";

    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "caption"]
    });

    me.__buttonGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      title: "业务按钮",
      tbar: [{
        text: "新增按钮",
        handler: me.onAddButton,
        scope: me
      }, "-", {
        text: "编辑按钮",
        handler: me.onEditButton,
        scope: me
      }, "-", {
        text: "删除按钮",
        handler: me.onDeleteButton,
        scope: me
      }],
      columnLines: true,
      columns: {
        defaults: {
          menuDisabled: true,
          sortable: false
        },
        items: [{
          header: "按钮标题",
          dataIndex: "caption",
          width: 200
        }]
      },
      store: Ext.create("Ext.data.Store", {
        model: modelName,
        autoLoad: false,
        data: []
      })
    });

    return me.__buttonGrid;
  },

  getQcGrid: function () {
    var me = this;

    if (me.__qcGrid) {
      return me.__qcGrid;
    }

    var modelName = "PSIFvQueryCondition";

    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "caption"]
    });

    me.__qcGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      title: "查询条件",
      tbar: [{
        text: "新增查询条件",
        handler: me.onAddQc,
        scope: me
      }, "-", {
        text: "编辑查询条件",
        handler: me.onEditQc,
        scope: me
      }, "-", {
        text: "删除查询条件",
        handler: me.onDeleteQc,
        scope: me
      }],
      columnLines: true,
      columns: {
        defaults: {
          menuDisabled: true,
          sortable: false
        },
        items: [{
          header: "标题",
          dataIndex: "caption",
          width: 200
        }]
      },
      store: Ext.create("Ext.data.Store", {
        model: modelName,
        autoLoad: false,
        data: []
      })
    });

    return me.__qcGrid;
  },

  refreshCategoryGrid: function (id) {
    var me = this;
    var grid = me.getCategoryGrid();
    var el = grid.getEl() || Ext.getBody();
    el.mask(PSI.Const.LOADING);
    var r = {
      url: me.URL("Home/FormView/categoryList"),
      callback: function (options, success, response) {
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
    };

    me.ajax(r);
  },

  refreshMainGrid: function (id) {
    var me = this;

    me.getMainGrid().getStore().reload();
  },

  onCategoryGridSelect: function () {
    var me = this;
    me.refreshMainGrid();
  },

  // 新增分类
  onAddCategory: function () {
    var me = this;

    var form = Ext.create("PSI.FormView.CategoryEditForm", {
      parentForm: me
    });

    form.show();
  },

  // 编辑分类
  onEditCategory: function () {
    var me = this;

    var item = me.getCategoryGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要编辑的视图分类");
      return;
    }

    var category = item[0];

    if (category.get("isSystem") == 1) {
      me.showInfo("不能编辑系统分类");
      return;
    }

    var form = Ext.create("PSI.FormView.CategoryEditForm", {
      parentForm: me,
      entity: category
    });

    form.show();
  },

  // 删除分类
  onDeleteCategory: function () {
    var me = this;
    var item = me.getCategoryGrid().getSelectionModel()
      .getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要删除的视图分类");
      return;
    }

    var category = item[0];
    if (category.get("isSystem") == 1) {
      me.showInfo("不能删除系统分类");
      return;
    }

    var store = me.getCategoryGrid().getStore();
    var index = store.findExact("id", category.get("id"));
    index--;
    var preIndex = null;
    var preItem = store.getAt(index);
    if (preItem) {
      preIndex = preItem.get("id");
    }

    var info = Ext.String.format("请确认是否删除视图分类: <span style='color:red'>{0}</span> ?",
      category.get("name"));

    var funcConfirm = function () {
      var el = Ext.getBody();
      el.mask("正在删除中...");

      var r = {
        url: me.URL("Home/FormView/deleteViewCategory"),
        params: {
          id: category.get("id")
        },
        callback: function (options, success, response) {
          el.unmask();

          if (success) {
            var data = me.decodeJSON(response.responseText);
            if (data.success) {
              me.tip("成功完成删除操作");
              me.refreshCategoryGrid(preIndex);
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

  getMainGrid: function () {
    var me = this;
    if (me.__mainGrid) {
      return me.__mainGrid;
    }

    var modelName = "PSIGoodsCategory";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "text", "code", "fid", "memo", "mdVersion", "isFixed",
        "moduleName", "leaf", "children", "xtype", "region",
        "widthOrHeight", "layoutType", "dataSourceType", "dataSourceTableName",
        "handlerClassName"]
    });

    var store = Ext.create("Ext.data.TreeStore", {
      model: modelName,
      proxy: {
        type: "ajax",
        actionMethods: {
          read: "POST"
        },
        url: me.URL("Home/FormView/fvList")
      },
      listeners: {
        beforeload: {
          fn: function () {
            store.proxy.extraParams = me.getQueryParamForMainGrid();
          },
          scope: me
        }
      }

    });

    me.__mainGrid = Ext.create("Ext.tree.Panel", {
      cls: "PSI",
      header: {
        height: 30,
        title: me.formatGridHeaderTitle("视图列表")
      },
      store: store,
      rootVisible: false,
      useArrows: true,
      viewConfig: {
        loadMask: true
      },
      columns: {
        defaults: {
          sortable: false,
          menuDisabled: true,
          draggable: true
        },
        items: [{
          xtype: "treecolumn",
          text: "名称",
          dataIndex: "text",
          width: 220
        }, {
          text: "编码",
          dataIndex: "code",
          width: 100
        }, {
          text: "位置",
          dataIndex: "region",
          width: 70
        }, {
          text: "宽度/高度",
          dataIndex: "widthOrHeight",
          align: "right",
          width: 100
        }, {
          text: "布局",
          dataIndex: "layoutType",
          width: 100
        }, {
          text: "数据源",
          dataIndex: "dataSourceType",
          width: 70
        }, {
          text: "数据源表名",
          dataIndex: "dataSourceTableName",
          width: 150
        }, {
          text: "业务逻辑类名",
          dataIndex: "handlerClassName",
          width: 200
        }, {
          text: "版本",
          dataIndex: "mdVersion",
          width: 70
        }, {
          text: "系统固有",
          dataIndex: "isFixed",
          align: "center",
          width: 80
        }, {
          text: "模块名称",
          dataIndex: "moduleName",
          width: 150
        }, {
          text: "xtype",
          dataIndex: "xtype",
          width: 300
        }, {
          text: "fid",
          dataIndex: "fid",
          width: 160
        }, {
          text: "备注",
          dataIndex: "memo",
          width: 200
        }]
      },
      listeners: {
        select: {
          fn: function (rowModel, record) {
            me.onMainGridNodeSelect(record);
          },
          scope: me
        }
      }
    });

    return me.__mainGrid;
  },

  getQueryParamForMainGrid: function () {
    var me = this;
    var item = me.getCategoryGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      return { categoryId: "" };
    }

    var category = item[0];

    return { categoryId: category.get("id") };
  },

  onMainGridNodeSelect: function (record) {
    var me = this;

    var fvId = record.get('id');

    me.refreshColsGrid(fvId);
  },

  refreshColsGrid: function (fvId, colId) {
    var me = this;

    var grid = me.getColsGrid();
    var el = grid.getEl() || Ext.getBody();
    el.mask(PSI.Const.LOADING);
    var r = {
      url: me.URL("Home/FormView/colList"),
      params: {
        fvId: fvId
      },
      callback: function (options, success, response) {
        var store = grid.getStore();

        store.removeAll();

        if (success) {
          var data = me.decodeJSON(response.responseText);
          store.add(data);

          if (store.getCount() > 0) {
            if (colId) {
              var r = store.findExact("id", colId);
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
    };

    me.ajax(r);
  },

  // 新增视图
  onAddFv: function () {
    var me = this;

    var item = me.getCategoryGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择一个的视图分类");
      return;
    }

    var category = item[0];

    var form = Ext.create("PSI.FormView.FvEditForm", {
      parentForm: me,
      category: category
    });
    form.show();
  },

  // 编辑视图
  onEditFv: function () {
    var me = this;

    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要编辑的视图");
      return;
    }

    var view = item[0];

    var form = Ext.create("PSI.FormView.FvEditForm", {
      parentForm: me,
      entity: view
    });
    form.show();
  },

  // 删除视图
  onDeleteFv: function () {
    var me = this;

    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要删除的视图");
      return;
    }

    var view = item[0];
    var info = Ext.String.format("请确认是否删除视图: <span style='color:red'>{0}</span> ？",
      view.get("text"));

    var funcConfirm = function () {
      var el = Ext.getBody();
      el.mask("正在删除中...");

      var r = {
        url: me.URL("Home/FormView/deleteFv"),
        params: {
          id: view.get("id")
        },
        callback: function (options, success, response) {
          el.unmask();

          if (success) {
            var data = me.decodeJSON(response.responseText);
            if (data.success) {
              me.tip("成功完成删除操作");
              me.refreshMainGrid();
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

  // 新增列
  onAddCol: function () {
    var me = this;

    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要新增列的视图");
      return;
    }

    var fv = item[0];

    var dsName = fv.get("dataSourceType");
    if (!(dsName == "码表" || dsName == "自定义表单")) {
      me.showInfo("只有码表和自定义表单才能定义视图列");
      return;
    }
    var dsTableName = fv.get("dataSourceTableName");
    if (!dsTableName) {
      me.showInfo("只有设置了数据源表名的视图才能定义视图列");
      return;
    }

    var form = Ext.create("PSI.FormView.FvColEditForm", {
      fv: fv,
      parentForm: me
    });
    form.show();
  },

  // 编辑列
  onEditCol: function () {
    var me = this;

    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要编辑列的视图");
      return;
    }

    var fv = item[0];

    var item = me.getColsGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要编辑的列");
      return;
    }
    var col = item[0];

    var form = Ext.create("PSI.FormView.FvColEditForm", {
      fv: fv,
      entity: col,
      parentForm: me
    });
    form.show();
  },

  // 删除列
  onDeleteCol: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择视图");
      return;
    }
    var fv = item[0];

    var item = me.getColsGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要删除的列");
      return;
    }
    var col = item[0];

    var store = me.getColsGrid().getStore();
    var index = store.findExact("id", col.get("id"));
    index--;
    var preIndex = null;
    var preItem = store.getAt(index);
    if (preItem) {
      preIndex = preItem.get("id");
    }

    var info = "请确认是否删除视图列: <span style='color:red'>"
      + col.get("caption")
      + "</span><br /><br />当前操作只删除视图列的元数据，数据库表的字段不会删除";

    var funcConfirm = function () {
      var el = Ext.getBody();
      el.mask("正在删除中...");

      var r = {
        url: me.URL("Home/FormView/deleteFvCol"),
        params: {
          fvId: fv.get("id"),
          id: col.get("id")
        },
        callback: function (options, success, response) {
          el.unmask();

          if (success) {
            var data = me.decodeJSON(response.responseText);
            if (data.success) {
              me.tip("成功完成删除操作");
              me.refreshColsGrid(fv.get("id"), preIndex);
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

  // 新增按钮
  onAddButton: function () {
    var me = this;
    me.showInfo("TODO")
  },

  // 编辑按钮
  onEditButton: function () {
    var me = this;
    me.showInfo("TODO")
  },

  // 删除按钮
  onDeleteButton: function () {
    var me = this;
    me.showInfo("TODO")
  },

  // 新增查询条件
  onAddQc: function () {
    var me = this;
    me.showInfo("TODO")
  },

  // 编辑查询条件
  onEditQc: function () {
    var me = this;
    me.showInfo("TODO")
  },

  // 删除查询条件
  onDeleteQc: function () {
    var me = this;
    me.showInfo("TODO")
  }
});
