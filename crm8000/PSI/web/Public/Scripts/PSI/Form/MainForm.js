/**
 * 自定义表单 - 主界面
 */
Ext.define("PSI.Form.MainForm", {
  extend: "PSI.AFX.BaseMainExForm",

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
            items: [me.getMainGrid()]
          }, {
            region: "south",
            layout: "fit",
            border: 0,
            height: "60%",
            split: true,
            items: [{
              xtype: "tabpanel",
              items: [{
                title: "主表列",
                layout: "fit",
                border: 0,
                items: me.getColsGrid()
              }, {
                title: "明细表",
                border: 0,
                layout: "border",
                items: [{
                  region: "west",
                  border: 0,
                  width: 300,
                  layout: "fit",
                  split: true,
                  items: [me.getDetailGrid()]
                }, {
                  region: "center",
                  border: 0,
                  layout: "fit",
                  items: [me.getDetailColsGrid()]
                }]
              }, {
                title: "查询条件",
                border: 0,
                layout: "fit",
                items: []
              }, {
                title: "业务按钮",
                border: 0,
                layout: "fit",
                items: []
              }]
            }]
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
      text: "新增表单分类",
      handler: me.onAddCategory,
      scope: me
    }, {
      text: "编辑表单分类",
      handler: me.onEditCategory,
      scope: me
    }, {
      text: "删除表单分类",
      handler: me.onDeleteCategory,
      scope: me
    }, "-", {
      text: "新增表单",
      handler: me.onAddForm,
      scope: me
    }, {
      text: "编辑表单",
      handler: me.onEditForm,
      scope: me
    }, {
      text: "删除表单",
      handler: me.onDeleteForm,
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

  getCategoryGrid: function () {
    var me = this;

    if (me.__categoryGrid) {
      return me.__categoryGrid;
    }

    var modelName = "PSIFormCategory";

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
        title: me.formatGridHeaderTitle("表单分类")
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
        header: "表单分类",
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

  onAddCategory: function () {
    var me = this;

    var form = Ext.create("PSI.Form.CategoryEditForm", {
      parentForm: me
    });

    form.show();
  },

  onEditCategory: function () {
    var me = this;

    var item = me.getCategoryGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要编辑的表单分类");
      return;
    }

    var category = item[0];

    var form = Ext.create("PSI.Form.CategoryEditForm", {
      parentForm: me,
      entity: category
    });

    form.show();
  },

  onDeleteCategory: function () {
    var me = this;
    var item = me.getCategoryGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要删除的表单分类");
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

    var info = "请确认是否删除表单分类: <span style='color:red'>"
      + category.get("name") + "</span>";

    var funcConfirm = function () {
      var el = Ext.getBody();
      el.mask("正在删除中...");

      var r = {
        url: me.URL("Home/Form/deleteFormCategory"),
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

  onCategoryGridSelect: function () {
    var me = this;

    me.refreshMainGrid();
  },

  getMainGrid: function () {
    var me = this;

    if (me.__mainGrid) {
      return me.__mainGrid;
    }

    var modelName = "PSIForm";

    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "code", "name", "tableName", "memo", "mdVersion",
        "sysForm", "fid", "moduleName"]
    });

    me.__mainGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      header: {
        height: 30,
        title: me.formatGridHeaderTitle("表单")
      },
      columnLines: true,
      columns: [{
        header: "编码",
        dataIndex: "code",
        width: 80,
        menuDisabled: true,
        sortable: false
      }, {
        header: "表单名称",
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
        header: "fid",
        dataIndex: "fid",
        width: 150,
        menuDisabled: true,
        sortable: false
      }, {
        header: "模块名称",
        dataIndex: "moduleName",
        width: 200,
        menuDisabled: true,
        sortable: false
      }, {
        header: "备注",
        dataIndex: "memo",
        width: 300,
        menuDisabled: true,
        sortable: false
      }, {
        header: "版本",
        dataIndex: "mdVersion",
        width: 90,
        menuDisabled: true,
        sortable: false
      }, {
        header: "系统固有",
        dataIndex: "sysForm",
        width: 80,
        menuDisabled: true,
        sortable: false,
        renderer: function (value) {
          return parseInt(value) == 1 ? "是" : "否";
        }
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

  onMainGridSelect: function () {
    var me = this;

    me.refreshColsGrid();
    me.refreshDetailGrid();
  },

  refreshMainGrid: function (id) {
    var me = this;

    me.getColsGrid().getStore().removeAll();
    me.getDetailGrid().getStore().removeAll();
    me.getDetailColsGrid().getStore().removeAll();

    var item = me.getCategoryGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.getMainGrid().setTitle(me.formatGridHeaderTitle("表单"));
      return;
    }

    var category = item[0];

    var grid = me.getMainGrid();
    grid.setTitle(me.formatGridHeaderTitle("属于分类[" + category.get("name") + "]的表单"));
    var el = grid.getEl() || Ext.getBody();
    el.mask(PSI.Const.LOADING);
    var r = {
      url: me.URL("Home/Form/formList"),
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

        el && el.unmask();
      }
    };

    me.ajax(r);
  },

  refreshColsGrid: function (id) {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      return;
    }

    var form = item[0];

    var grid = me.getColsGrid();
    var el = grid.getEl() || Ext.getBody();
    el.mask(PSI.Const.LOADING);
    var r = {
      url: me.URL("Home/form/formColList"),
      params: {
        id: form.get("id")
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

  refreshDetailGrid: function (id) {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      return;
    }

    var form = item[0];

    var grid = me.getDetailGrid();
    var el = grid.getEl() || Ext.getBody();
    el.mask(PSI.Const.LOADING);
    var r = {
      url: me.URL("Home/form/formDetailList"),
      params: {
        id: form.get("id")
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

  refreshDetailColsGrid: function (id) {
    var me = this;
    var item = me.getDetailGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      return;
    }

    var formDetail = item[0];

    var grid = me.getDetailColsGrid();
    var el = grid.getEl() || Ext.getBody();
    el.mask(PSI.Const.LOADING);
    var r = {
      url: me.URL("Home/form/formDetailColList"),
      params: {
        id: formDetail.get("id")
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

  onAddForm: function () {
    var me = this;

    var item = me.getCategoryGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择一个的表单分类");
      return;
    }

    var category = item[0];

    var form = Ext.create("PSI.Form.FormEditForm", {
      parentForm: me,
      category: category
    });
    form.show();
  },

  onEditForm: function () {
    var me = this;

    var item = me.getCategoryGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择一个的表单分类");
      return;
    }

    var category = item[0];

    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要编辑的表单");
      return;
    }

    var fm = item[0];

    var form = Ext.create("PSI.Form.FormEditForm", {
      parentForm: me,
      entity: fm,
      category: category
    });
    form.show();
  },

  // 删除表单元数据
  onDeleteForm: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要删除的表单");
      return;
    }

    var form = item[0];

    var store = me.getMainGrid().getStore();
    var index = store.findExact("id", form.get("id"));
    index--;
    var preIndex = null;
    var preItem = store.getAt(index);
    if (preItem) {
      preIndex = preItem.get("id");
    }

    var info = "请确认是否删除表单: <span style='color:red'>"
      + form.get("name")
      + "</span><br /><br />当前操作只删除表单元数据，<br />数据库实际表不会删除";

    var funcConfirm = function () {
      var el = Ext.getBody();
      el.mask("正在删除中...");

      var r = {
        url: me.URL("Home/Form/deleteForm"),
        params: {
          id: form.get("id")
        },
        callback: function (options, success, response) {
          el.unmask();

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

  refreshCategoryGrid: function (id) {
    var me = this;
    var grid = me.getCategoryGrid();
    var el = grid.getEl() || Ext.getBody();
    el.mask(PSI.Const.LOADING);
    var r = {
      url: me.URL("Home/Form/categoryList"),
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

  getColsGrid: function () {
    var me = this;

    if (me.__colsGrid) {
      return me.__colsGrid;
    }

    var modelName = "PSIFormCols";

    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "caption", "fieldName",
        "fieldType", "fieldLength", "fieldDecimal",
        "valueFrom", "valueFromTableName",
        "valueFromColName", "valueFromColNameDisplay", "mustInput",
        "showOrder", "sysCol", "isVisible", "note", "editorXtype",
        "colSpan", "dataIndex", "widthInView", "showOrderInView"]
    });

    me.__colsGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      border: 0,
      tbar: [{
        text: "新增主表列",
        handler: me.onAddCol,
        scope: me
      }, "-", {
        text: "编辑主表列",
        handler: me.onEditCol,
        scope: me
      }, "-", {
        text: "删除主表列",
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
          header: "列标题",
          dataIndex: "caption",
          width: 150,
          locked: true
        }, {
          header: "列数据库名",
          dataIndex: "fieldName",
          width: 150
        }, {
          header: "列数据类型",
          dataIndex: "fieldType",
          width: 80
        }, {
          header: "列数据长度",
          dataIndex: "fieldLength",
          align: "right",
          width: 90
        }, {
          header: "列小数位数",
          dataIndex: "fieldDecimal",
          align: "right",
          width: 90
        }, {
          header: "值来源",
          dataIndex: "valueFrom",
          width: 120
        }, {
          header: "值来源表",
          dataIndex: "valueFromTableName",
          width: 150
        }, {
          header: "值来源字段(关联用)",
          dataIndex: "valueFromColName",
          width: 150
        }, {
          header: "值来源字段(显示用)",
          dataIndex: "valueFromColNameDisplay",
          width: 150
        }, {
          header: "系统字段",
          dataIndex: "sysCol",
          width: 70
        }, {
          header: "对用户可见",
          dataIndex: "isVisible",
          width: 80
        }, {
          header: "必须录入",
          dataIndex: "mustInput",
          width: 70
        }, {
          header: "编辑界面中显示次序",
          dataIndex: "showOrder",
          width: 140,
          align: "right"
        }, {
          header: "编辑器类型",
          dataIndex: "editorXtype",
          width: 130
        }, {
          header: "编辑器列占位",
          dataIndex: "colSpan",
          align: "right",
          width: 130
        }, {
          header: "dataIndex",
          dataIndex: "dataIndex"
        }, {
          header: "列宽度(px)",
          align: "right",
          dataIndex: "widthInView"
        }, {
          header: "视图界面中显示次序",
          dataIndex: "showOrderInView",
          width: 140,
          align: "right"
        }, {
          header: "备注",
          dataIndex: "note",
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

  getDetailGrid: function () {
    var me = this;

    if (me.__detailGrid) {
      return me.__detailGrid;
    }

    var modelName = "PSIFormDetail";

    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "name", "tableName", "fkName", "showOrder"]
    });

    me.__detailGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      header: {
        height: 30,
        title: me.formatGridHeaderTitle("明细表")
      },
      tbar: [{
        text: "新增明细表",
        handler: me.onAddFormDetail,
        scope: me
      }, "-", {
        text: "编辑明细表",
        handler: me.onEditFormDetail,
        scope: me
      }, "-", {
        text: "删除明细表",
        handler: me.onDeleteFormDetail,
        scope: me
      }],
      columnLines: true,
      columns: [{
        header: "明细表名称",
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
        header: "外键",
        dataIndex: "fkName",
        width: 200,
        menuDisabled: true,
        sortable: false
      }, {
        header: "显示次序",
        dataIndex: "showOrder",
        width: 100,
        align: "right",
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
          fn: me.onDetailGridSelect,
          scope: me
        }
      }
    });

    return me.__detailGrid;
  },

  onDetailGridSelect: function () {
    var me = this;

    me.refreshDetailColsGrid();
  },

  getDetailColsGrid: function () {
    var me = this;

    if (me.__detailColsGrid) {
      return me.__detailColsGrid;
    }

    var modelName = "PSIFormDetailCols";

    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "caption", "fieldName",
        "fieldType", "fieldLength", "fieldDecimal",
        "valueFrom", "valueFromTableName",
        "valueFromColName", "valueFromColNameDisplay", "mustInput",
        "showOrder", "sysCol", "isVisible",
        "widthInView", "note", "editorXtype", "dataIndex"]
    });

    me.__detailColsGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      header: {
        height: 30,
        title: me.formatGridHeaderTitle("明细表列")
      },
      tbar: [{
        text: "新增列",
        handler: me.onAddDetailCol,
        scope: me
      }, "-", {
        text: "编辑列",
        handler: me.onEditDetailCol,
        scope: me
      }, "-", {
        text: "删除列",
        handler: me.onDeleteDetailCol,
        scope: me
      }],
      columnLines: true,
      columns: {
        defaults: {
          menuDisabled: true,
          sortable: false
        },
        items: [{
          header: "列标题",
          dataIndex: "caption",
          width: 150,
          locked: true
        }, {
          header: "列数据库名",
          dataIndex: "fieldName",
          width: 150
        }, {
          header: "列数据类型",
          dataIndex: "fieldType",
          width: 80
        }, {
          header: "列数据长度",
          dataIndex: "fieldLength",
          align: "right",
          width: 90
        }, {
          header: "列小数位数",
          dataIndex: "fieldDecimal",
          align: "right",
          width: 90
        }, {
          header: "值来源",
          dataIndex: "valueFrom",
          width: 120
        }, {
          header: "值来源表",
          dataIndex: "valueFromTableName",
          width: 150
        }, {
          header: "值来源字段(关联用)",
          dataIndex: "valueFromColName",
          width: 150
        }, {
          header: "值来源字段(显示用)",
          dataIndex: "valueFromColNameDisplay",
          width: 150
        }, {
          header: "系统字段",
          dataIndex: "sysCol",
          width: 70
        }, {
          header: "对用户可见",
          dataIndex: "isVisible",
          width: 80
        }, {
          header: "必须录入",
          dataIndex: "mustInput",
          width: 70
        }, {
          header: "列宽度(px)",
          dataIndex: "widthInView",
          width: 120,
          align: "right"
        }, {
          header: "列显示次序",
          dataIndex: "showOrder",
          width: 140,
          align: "right"
        }, {
          header: "编辑器类型",
          dataIndex: "editorXtype",
          width: 130
        }, {
          header: "dataIndex",
          dataIndex: "dataIndex"
        }, {
          header: "备注",
          dataIndex: "note",
          width: 200
        }]
      },
      store: Ext.create("Ext.data.Store", {
        model: modelName,
        autoLoad: false,
        data: []
      })
    });

    return me.__detailColsGrid;
  },

  onAddCol: function () {
    var me = this;

    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请先选择表单");
      return;
    }

    var fm = item[0];

    var form = Ext.create("PSI.Form.FormColEditForm", {
      parentForm: me,
      form: fm
    });
    form.show();
  },

  onEditCol: function () {
    var me = this;

    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请先选择表单");
      return;
    }
    var fm = item[0];

    var item = me.getColsGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要编辑的表单主表列");
      return;
    }
    var col = item[0];

    var form = Ext.create("PSI.Form.FormColEditForm", {
      parentForm: me,
      form: fm,
      entity: col
    });
    form.show();
  },

  // 删除主表列
  onDeleteCol: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择表单");
      return;
    }

    var form = item[0];

    var item = me.getColsGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要删除的表单主表列");
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

    var info = "请确认是否删除表单主表列: <span style='color:red'>"
      + col.get("caption")
      + "</span>?<br /><br />当前操作只删除主表列元数据，<br />数据库表的字段不会删除";

    var funcConfirm = function () {
      var el = Ext.getBody();
      el.mask("正在删除中...");

      var r = {
        url: me.URL("Home/Form/deleteFormCol"),
        params: {
          id: col.get("id"),
          formId: form.get("id")
        },
        callback: function (options, success, response) {
          el.unmask();

          if (success) {
            var data = me.decodeJSON(response.responseText);
            if (data.success) {
              me.tip("成功完成删除操作");
              me.refreshColsGrid(preIndex);
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

  // 新增明细表
  onAddFormDetail: function () {
    var me = this;
    me.showInfo("TODO");
  },

  onEditFormDetail: function () {
    var me = this;
    me.showInfo("TODO");
  },

  onDeleteFormDetail: function () {
    var me = this;
    me.showInfo("TODO");
  },

  // 新增明细表的列
  onAddDetailCol: function () {
    var me = this;

    var item = me.getDetailGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请先选择明细表");
      return;
    }

    var fm = item[0];

    var form = Ext.create("PSI.Form.FormDetailColEditForm", {
      parentForm: me,
      form: fm
    });
    form.show();
  },

  // 编辑明细表的列
  onEditDetailCol: function () {
    var me = this;
    var item = me.getDetailGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请先选择明细表");
      return;
    }

    var fm = item[0];

    var item = me.getDetailColsGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要编辑的明细表列");
      return;
    }
    var col = item[0];

    var form = Ext.create("PSI.Form.FormDetailColEditForm", {
      parentForm: me,
      form: fm,
      entity: col
    });
    form.show();
  },

  // 删除明细表列
  onDeleteDetailCol: function () {
    var me = this;
    var item = me.getDetailGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择明细表");
      return;
    }

    var form = item[0];

    var item = me.getDetailColsGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要删除的明细表列");
      return;
    }

    var col = item[0];

    var store = me.getDetailColsGrid().getStore();
    var index = store.findExact("id", col.get("id"));
    index--;
    var preIndex = null;
    var preItem = store.getAt(index);
    if (preItem) {
      preIndex = preItem.get("id");
    }

    var info = "请确认是否删除明细表列: <span style='color:red'>"
      + col.get("caption")
      + "</span>?<br /><br />当前操作只删除明细表列元数据，<br />数据库表的字段不会删除";

    var funcConfirm = function () {
      var el = Ext.getBody();
      el.mask("正在删除中...");

      var r = {
        url: me.URL("Home/Form/deleteFormDetailCol"),
        params: {
          id: col.get("id"),
          formId: form.get("id")
        },
        callback: function (options, success, response) {
          el.unmask();

          if (success) {
            var data = me.decodeJSON(response.responseText);
            if (data.success) {
              me.tip("成功完成删除操作");
              me.refreshDetailColsGrid(preIndex);
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
  }
});
