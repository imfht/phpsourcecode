/**
 * 采购入库单 - 查看界面
 */
Ext.define("PSI.Bill.PWViewForm", {
  extend: "Ext.window.Window",

  config: {
    ref: null
  },

  initComponent: function () {
    var me = this;

    Ext.apply(me, {
      title: "查看采购入库单",
      modal: true,
      closable: false,
      onEsc: Ext.emptyFn,
      maximized: true,
      width: 1000,
      height: 600,
      layout: "border",
      items: [{
        region: "center",
        layout: "fit",
        border: 0,
        bodyPadding: 10,
        items: [me.getGoodsGrid()]
      }, {
        region: "north",
        id: "editForm",
        layout: {
          type: "table",
          columns: 2
        },
        height: 100,
        bodyPadding: 10,
        border: 0,
        items: [{
          id: "editRef",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: ":",
          fieldLabel: "单号",
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
          id: "editSupplier",
          colspan: 2,
          width: 430,
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: ":",
          xtype: "displayfield",
          fieldLabel: "供应商"
        }, {
          id: "editWarehouse",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: ":",
          fieldLabel: "入库仓库",
          xtype: "displayfield"
        }, {
          id: "editBizUser",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: ":",
          fieldLabel: "业务员",
          xtype: "displayfield"
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
      url: PSI.Const.BASE_URL + "Home/Bill/pwBillInfo",
      params: {
        ref: me.getRef()
      },
      method: "POST",
      callback: function (options, success, response) {
        el.unmask();

        if (success) {
          var data = Ext.JSON.decode(response.responseText);

          Ext.getCmp("editSupplier").setValue(data.supplierName);
          Ext.getCmp("editWarehouse").setValue(data.warehouseName);
          Ext.getCmp("editBizUser").setValue(data.bizUserName);
          Ext.getCmp("editBizDT").setValue(data.bizDT);

          var store = me.getGoodsGrid().getStore();
          store.removeAll();
          if (data.items) {
            store.add(data.items);
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
    var modelName = "PSIPWBillDetail_ViewForm";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "goodsId", "goodsCode",
        "goodsName", "goodsSpec", "unitName",
        "goodsCount", { name: "goodsMoney", type: "float" }, "goodsPrice",
        "memo", "taxRate", { name: "tax", type: "float" }, { name: "moneyWithTax", type: "float" }]
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
      features: [{
        ftype: "summary"
      }],
      columnLines: true,
      columns: [{
        xtype: "rownumberer",
        width: 40,
        text: "序号"
      }, {
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
        header: "采购数量",
        dataIndex: "goodsCount",
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
        header: "采购单价",
        dataIndex: "goodsPrice",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        summaryRenderer: function () {
          return "采购金额合计";
        },
        width: 100
      }, {
        header: "采购金额",
        dataIndex: "goodsMoney",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        summaryType: "sum",
        width: 120
      }, {
        header: "税率(%)",
        dataIndex: "taxRate",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        format: "#",
        width: 80
      }, {
        header: "税金",
        dataIndex: "tax",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        summaryType: "sum",
        width: 120
      }, {
        header: "价税合计",
        dataIndex: "moneyWithTax",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        summaryType: "sum",
        width: 120
      }, {
        header: "备注",
        dataIndex: "memo",
        menuDisabled: true,
        sortable: false,
        width: 200
      }],
      store: store
    });

    return me.__goodsGrid;
  }
});
