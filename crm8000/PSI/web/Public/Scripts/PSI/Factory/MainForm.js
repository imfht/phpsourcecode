//
// 工厂 - 主界面
//
Ext.define("PSI.Factory.MainForm", {
  extend: "PSI.AFX.BaseMainExForm",

  config: {
    permission: null
  },

  initComponent: function () {
    var me = this;

    Ext.apply(me, {
      tbar: me.getToolbarCmp(),
      items: [{
        id: "panelQueryCmp",
        region: "north",
        height: 65,
        border: 0,
        collapsible: true,
        collapseMode: "mini",
        header: false,
        layout: {
          type: "table",
          columns: 4
        },
        items: me.getQueryCmp()
      }, {
        region: "center",
        xtype: "container",
        layout: "border",
        border: 0,
        items: [{
          region: "center",
          xtype: "panel",
          layout: "fit",
          border: 0,
          items: [me.getMainGrid()]
        }, {
          id: "panelCategory",
          xtype: "panel",
          region: "west",
          layout: "fit",
          width: 370,
          split: true,
          collapsible: true,
          header: false,
          border: 0,
          items: [me.getCategoryGrid()]
        }]
      }]
    });

    me.callParent(arguments);

    me.__queryEditNameList = ["editQueryCode", "editQueryName",
      "editQueryAddress", "editQueryContact", "editQueryMobile",
      "editQueryTel"];

    me.freshCategoryGrid();
  },

  getToolbarCmp: function () {
    var me = this;

    return [{
      text: "新增工厂分类",
      hidden: me.getPermission().addCategory == "0",
      handler: me.onAddCategory,
      scope: me
    }, {
      hidden: me.getPermission().addCategory == "0",
      xtype: "tbseparator"
    }, {
      text: "编辑工厂分类",
      hidden: me.getPermission().editCategory == "0",
      handler: me.onEditCategory,
      scope: me
    }, {
      hidden: me.getPermission().editCategory == "0",
      xtype: "tbseparator"
    }, {
      text: "删除工厂分类",
      hidden: me.getPermission().deleteCategory == "0",
      handler: me.onDeleteCategory,
      scope: me
    }, {
      hidden: me.getPermission().deleteCategory == "0",
      xtype: "tbseparator"
    }, {
      text: "新增工厂",
      hidden: me.getPermission().add == "0",
      handler: me.onAddFactory,
      scope: me
    }, {
      hidden: me.getPermission().add == "0",
      xtype: "tbseparator"
    }, {
      text: "编辑工厂",
      hidden: me.getPermission().edit == "0",
      handler: me.onEditFactory,
      scope: me
    }, {
      hidden: me.getPermission().edit == "0",
      xtype: "tbseparator"
    }, {
      text: "删除工厂",
      hidden: me.getPermission().del == "0",
      handler: me.onDeleteFactory,
      scope: me
    }, {
      hidden: me.getPermission().del == "0",
      xtype: "tbseparator"
    }, {
      text: "帮助",
      handler: function () {
        window.open(me.URL("Home/Help/index?t=factory"));
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
      labelWidth: 70,
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "工厂编码",
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
      labelWidth: 70,
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "工厂名称",
      margin: "5, 0, 0, 0",
      xtype: "textfield",
      listeners: {
        specialkey: {
          fn: me.onQueryEditSpecialKey,
          scope: me
        }
      }
    }, {
      id: "editQueryAddress",
      labelWidth: 70,
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "地址",
      margin: "5, 0, 0, 0",
      xtype: "textfield",
      listeners: {
        specialkey: {
          fn: me.onQueryEditSpecialKey,
          scope: me
        }
      }
    }, {
      id: "editQueryContact",
      labelWidth: 70,
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "联系人",
      margin: "5, 0, 0, 0",
      xtype: "textfield",
      listeners: {
        specialkey: {
          fn: me.onQueryEditSpecialKey,
          scope: me
        }
      }
    }, {
      id: "editQueryMobile",
      labelWidth: 70,
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "手机",
      margin: "5, 0, 0, 0",
      xtype: "textfield",
      listeners: {
        specialkey: {
          fn: me.onQueryEditSpecialKey,
          scope: me
        }
      }
    }, {
      id: "editQueryTel",
      labelWidth: 70,
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "固话",
      margin: "5, 0, 0, 0",
      xtype: "textfield",
      listeners: {
        specialkey: {
          fn: me.onQueryEditSpecialKey,
          scope: me
        }
      }
    }, {
      id: "editQueryRecordStatus",
      xtype: "combo",
      queryMode: "local",
      editable: false,
      valueField: "id",
      labelWidth: 70,
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "状态",
      margin: "5, 0, 0, 0",
      store: Ext.create("Ext.data.ArrayStore", {
        fields: ["id", "text"],
        data: [[-1, "全部"], [1000, "启用"], [0, "停用"]]
      }),
      value: -1
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
        margin: "5, 0, 0, 15",
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

  getCategoryGrid: function () {
    var me = this;
    if (me.__categoryGrid) {
      return me.__categoryGrid;
    }

    var modelName = "PSIFactoryCategory";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "code", "name", {
        name: "cnt",
        type: "int"
      }]
    });

    me.__categoryGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      header: {
        height: 30,
        title: me.formatGridHeaderTitle("工厂分类")
      },
      tools: [{
        type: "close",
        handler: function () {
          Ext.getCmp("panelCategory").collapse();
        }
      }],
      features: [{
        ftype: "summary"
      }],
      columnLines: true,
      columns: [{
        header: "分类编码",
        dataIndex: "code",
        width: 80,
        menuDisabled: true,
        sortable: false
      }, {
        header: "工厂分类",
        dataIndex: "name",
        width: 160,
        menuDisabled: true,
        sortable: false,
        summaryRenderer: function () {
          return "工厂个数合计";
        }
      }, {
        header: "工厂个数",
        dataIndex: "cnt",
        width: 100,
        menuDisabled: true,
        sortable: false,
        summaryType: "sum",
        align: "right"
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
        },
        itemdblclick: {
          fn: me.onEditCategory,
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

    var modelName = "PSIFactory";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "code", "name", "contact01", "tel01",
        "mobile01", "contact02", "tel02", "mobile02",
        "categoryId", "initPayables", "initPayablesDT",
        "address", "bankName", "bankAccount", "tax", "fax",
        "note", "dataOrg", "recordStatus"]
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
        url: me.URL("Home/Factory/factoryList"),
        reader: {
          root: 'dataList',
          totalProperty: 'totalCount'
        }
      },
      listeners: {
        beforeload: {
          fn: function () {
            store.proxy.extraParams = me.getQueryParam();
          },
          scope: me
        },
        load: {
          fn: function (e, records, successful) {
            if (successful) {
              me.refreshCategoryCount();
              me.gotoMainGridRecord(me.__lastId);
            }
          },
          scope: me
        }
      }
    });

    me.__mainGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      header: {
        height: 30,
        title: "工厂列表"
      },
      columnLines: true,
      columns: {
        defaults: {
          menuDisabled: true,
          sortable: false
        },
        items: [Ext.create("Ext.grid.RowNumberer", {
          text: "序号",
          width: 40
        }), {
          header: "工厂编码",
          locked: true,
          dataIndex: "code",
          renderer: function (value, metaData, record) {
            if (parseInt(record.get("recordStatus")) == 1000) {
              return value;
            } else {
              return "<span style='color:gray;text-decoration:line-through;'>"
                + value + "</span>";
            }
          }
        }, {
          header: "工厂名称",
          locked: true,
          dataIndex: "name",
          width: 300
        }, {
          header: "地址",
          dataIndex: "address",
          width: 300
        }, {
          header: "联系人",
          dataIndex: "contact01"
        }, {
          header: "手机",
          dataIndex: "mobile01"
        }, {
          header: "固话",
          dataIndex: "tel01"
        }, {
          header: "备用联系人",
          dataIndex: "contact02"
        }, {
          header: "备用联系人手机",
          dataIndex: "mobile02",
          width: 150
        }, {
          header: "备用联系人固话",
          dataIndex: "tel02",
          width: 150
        }, {
          header: "开户行",
          dataIndex: "bankName"
        }, {
          header: "开户行账号",
          dataIndex: "bankAccount"
        }, {
          header: "税号",
          dataIndex: "tax"
        }, {
          header: "传真",
          dataIndex: "fax"
        }, {
          header: "应付期初余额",
          dataIndex: "initPayables",
          align: "right",
          xtype: "numbercolumn",
          width: 150
        }, {
          header: "应付期初余额日期",
          dataIndex: "initPayablesDT",
          width: 150
        }, {
          header: "备注",
          dataIndex: "note",
          width: 400
        }, {
          header: "数据域",
          dataIndex: "dataOrg"
        }, {
          header: "状态",
          dataIndex: "recordStatus",
          renderer: function (value) {
            if (parseInt(value) == 1000) {
              return "启用";
            } else {
              return "<span style='color:red'>停用</span>";
            }
          }
        }]
      },
      store: store,
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
      listeners: {
        itemdblclick: {
          fn: me.onEditFactory,
          scope: me
        }
      }
    });

    return me.__mainGrid;
  },

  onAddCategory: function () {
    var me = this;

    var form = Ext.create("PSI.Factory.CategoryEditForm", {
      parentForm: me
    });

    form.show();
  },

  onEditCategory: function () {
    var me = this;
    if (me.getPermission().editCategory == "0") {
      return;
    }

    var item = me.getCategoryGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要编辑的工厂分类");
      return;
    }

    var category = item[0];

    var form = Ext.create("PSI.Factory.CategoryEditForm", {
      parentForm: me,
      entity: category
    });

    form.show();
  },

  onDeleteCategory: function () {
    var me = this;

    var item = me.getCategoryGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      PSI.MsgBox.showInfo("请选择要删除的工厂分类");
      return;
    }

    var category = item[0];
    var info = "请确认是否删除工厂分类: <span style='color:red'>"
      + category.get("name") + "</span>";

    var store = me.getCategoryGrid().getStore();
    var index = store.findExact("id", category.get("id"));
    index--;
    var preIndex = null;
    var preItem = store.getAt(index);
    if (preItem) {
      preIndex = preItem.get("id");
    }

    me.confirm(info, function () {
      var el = Ext.getBody();
      el.mask("正在删除中...");
      me.ajax({
        url: me.URL("Home/Factory/deleteCategory"),
        params: {
          id: category.get("id")
        },
        callback: function (options, success, response) {
          el.unmask();

          if (success) {
            var data = me.decodeJSON(response.responseText);
            if (data.success) {
              me.tip("成功完成删除操作");
              me.freshCategoryGrid(preIndex);
            } else {
              me.showInfo(data.msg);
            }
          }
        }
      });
    });
  },

  freshCategoryGrid: function (id) {
    var me = this;
    var grid = me.getCategoryGrid();
    var el = grid.getEl() || Ext.getBody();
    el.mask(PSI.Const.LOADING);
    me.ajax({
      url: me.URL("Home/Factory/categoryList"),
      params: me.getQueryParam(),
      callback: function (options, success, response) {
        var store = grid.getStore();

        store.removeAll();

        if (success) {
          var data = me.decodeJSON(response.responseText);
          store.add(data);

          if (id) {
            var r = store.findExact("id", id);
            if (r != -1) {
              grid.getSelectionModel().select(r);
            }
          } else {
            grid.getSelectionModel().select(0);
          }
        }

        el.unmask();
      }
    });
  },

  freshMainGrid: function (id) {
    var me = this;

    var item = me.getCategoryGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      var grid = me.getMainGrid();
      grid.setTitle(me.formatGridHeaderTitle("工厂"));
      return;
    }

    var category = item[0];

    var grid = me.getMainGrid();
    grid.setTitle(me.formatGridHeaderTitle("属于分类 [" + category.get("name")
      + "] 的工厂"));

    me.__lastId = id;
    Ext.getCmp("pagingToolbar").doRefresh()
  },

  onCategoryGridSelect: function () {
    var me = this;
    me.getMainGrid().getStore().currentPage = 1;
    me.freshMainGrid();
  },

  onAddFactory: function () {
    var me = this;

    if (me.getCategoryGrid().getStore().getCount() == 0) {
      me.showInfo("没有工厂分类，请先新增工厂分类");
      return;
    }

    var form = Ext.create("PSI.Factory.FactoryEditForm", {
      parentForm: me
    });

    form.show();
  },

  onEditFactory: function () {
    var me = this;
    if (me.getPermission().edit == "0") {
      return;
    }

    var item = me.getCategoryGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("没有选择工厂分类");
      return;
    }
    var category = item[0];

    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要编辑的工厂");
      return;
    }

    var factory = item[0];
    factory.set("categoryId", category.get("id"));
    var form = Ext.create("PSI.Factory.FactoryEditForm", {
      parentForm: me,
      entity: factory
    });

    form.show();
  },

  onDeleteFactory: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要删除的工厂");
      return;
    }

    var factory = item[0];

    var store = me.getMainGrid().getStore();
    var index = store.findExact("id", factory.get("id"));
    index--;
    var preIndex = null;
    var preItem = store.getAt(index);
    if (preItem) {
      preIndex = preItem.get("id");
    }

    var info = "请确认是否删除工厂: <span style='color:red'>" + factory.get("name")
      + "</span>";
    me.confirm(info, function () {
      var el = Ext.getBody();
      el.mask("正在删除中...");
      me.ajax({
        url: me.URL("Home/Factory/deleteFactory"),
        params: {
          id: factory.get("id")
        },
        callback: function (options, success, response) {
          el.unmask();

          if (success) {
            var data = me.decodeJSON(response.responseText);
            if (data.success) {
              me.tip("成功完成删除操作");
              me.freshMainGrid(preIndex);
            } else {
              me.showInfo(data.msg);
            }
          }
        }

      });
    });
  },

  gotoMainGridRecord: function (id) {
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

  refreshCategoryCount: function () {
    var me = this;
    var item = me.getCategoryGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      return;
    }

    var category = item[0];
    category.set("cnt", me.getMainGrid().getStore().getTotalCount());
    me.getCategoryGrid().getStore().commitChanges();
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
    var me = this;

    if (e.getKey() === e.ENTER) {
      me.onQuery();
    }
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

    result.recordStatus = Ext.getCmp("editQueryRecordStatus").getValue();

    return result;
  },

  onQuery: function () {
    var me = this;

    me.getMainGrid().getStore().removeAll();
    me.freshCategoryGrid();
  },

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

    Ext.getCmp("editQueryRecordStatus").setValue(-1);

    me.onQuery();
  }
});
