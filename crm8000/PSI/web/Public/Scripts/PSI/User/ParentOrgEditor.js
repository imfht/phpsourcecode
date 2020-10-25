/**
 * 自定义字段 - 上级组织机构字段
 */
Ext.define("PSI.User.ParentOrgEditor", {
  extend: "Ext.form.field.Trigger",
  alias: "widget.PSI_parent_org_editor",

  initComponent: function () {
    var me = this;

    me.enableKeyEvents = true;

    me.callParent(arguments);

    me.on("keydown", function (field, e) {
      if (e.getKey() === e.BACKSPACE) {
        e.preventDefault();
        return false;
      }

      if (e.getKey() !== e.ENTER) {
        me.onTriggerClick(e);
      }
    });

    me.on("render", function (p) {
      p.getEl().on("dblclick", function () {
        me.onTriggerClick();
      });
    });
  },

  onTriggerClick: function (e) {
    Ext.define("PSIOrgModel_ParentOrgEditor", {
      extend: "Ext.data.Model",
      fields: ["id", "text", "fullName", "orgCode",
        "leaf", "children"]
    });

    var orgStore = Ext.create("Ext.data.TreeStore", {
      model: "PSIOrgModel_ParentOrgEditor",
      proxy: {
        type: "ajax",
        actionMethods: {
          read: "POST"
        },
        extraParams: {
          enabled: -1
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
      title: "选择上级组织",
      modal: true,
      width: 400,
      height: 300,
      layout: "fit",
      items: [orgTree],
      buttons: [{
        text: "没有上级组织",
        handler: this.onNone,
        scope: this
      }, {
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

  onOK: function () {
    var tree = this.tree;
    var item = tree.getSelectionModel().getSelection();

    if (item === null || item.length !== 1) {
      PSI.MsgBox.showInfo("没有选择上级组织");

      return;
    }

    var data = item[0].data;
    var parentItem = this.initialConfig.parentItem;
    this.focus();
    parentItem.setParentOrg(data);
    this.wnd.close();
    this.focus();
  },

  onNone: function () {
    var parentItem = this.initialConfig.parentItem;
    parentItem.setParentOrg({
      id: "",
      fullName: ""
    });
    this.wnd.close();
    this.focus();
  }
});
