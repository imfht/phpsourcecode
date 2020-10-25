//
// 拆分单 - 查看界面
//
Ext.define("PSI.Bill.WSPViewForm", {
  extend: "Ext.window.Window",

  config: {
    ref: null
  },

  initComponent: function () {
    var me = this;

    Ext.apply(me, {
      title: "查看拆分单",
      modal: true,
      onEsc: Ext.emptyFn,
      maximized: true,
      closable: false,
      width: 1000,
      height: 600,
      layout: "border",
      items: [{
        region: "center",
        border: 0,
        layout: "border",
        items: [{
          region: "center",
          border: 0,
          layout: "fit",
          items: me.getGoodsGrid()
        }, {
          region: "south",
          layout: "fit",
          border: 0,
          split: true,
          height: "50%",
          items: me.getGoodsGridEx()
        }]
      }, {
        region: "north",
        border: 0,
        layout: {
          type: "table",
          columns: 2
        },
        height: 100,
        bodyPadding: 10,
        items: [{
          id: "editRef",
          fieldLabel: "单号",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: ":",
          xtype: "displayfield",
          value: me.getRef()
        }, {
          id: "editBizDT",
          fieldLabel: "业务日期",
          labelWidth: 120,
          labelAlign: "right",
          labelSeparator: ":",
          xtype: "displayfield"
        }, {
          id: "editFromWarehouse",
          fieldLabel: "仓库",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: ":",
          xtype: "displayfield"
        }, {
          id: "editToWarehouse",
          fieldLabel: "拆分后调入仓库",
          labelWidth: 120,
          labelAlign: "right",
          labelSeparator: ":",
          xtype: "displayfield"
        }, {
          id: "editBizUser",
          fieldLabel: "业务员",
          xtype: "displayfield",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: ":"
        }, {
          id: "editBillMemo",
          fieldLabel: "备注",
          xtype: "displayfield",
          labelWidth: 120,
          labelAlign: "right",
          labelSeparator: ":"
        }]
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
    var el = me.getEl() || Ext.getBody();
    el.mask(PSI.Const.LOADING);
    Ext.Ajax.request({
      url: PSI.Const.BASE_URL + "Home/Bill/wspBillInfo",
      params: {
        ref: me.getRef()
      },
      method: "POST",
      callback: function (options, success, response) {
        el.unmask();

        if (success) {
          var data = Ext.JSON.decode(response.responseText);

          Ext.getCmp("editBizUser").setValue(data.bizUserName);
          Ext.getCmp("editBizDT").setValue(data.bizDT);
          Ext.getCmp("editFromWarehouse").setValue(data.fromWarehouseName);
          Ext.getCmp("editToWarehouse").setValue(data.toWarehouseName);
          Ext.getCmp("editBillMemo").setValue(data.billMemo);

          var store = me.getGoodsGrid().getStore();
          store.removeAll();
          if (data.items) {
            store.add(data.items);
          }

          var store = me.getGoodsGridEx().getStore();
          store.removeAll();
          if (data.itemsEx) {
            store.add(data.itemsEx);
          }
        } else {
          PSI.MsgBox.showInfo("网络错误")
        }
      }
    });
  },

  formatGridHeaderTitle: function (title) {
    return "<span style='font-size:13px'>" + title + "</sapn>";
  },

  getGoodsGrid: function () {
    var me = this;
    if (me.__goodsGrid) {
      return me.__goodsGrid;
    }
    var modelName = "PSIWSPBillDetail_ViewForm";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "goodsId", "goodsCode",
        "goodsName", "goodsSpec", "unitName",
        "goodsCount", "memo"]
    });
    var store = Ext.create("Ext.data.Store", {
      autoLoad: false,
      model: modelName,
      data: []
    });

    me.__goodsGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      title: me.formatGridHeaderTitle("拆分前商品明细"),
      viewConfig: {
        enableTextSelection: true
      },
      columnLines: true,
      columns: {
        defaults: {
          menuDisabled: true,
          sortable: false
        },
        items: [Ext.create("Ext.grid.RowNumberer", {
          text: "序号",
          width: 40
        }), {
          header: "商品编码",
          dataIndex: "goodsCode"
        }, {
          header: "商品名称",
          dataIndex: "goodsName",
          width: 200
        }, {
          header: "规格型号",
          dataIndex: "goodsSpec",
          width: 200
        }, {
          header: "拆分数量",
          dataIndex: "goodsCount",
          align: "right",
          width: 100
        }, {
          header: "单位",
          dataIndex: "unitName",
          width: 60
        }, {
          header: "备注",
          dataIndex: "memo",
          width: 300
        }]
      },
      store: store
    });

    return me.__goodsGrid;
  },

  getGoodsGridEx: function () {
    var me = this;
    if (me.__goodsGridEx) {
      return me.__goodsGridEx;
    }
    var modelName = "PSIWSPBillDetail_ViewForm_Ex";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "goodsId", "goodsCode",
        "goodsName", "goodsSpec", "unitName",
        "goodsCount"]
    });
    var store = Ext.create("Ext.data.Store", {
      autoLoad: false,
      model: modelName,
      data: []
    });

    me.__goodsGridEx = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      title: me.formatGridHeaderTitle("拆分后商品明细"),
      viewConfig: {
        enableTextSelection: true
      },
      columnLines: true,
      columns: {
        defaults: {
          menuDisabled: true,
          sortable: false
        },
        items: [Ext.create("Ext.grid.RowNumberer", {
          text: "序号",
          width: 40
        }), {
          header: "商品编码",
          dataIndex: "goodsCode"
        }, {
          header: "商品名称",
          dataIndex: "goodsName",
          width: 200
        }, {
          header: "规格型号",
          dataIndex: "goodsSpec",
          width: 200
        }, {
          header: "拆分后入库数量",
          dataIndex: "goodsCount",
          align: "right",
          width: 130
        }, {
          header: "单位",
          dataIndex: "unitName",
          width: 60
        }]
      },
      store: store
    });

    return me.__goodsGridEx;
  }
});
