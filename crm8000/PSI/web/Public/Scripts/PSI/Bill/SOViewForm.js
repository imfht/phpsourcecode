/**
 * 销售订单 - 查看界面
 */
Ext.define("PSI.Bill.SOViewForm", {
  extend: "Ext.window.Window",

  config: {
    ref: null
  },

  initComponent: function () {
    var me = this;

    Ext.apply(me, {
      title: "查看销售订单",
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
          columns: 4
        },
        height: 120,
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
          id: "editDealDate",
          fieldLabel: "交货日期",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: ":",
          xtype: "displayfield"
        }, {
          id: "editCustomer",
          colspan: 2,
          width: 430,
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: ":",
          xtype: "displayfield",
          fieldLabel: "客户"
        }, {
          id: "editDealAddress",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: ":",
          fieldLabel: "交货地址",
          colspan: 2,
          width: 430,
          xtype: "displayfield"
        }, {
          id: "editContact",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: ":",
          fieldLabel: "联系人",
          xtype: "displayfield"
        }, {
          id: "editTel",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: ":",
          fieldLabel: "电话",
          xtype: "displayfield"
        }, {
          id: "editFax",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: ":",
          fieldLabel: "传真",
          xtype: "displayfield"
        }, {
          id: "editOrg",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: ":",
          fieldLabel: "组织机构",
          xtype: "displayfield",
          colspan: 2,
          width: 430
        }, {
          id: "editBizUser",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: ":",
          fieldLabel: "业务员",
          xtype: "displayfield"
        }, {
          id: "editPaymentType",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: ":",
          fieldLabel: "付款方式",
          xtype: "displayfield"
        }, {
          id: "editBillMemo",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: ":",
          fieldLabel: "备注",
          xtype: "displayfield",
          colspan: 3,
          width: 645
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
      url: PSI.Const.BASE_URL + "Home/Bill/soBillInfo",
      params: {
        ref: me.getRef()
      },
      method: "POST",
      callback: function (options, success, response) {
        el.unmask();

        if (success) {
          var data = Ext.JSON.decode(response.responseText);

          Ext.getCmp("editCustomer").setValue(data.customerName);
          Ext.getCmp("editBillMemo").setValue(data.billMemo);
          Ext.getCmp("editDealDate").setValue(data.dealDate);
          Ext.getCmp("editDealAddress").setValue(data.dealAddress);
          Ext.getCmp("editContact").setValue(data.contact);
          Ext.getCmp("editTel").setValue(data.tel);
          Ext.getCmp("editFax").setValue(data.fax);

          Ext.getCmp("editBizUser").setValue(data.bizUserName);
          Ext.getCmp("editOrg").setValue(data.orgFullName);

          Ext.getCmp("editPaymentType").setValue(data.paymentType);

          var store = me.getGoodsGrid().getStore();
          store.removeAll();
          store.add(data.items);
        }
      }
    });
  },

  getGoodsGrid: function () {
    var me = this;
    if (me.__goodsGrid) {
      return me.__goodsGrid;
    }
    var modelName = "PSISOBillDetail_ViewForm";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "goodsId", "goodsCode",
        "goodsName", "goodsSpec", "unitName",
        "goodsCount", "goodsMoney", "goodsPrice",
        "memo", "taxRate", "tax", "moneyWithTax"]
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
        header: "销售数量",
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
        width: 120
      }, {
        header: "价税合计",
        dataIndex: "moneyWithTax",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
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
