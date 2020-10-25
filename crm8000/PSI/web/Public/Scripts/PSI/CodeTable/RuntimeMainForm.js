//
// 码表运行- 主界面
//
Ext.define("PSI.CodeTable.RuntimeMainForm", {
  extend: "PSI.AFX.BaseMainExForm",
  border: 0,

  config: {
    fid: null,
    pDesignTool: null
  },

  initComponent: function () {
    var me = this;

    Ext.apply(me, {
      tbar: {
        id: "PSI_CodeTable_RuntimeMainForm_toolBar",
        xtype: "toolbar"
      },
      layout: "border",
      items: [{
        region: "center",
        id: "PSI_CodeTable_RuntimeMainForm_panelMain",
        layout: "fit",
        border: 0,
        items: []
      }]
    });

    me.callParent(arguments);

    me.__toolBar = Ext.getCmp("PSI_CodeTable_RuntimeMainForm_toolBar");
    me.__panelMain = Ext.getCmp("PSI_CodeTable_RuntimeMainForm_panelMain");

    me.fetchMeatData();
  },

  getMetaData: function () {
    return this.__md;
  },

  fetchMeatData: function () {
    var me = this;
    var el = me.getEl();
    el && el.mask(PSI.Const.LOADING);
    me.ajax({
      url: me.URL("Home/CodeTable/getMetaDataForRuntime"),
      params: {
        fid: me.getFid()
      },
      callback: function (options, success, response) {
        if (success) {
          var data = me.decodeJSON(response.responseText);

          me.__md = data;

          me.initUI();
        }

        el && el.unmask();
      }
    });
  },

  initUI: function () {
    var me = this;

    var md = me.getMetaData();
    if (!md) {
      return;
    }

    var name = md.name;
    if (!name) {
      return;
    }

    // MainGrid
    me.__mainGrid = md.treeView ? me.createMainTreeGrid(md) : me.createMainGrid(md);
    me.__panelMain.add(me.__mainGrid);

    // 按钮
    var toolBar = me.__toolBar;
    var buttons = md.buttons;
    for (var i = 0; i < buttons.length; i++) {
      var btn = buttons[i];
      if (btn.caption == "-") {
        toolBar.add("-");
      } else {
        toolBar.add({
          text: btn.caption,
          handler: me[btn.onClick],
          scope: me
        });
      }
    }

    if (md.viewPaging == "1") {
      var store = me.getMainGrid().getStore();

      toolBar.add(["-", {
        cls: "PSI-toolbox",
        id: "pagingToobar",
        xtype: "pagingtoolbar",
        border: 0,
        store: store
      }, "-", {
          xtype: "displayfield",
          value: "每页显示"
        }, {
          cls: "PSI-toolbox",
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
                Ext.getCmp("pagingToobar").doRefresh();
              },
              scope: me
            }
          }
        }, {
          xtype: "displayfield",
          value: "条记录"
        }]);
    }

    // 开发者工具
    if (me.getPDesignTool() == "1") {
      toolBar.add(["-", {
        text: "开发者工具",
        menu: [{
          text: "保存列视图布局",
          scope: me,
          handler: me.onSaveViewLayout
        }
        ]
      }]);
    }

    toolBar.add(["-", {
      text: "关闭",
      handler: function () {
        me.closeWindow();
      }
    }]);

    me.refreshMainGrid();
  },

  createMainGrid: function (md) {
    var me = this;
    var modelName = "PSICodeTableRuntime_" + md.tableName;

    var fields = ["id", "record_status_code_int"];
    var cols = [];
    var colsLength = md.colsForView.length;
    for (var i = 0; i < colsLength; i++) {
      var mdCol = md.colsForView[i];

      fields.push(mdCol.fieldName);
      var col = {
        header: mdCol.caption,
        dataIndex: mdCol.fieldName,
        width: parseInt(mdCol.widthInView),
        menuDisabled: true,
        sortable: false
      };

      if (mdCol.fieldName == "record_status") {
        Ext.apply(col, {
          renderer: function (value, metaData, record) {
            if (parseInt(record.get("record_status_code_int")) == 1000) {
              return value;
            } else {
              return "<span style='color:red'>" + value + "</span>";
            }
          }
        });
      }

      cols.push(col);
    }

    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: fields
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
        url: me.URL("Home/CodeTable/codeTableRecordList"),
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
        me.gotoMainGridRecord(me.__lastId);
      }
    });

    return Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      columnLines: true,
      border: 0,
      columns: cols,
      store: store
    });
  },

  getQueryParam: function () {
    var me = this;

    var result = {
      fid: me.getMetaData().fid
    };

    return result;
  },

  gotoMainGridRecord: function (id) {
    var me = this;
    var grid = me.getMainGrid();
    grid.getSelectionModel().deselectAll();
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


  createMainTreeGrid: function (md) {
    var me = this;
    var modelName = "PSICodeTableRuntime_" + md.tableName;

    var fields = ["id", "leaf", "children"];
    var cols = [];
    var colsLength = md.colsForView.length;
    for (var i = 0; i < colsLength; i++) {
      var mdCol = md.colsForView[i];

      fields.push(mdCol.fieldName);

      if (i == 0) {
        cols.push({
          xtype: "treecolumn",
          header: mdCol.caption,
          dataIndex: mdCol.fieldName,
          width: parseInt(mdCol.widthInView),
          menuDisabled: true,
          sortable: false
        });
      } else {
        cols.push({
          header: mdCol.caption,
          dataIndex: mdCol.fieldName,
          width: parseInt(mdCol.widthInView),
          menuDisabled: true,
          sortable: false
        });
      }
    }

    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: fields
    });

    var store = new Ext.data.TreeStore({
      model: modelName,
      autoLoad: false,
      root: {
        expanded: false
      },
      proxy: {
        type: "ajax",
        actionMethods: {
          read: "POST"
        },
        url: me.URL("Home/CodeTable/codeTableRecordListForTreeView"),
        extraParams: {
          fid: me.getFid()
        }
      },
      listeners: {
        load: {
          fn: me.onTreeStoreLoad,
          scope: me
        }
      }
    });

    return Ext.create("Ext.tree.Panel", {
      cls: "PSI",
      rootVisible: false,
      useArrows: true,
      viewConfig: {
        loadMask: true
      },
      columnLines: true,
      border: 0,
      columns: cols,
      store: store
    });
  },

  onTreeStoreLoad: function () {
    var me = this;
    var md = me.getMetaData();
    if (!md.treeView) {
      return;
    }

    var id = me.__lastRecordId;
    var grid = me.getMainGrid();
    if (id) {
      // 编辑后刷新记录，然后定位到该记录
      var node = grid.getStore().getNodeById(id);
      if (node) {
        grid.getSelectionModel().select(node);
      }
    } else {
      // 首次进入模块
      var root = grid.getRootNode();
      if (root) {
        var node = root.firstChild;
        if (node) {
          grid.getSelectionModel().select(node);
        }
      }
    }
  },

  // 新增码表记录
  // onAddCodeTableRecord这个是固定的名称
  // 和表t_code_table_buttons的on_click_frontend对应
  onAddCodeTableRecord: function () {
    var me = this;

    var form = Ext.create("PSI.CodeTable.RuntimeEditForm", {
      parentForm: me,
      metaData: me.getMetaData()
    });

    form.show();
  },

  // 编辑码表记录
  onEditCodeTableRecord: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要编辑的码表记录");
      return;
    }

    var entity = item[0];
    var form = Ext.create("PSI.CodeTable.RuntimeEditForm", {
      parentForm: me,
      entity: entity,
      metaData: me.getMetaData()
    });

    form.show();
  },

  // 根据当前id查找之前的id，用于删除后定位
  getPreIndexById: function (id) {
    var me = this;
    var md = me.getMetaData();
    if (md.treeView) {
      var store = me.getMainGrid().getStore();
      var currentNode = store.getNodeById(id);
      if (currentNode) {
        var preNode = currentNode.previousSibling;
        if (preNode) {
          return preNode.data.id;
        } else {
          // 没有同级node，就找上级
          var parentNode = currentNode.parentNode;
          if (parentNode) {
            return parentNode.data.id;
          } else {
            // 什么也没有找到
            return null;
          }
        }
      }
    } else {
      var store = me.getMainGrid().getStore();
      var index = store.findExact("id", id) - 1;

      var result = null;
      var preEntity = store.getAt(index);
      if (preEntity) {
        result = preEntity.get("id");
      }

      return result;
    }

    // 没有找到，或者是bug
    return null;
  },

  // 删除码表记录
  onDeleteCodeTableRecord: function () {
    var me = this;
    var md = me.getMetaData();
    var name = md.name;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要删除的" + name);
      return;
    }

    var entity = item[0];
    var info = "请确认是否删除" + name + " <span style='color:red'>" + entity.get("name")
      + "</span> ?";

    var preIndex = me.getPreIndexById(entity.get("id"));

    var funcConfirm = function () {
      var el = Ext.getBody();
      el && el.mask(PSI.Const.LOADING);
      var r = {
        url: me.URL("Home/CodeTable/deleteCodeTableRecord"),
        params: {
          id: entity.get("id"),
          fid: md.fid
        },
        method: "POST",
        callback: function (options, success, response) {
          el && el.unmask();
          if (success) {
            var data = me.decodeJSON(response.responseText);
            if (data.success) {
              me.tip("成功完成删除操作");
              me.refreshMainGrid(preIndex);
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

  onRefreshCodeTableRecord: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    var id = null;
    if (item == null || item.length != 1) {
      id = me.__lastRecrodId;
    } else {
      var entity = item[0];
      id = entity.get("id");
    }

    me.refreshMainGrid(id);
  },

  getMainGrid: function () {
    return this.__mainGrid;
  },

  refreshMainGrid: function (id) {
    var me = this;
    me.__lastId = id;

    var md = me.getMetaData();
    if (md.treeView) {
      var store = me.getMainGrid().getStore();
      store.reload();
      store.setRootNode({
        expanded: true
      });
    } else {
      var store = me.getMainGrid().getStore();
      store.reload();
    }
  },

  // 保存列视图布局
  onSaveViewLayout: function () {
    var me = this;
    var md = me.getMetaData();

    var grid = me.getMainGrid();
    var cols = grid.columnManager.columns;
    var layout = [];
    for (var i = 0; i < cols.length; i++) {
      var c = cols[i];
      layout.push({ dataIndex: c.dataIndex, width: c.width });
    }
    var json = Ext.JSON.encode(layout);

    var info = "请确认是否保存视图布局?";

    var funcConfirm = function () {
      var el = Ext.getBody();
      el && el.mask(PSI.Const.LOADING);
      var r = {
        url: me.URL("Home/CodeTable/saveColViewLayout"),
        params: {
          fid: md.fid,
          json: json
        },
        method: "POST",
        callback: function (options, success, response) {
          el && el.unmask();
          if (success) {
            var data = me.decodeJSON(response.responseText);
            if (data.success) {
              me.tip("成功完成操作");
            } else {
              me.showInfo(data.msg);
            }
          } else {
            me.showInfo("网络错误");
          }
        }
      };

      me.ajax(r);
    }

    me.confirm(info, funcConfirm);
  }
});
