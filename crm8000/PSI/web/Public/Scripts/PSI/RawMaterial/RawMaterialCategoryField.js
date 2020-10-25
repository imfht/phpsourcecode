/**
 * 自定义字段 - 原材料分类字段
 */
Ext.define("PSI.RawMaterial.RawMaterialCategoryField", {
  extend: "Ext.form.field.Trigger",
  alias: "widget.psi_rawmaterialcategoryfield",

  /**
   * 初始化组件
   */
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
    var modelName = "PSIRawMaterialCategoryModel_Field";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "text", "fullName", "code", "leaf",
        "children"]
    });

    var orgStore = Ext.create("Ext.data.TreeStore", {
      model: modelName,
      proxy: {
        type: "ajax",
        actionMethods: {
          read: "POST"
        },
        url: PSI.Const.BASE_URL + "Home/Material/allRawMaterialCategories"
      }
    });

    var tree = Ext.create("Ext.tree.Panel", {
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
          dataIndex: "code"
        }]
      }
    });
    tree.on("itemdblclick", this.onOK, this);
    this.tree = tree;

    var wnd = Ext.create("Ext.window.Window", {
      title: "选择原材料分类",
      header: false,
      border: 0,
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
    wnd.on("deactivate", function () {
      wnd.close();
    });

    wnd.showBy(this);
  },

  onOK: function () {
    var me = this;

    var tree = me.tree;
    var item = tree.getSelectionModel().getSelection();

    if (item === null || item.length !== 1) {
      PSI.MsgBox.showInfo("没有选择原材料分类");

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
