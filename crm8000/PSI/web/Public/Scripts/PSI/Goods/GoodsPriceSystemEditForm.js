/**
 * 商品价格体系 - 设置界面
 */
Ext.define("PSI.Goods.GoodsPriceSystemEditForm", {
  extend: "PSI.AFX.BaseDialogForm",

  /**
   * 初始化组件
   */
  initComponent: function () {
    var me = this;
    var entity = me.getEntity();

    Ext.apply(me, {
      header: {
        title: me.formatTitle("设置商品价格体系"),
        height: 40,
        iconCls: "PSI-button-commit"
      },
      width: 580,
      height: 400,
      layout: "border",
      items: [{
        region: "center",
        border: 0,
        bodyPadding: 10,
        layout: "fit",
        items: [me.getGoodsGrid()]
      }, {
        region: "north",
        border: 0,
        layout: {
          type: "table",
          columns: 2
        },
        height: 40,
        bodyPadding: 10,
        items: [{
          xtype: "hidden",
          id: "hiddenId",
          name: "id",
          value: entity.get("id")
        }, {
          id: "editRef",
          fieldLabel: "商品",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: ":",
          xtype: "displayfield",
          value: entity.get("code")
            + " "
            + entity
              .get("name")
            + " "
            + entity
              .get("spec")
        }]
      }],
      buttons: [{
        text: "保存",
        iconCls: "PSI-button-ok",
        handler: me.onOK,
        scope: me,
        id: "buttonSave"
      }, {
        text: "取消",
        handler: function () {
          PSI.MsgBox.confirm("请确认是否取消当前操作？",
            function () {
              me.close();
            });
        },
        scope: me,
        id: "buttonCancel"
      }],
      listeners: {
        show: {
          fn: me.onWndShow,
          scope: me
        },
        close: {
          fn: me.onWndClose,
          scope: me
        }
      }
    });

    me.callParent(arguments);
  },

  onWindowBeforeUnload: function (e) {
    return (window.event.returnValue = e.returnValue = '确认离开当前页面？');
  },

  onWndClose: function () {
    var me = this;

    Ext.get(window).un('beforeunload', me.onWindowBeforeUnload);
  },

  onWndShow: function () {
    var me = this;

    Ext.get(window).on('beforeunload', me.onWindowBeforeUnload);

    var el = me.getEl() || Ext.getBody();
    el.mask(PSI.Const.LOADING);
    Ext.Ajax.request({
      url: PSI.Const.BASE_URL
        + "Home/Goods/goodsPriceSystemInfo",
      params: {
        id: Ext.getCmp("hiddenId").getValue()
      },
      method: "POST",
      callback: function (options, success, response) {
        el.unmask();

        if (success) {
          var data = Ext.JSON
            .decode(response.responseText);

          var store = me.getGoodsGrid().getStore();
          store.removeAll();
          store.add(data.priceList);

          Ext.getCmp("editBaseSalePrice")
            .setValue(data.baseSalePrice);
        } else {
          PSI.MsgBox.showInfo("网络错误")
        }
      }
    });
  },

  onOK: function () {
    var me = this;
    Ext.getBody().mask("正在保存中...");
    Ext.Ajax.request({
      url: PSI.Const.BASE_URL
        + "Home/Goods/editGoodsPriceSystem",
      method: "POST",
      params: {
        jsonStr: me.getSaveData()
      },
      callback: function (options, success, response) {
        Ext.getBody().unmask();

        if (success) {
          var data = Ext.JSON
            .decode(response.responseText);
          if (data.success) {
            PSI.MsgBox.showInfo("成功保存数据",
              function () {
                me.close();
                me.getParentForm()
                  .onGoodsSelect();
              });
          } else {
            PSI.MsgBox.showInfo(data.msg);
          }
        }
      }
    });
  },

  getGoodsGrid: function () {
    var me = this;
    if (me.__goodsGrid) {
      return me.__goodsGrid;
    }
    var modelName = "PSIGoodsPriceSystem_EditForm";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "name", "factor", "price"]
    });
    var store = Ext.create("Ext.data.Store", {
      autoLoad: false,
      model: modelName,
      data: []
    });

    me.__cellEditing = Ext.create("PSI.UX.CellEditing", {
      clicksToEdit: 1,
      listeners: {
        edit: {
          fn: me.cellEditingAfterEdit,
          scope: me
        }
      }
    });

    me.__goodsGrid = Ext.create("Ext.grid.Panel", {
      viewConfig: {
        enableTextSelection: true
      },
      plugins: [me.__cellEditing],
      columnLines: true,
      tbar: [{
        fieldLabel: "销售基准价",
        labelWidth: 70,
        labelAlign: "right",
        id: "editBaseSalePrice",
        xtype: "numberfield",
        hideTrigger: true
      }, " ", {
        xtype: "button",
        text: "根据销售基准价自动计算其他价格",
        iconCls: "PSI-button-ok",
        handler: me.onCalPrice,
        scope: me
      }],
      columns: [{
        header: "名称",
        dataIndex: "name",
        width: 120,
        menuDisabled: true,
        sortable: false
      }, {
        header: "销售基准价倍数",
        dataIndex: "factor",
        width: 120,
        menuDisabled: true,
        sortable: false
      }, {
        header: "价格",
        dataIndex: "price",
        width: 120,
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        editor: {
          xtype: "numberfield",
          hideTrigger: true
        }
      }],
      store: store
    });

    return me.__goodsGrid;
  },

  cellEditingAfterEdit: function (editor, e) {
  },

  getSaveData: function () {
    var me = this;

    var result = {
      id: Ext.getCmp("hiddenId").getValue(),
      basePrice: Ext.getCmp("editBaseSalePrice").getValue(),
      items: []
    };

    var store = me.getGoodsGrid().getStore();
    for (var i = 0; i < store.getCount(); i++) {
      var item = store.getAt(i);
      result.items.push({
        id: item.get("id"),
        price: item.get("price")
      });
    }

    return Ext.JSON.encode(result);
  },

  onCalPrice: function () {
    var me = this;
    var editBaseSalePrice = Ext.getCmp("editBaseSalePrice");
    var basePrice = editBaseSalePrice.getValue();

    if (!basePrice) {
      PSI.MsgBox.showInfo("请设置基准价格", function () {
        editBaseSalePrice.focus();
      });
      return;
    }

    var store = me.getGoodsGrid().getStore();
    for (var i = 0; i < store.getCount(); i++) {
      var item = store.getAt(i);
      item.set("price", item.get("factor") * basePrice);
    }
  }
});
