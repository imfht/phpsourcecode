/**
 * 销售退货入库单-选择销售出库单界面
 */
Ext.define("PSI.Sale.SRSelectWSBillForm", {
  extend: "PSI.AFX.BaseDialogForm",

  initComponent: function () {
    var me = this;
    Ext.apply(me, {
      title: "选择销售出库单",
      width: 1200,
      height: 600,
      layout: "border",
      items: [{
        region: "center",
        border: 0,
        bodyPadding: 10,
        layout: "border",
        items: [{
          region: "north",
          height: "50%",
          split: true,
          layout: "fit",
          items: [me.getWSBillGrid()]
        }, {
          region: "center",
          layout: "fit",
          items: [me.getDetailGrid()]
        }]
      }, {
        region: "north",
        border: 0,
        layout: {
          type: "table",
          columns: 4
        },
        height: 130,
        bodyPadding: 10,
        items: [{
          html: "<h1>选择要退货的销售出库单</h1>",
          border: 0,
          colspan: 4
        }, {
          id: "editWSRef",
          xtype: "textfield",
          labelAlign: "right",
          labelSeparator: "",
          fieldLabel: "销售出库单单号"
        }, {
          xtype: "psi_customerfield",
          showModal: true,
          id: "editWSCustomer",
          labelAlign: "right",
          labelSeparator: "",
          fieldLabel: "客户",
          labelWidth: 60,
          width: 200
        }, {
          id: "editFromDT",
          xtype: "datefield",
          format: "Y-m-d",
          labelAlign: "right",
          labelSeparator: "",
          fieldLabel: "业务日期（起）"
        }, {
          id: "editToDT",
          xtype: "datefield",
          format: "Y-m-d",
          labelAlign: "right",
          labelSeparator: "",
          fieldLabel: "业务日期（止）"
        }, {
          xtype: "psi_warehousefield",
          showModal: true,
          id: "editWSWarehouse",
          labelAlign: "right",
          labelSeparator: "",
          fieldLabel: "仓库"
        }, {
          id: "editWSSN",
          xtype: "textfield",
          labelAlign: "right",
          labelSeparator: "",
          fieldLabel: "序列号",
          labelWidth: 60,
          width: 200
        }, {
          xtype: "container",
          items: [{
            xtype: "button",
            text: "查询",
            width: 100,
            margin: "0 0 0 10",
            iconCls: "PSI-button-refresh",
            handler: me.onQuery,
            scope: me
          }, {
            xtype: "button",
            text: "清空查询条件",
            width: 100,
            margin: "0, 0, 0, 10",
            handler: me.onClearQuery,
            scope: me
          }]
        }]
      }],
      listeners: {
        show: {
          fn: me.onWndShow,
          scope: me
        }
      },
      buttons: [{
        text: "选择",
        iconCls: "PSI-button-ok",
        formBind: true,
        handler: me.onOK,
        scope: me
      }, {
        text: "取消",
        handler: function () {
          me.close();
        },
        scope: me
      }]
    });

    me.callParent(arguments);
  },

  onWndShow: function () {
    var me = this;
  },

  onOK: function () {
    var me = this;

    var item = me.getWSBillGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      PSI.MsgBox.showInfo("请选择销售出库单");
      return;
    }
    var wsBill = item[0];
    me.close();
    me.getParentForm().getWSBillInfo(wsBill.get("id"));
  },

  getWSBillGrid: function () {
    var me = this;

    if (me.__wsBillGrid) {
      return me.__wsBillGrid;
    }

    var modelName = "PSIWSBill_SRSelectForm";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "ref", "bizDate", "customerName",
        "warehouseName", "inputUserName", "bizUserName",
        "amount", "tax", "moneyWithTax"]
    });
    var storeWSBill = Ext.create("Ext.data.Store", {
      autoLoad: false,
      model: modelName,
      data: [],
      pageSize: 20,
      proxy: {
        type: "ajax",
        actionMethods: {
          read: "POST"
        },
        url: PSI.Const.BASE_URL + "Home/Sale/selectWSBillList",
        reader: {
          root: 'dataList',
          totalProperty: 'totalCount'
        }
      }
    });
    storeWSBill.on("beforeload", function () {
      storeWSBill.proxy.extraParams = me.getQueryParam();
    });

    me.__wsBillGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      columnLines: true,
      columns: [Ext.create("Ext.grid.RowNumberer", {
        text: "序号",
        width: 50
      }), {
        header: "单号",
        dataIndex: "ref",
        width: 110,
        menuDisabled: true,
        sortable: false
      }, {
        header: "业务日期",
        dataIndex: "bizDate",
        menuDisabled: true,
        sortable: false
      }, {
        header: "客户",
        dataIndex: "customerName",
        width: 200,
        menuDisabled: true,
        sortable: false
      }, {
        header: "销售金额(不含税)",
        dataIndex: "amount",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        width: 130
      }, {
        header: "税金",
        dataIndex: "tax",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        width: 100
      }, {
        header: "价税合计",
        dataIndex: "moneyWithTax",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        width: 120
      }, {
        header: "出库仓库",
        dataIndex: "warehouseName",
        menuDisabled: true,
        sortable: false
      }, {
        header: "业务员",
        dataIndex: "bizUserName",
        menuDisabled: true,
        sortable: false
      }, {
        header: "录单人",
        dataIndex: "inputUserName",
        menuDisabled: true,
        sortable: false
      }],
      listeners: {
        select: {
          fn: me.onMainGridSelect,
          scope: me
        },
        itemdblclick: {
          fn: me.onOK,
          scope: me
        }
      },
      store: storeWSBill,
      bbar: [{
        id: "srbill_selectform_pagingToobar",
        xtype: "pagingtoolbar",
        border: 0,
        store: storeWSBill
      }, "-", {
        xtype: "displayfield",
        value: "每页显示"
      }, {
        id: "srbill_selectform_comboCountPerPage",
        xtype: "combobox",
        editable: false,
        width: 60,
        store: Ext.create("Ext.data.ArrayStore", {
          fields: ["text"],
          data: [["20"], ["50"], ["100"], ["300"],
          ["1000"]]
        }),
        value: 20,
        listeners: {
          change: {
            fn: function () {
              storeWSBill.pageSize = Ext
                .getCmp("srbill_selectform_comboCountPerPage")
                .getValue();
              storeWSBill.currentPage = 1;
              Ext
                .getCmp("srbill_selectform_pagingToobar")
                .doRefresh();
            },
            scope: me
          }
        }
      }, {
        xtype: "displayfield",
        value: "条记录"
      }]
    });

    return me.__wsBillGrid;
  },

  onQuery: function () {
    Ext.getCmp("srbill_selectform_pagingToobar").doRefresh();
  },

  getQueryParam: function () {
    var result = {};

    var ref = Ext.getCmp("editWSRef").getValue();
    if (ref) {
      result.ref = ref;
    }

    var customerId = Ext.getCmp("editWSCustomer").getIdValue();
    if (customerId) {
      result.customerId = customerId;
    }

    var warehouseId = Ext.getCmp("editWSWarehouse").getIdValue();
    if (warehouseId) {
      result.warehouseId = warehouseId;
    }

    var fromDT = Ext.getCmp("editFromDT").getValue();
    if (fromDT) {
      result.fromDT = Ext.Date.format(fromDT, "Y-m-d");
    }

    var toDT = Ext.getCmp("editToDT").getValue();
    if (toDT) {
      result.toDT = Ext.Date.format(toDT, "Y-m-d");
    }

    var sn = Ext.getCmp("editWSSN").getValue();
    if (sn) {
      result.sn = sn;
    }

    return result;
  },

  onClearQuery: function () {
    Ext.getCmp("editWSRef").setValue(null);
    Ext.getCmp("editWSCustomer").clearIdValue();
    Ext.getCmp("editWSWarehouse").clearIdValue();
    Ext.getCmp("editFromDT").setValue(null);
    Ext.getCmp("editToDT").setValue(null);
    Ext.getCmp("editWSSN").setValue(null);

    this.onQuery();
  },

  getDetailGrid: function () {
    var me = this;

    if (me.__detailGrid) {
      return me.__detailGrid;
    }

    var modelName = "SRSelectWSBillForm_PSIWSBillDetail";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "goodsCode", "goodsName", "goodsSpec",
        "unitName", "goodsCount", "goodsMoney",
        "goodsPrice", "sn", "memo", "taxRate", "tax",
        "moneyWithTax", "goodsPriceWithTax"]
    });
    var store = Ext.create("Ext.data.Store", {
      autoLoad: false,
      model: modelName,
      data: []
    });

    me.__detailGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      title: "销售出库单明细",
      columnLines: true,
      columns: [Ext.create("Ext.grid.RowNumberer", {
        text: "序号",
        width: 40
      }), {
        header: "商品编码",
        dataIndex: "goodsCode",
        menuDisabled: true,
        sortable: false,
        width: 120
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
        header: "数量",
        dataIndex: "goodsCount",
        menuDisabled: true,
        sortable: false,
        align: "right"
      }, {
        header: "单位",
        dataIndex: "unitName",
        menuDisabled: true,
        sortable: false,
        width: 60
      }, {
        header: "单价(不含税)",
        dataIndex: "goodsPrice",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        width: 150
      }, {
        header: "销售金额(不含税)",
        dataIndex: "goodsMoney",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        width: 150
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
        width: 150
      }, {
        header: "价税合计",
        dataIndex: "moneyWithTax",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        width: 150
      }, {
        header: "含税价",
        dataIndex: "goodsPriceWithTax",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        width: 150
      }, {
        header: "序列号",
        dataIndex: "sn",
        menuDisabled: true,
        sortable: false
      }, {
        header: "备注",
        dataIndex: "memo",
        width: 200,
        menuDisabled: true,
        sortable: false
      }],
      store: store
    });

    return me.__detailGrid;
  },

  onMainGridSelect: function () {
    var me = this;
    me.getDetailGrid().setTitle("销售出库单明细");

    me.refreshDetailGrid();
  },

  refreshDetailGrid: function () {
    var me = this;
    me.getDetailGrid().setTitle("销售出库单明细");
    var grid = me.getWSBillGrid();
    var item = grid.getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      return;
    }
    var bill = item[0];

    grid = me.getDetailGrid();
    grid.setTitle("单号: " + bill.get("ref") + " 客户: "
      + bill.get("customerName") + " 出库仓库: "
      + bill.get("warehouseName"));
    var el = grid.getEl();
    el.mask(PSI.Const.LOADING);

    var r = {
      url: PSI.Const.BASE_URL + "Home/Sale/wsBillDetailListForSRBill",
      params: {
        billId: bill.get("id")
      },
      method: "POST",
      callback: function (options, success, response) {
        var store = grid.getStore();

        store.removeAll();

        if (success) {
          var data = Ext.JSON.decode(response.responseText);
          store.add(data);
        }

        el.unmask();
      }
    };

    Ext.Ajax.request(r);
  }
});
