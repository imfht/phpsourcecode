/**
 * 自定义字段 - 上级字段
 */
Ext.define("PSI.CodeTable.CodeTableParentIdField", {
  extend: "Ext.form.field.Trigger",
  alias: "widget.psi_codetable_parentidfield",

  config: {
    // 当前码表的完整元数据
    metadata: null,
    idCmp: null
  },

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
    var modelName = "PSICodeTableParentIdField";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "name", "full_name", "code",
        "leaf", "children"]
    });

    var store = Ext.create("Ext.data.TreeStore", {
      model: modelName,
      proxy: {
        type: "ajax",
        extraParams: {
          fid: me.getMetadata().fid
        },
        actionMethods: {
          read: "POST"
        },
        url: PSI.Const.BASE_URL + "Home/CodeTable/codeTableRecordListForTreeView"
      }
    });

    var tree = Ext.create("Ext.tree.Panel", {
      cls: "PSI",
      store: store,
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
          dataIndex: "name"
        }, {
          text: "编码",
          dataIndex: "code"
        }]
      }
    });
    tree.on("itemdblclick", this.onOK, this);
    this.tree = tree;

    var wnd = Ext.create("Ext.window.Window", {
      title: "选择上级",
      modal: true,
      width: 400,
      height: 300,
      layout: "fit",
      items: [tree],
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
      PSI.MsgBox.showInfo("没有选择记录");

      return;
    }

    var data = item[0];

    me.setIdValue(data.get("id"));
    me.setValue(data.get("full_name"));

    me.wnd.close();
    me.focus();
  },

  setIdValue: function (id) {
    var me = this;
    me.__idValue = id;

    var idCmp = me.getIdCmp();
    debugger;
    if (idCmp) {
      idCmp.setValue(id);
    }
  },

  getIdValue: function () {
    return this.__idValue;
  }
});
