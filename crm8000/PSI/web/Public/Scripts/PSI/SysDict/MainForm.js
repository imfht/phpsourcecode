//
// 系统数据字典 - 主界面
//
Ext.define("PSI.SysDict.MainForm", {
  extend: "PSI.AFX.BaseMainExForm",
  border: 0,

  initComponent: function () {
    var me = this;

    Ext.apply(me, {
      tbar: me.getToolbarCmp(),
      layout: "border",
      items: [{
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
            items: me
              .getMainGrid()
          }, {
            region: "south",
            layout: "fit",
            border: 0,
            height: "60%",
            split: true,
            items: [me
              .getDictDataGrid()]
          }]
        }, {
          id: "panelCategory",
          xtype: "panel",
          region: "west",
          layout: "fit",
          width: 300,
          split: true,
          collapsible: true,
          header: false,
          border: 0,
          items: [me.getCategoryGrid()]
        }]
      }]
    });

    me.callParent(arguments);

    me.refreshCategoryGrid();
  },

  getToolbarCmp: function () {
    var me = this;

    return [{
      text: "帮助",
      handler: function () {
        window.open(me.URL("Home/Help/index?t=sysdict"));
      }
    }, "-", {
      text: "关闭",
      handler: function () {
        me.closeWindow();
      }
    }];
  },

  getCategoryGrid: function () {
    var me = this;

    if (me.__categoryGrid) {
      return me.__categoryGrid;
    }

    var modelName = "PSISysDictCategory";

    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "code", "name"]
    });

    me.__categoryGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      header: {
        height: 30,
        title: me.formatGridHeaderTitle("系统数据字典分类")
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
        header: "分类",
        dataIndex: "name",
        width: 200,
        menuDisabled: true,
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

  getMainGrid: function () {
    var me = this;

    if (me.__mainGrid) {
      return me.__mainGrid;
    }

    var modelName = "PSISysDict";

    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "code", "name", "tableName", "memo"]
    });

    me.__mainGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      header: {
        height: 30,
        title: me.formatGridHeaderTitle("数据字典")
      },
      columnLines: true,
      columns: [{
        header: "编码",
        dataIndex: "code",
        width: 80,
        menuDisabled: true,
        sortable: false
      }, {
        header: "数据字典名称",
        dataIndex: "name",
        width: 200,
        menuDisabled: true,
        sortable: false
      }, {
        header: "数据库表名",
        dataIndex: "tableName",
        width: 200,
        menuDisabled: true,
        sortable: false
      }, {
        header: "备注",
        dataIndex: "memo",
        width: 300,
        menuDisabled: true,
        sortable: false
      }],
      store: Ext.create("Ext.data.Store", {
        model: modelName,
        autoLoad: false,
        data: []
      }),
      listeners: {
        select: {
          fn: me.onMainGridSelect,
          scope: me
        }
      }
    });

    return me.__mainGrid;
  },

  getDictDataGrid: function () {
    var me = this;

    if (me.__dataGrid) {
      return me.__dataGrid;
    }

    var modelName = "PSISysDictData";

    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "code", "codeInt", "name", "memo"]
    });

    me.__dataGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      header: {
        height: 30,
        title: me.formatGridHeaderTitle("数据")
      },
      columnLines: true,
      columns: {
        defaults: {
          menuDisabled: true,
          sortable: false
        },
        items: [{
          header: "编码(字符类型)",
          dataIndex: "code",
          width: 150
        }, {
          header: "编码(整数类型)",
          dataIndex: "codeInt",
          width: 150,
          align: "right"
        }, {
          header: "值",
          dataIndex: "name",
          width: 250
        }, {
          header: "备注",
          dataIndex: "memo",
          width: 200
        }]
      },
      store: Ext.create("Ext.data.Store", {
        model: modelName,
        autoLoad: false,
        data: []
      })
    });

    return me.__dataGrid;
  },

  refreshCategoryGrid: function (id) {
    var me = this;
    var grid = me.getCategoryGrid();
    var el = grid.getEl() || Ext.getBody();
    el.mask(PSI.Const.LOADING);
    var r = {
      url: me.URL("Home/SysDict/categoryList"),
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

  onCategoryGridSelect: function () {
    var me = this;
    me.refreshMainGrid();
  },

  refreshMainGrid: function (id) {
    var me = this;
    me.getDictDataGrid().getStore().removeAll();

    var item = me.getCategoryGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.getMainGrid().setTitle(me.formatGridHeaderTitle("数据字典"));
      return;
    }

    var category = item[0];

    var grid = me.getMainGrid();
    grid.setTitle(me.formatGridHeaderTitle("属于分类[ " + category.get("name")
      + " ]的数据字典"));
    var el = grid.getEl() || Ext.getBody();
    el.mask(PSI.Const.LOADING);
    var r = {
      url: me.URL("Home/SysDict/sysDictList"),
      params: {
        categoryId: category.get("id")
      },
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

  onMainGridSelect: function () {
    var me = this;
    me.refreshDictDataGrid();
  },

  refreshDictDataGrid: function (id) {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.getMainGrid().setTitle(me.formatGridHeaderTitle("数据字典"));
      me.getDictDataGrid().setTitle(me.formatGridHeaderTitle("数据"));
      return;
    }

    var sysDict = item[0];

    var grid = me.getDictDataGrid();
    grid.setTitle(me.formatGridHeaderTitle("[ " + sysDict.get("name")
      + " ]的数据"));
    var el = grid.getEl() || Ext.getBody();
    el.mask(PSI.Const.LOADING);
    var r = {
      url: me.URL("Home/SysDict/dictDataList"),
      params: {
        id: sysDict.get("id")
      },
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
  }
});
