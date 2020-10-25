/**
 * 选择数据域
 */
Ext.define("PSI.Permission.SelectDataOrgForm", {
  extend: "Ext.window.Window",

  config: {
    parentForm: null,
    editForm: null
    // PSI.Permission.EditForm
  },

  title: "选择数据域",
  width: 600,
  height: 500,
  modal: true,
  layout: "fit",

  initComponent: function () {
    var me = this;

    Ext.apply(me, {
      items: [me.getMainGrid()],
      buttons: [{
        text: "把数据域设置为[本人数据]",
        handler: me.onSetSelf,
        scope: me
      }, {
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
  },

  onWndShow: function () {
    var me = this;
    var store = me.getMainGrid().getStore();

    var el = me.getEl() || Ext.getBody();
    el.mask("数据加载中...");
    Ext.Ajax.request({
      url: PSI.Const.BASE_URL
        + "Home/Permission/selectDataOrg",
      params: {},
      method: "POST",
      callback: function (options, success, response) {
        if (success) {
          var data = Ext.JSON
            .decode(response.responseText);
          store.add(data);
        }

        el.unmask();
      }
    });
  },

  onOK: function () {
    var me = this;
    var grid = me.getMainGrid();

    var items = grid.getSelectionModel().getSelection();
    if (items == null || items.length == 0) {
      PSI.MsgBox.showInfo("没有选择数据域");

      return;
    }

    var fullNameList = [];
    var dataOrgList = [];
    for (var i = 0; i < items.length; i++) {
      var it = items[i];
      fullNameList.push(it.get("fullName"));
      dataOrgList.push(it.get("dataOrg"));
    }

    if (me.getParentForm()) {
      me.getParentForm().setDataOrgList(fullNameList.join(";"),
        dataOrgList.join(";"));
    }

    if (me.getEditForm()) {
      me.getEditForm().onEditDataOrgCallback(dataOrgList
        .join(";"));
    }

    me.close();
  },

  getMainGrid: function () {
    var me = this;
    if (me.__mainGrid) {
      return me.__mainGrid;
    }

    var modelName = "PSIDataOrg_SelectForm";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "fullName", "dataOrg"]
    });

    var store = Ext.create("Ext.data.Store", {
      model: modelName,
      autoLoad: false,
      data: []
    });

    me.__mainGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      padding: 5,
      selModel: {
        mode: "MULTI"
      },
      selType: "checkboxmodel",
      store: store,
      columnLines: true,
      columns: [{
        header: "组织机构",
        dataIndex: "fullName",
        flex: 2,
        menuDisabled: true
      }, {
        header: "数据域",
        dataIndex: "dataOrg",
        flex: 1,
        menuDisabled: true
      }]
    });

    return me.__mainGrid;
  },

  onSetSelf: function () {
    var me = this;
    if (me.getParentForm()) {
      me.getParentForm().setDataOrgList("[本人数据]", "#");
    }

    if (me.getEditForm()) {
      me.getEditForm().onEditDataOrgCallback("#");
    }

    me.close();
  }
});
