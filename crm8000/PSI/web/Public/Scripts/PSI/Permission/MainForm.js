/**
 * 权限管理 - 主界面
 */
Ext.define("PSI.Permission.MainForm", {
  extend: "PSI.AFX.BaseMainExForm",

  config: {
    pAdd: "",
    pEdit: "",
    pDelete: ""
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
        layout: "fit",
        border: 0,
        items: [{
          layout: "border",
          border: 0,
          items: [{
            region: "north",
            height: "50%",
            border: 0,
            split: true,
            layout: "border",
            items: [{
              region: "center",
              layout: "fit",
              border: 0,
              items: [me
                .getPermissionGrid()]
            }, {
              region: "east",
              layout: "fit",
              width: "40%",
              border: 0,
              split: true,
              items: [me
                .getDataOrgGrid()]
            }]
          }, {
            xtype: "panel",
            region: "center",
            border: 0,
            layout: "fit",
            items: [me.getUserGrid()]
          }]
        }]
      }, {
        region: "west",
        layout: "fit",
        width: 250,
        split: true,
        border: 0,
        items: [me.getRoleGrid()]
      }]
    });

    me.callParent(arguments);

    me.roleGrid = me.getRoleGrid();
    me.permissionGrid = me.getPermissionGrid();
    me.userGrid = me.getUserGrid();

    me.refreshRoleGrid();
  },

  getQueryCmp: function () {
    var me = this;
    return [{
      id: "editQueryLoginName",
      labelWidth: 60,
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "登录名",
      margin: "5, 0, 0, 0",
      xtype: "textfield"
    }, {
      id: "editQueryName",
      labelWidth: 60,
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "用户姓名",
      margin: "5, 0, 0, 0",
      xtype: "textfield"
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

  getToolbarCmp: function () {
    var me = this;
    return [{
      text: "新增角色",
      handler: me.onAddRole,
      scope: me,
      disabled: me.getPAdd() == "0"
    }, {
      text: "编辑角色",
      handler: me.onEditRole,
      scope: me,
      disabled: me.getPEdit() == "0"
    }, {
      text: "删除角色",
      handler: me.onDeleteRole,
      scope: me,
      disabled: me.getPDelete() == "0"
    }, "-", {
      text: "帮助",
      handler: function () {
        window.open(me.URL("Home/Help/index?t=permission"));
      }
    }, "-", {
      text: "关闭",
      handler: function () {
        me.closeWindow();
      }
    }];
  },

  getRoleGrid: function () {
    var me = this;
    if (me.__roleGrid) {
      return me.__roleGrid;
    }

    var modelName = "PSIRole";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "name", "code"]
    });

    var roleStore = Ext.create("Ext.data.Store", {
      model: modelName,
      autoLoad: false,
      data: []
    });

    var roleGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      header: {
        height: 30,
        title: me.formatGridHeaderTitle("角色")
      },
      store: roleStore,
      columns: [{
        header: "编码",
        dataIndex: "code",
        width: 100,
        menuDisabled: true
      }, {
        header: "角色名称",
        dataIndex: "name",
        flex: 1,
        menuDisabled: true
      }]
    });

    roleGrid.on("itemclick", me.onRoleGridItemClick, me);

    me.__roleGrid = roleGrid;
    return me.__roleGrid;
  },

  getPermissionGrid: function () {
    var me = this;
    if (me.__permissionGrid) {
      return me.__permissionGrid;
    }

    var modelName = "PSIPermission";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "name", "dataOrg", "note"]
    });

    var permissionStore = Ext.create("Ext.data.Store", {
      model: modelName,
      autoLoad: false,
      data: []
    });

    me.__permissionGrid = Ext.create("Ext.grid.Panel", {
      header: {
        height: 30,
        title: me.formatGridHeaderTitle("权限")
      },
      cls: "PSI",
      store: permissionStore,
      columnLines: true,
      columns: [{
        header: "权限名称",
        dataIndex: "name",
        width: 200,
        menuDisabled: true
      }, {
        header: "说明",
        dataIndex: "note",
        flex: 1,
        menuDisabled: true
      }, {
        header: "数据域",
        dataIndex: "dataOrg",
        width: 100,
        menuDisabled: true
      }],
      listeners: {
        itemclick: {
          fn: me.onPermissionGridItemClick,
          scope: me
        }
      }
    });

    return me.__permissionGrid;
  },

  getUserGrid: function () {
    var me = this;
    if (me.__userGrid) {
      return me.__userGrid;
    }

    var modelName = "PSIUser";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "loginName", "name", "orgFullName",
        "enabled"]
    });

    var userStore = Ext.create("Ext.data.Store", {
      model: modelName,
      autoLoad: false,
      data: []
    });

    me.__userGrid = Ext.create("Ext.grid.Panel", {
      header: {
        height: 30,
        title: me.formatGridHeaderTitle("用户")
      },
      cls: "PSI",
      store: userStore,
      columns: [{
        header: "用户姓名",
        dataIndex: "name",
        menuDisabled: true,
        flex: 1
      }, {
        header: "登录名",
        dataIndex: "loginName",
        menuDisabled: true,
        flex: 1
      }, {
        header: "所属组织",
        dataIndex: "orgFullName",
        menuDisabled: true,
        flex: 1
      }]
    });
    return me.__userGrid;
  },

  /**
   * 刷新角色Grid
   */
  refreshRoleGrid: function (id) {
    var me = this;

    var grid = me.getRoleGrid();
    var store = grid.getStore();
    var me = this;
    Ext.getBody().mask("数据加载中...");
    me.ajax({
      url: me.URL("Home/Permission/roleList"),
      params: {
        queryLoginName: Ext.getCmp("editQueryLoginName")
          .getValue(),
        queryName: Ext.getCmp("editQueryName").getValue()
      },
      callback: function (options, success, response) {
        store.removeAll();

        if (success) {
          var data = me.decodeJSON(response.responseText);
          store.add(data);

          if (data.length > 0) {
            if (id) {
              var r = store.findExact("id", id);
              if (r != -1) {
                grid.getSelectionModel().select(r);
              }
            } else {
              grid.getSelectionModel().select(0);
            }
            me.onRoleGridItemClick();
          }
        }

        Ext.getBody().unmask();
      }
    });
  },

  onRoleGridItemClick: function () {
    var me = this;
    me.getDataOrgGrid().getStore().removeAll();
    me.getDataOrgGrid().setTitle(me.formatGridHeaderTitle("数据域"));

    var grid = me.getPermissionGrid();

    var item = me.getRoleGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      return;
    }

    var role = item[0].data;
    var store = grid.getStore();
    grid.setTitle(me.formatGridHeaderTitle("角色 [" + role.name + "] 的权限列表"));

    var el = grid.getEl() || Ext.getBody();

    el.mask("数据加载中...");
    me.ajax({
      url: me.URL("Home/Permission/permissionList"),
      params: {
        roleId: role.id
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

    var userGrid = me.getUserGrid();
    var userStore = userGrid.getStore();
    var userEl = userGrid.getEl() || Ext.getBody();
    userGrid.setTitle(me.formatGridHeaderTitle("属于角色 [" + role.name
      + "] 的人员列表"));
    userEl.mask("数据加载中...");
    me.ajax({
      url: me.URL("Home/Permission/userList"),
      params: {
        roleId: role.id
      },
      callback: function (options, success, response) {
        userStore.removeAll();

        if (success) {
          var data = me.decodeJSON(response.responseText);
          userStore.add(data);
        }

        userEl.unmask();
      }
    });
  },

  /**
   * 新增角色
   */
  onAddRole: function () {
    var me = this;
    var form = Ext.create("PSI.Permission.EditForm", {
      parentForm: me
    });

    form.show();
  },

  /**
   * 编辑角色
   */
  onEditRole: function () {
    var me = this;

    var grid = me.getRoleGrid();
    var items = grid.getSelectionModel().getSelection();

    if (items == null || items.length != 1) {
      me.showInfo("请选择要编辑的角色");
      return;
    }

    var role = items[0].data;

    var form = Ext.create("PSI.Permission.EditForm", {
      entity: role,
      parentForm: me
    });

    form.show();
  },

  /**
   * 删除角色
   */
  onDeleteRole: function () {
    var me = this;
    var grid = me.getRoleGrid();
    var items = grid.getSelectionModel().getSelection();

    if (items == null || items.length != 1) {
      me.showInfo("请选择要删除的角色");
      return;
    }

    var role = items[0].data;

    var info = "请确认是否删除角色 <span style='color:red'>" + role.name
      + "</span> ?";
    var funcConfirm = function () {
      Ext.getBody().mask("正在删除中...");
      var r = {
        url: me.URL("Home/Permission/deleteRole"),
        params: {
          id: role.id
        },
        callback: function (options, success, response) {
          Ext.getBody().unmask();

          if (success) {
            var data = Ext.JSON.decode(response.responseText);
            if (data.success) {
              me.showInfo("成功完成删除操作", function () {
                me.refreshRoleGrid();
              });
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

  getDataOrgGrid: function () {
    var me = this;
    if (me.__dataOrgGrid) {
      return me.__dataOrgGrid;
    }

    var modelName = "PSIPermissionDataOrg_MainForm";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["dataOrg", "fullName"]
    });

    var store = Ext.create("Ext.data.Store", {
      model: modelName,
      autoLoad: false,
      data: []
    });

    me.__dataOrgGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      header: {
        height: 30,
        title: me.formatGridHeaderTitle("数据域")
      },
      store: store,
      columns: [{
        header: "数据域",
        dataIndex: "dataOrg",
        width: 120,
        menuDisabled: true
      }, {
        header: "组织机构/人",
        dataIndex: "fullName",
        flex: 1,
        menuDisabled: true
      }]
    });

    return me.__dataOrgGrid;
  },

  onPermissionGridItemClick: function () {
    var me = this;
    var grid = me.getRoleGrid();
    var items = grid.getSelectionModel().getSelection();

    if (items == null || items.length != 1) {
      return;
    }

    var role = items[0];

    var grid = me.getPermissionGrid();
    var items = grid.getSelectionModel().getSelection();

    if (items == null || items.length != 1) {
      return;
    }
    var permission = items[0];

    var grid = me.getDataOrgGrid();
    grid.setTitle(me.formatGridHeaderTitle("角色 [" + role.get("name")
      + "] - 权限 [" + permission.get("name") + "] - 数据域"));

    var el = grid.getEl() || Ext.getBody();
    var store = grid.getStore();

    el.mask("数据加载中...");
    me.ajax({
      url: me.URL("Home/Permission/dataOrgList"),
      params: {
        roleId: role.get("id"),
        permissionId: permission.get("id")
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

  onClearQuery: function () {
    var me = this;

    Ext.getCmp("editQueryLoginName").setValue(null);
    Ext.getCmp("editQueryName").setValue(null);

    me.onQuery();
  },

  onQuery: function () {
    var me = this;

    me.getPermissionGrid().getStore().removeAll();
    me.getPermissionGrid().setTitle("权限列表");
    me.getUserGrid().getStore().removeAll();
    me.getUserGrid().setTitle("用户列表");
    me.getDataOrgGrid().getStore().removeAll();
    me.getDataOrgGrid().setTitle("数据域");

    me.refreshRoleGrid();
  }
});
