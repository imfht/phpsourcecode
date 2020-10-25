/**
 * 自定义字段 - 子商品字段
 */
Ext.define("PSI.Goods.SubGoodsField", {
  extend: "Ext.form.field.Trigger",
  alias: "widget.psi_subgoodsfield",

  config: {
    parentCmp: null,
    parentGoodsId: null
  },

	/**
	 * 初始化组件
	 */
  initComponent: function () {
    var me = this;

    me.enableKeyEvents = true;

    me.callParent(arguments);

    me.on("keydown", function (field, e) {
      if (e.getKey() == e.BACKSPACE) {
        field.setValue(null);
        e.preventDefault();
        if (me.getParentCmp()
          && me.getParentCmp().__setGoodsInfo) {
          me.getParentCmp().__setGoodsInfo(null);
        }
        return false;
      }

      if (e.getKey() != e.ENTER && !e.isSpecialKey(e.getKey())) {
        this.onTriggerClick(e);
      }
    });
  },

  onTriggerClick: function (e) {
    var me = this;
    var modelName = "PSISubGoodsField";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "code", "name", "spec", "unitName"]
    });

    var store = Ext.create("Ext.data.Store", {
      model: modelName,
      autoLoad: false,
      data: []
    });
    var lookupGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      columnLines: true,
      border: 0,
      store: store,
      columns: [{
        header: "编码",
        dataIndex: "code",
        menuDisabled: true,
        width: 70
      }, {
        header: "商品",
        dataIndex: "name",
        menuDisabled: true,
        flex: 1
      }, {
        header: "规格型号",
        dataIndex: "spec",
        menuDisabled: true,
        flex: 1
      }, {
        header: "单位",
        dataIndex: "unitName",
        menuDisabled: true,
        width: 60
      }]
    });
    me.lookupGrid = lookupGrid;
    me.lookupGrid.on("itemdblclick", me.onOK, me);

    var wnd = Ext.create("Ext.window.Window", {
      title: "选择 - 子商品",
      modal: true,
      width: 600,
      height: 300,
      layout: "border",
      items: [{
        region: "center",
        xtype: "panel",
        layout: "fit",
        border: 0,
        items: [lookupGrid]
      }, {
        xtype: "panel",
        region: "south",
        height: 40,
        layout: "fit",
        border: 0,
        items: [{
          xtype: "form",
          layout: "form",
          bodyPadding: 5,
          items: [{
            id: "__editGoods",
            xtype: "textfield",
            fieldLabel: "商品",
            labelWidth: 50,
            labelAlign: "right",
            labelSeparator: ""
          }]
        }]
      }],
      buttons: [{
        text: "确定",
        handler: me.onOK,
        scope: me
      }, {
        text: "取消",
        handler: function () {
          wnd.close();
        }
      }]
    });

    wnd.on("close", function () {
      me.focus();
    });
    me.wnd = wnd;

    var editName = Ext.getCmp("__editGoods");
    editName.on("change", function () {
      var store = me.lookupGrid.getStore();
      Ext.Ajax.request({
        url: PSI.Const.BASE_URL
          + "Home/Goods/queryDataForSubGoods",
        params: {
          queryKey: editName.getValue(),
          parentGoodsId: me.getParentGoodsId()
        },
        method: "POST",
        callback: function (opt, success, response) {
          store.removeAll();
          if (success) {
            var data = Ext.JSON
              .decode(response.responseText);
            store.add(data);
            if (data.length > 0) {
              me.lookupGrid.getSelectionModel().select(0);
              editName.focus();
            }
          } else {
            PSI.MsgBox.showInfo("网络错误");
          }
        },
        scope: this
      });

    }, me);

    editName.on("specialkey", function (field, e) {
      if (e.getKey() == e.ENTER) {
        me.onOK();
      } else if (e.getKey() == e.UP) {
        var m = me.lookupGrid.getSelectionModel();
        var store = me.lookupGrid.getStore();
        var index = 0;
        for (var i = 0; i < store.getCount(); i++) {
          if (m.isSelected(i)) {
            index = i;
          }
        }
        index--;
        if (index < 0) {
          index = 0;
        }
        m.select(index);
        e.preventDefault();
        editName.focus();
      } else if (e.getKey() == e.DOWN) {
        var m = me.lookupGrid.getSelectionModel();
        var store = me.lookupGrid.getStore();
        var index = 0;
        for (var i = 0; i < store.getCount(); i++) {
          if (m.isSelected(i)) {
            index = i;
          }
        }
        index++;
        if (index > store.getCount() - 1) {
          index = store.getCount() - 1;
        }
        m.select(index);
        e.preventDefault();
        editName.focus();
      }
    }, me);

    me.wnd.on("show", function () {
      editName.focus();
      editName.fireEvent("change");
    }, me);
    wnd.show();
  },

  onOK: function () {
    var me = this;
    var grid = me.lookupGrid;
    var item = grid.getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      return;
    }

    var data = item[0];

    me.wnd.close();
    me.focus();
    me.setValue(data.get("code"));
    me.focus();

    if (me.getParentCmp() && me.getParentCmp().__setGoodsInfo) {
      me.getParentCmp().__setGoodsInfo(data);
    }
  }
});
