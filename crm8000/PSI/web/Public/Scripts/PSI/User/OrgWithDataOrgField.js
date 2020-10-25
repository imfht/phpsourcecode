/**
 * 自定义字段 - 组织机构字段,用数据域过滤
 */
Ext.define("PSI.User.OrgWithDataOrgField", {
  extend: "Ext.form.field.Trigger",
  alias: "widget.psi_orgwithdataorgfield",

  initComponent: function () {
    var me = this;

    me.__idValue = null;

    me.enableKeyEvents = true;

    me.callParent(arguments);

    me.on("keydown", function (field, e) {
      if (e.getKey() == e.BACKSPACE) {
        field.setValue(null);
        me.setIdValue(null);
        e.preventDefault();
        return false;
      }

      if (e.getKey() !== e.ENTER) {
        this.onTriggerClick(e);
      }
    });
    me.on({
      render: function (p) {
        p.getEl().on("dblclick", function () {
          me.onTriggerClick();
        });
      },
      single: true
    });
  },

  onTriggerClick: function (e) {
    var me = this;

    var modelName = "PSIOrgModel_OrgWithDataOrgField";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "fullName"]
    });

    var orgStore = Ext.create("Ext.data.Store", {
      model: modelName,
      autoLoad: false,
      data: []
    });

    var orgTree = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      store: orgStore,
      columns: [{
        header: "组织机构",
        dataIndex: "fullName",
        flex: 1,
        menuDisabled: true,
        sortable: false
      }]
    });
    orgTree.on("itemdblclick", this.onOK, this);
    this.tree = orgTree;

    var wnd = Ext.create("Ext.window.Window", {
      title: "选择组织机构",
      border: 0,
      header: false,
      width: 500,
      height: 400,
      layout: "fit",
      items: [orgTree],
      buttons: [{
        text: "确定",
        handler: this.onOK,
        scope: this
      }, {
        text: "取消",
        handler: function () {
          wnd.close();
        }
      }]
    });
    this.wnd = wnd;

    wnd.on("deactivate", function () {
      wnd.close();
    });

    wnd.showBy(this);

    me.refreshGrid();
  },

  refreshGrid: function () {
    var me = this;
    var grid = me.tree;

    var el = grid.getEl() || Ext.getBody();
    el.mask(PSI.Const.LOADING);
    Ext.Ajax.request({
      url: PSI.Const.BASE_URL
        + "Home/User/orgWithDataOrg",
      method: "POST",
      callback: function (options, success, response) {
        var store = grid.getStore();

        store.removeAll();

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

    var tree = me.tree;
    var item = tree.getSelectionModel().getSelection();

    if (item === null || item.length !== 1) {
      PSI.MsgBox.showInfo("没有选择组织机构");

      return;
    }

    var data = item[0];

    me.setIdValue(data.get("id"));
    me.setValue(data.get("fullName"));
    me.wnd.close();
    me.focus();
  },

  setIdValue: function (id) {
    this.__idValue = id;
  },

  getIdValue: function () {
    return this.__idValue;
  }
});
