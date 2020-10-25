/**
 * 自定义字段 - 组织机构字段
 */
Ext.define("PSI.User.OrgField", {
  extend: "Ext.form.field.Trigger",
  alias: "widget.psi_orgfield",

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
    var modelName = "PSIOrgModel_OrgField";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "text", "fullName", "orgCode",
        "leaf", "children"]
    });

    var orgStore = Ext.create("Ext.data.TreeStore", {
      model: modelName,
      proxy: {
        type: "ajax",
        extraParams: {
          enabled: -1
        },
        actionMethods: {
          read: "POST"
        },
        url: PSI.Const.BASE_URL + "Home/User/allOrgs"
      }
    });

    var orgTree = Ext.create("Ext.tree.Panel", {
      cls: "PSI",
      store: orgStore,
      rootVisible: false,
      useArrows: true,
      viewConfig: {
        loadMask: true
      },
      columns: {
        defaults: {
          flex: 1,
          sortable: false,
          menuDisabled: true,
          draggable: false
        },
        items: [{
          xtype: "treecolumn",
          text: "名称",
          dataIndex: "text"
        }, {
          text: "编码",
          dataIndex: "orgCode"
        }]
      }
    });
    orgTree.on("itemdblclick", this.onOK, this);
    this.tree = orgTree;

    var wnd = Ext.create("Ext.window.Window", {
      title: "选择组织机构",
      modal: true,
      width: 400,
      height: 300,
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
    wnd.show();
  },

  // private
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
