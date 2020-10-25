/**
 * 选择用户
 */
Ext.define("PSI.Permission.SelectUserForm", {
  extend: "PSI.AFX.BaseDialogForm",

  config: {
    idList: null, // idList是数组
    parentForm: null
  },

  title: "选择用户",
  width: 850,
  height: 570,
  modal: true,
  layout: "border",

  initComponent: function () {
    var me = this;
    Ext.define("PSIUser_SelectUserForm", {
      extend: "Ext.data.Model",
      fields: ["id", "loginName", "code", "name", "orgFullName"]
    });

    var userStore = Ext.create("Ext.data.Store", {
      model: "PSIUser_SelectUserForm",
      autoLoad: false,
      data: []
    });

    var grid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      header: {
        height: 30,
        title: me.formatGridHeaderTitle("可以添加到当前角色的用户(已禁止登录的用户不会出现在列表中)")
      },
      padding: 5,
      selModel: {
        mode: "MULTI"
      },
      selType: "checkboxmodel",
      viewConfig: {
        deferEmptyText: false,
        emptyText: "所有用户都已经加入到当前角色中了"
      },
      store: userStore,
      columnLines: true,
      columns: [{
        header: "用户编码",
        dataIndex: "code",
        width: 120,
        menuDisabled: true
      }, {
        header: "用户姓名",
        dataIndex: "name",
        width: 120,
        menuDisabled: true
      }, {
        header: "登录名",
        dataIndex: "loginName",
        width: 120,
        menuDisabled: true
      }, {
        header: "所属组织",
        dataIndex: "orgFullName",
        flex: 1,
        menuDisabled: true
      }]
    });

    me.__grid = grid;

    Ext.apply(me, {
      items: [{
        region: "center",
        layout: "fit",
        border: 0,
        items: grid
      }, {
        region: "south",
        height: 70,
        border: 0,
        items: [{
          xtype: "form",
          layout: "form",
          border: 0,
          bodyPadding: 5,
          items: [{
            id: "PSI_Permission_SelectUserForm_editUser",
            xtype: "textfield",
            fieldLabel: "用户",
            labelWidth: 30,
            labelAlign: "right",
            labelSeparator: ""
          }, {
            xtype: "displayfield",
            value: "输入人员编码、登录名、拼音字头可以过滤"
          }]
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
        show: {
          fn: me.onWndShow,
          scope: me
        }
      }
    });

    me.callParent(arguments);

    me.editName = Ext.getCmp("PSI_Permission_SelectUserForm_editUser");
    me.editName.on("change", function () {
      me.refreshMainGrid();
    });
  },

  refreshMainGrid() {
    var me = this;
    var idList = me.getIdList();
    var userStore = me.__grid.getStore();

    var el = me.getEl() || Ext.getBody();
    el.mask("数据加载中...");
    Ext.Ajax.request({
      url: me.URL("Home/Permission/selectUsers"),
      params: {
        idList: idList.join(),
        name: me.editName.getValue()
      },
      method: "POST",
      callback: function (options, success, response) {
        if (success) {
          var data = Ext.JSON.decode(response.responseText);
          userStore.removeAll();
          userStore.add(data);
        }

        el.unmask();
      }
    });

  },

  onWndShow: function () {
    var me = this;
    me.editName.focus();

    me.refreshMainGrid();
  },

  onOK: function () {
    var grid = this.__grid;

    var items = grid.getSelectionModel().getSelection();
    if (items == null || items.length == 0) {
      PSI.MsgBox.showInfo("没有选择用户");

      return;
    }

    if (this.getParentForm()) {
      this.getParentForm().setSelectedUsers(items);
    }

    this.close();
  }
});
