/**
 * 销售退货入库单 - 查看界面
 */
Ext.define("PSI.Bill.SRViewForm", {
  extend: "Ext.window.Window",

  config: {
    ref: null
  },

  initComponent: function () {
    var me = this;

    Ext.apply(me, {
      title: "查看销售退货入库单",
      modal: true,
      onEsc: Ext.emptyFn,
      maximized: true,
      closable: false,
      width: 900,
      height: 600,
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
        height: 100,
        bodyPadding: 10,
        items: [{
          id: "editCustomer",
          xtype: "displayfield",
          fieldLabel: "客户",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: ":",
          colspan: 2,
          width: 430
        }, {
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
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: ":",
          xtype: "displayfield"
        }, {
          id: "editWarehouse",
          fieldLabel: "入库仓库",
          labelWidth: 60,
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
      url: PSI.Const.BASE_URL + "Home/Bill/srBillInfo",
      params: {
        ref: me.getRef()
      },
      method: "POST",
      callback: function (options, success, response) {
        el.unmask();

        if (success) {
          var data = Ext.JSON.decode(response.responseText);

          Ext.getCmp("editCustomer").setValue(data.customerName + " 销售单号: " + data.wsBillRef);

          Ext.getCmp("editWarehouse").setValue(data.warehouseName);

          Ext.getCmp("editBizUser").setValue(data.bizUserName);
          Ext.getCmp("editBizDT").setValue(data.bizDT);

          var store = me.getGoodsGrid().getStore();
          store.removeAll();
          if (data.items) {
            store.add(data.items);
          }
        } else {
          PSI.MsgBox.showInfo("网络错误")
        }
      }
    });
  },

  getGoodsGrid: function () {
    var me = this;
    if (me.__goodsGrid) {
      return me.__goodsGrid;
    }

    var modelName = "PSISRBillDetail_ViewForm";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "goodsId", "goodsCode",
        "goodsName", "goodsSpec", "unitName",
        "goodsCount", "goodsMoney", "goodsPrice",
        "rejCount", "rejPrice", "rejMoney", "sn"]
    });
    var store = Ext.create("Ext.data.Store", {
      autoLoad: false,
      model: modelName,
      data: []
    });

    me.__goodsGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      columnLines: true,
      columns: [Ext.create("Ext.grid.RowNumberer", {
        text: "序号",
        width: 40
      }), {
        header: "商品编码",
        dataIndex: "goodsCode",
        menuDisabled: true,
        sortable: false
      }, {
        header: "商品名称",
        dataIndex: "goodsName",
        menuDisabled: true,
        sortable: false,
        width: 200
      }, {
        header: "规格型号",
        dataIndex: "goodsSpec",
        menuDisabled: true,
        sortable: false,
        width: 200
      }, {
        header: "退货数量",
        dataIndex: "rejCount",
        menuDisabled: true,
        sortable: false,
        align: "right",
        width: 100
      }, {
        header: "单位",
        dataIndex: "unitName",
        menuDisabled: true,
        sortable: false,
        width: 60
      }, {
        header: "退货单价",
        dataIndex: "rejPrice",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        width: 100
      }, {
        header: "退货金额",
        dataIndex: "rejMoney",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        width: 120
      }, {
        header: "销售数量",
        dataIndex: "goodsCount",
        menuDisabled: true,
        sortable: false,
        align: "right",
        width: 100
      }, {
        header: "销售单价",
        dataIndex: "goodsPrice",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        width: 100
      }, {
        header: "销售金额",
        dataIndex: "goodsMoney",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        width: 120
      }, {
        header: "序列号",
        dataIndex: "sn",
        menuDisabled: true,
        sortable: false
      }],
      store: store
    });

    return me.__goodsGrid;
  }
});
