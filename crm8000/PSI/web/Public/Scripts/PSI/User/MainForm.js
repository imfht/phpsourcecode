/**
 * 用户管理 - 主界面
 */
Ext.define("PSI.User.MainForm", {
  extend: "PSI.AFX.BaseMainExForm",

  config: {
    pAddOrg: null,
    pEditOrg: null,
    pDeleteOrg: null,
    pAddUser: null,
    pEditUser: null,
    pDeleteUser: null,
    pChangePassword: null
  },

	/**
	 * 初始化组件
	 */
  initComponent: function () {
    var me = this;

    Ext.apply(me, {
      tbar: [{
        text: "新增组织机构",
        disabled: me.getPAddOrg() == "0",
        handler: me.onAddOrg,
        scope: me
      }, {
        text: "编辑组织机构",
        disabled: me.getPEditOrg() == "0",
        handler: me.onEditOrg,
        scope: me
      }, {
        text: "删除组织机构",
        disabled: me.getPDeleteOrg() == "0",
        handler: me.onDeleteOrg,
        scope: me
      }, "-", {
        text: "新增用户",
        disabled: me.getPAddUser() == "0",
        handler: me.onAddUser,
        scope: me
      }, {
        text: "编辑用户",
        disabled: me.getPEditUser() == "0",
        handler: me.onEditUser,
        scope: me
      }, {
        text: "删除用户",
        disabled: me.getPDeleteUser() == "0",
        handler: me.onDeleteUser,
        scope: me
      }, "-", {
        text: "修改用户密码",
        disabled: me.getPChangePassword() == "0",
        handler: me.onEditUserPassword,
        scope: me
      }, "-", {
        text: "帮助",
        handler: function () {
          window.open(me
            .URL("/Home/Help/index?t=user"));
        }
      }, "-", {
        text: "关闭",
        handler: function () {
          me.closeWindow();
        }
      }],
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
        xtype: "panel",
        layout: "fit",
        border: 0,
        items: [me.getUserGrid()]
      }, {
        id: "panelOrg",
        xtype: "panel",
        region: "west",
        layout: "fit",
        width: 510,
        split: true,
        collapsible: true,
        header: false,
        border: 0,
        items: [me.getOrgGrid()]
      }]
    });

    me.callParent(arguments);

    me.orgTree = me.getOrgGrid();
    me.grid = me.getUserGrid();
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
      fieldLabel: "姓名",
      margin: "5, 0, 0, 0",
      xtype: "textfield"
    }, {
      id: "editQueryEnabled",
      xtype: "combo",
      queryMode: "local",
      editable: false,
      valueField: "id",
      labelWidth: 60,
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "状态",
      margin: "5, 0, 0, 0",
      store: Ext.create("Ext.data.ArrayStore", {
        fields: ["id", "text"],
        data: [[-1, "全部"], [1, "允许登录"], [0, "禁止登录"]]
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

  getOrgGrid: function () {
    var me = this;
    if (me.__orgGrid) {
      return me.__orgGrid;
    }

    var modelName = "PSIOrgModel";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "text", "fullName", "orgCode", "dataOrg",
        "leaf", "children", "userCount", "orgType"]
    });

    var orgStore = Ext.create("Ext.data.TreeStore", {
      model: modelName,
      proxy: {
        type: "ajax",
        actionMethods: {
          read: "POST"
        },
        url: me.URL("Home/User/allOrgs")
      },
      listeners: {
        beforeload: {
          fn: function () {
            orgStore.proxy.extraParams = me
              .getQueryParamForCategory();
          },
          scope: me
        }
      }
    });

    orgStore.on("load", me.onOrgStoreLoad, me);

    var orgTree = Ext.create("Ext.tree.Panel", {
      cls: "PSI",
      header: {
        height: 30,
        title: me.formatGridHeaderTitle("组织机构")
      },
      store: orgStore,
      rootVisible: false,
      useArrows: true,
      viewConfig: {
        loadMask: true
      },
      tools: [{
        type: "close",
        handler: function () {
          Ext.getCmp("panelOrg").collapse();
        }
      }],
      columns: {
        defaults: {
          sortable: false,
          menuDisabled: true,
          draggable: false
        },
        items: [{
          xtype: "treecolumn",
          text: "名称",
          dataIndex: "text",
          width: 220
        }, {
          text: "编码",
          dataIndex: "orgCode",
          width: 100
        }, {
          text: "数据域",
          dataIndex: "dataOrg",
          width: 100
        }, {
          text: "用户数",
          dataIndex: "userCount",
          width: 80,
          align: "right"
        }, {
          text: "组织机构性质",
          dataIndex: "orgType",
          width: 200
        }]
      }
    });

    orgTree.on("select", function (rowModel, record) {
      me.onOrgTreeNodeSelect(record);
    }, me);

    orgTree.on("itemdblclick", me.onEditOrg, me);

    me.__orgGrid = orgTree;

    return me.__orgGrid;
  },

  getUserGrid: function () {
    var me = this;

    if (me.__userGrid) {
      return me.__userGrid;
    }

    var modelName = "PSIUser";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "loginName", "name", "enabled", "orgCode",
        "gender", "birthday", "idCardNumber", "tel",
        "tel02", "address", "dataOrg", "roleName"]
    });
    var storeGrid = Ext.create("Ext.data.Store", {
      autoLoad: false,
      model: modelName,
      data: [],
      pageSize: 20,
      proxy: {
        type: "ajax",
        actionMethods: {
          read: "POST"
        },
        url: me.URL("Home/User/users"),
        reader: {
          root: 'dataList',
          totalProperty: 'totalCount'
        }
      }
    });
    storeGrid.on("beforeload", function () {
      storeGrid.proxy.extraParams = me.getUserParam();
    });

    me.__userGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      header: {
        height: 30,
        title: me.formatGridHeaderTitle("人员列表")
      },
      viewConfig: {
        enableTextSelection: true
      },
      columnLines: true,
      columns: [Ext.create("Ext.grid.RowNumberer", {
        text: "序号",
        width: 40
      }), {
        header: "登录名",
        dataIndex: "loginName",
        menuDisabled: true,
        sortable: false,
        locked: true,
        width: 250,
        renderer: function (value, metaData, record) {
          if (parseInt(record.get("enabled")) == 1) {
            return value;
          } else {
            return "<span style='color:gray;text-decoration:line-through;'>"
              + value + "</span>";
          }
        }
      }, {
        header: "姓名",
        dataIndex: "name",
        menuDisabled: true,
        sortable: false,
        locked: true
      }, {
        header: "权限角色",
        dataIndex: "roleName",
        menuDisabled: true,
        sortable: false,
        width: 200
      }, {
        header: "编码",
        dataIndex: "orgCode",
        menuDisabled: true,
        sortable: false
      }, {
        header: "是否允许登录",
        dataIndex: "enabled",
        menuDisabled: true,
        sortable: false,
        renderer: function (value) {
          return value == 1
            ? "允许登录"
            : "<span style='color:red'>禁止登录</span>";
        }
      }, {
        header: "性别",
        dataIndex: "gender",
        menuDisabled: true,
        sortable: false,
        width: 70
      }, {
        header: "生日",
        dataIndex: "birthday",
        menuDisabled: true,
        sortable: false
      }, {
        header: "身份证号",
        dataIndex: "idCardNumber",
        menuDisabled: true,
        sortable: false,
        width: 200
      }, {
        header: "联系电话",
        dataIndex: "tel",
        menuDisabled: true,
        sortable: false
      }, {
        header: "备用联系电话",
        dataIndex: "tel02",
        menuDisabled: true,
        sortable: false
      }, {
        header: "家庭住址",
        dataIndex: "address",
        menuDisabled: true,
        sortable: false,
        width: 200
      }, {
        header: "数据域",
        dataIndex: "dataOrg",
        menuDisabled: true,
        sortable: false,
        width: 100
      }],
      store: storeGrid,
      listeners: {
        itemdblclick: {
          fn: me.onEditUser,
          scope: me
        }
      },
      bbar: ["->", {
        id: "pagingToolbar",
        border: 0,
        xtype: "pagingtoolbar",
        store: storeGrid
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
                storeGrid.pageSize = Ext
                  .getCmp("comboCountPerPage")
                  .getValue();
                storeGrid.currentPage = 1;
                Ext.getCmp("pagingToolbar").doRefresh();
              },
              scope: me
            }
          }
        }, {
          xtype: "displayfield",
          value: "条记录"
        }]
    });

    return me.__userGrid;
  },

  getGrid: function () {
    return this.grid;
  },

	/**
	 * 新增组织机构
	 */
  onAddOrg: function () {
    var me = this;

    var form = Ext.create("PSI.User.OrgEditForm", {
      parentForm: me
    });
    form.show();
  },

	/**
	 * 编辑组织机构
	 */
  onEditOrg: function () {
    var me = this;
    if (me.getPEditOrg() == "0") {
      return;
    }

    var tree = me.getOrgGrid();
    var item = tree.getSelectionModel().getSelection();
    if (item === null || item.length !== 1) {
      me.showInfo("请选择要编辑的组织机构");
      return;
    }

    var org = item[0];

    var form = Ext.create("PSI.User.OrgEditForm", {
      parentForm: me,
      entity: org
    });
    form.show();
  },

	/**
	 * 删除组织机构
	 */
  onDeleteOrg: function () {
    var me = this;
    var tree = me.getOrgGrid();
    var item = tree.getSelectionModel().getSelection();
    if (item === null || item.length !== 1) {
      me.showInfo("请选择要删除的组织机构");
      return;
    }

    var org = item[0].getData();

    var funcConfirm = function () {
      Ext.getBody().mask("正在删除中...");
      var r = {
        url: me.URL("Home/User/deleteOrg"),
        params: {
          id: org.id
        },
        callback: function (options, success, response) {
          Ext.getBody().unmask();

          if (success) {
            var data = me.decodeJSON(response.responseText);
            if (data.success) {
              me.showInfo("成功完成删除操作", function () {
                me.freshOrgGrid();
              });
            } else {
              me.showInfo(data.msg);
            }
          }
        }
      };

      me.ajax(r);
    };

    var info = "请确认是否删除组织机构 <span style='color:red'>" + org.fullName
      + "</span> ?";
    me.confirm(info, funcConfirm);
  },

  freshOrgGrid: function () {
    var me = this;

    me.getOrgGrid().getStore().reload();
  },

  freshUserGrid: function () {
    var me = this;

    var tree = me.getOrgGrid();
    var item = tree.getSelectionModel().getSelection();
    if (item === null || item.length !== 1) {
      return;
    }

    me.onOrgTreeNodeSelect(item[0]);
  },

	/**
	 * 新增用户
	 */
  onAddUser: function () {
    var me = this;

    var tree = me.getOrgGrid();
    var item = tree.getSelectionModel().getSelection();
    var org = null;
    if (item != null && item.length > 0) {
      org = item[0];
    }

    var form = Ext.create("PSI.User.UserEditForm", {
      parentForm: me,
      defaultOrg: org
    });
    form.show();
  },

	/**
	 * 编辑用户
	 */
  onEditUser: function () {
    var me = this;
    if (me.getPEditUser() == "0") {
      return;
    }

    var item = me.getUserGrid().getSelectionModel().getSelection();
    if (item === null || item.length !== 1) {
      me.showInfo("请选择要编辑的用户");
      return;
    }

    var user = item[0].data;

    var tree = me.orgTree;
    var node = tree.getSelectionModel().getSelection();
    if (node && node.length === 1) {
      var org = node[0].data;

      user.orgId = org.id;
      user.orgName = org.fullName;
    }

    var form = Ext.create("PSI.User.UserEditForm", {
      parentForm: me,
      entity: user
    });
    form.show();
  },

	/**
	 * 修改用户密码
	 */
  onEditUserPassword: function () {
    var me = this;

    var item = me.getUserGrid().getSelectionModel().getSelection();
    if (item === null || item.length !== 1) {
      me.showInfo("请选择要修改密码的用户");
      return;
    }

    var user = item[0].getData();
    var form = Ext.create("PSI.User.ChangeUserPasswordForm", {
      entity: user
    });
    form.show();
  },

	/**
	 * 删除用户
	 */
  onDeleteUser: function () {
    var me = this;
    var item = me.getUserGrid().getSelectionModel().getSelection();
    if (item === null || item.length !== 1) {
      me.showInfo("请选择要删除的用户");
      return;
    }

    var user = item[0].getData();

    var funcConfirm = function () {
      Ext.getBody().mask("正在删除中...");
      var r = {
        url: me.URL("Home/User/deleteUser"),
        params: {
          id: user.id
        },
        callback: function (options, success, response) {
          Ext.getBody().unmask();

          if (success) {
            var data = me.decodeJSON(response.responseText);
            if (data.success) {
              me.showInfo("成功完成删除操作", function () {
                me.freshUserGrid();
              });
            } else {
              me.showInfo(data.msg);
            }
          }
        }
      };
      me.ajax(r);
    };

    var info = "请确认是否删除用户 <span style='color:red'>" + user.name
      + "</span> ?";
    me.confirm(info, funcConfirm);
  },

  onOrgTreeNodeSelect: function (rec) {
    if (!rec) {
      return;
    }

    var org = rec.data;
    if (!org) {
      return;
    }

    var me = this;
    var grid = me.getUserGrid();

    grid.setTitle(me.formatGridHeaderTitle(org.fullName + " - 人员列表"));

    Ext.getCmp("pagingToolbar").doRefresh();
  },

  onOrgStoreLoad: function () {
    var me = this;

    var tree = me.getOrgGrid();
    var root = tree.getRootNode();
    if (root) {
      var node = root.firstChild;
      if (node) {
        tree.getSelectionModel().select(node);
      }
    }
  },

  getUserParam: function () {
    var me = this;
    var item = me.getOrgGrid().getSelectionModel().getSelection();
    if (item == null || item.length == 0) {
      return {};
    }

    var org = item[0];

    var queryLoginName = null;
    var editLoginName = Ext.getCmp("editQueryLoginName");
    if (editLoginName) {
      queryLoginName = editLoginName.getValue();
    }

    var queryName = null;
    var editQueryName = Ext.getCmp("editQueryName");
    if (editQueryName) {
      queryName = editQueryName.getValue();
    }

    var enabled = -1;
    var edit = Ext.getCmp("editQueryEnabled");
    if (edit) {
      enabled = edit.getValue();
    }

    return {
      orgId: org.get("id"),
      queryLoginName: queryLoginName,
      queryName: queryName,
      enabled: enabled
    }
  },

  onClearQuery: function () {
    var me = this;

    Ext.getCmp("editQueryLoginName").setValue(null);
    Ext.getCmp("editQueryName").setValue(null);
    Ext.getCmp("editQueryEnabled").setValue(-1);

    me.onQuery();
  },

  onQuery: function () {
    var me = this;

    me.getUserGrid().getStore().removeAll();

    me.freshOrgGrid();
  },

  getQueryParamForCategory: function () {
    var queryLoginName = null;
    var editLoginName = Ext.getCmp("editQueryLoginName");
    if (editLoginName) {
      queryLoginName = editLoginName.getValue();
    }

    var queryName = null;
    var editQueryName = Ext.getCmp("editQueryName");
    if (editQueryName) {
      queryName = editQueryName.getValue();
    }

    var enabled = -1;
    var edit = Ext.getCmp("editQueryEnabled");
    if (edit) {
      enabled = edit.getValue();
    }

    return {
      queryLoginName: queryLoginName,
      queryName: queryName,
      enabled: enabled
    };
  }
});
