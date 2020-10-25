/**
 * 选择权限
 */
Ext.define("PSI.Permission.SelectPermissionForm", {
  extend: "PSI.AFX.BaseDialogForm",

  config: {
    // idList是数组
    idList: []
  },

  title: "选择权限",
  width: 1200,
  height: 600,
  layout: "border",

  initComponent: function () {
    var me = this;

    Ext.apply(me, {
      padding: 5,
      items: [{
        region: "center",
        layout: "border",
        bodyStyle: "background-color:white",
        border: 0,
        items: [{
          region: "north",
          layout: "border",
          height: "50%",
          border: 1,
          cls: "PSI",
          margin: 5,
          header: {
            height: 30,
            title: me
              .formatGridHeaderTitle("所有可以选择的权限")
          },
          items: [{
            region: "west",
            width: 200,
            xtype: "container",
            layout: "border",
            border: 0,
            items: [{
              region: "center",
              layout: "fit",
              border: 0,
              bodyPadding: 5,
              items: me.getCategoryGrid()
            }, {
              region: "south",
              height: 40,
              layout: "form",
              border: 0,
              bodyPadding: 5,
              items: [{
                id: "PSI_Permission_SelectPermissionForm_editQuery",
                xtype: "textfield",
                fieldLabel: "分类",
                labelWidth: 30,
                labelAlign: "right",
                labelSeparator: ""
              }]
            }]
          }, {
            region: "center",
            border: 0,
            layout: "fit",
            bodyPadding: 5,
            items: [me.getPermissionGrid()]
          }]
        }, {
          region: "center",
          layout: "fit",
          cls: "PSI",
          border: 0,
          items: [me.getSelectedGrid()]
        }]
      }, {
        region: "south",
        layout: {
          type: "table",
          columns: 3
        },
        border: 0,
        height: 40,
        items: [{
          xtype: "textfield",
          fieldLabel: "数据域",
          margin: "5 5 5 5",
          labelWidth: 40,
          labelAlign: "right",
          labelSeparator: "",
          width: 590,
          readOnly: true,
          id: "editDataOrg"
        }, {
          xtype: "hidden",
          id: "editDataOrgIdList"
        }, {
          xtype: "button",
          text: "选择数据域",
          handler: me.onSelectDataOrg,
          scope: me
        }, {
          text: "数据域的使用帮助",
          xtype: "button",
          margin: "5 5 5 20",
          iconCls: "PSI-help",
          handler: function () {
            var url = me
              .URL("/Home/Help/index?t=dataOrg")
            window.open(url);
          }
        }]
      }],
      buttons: [{
        text: "确定",
        formBind: true,
        iconCls: "PSI-button-ok",
        handler: me.onOK,
        scope: me
      }, {
        text: "取消",
        handler: function () {
          me.close();
        },
        scope: me
      }],
      listeners: {
        show: me.onWndShow
      }
    });

    me.callParent(arguments);

    me.editQuery = Ext.getCmp("PSI_Permission_SelectPermissionForm_editQuery");

    me.editQuery.on("change", function () {
      me.refreshCategoryGrid();
    });

  },

  refreshCategoryGrid: function () {
    var me = this;
    var idList = me.getIdList();
    var store = me.getCategoryGrid().getStore();

    var el = me.getCategoryGrid().getEl() || Ext.getBody();
    el.mask("数据加载中...");
    var r = {
      url: me.URL("Home/Permission/permissionCategory"),
      params: {
        queryKey: me.editQuery.getValue(),
      },
      callback: function (options, success, response) {
        store.removeAll();

        if (success) {
          var data = me.decodeJSON(response.responseText);

          store.add(data);

          if (data.length > 0) {
            me.getCategoryGrid().getSelectionModel().select(0);
            me.editQuery.focus();
          }
        }

        el.unmask();
      }
    };

    me.ajax(r);
  },

  onWndShow: function () {
    var me = this;
    me.refreshCategoryGrid();
  },

  onOK: function () {
    var me = this;
    var grid = me.getSelectedGrid();

    if (grid.getStore().getCount() == 0) {
      PSI.MsgBox.showInfo("没有选择权限");

      return;
    }

    var items = [];
    for (var i = 0; i < grid.getStore().getCount(); i++) {
      var item = grid.getStore().getAt(i);
      items.push({
        id: item.get("id"),
        name: item.get("name")
      });
    }

    var dataOrgList = Ext.getCmp("editDataOrgIdList").getValue();
    if (!dataOrgList) {
      PSI.MsgBox.showInfo("没有选择数据域");
      return;
    }

    if (me.getParentForm()) {
      var fullNameList = Ext.getCmp("editDataOrg").getValue();
      me.getParentForm().setSelectedPermission(items, dataOrgList,
        fullNameList);
    }

    me.close();
  },

  onSelectDataOrg: function () {
    var me = this;
    var form = Ext.create("PSI.Permission.SelectDataOrgForm", {
      parentForm: me
    });
    form.show();
  },

  setDataOrgList: function (fullNameList, dataOrgList) {
    Ext.getCmp("editDataOrg").setValue(fullNameList);
    Ext.getCmp("editDataOrgIdList").setValue(dataOrgList);
  },

  /**
   * 所有可以选择的权限的Grid
   */
  getPermissionGrid: function () {
    var me = this;
    if (me.__permissionGrid) {
      return me.__permissionGrid;
    }

    var modelName = "PSIPermission_SelectPermissionForm";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "name", "note"]
    });

    var store = Ext.create("Ext.data.Store", {
      model: modelName,
      autoLoad: false,
      data: []
    });

    me.__permissionGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      store: store,
      columnLines: true,
      bbar: [{
        text: "全部添加",
        handler: me.addAllPermission,
        scope: me,
        iconCls: "PSI-button-add-detail"
      }],
      columns: [{
        header: "权限名称",
        dataIndex: "name",
        width: 300,
        menuDisabled: true
      }, {
        header: "",
        align: "center",
        menuDisabled: true,
        draggable: false,
        width: 30,
        xtype: "actioncolumn",
        items: [{
          icon: PSI.Const.BASE_URL
            + "Public/Images/icons/add.png",
          handler: me.onAddPermission,
          scope: me
        }]
      }, {
        header: "权限说明",
        dataIndex: "note",
        width: 600,
        menuDisabled: true
      }]
    });

    return me.__permissionGrid;
  },

  onAddPermission: function (grid, row) {
    var item = grid.getStore().getAt(row);
    var me = this;
    var store = me.getSelectedGrid().getStore();
    if (store.findExact("id", item.get("id")) == -1) {
      store.add({
        id: item.get("id"),
        name: item.get("name")
      });
    }
  },

  /**
   * 最终用户选择权限的Grid
   */
  getSelectedGrid: function () {
    var me = this;
    if (me.__selectedGrid) {
      return me.__selectedGrid;
    }

    var modelName = "PSISelectedPermission_SelectPermissionForm";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "name"]
    });

    var store = Ext.create("Ext.data.Store", {
      model: modelName,
      autoLoad: false,
      data: []
    });

    me.__selectedGrid = Ext.create("Ext.grid.Panel", {
      header: {
        height: 30,
        title: me.formatGridHeaderTitle("已经选择的权限")
      },
      padding: 5,
      store: store,
      columns: [{
        header: "权限名称",
        dataIndex: "name",
        flex: 1,
        menuDisabled: true,
        draggable: false
      }, {
        header: "",
        align: "center",
        menuDisabled: true,
        draggable: false,
        width: 40,
        xtype: "actioncolumn",
        items: [{
          icon: PSI.Const.BASE_URL
            + "Public/Images/icons/delete.png",
          handler: function (grid, row) {
            grid.getStore().removeAt(row);
          },
          scope: me
        }]
      }]
    });

    return me.__selectedGrid;
  },

  /**
   * 权限分类Grid
   */
  getCategoryGrid: function () {
    var me = this;
    if (me.__categoryGrid) {
      return me.__categoryGrid;
    }

    var modelName = "PSIPermissionCategory_SelectPermissionForm";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["name"]
    });

    var store = Ext.create("Ext.data.Store", {
      model: modelName,
      autoLoad: false,
      data: []
    });

    me.__categoryGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      store: store,
      columns: [{
        header: "权限分类",
        dataIndex: "name",
        flex: 1,
        menuDisabled: true
      }],
      listeners: {
        select: {
          fn: me.onCategoryGridSelect,
          scope: me
        }
      }
    });

    return me.__categoryGrid;

  },

  onCategoryGridSelect: function () {
    var me = this;
    var item = me.getCategoryGrid().getSelectionModel().getSelection();

    if (item == null || item.length != 1) {
      return;
    }

    var category = item[0].get("name");
    var grid = me.getPermissionGrid();
    var store = grid.getStore();
    var el = grid.getEl() || Ext.getBody();

    el.mask("数据加载中...");
    me.ajax({
      url: me.URL("Home/Permission/permissionByCategory"),
      params: {
        category: category
      },
      callback: function (options, success, response) {
        store.removeAll();

        if (success) {
          var data = me.decodeJSON(response.responseText);
          store.add(data);
        }

        el.unmask();
      }
    });
  },

  addAllPermission: function () {
    var me = this;
    var store = me.getPermissionGrid().getStore();

    var selectStore = me.getSelectedGrid().getStore();

    var cnt = store.getCount();

    var d = [];

    for (var i = 0; i < cnt; i++) {
      var item = store.getAt(i);

      if (selectStore.findExact("id", item.get("id")) == -1) {
        d.push({
          id: item.get("id"),
          name: item.get("name")
        });
      }
    }

    selectStore.add(d);

    me.getSelectedGrid().focus();
  }
});
