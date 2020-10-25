/**
 * 销售退货入库 - 主界面
 */
Ext.define("PSI.Sale.SRMainForm", {
  extend: "PSI.AFX.BaseMainExForm",

  config: {
    permission: null
  },

  initComponent: function () {
    var me = this;

    Ext.apply(me, {
      tbar: me.getToolbarCmp(),
      items: [{
        id: "panelQueryCmp",
        region: "north",
        height: 95,
        layout: "fit",
        border: 0,
        header: false,
        collapsible: true,
        collapseMode: "mini",
        layout: {
          type: "table",
          columns: 4
        },
        items: me.getQueryCmp()
      }, {
        region: "center",
        layout: "border",
        border: 0,
        items: [{
          region: "north",
          height: "40%",
          split: true,
          layout: "fit",
          border: 0,
          items: [me.getMainGrid()]
        }, {
          region: "center",
          layout: "fit",
          border: 0,
          items: [me.getDetailGrid()]
        }]
      }]
    });

    me.callParent(arguments);

    me.refreshMainGrid();
  },

  getToolbarCmp: function () {
    var me = this;
    return [{
      text: "新建销售退货入库单",
      hidden: me.getPermission().add == "0",
      id: "buttonAdd",
      scope: me,
      handler: me.onAddBill
    }, {
      hidden: me.getPermission().add == "0",
      xtype: "tbseparator"
    }, {
      hidden: me.getPermission().edit == "0",
      text: "编辑销售退货入库单",
      id: "buttonEdit",
      scope: me,
      handler: me.onEditBill
    }, {
      hidden: me.getPermission().edit == "0",
      xtype: "tbseparator"
    }, {
      text: "删除销售退货入库单",
      hidden: me.getPermission().del == "0",
      id: "buttonDelete",
      scope: me,
      handler: me.onDeleteBill
    }, {
      hidden: me.getPermission().del == "0",
      xtype: "tbseparator"
    }, {
      hidden: me.getPermission().commit == "0",
      text: "提交入库",
      id: "buttonCommit",
      scope: me,
      handler: me.onCommit
    }, {
      hidden: me.getPermission().commit == "0",
      xtype: "tbseparator"
    }, {
      text: "导出",
      hidden: me.getPermission().genPDF == "0",
      menu: [{
        text: "单据生成pdf",
        iconCls: "PSI-button-pdf",
        id: "buttonPDF",
        scope: me,
        handler: me.onPDF
      }]
    }, {
      hidden: me.getPermission().genPDF == "0",
      xtype: "tbseparator"
    }, {
      text: "打印",
      hidden: me.getPermission().print == "0",
      menu: [{
        text: "打印预览",
        iconCls: "PSI-button-print-preview",
        scope: me,
        handler: me.onPrintPreview
      }, "-", {
        text: "直接打印",
        iconCls: "PSI-button-print",
        scope: me,
        handler: me.onPrint
      }]
    }, {
      xtype: "tbseparator",
      hidden: me.getPermission().print == "0"
    }, {
      text: "帮助",
      handler: function () {
        window.open(me.URL("Home/Help/index?t=srbill"));
      }
    }, "-", {
      text: "关闭",
      handler: function () {
        me.closeWindow();
      }
    }];
  },

  getQueryCmp: function () {
    var me = this;
    return [{
      id: "editQueryBillStatus",
      xtype: "combo",
      queryMode: "local",
      editable: false,
      valueField: "id",
      labelWidth: 60,
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "状态",
      margin: "5, 0, 0, 0",
      store: Ext.create("Ext.data.ArrayStore", {
        fields: ["id", "text"],
        data: [[-1, "全部"], [0, "待入库"], [1000, "已入库"]]
      }),
      value: -1
    }, {
      id: "editQueryRef",
      labelWidth: 60,
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "单号",
      margin: "5, 0, 0, 0",
      xtype: "textfield"
    }, {
      id: "editQueryFromDT",
      xtype: "datefield",
      margin: "5, 0, 0, 0",
      format: "Y-m-d",
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "业务日期（起）"
    }, {
      id: "editQueryToDT",
      xtype: "datefield",
      margin: "5, 0, 0, 0",
      format: "Y-m-d",
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "业务日期（止）"
    }, {
      id: "editQueryCustomer",
      xtype: "psi_customerfield",
      showModal: true,
      labelAlign: "right",
      labelSeparator: "",
      labelWidth: 60,
      margin: "5, 0, 0, 0",
      fieldLabel: "客户"
    }, {
      id: "editQueryWarehouse",
      xtype: "psi_warehousefield",
      showModal: true,
      labelAlign: "right",
      labelSeparator: "",
      labelWidth: 60,
      margin: "5, 0, 0, 0",
      fieldLabel: "仓库"
    }, {
      id: "editQuerySN",
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "序列号",
      margin: "5, 0, 0, 0",
      xtype: "textfield"
    }, {
      id: "editQueryPaymentType",
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "付款方式",
      margin: "5, 0, 0, 0",
      xtype: "combo",
      queryMode: "local",
      editable: false,
      valueField: "id",
      store: Ext.create("Ext.data.ArrayStore", {
        fields: ["id", "text"],
        data: [[-1, "全部"], [0, "记应付账款"], [1, "现金付款"],
        [2, "退款转入预收款"]]
      }),
      value: -1
    }, {
      id: "editQueryGoods",
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "商品",
      labelWidth: 60,
      margin: "5, 0, 0, 0",
      xtype: "psi_goodsfield",
      showModal: true
    }, {
      xtype: "container",
      items: [{
        xtype: "button",
        text: "查询",
        width: 100,
        height: 26,
        margin: "5 0 0 10",
        handler: me.onQuery,
        scope: me
      }, {
        xtype: "button",
        text: "清空查询条件",
        width: 100,
        height: 26,
        margin: "5, 0, 0, 10",
        handler: me.onClearQuery,
        scope: me
      }]
    }, {
      xtype: "container",
      items: [{
        xtype: "button",
        text: "隐藏查询条件栏",
        width: 130,
        height: 26,
        iconCls: "PSI-button-hide",
        margin: "5 0 0 10",
        handler: function () {
          Ext.getCmp("panelQueryCmp").collapse();
        },
        scope: me
      }]
    }];
  },

  refreshMainGrid: function (id) {
    var me = this;

    Ext.getCmp("buttonEdit").setDisabled(true);
    Ext.getCmp("buttonDelete").setDisabled(true);
    Ext.getCmp("buttonCommit").setDisabled(true);

    var gridDetail = this.getDetailGrid();
    gridDetail.setTitle(me.formatGridHeaderTitle("销售退货入库单明细"));
    gridDetail.getStore().removeAll();
    Ext.getCmp("pagingToobar").doRefresh();
    this.__lastId = id;
  },

  // 新增销售退货入库单
  onAddBill: function () {
    var form = Ext.create("PSI.Sale.SREditForm", {
      parentForm: this
    });
    form.show();
  },

  // 编辑销售退货入库单
  onEditBill: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      PSI.MsgBox.showInfo("请选择要编辑的销售退货入库单");
      return;
    }
    var bill = item[0];

    var form = Ext.create("PSI.Sale.SREditForm", {
      parentForm: me,
      entity: bill
    });
    form.show();
  },

  // 删除销售退货入库单
  onDeleteBill: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      PSI.MsgBox.showInfo("请选择要删除的销售退货入库单");
      return;
    }
    var bill = item[0];

    if (bill.get("billStatus") == "已入库") {
      PSI.MsgBox.showInfo("当前销售退货入库单已经提交入库，不能删除");
      return;
    }

    var info = "请确认是否删除销售退货入库单: <span style='color:red'>" + bill.get("ref")
      + "</span>";
    PSI.MsgBox.confirm(info, function () {
      var el = Ext.getBody();
      el.mask("正在删除中...");
      Ext.Ajax.request({
        url: PSI.Const.BASE_URL
          + "Home/Sale/deleteSRBill",
        method: "POST",
        params: {
          id: bill.get("id")
        },
        callback: function (options, success, response) {
          el.unmask();

          if (success) {
            var data = Ext.JSON
              .decode(response.responseText);
            if (data.success) {
              PSI.MsgBox.showInfo("成功完成删除操作",
                function () {
                  me.refreshMainGrid();
                });
            } else {
              PSI.MsgBox.showInfo(data.msg);
            }
          } else {
            PSI.MsgBox.showInfo("网络错误", function () {
              window.location.reload();
            });
          }
        }
      });
    });
  },

  // 提交销售退货入库单
  onCommit: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      PSI.MsgBox.showInfo("没有选择要提交的销售退货入库单");
      return;
    }
    var bill = item[0];

    if (bill.get("billStatus") == "已入库") {
      PSI.MsgBox.showInfo("当前销售退货入库单已经提交入库，不能再次提交");
      return;
    }

    var info = "请确认是否提交单号: <span style='color:red'>" + bill.get("ref")
      + "</span> 的销售退货入库单?";
    PSI.MsgBox.confirm(info, function () {
      var el = Ext.getBody();
      el.mask("正在提交中...");
      Ext.Ajax.request({
        url: PSI.Const.BASE_URL
          + "Home/Sale/commitSRBill",
        method: "POST",
        params: {
          id: bill.get("id")
        },
        callback: function (options, success, response) {
          el.unmask();

          if (success) {
            var data = Ext.JSON
              .decode(response.responseText);
            if (data.success) {
              PSI.MsgBox.showInfo("成功完成提交操作",
                function () {
                  me
                    .refreshMainGrid(data.id);
                });
            } else {
              PSI.MsgBox.showInfo(data.msg);
            }
          } else {
            PSI.MsgBox.showInfo("网络错误", function () {
              window.location.reload();
            });
          }
        }
      });
    });
  },

  getMainGrid: function () {
    var me = this;
    if (me.__mainGrid) {
      return me.__mainGrid;
    }

    var modelName = "PSISRBill";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "ref", "bizDate", "customerName",
        "warehouseName", "inputUserName", "bizUserName",
        "billStatus", "amount", "dateCreated",
        "paymentType", "billMemo", "tax", "moneyWithTax"]
    });
    var store = Ext.create("Ext.data.Store", {
      autoLoad: false,
      model: modelName,
      data: [],
      pageSize: 20,
      proxy: {
        type: "ajax",
        actionMethods: {
          read: "POST"
        },
        url: PSI.Const.BASE_URL + "Home/Sale/srbillList",
        reader: {
          root: 'dataList',
          totalProperty: 'totalCount'
        }
      }
    });
    store.on("beforeload", function () {
      store.proxy.extraParams = me.getQueryParam();
    });
    store.on("load", function (e, records, successful) {
      if (successful) {
        me.gotoMainGridRecord(me.__lastId);
      }
    });

    me.__mainGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      border: 1,
      columnLines: true,
      columns: [{
        xtype: "rownumberer",
        width: 50
      }, {
        header: "状态",
        dataIndex: "billStatus",
        menuDisabled: true,
        sortable: false,
        width: 60,
        renderer: function (value) {
          return value == "待入库"
            ? "<span style='color:red'>"
            + value + "</span>"
            : value;
        }
      }, {
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
        header: "退货金额(不含税)",
        dataIndex: "amount",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        width: 130
      }, {
        header: "税金(红字)",
        dataIndex: "tax",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        width: 120
      }, {
        header: "退货金额(含税)",
        dataIndex: "moneyWithTax",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        width: 120
      }, {
        header: "付款方式",
        dataIndex: "paymentType",
        menuDisabled: true,
        sortable: false,
        width: 100,
        renderer: function (value) {
          if (value == 0) {
            return "记应付账款";
          } else if (value == 1) {
            return "现金付款";
          } else if (value == 2) {
            return "退款转入预收款";
          } else {
            return "";
          }
        }
      }, {
        header: "入库仓库",
        dataIndex: "warehouseName",
        menuDisabled: true,
        sortable: false
      }, {
        header: "业务员",
        dataIndex: "bizUserName",
        menuDisabled: true,
        sortable: false
      }, {
        header: "制单人",
        dataIndex: "inputUserName",
        menuDisabled: true,
        sortable: false
      }, {
        header: "制单时间",
        dataIndex: "dateCreated",
        width: 140,
        menuDisabled: true,
        sortable: false
      }, {
        header: "备注",
        dataIndex: "billMemo",
        width: 300,
        menuDisabled: true,
        sortable: false
      }],
      listeners: {
        select: {
          fn: me.onMainGridSelect,
          scope: me
        },
        itemdblclick: {
          fn: me.getPermission().edit == "1"
            ? me.onEditBill
            : Ext.emptyFn,
          scope: me
        }
      },
      store: store,
      bbar: ["->", {
        id: "pagingToobar",
        xtype: "pagingtoolbar",
        border: 0,
        store: store
      }, "-", {
          xtype: "displayfield",
          value: "每页显示"
        }, {
          id: "comboCountPerPage",
          xtype: "combobox",
          editable: false,
          width: 60,
          store: Ext.create("Ext.data.ArrayStore", {
            fields: ["text"],
            data: [["20"], ["50"], ["100"],
            ["300"], ["1000"]]
          }),
          value: 20,
          listeners: {
            change: {
              fn: function () {
                store.pageSize = Ext
                  .getCmp("comboCountPerPage")
                  .getValue();
                store.currentPage = 1;
                Ext.getCmp("pagingToobar")
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

    return me.__mainGrid;
  },

  getDetailGrid: function () {
    var me = this;
    if (me.__detailGrid) {
      return me.__detailGrid;
    }

    var modelName = "PSISRBillDetail";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "goodsCode", "goodsName", "goodsSpec",
        "unitName", "goodsCount", "goodsMoney",
        "goodsPrice", "rejCount", "rejPrice",
        "rejSaleMoney", "sn", "memo", "taxRate", "tax",
        "rejSaleMoneyWithTax", "rejPriceWithTax"]
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
      header: {
        height: 30,
        title: me.formatGridHeaderTitle("销售退货入库单明细")
      },
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
        header: "退货数量",
        dataIndex: "rejCount",
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
        header: "退货单价(不含税)",
        dataIndex: "rejPrice",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        width: 150
      }, {
        header: "退货金额(不含税)",
        dataIndex: "rejSaleMoney",
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
        header: "税金(红字)",
        dataIndex: "tax",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        width: 150
      }, {
        header: "价税合计",
        dataIndex: "rejSaleMoneyWithTax",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        width: 150
      }, {
        header: "含税价",
        dataIndex: "rejPriceWithTax",
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

  gotoMainGridRecord: function (id) {
    var me = this;
    var grid = me.getMainGrid();
    grid.getSelectionModel().deselectAll();
    var store = grid.getStore();
    if (id) {
      var r = store.findExact("id", id);
      if (r != -1) {
        grid.getSelectionModel().select(r);
      } else {
        grid.getSelectionModel().select(0);
      }
    } else {
      grid.getSelectionModel().select(0);
    }
  },

  onMainGridSelect: function () {
    var me = this;
    me.getDetailGrid().setTitle(me.formatGridHeaderTitle("销售退货入库单明细"));
    var grid = me.getMainGrid();
    var item = grid.getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      Ext.getCmp("buttonEdit").setDisabled(true);
      Ext.getCmp("buttonDelete").setDisabled(true);
      Ext.getCmp("buttonCommit").setDisabled(true);
      return;
    }
    var bill = item[0];

    var commited = bill.get("billStatus") == "已入库";

    var buttonEdit = Ext.getCmp("buttonEdit");
    buttonEdit.setDisabled(false);
    if (commited) {
      buttonEdit.setText("查看销售退货入库单");
    } else {
      buttonEdit.setText("编辑销售退货入库单");
    }

    Ext.getCmp("buttonDelete").setDisabled(commited);
    Ext.getCmp("buttonCommit").setDisabled(commited);

    me.freshDetailGrid();
  },

  freshDetailGrid: function (id) {
    var me = this;
    me.getDetailGrid().setTitle(me.formatGridHeaderTitle("销售退货入库单明细"));
    var grid = me.getMainGrid();
    var item = grid.getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      return;
    }
    var bill = item[0];

    grid = me.getDetailGrid();
    grid.setTitle(me.formatGridHeaderTitle("单号: " + bill.get("ref")
      + " 客户: " + bill.get("customerName") + " 入库仓库: "
      + bill.get("warehouseName")));
    var el = grid.getEl();
    el.mask(PSI.Const.LOADING);
    Ext.Ajax.request({
      url: PSI.Const.BASE_URL + "Home/Sale/srBillDetailList",
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

          if (store.getCount() > 0) {
            if (id) {
              var r = store.findExact("id", id);
              if (r != -1) {
                grid.getSelectionModel().select(r);
              }
            }
          }
        }

        el.unmask();
      }
    });
  },

  onQuery: function () {
    var me = this;

    me.getMainGrid().getStore().currentPage = 1;
    me.refreshMainGrid();
  },

  onClearQuery: function () {
    var me = this;

    Ext.getCmp("editQueryBillStatus").setValue(-1);
    Ext.getCmp("editQueryRef").setValue(null);
    Ext.getCmp("editQueryFromDT").setValue(null);
    Ext.getCmp("editQueryToDT").setValue(null);
    Ext.getCmp("editQueryCustomer").clearIdValue();
    Ext.getCmp("editQueryWarehouse").clearIdValue();
    Ext.getCmp("editQuerySN").setValue(null);
    Ext.getCmp("editQueryPaymentType").setValue(-1);
    Ext.getCmp("editQueryGoods").clearIdValue();

    me.onQuery();
  },

  getQueryParam: function () {
    var me = this;

    var result = {
      billStatus: Ext.getCmp("editQueryBillStatus").getValue()
    };

    var ref = Ext.getCmp("editQueryRef").getValue();
    if (ref) {
      result.ref = ref;
    }

    var customerId = Ext.getCmp("editQueryCustomer").getIdValue();
    if (customerId) {
      result.customerId = customerId;
    }

    var warehouseId = Ext.getCmp("editQueryWarehouse").getIdValue();
    if (warehouseId) {
      result.warehouseId = warehouseId;
    }

    var fromDT = Ext.getCmp("editQueryFromDT").getValue();
    if (fromDT) {
      result.fromDT = Ext.Date.format(fromDT, "Y-m-d");
    }

    var toDT = Ext.getCmp("editQueryToDT").getValue();
    if (toDT) {
      result.toDT = Ext.Date.format(toDT, "Y-m-d");
    }

    var sn = Ext.getCmp("editQuerySN").getValue();
    if (sn) {
      result.sn = sn;
    }

    var paymentType = Ext.getCmp("editQueryPaymentType").getValue();
    result.paymentType = paymentType;

    var goodsId = Ext.getCmp("editQueryGoods").getIdValue();
    if (goodsId) {
      result.goodsId = goodsId;
    }

    return result;
  },

  onPDF: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      PSI.MsgBox.showInfo("没有选择要生成pdf文件的销售退货入库单");
      return;
    }
    var bill = item[0];

    var url = PSI.Const.BASE_URL + "Home/Sale/srBillPdf?ref="
      + bill.get("ref");
    window.open(url);
  },

	/**
	 * 打印预览
	 */
  onPrintPreview: function () {
    var lodop = getLodop();
    if (!lodop) {
      PSI.MsgBox.showInfo("没有安装Lodop控件，无法打印");
      return;
    }

    var me = this;

    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("没有选择要打印的销售退货入库单");
      return;
    }
    var bill = item[0];

    var el = Ext.getBody();
    el.mask("数据加载中...");
    var r = {
      url: PSI.Const.BASE_URL + "Home/Sale/genSRBillPrintPage",
      params: {
        id: bill.get("id")
      },
      callback: function (options, success, response) {
        el.unmask();

        if (success) {
          var data = response.responseText;
          me.previewSRBill(bill.get("ref"), data);
        }
      }
    };
    me.ajax(r);
  },

  PRINT_PAGE_WIDTH: "200mm",
  PRINT_PAGE_HEIGHT: "95mm",

  previewSRBill: function (ref, data) {
    var me = this;

    var lodop = getLodop();
    if (!lodop) {
      PSI.MsgBox.showInfo("Lodop打印控件没有正确安装");
      return;
    }

    lodop.PRINT_INIT("销售退货入库单" + ref);
    lodop.SET_PRINT_PAGESIZE(1, me.PRINT_PAGE_WIDTH, me.PRINT_PAGE_HEIGHT,
      "");
    lodop.ADD_PRINT_HTM("0mm", "0mm", "100%", "100%", data);
    var result = lodop.PREVIEW("_blank");
  },

	/**
	 * 直接打印
	 */
  onPrint: function () {
    var lodop = getLodop();
    if (!lodop) {
      PSI.MsgBox.showInfo("没有安装Lodop控件，无法打印");
      return;
    }

    var me = this;

    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("没有选择要打印的销售退货入库单");
      return;
    }
    var bill = item[0];

    var el = Ext.getBody();
    el.mask("数据加载中...");
    var r = {
      url: PSI.Const.BASE_URL + "Home/Sale/genSRBillPrintPage",
      params: {
        id: bill.get("id")
      },
      callback: function (options, success, response) {
        el.unmask();

        if (success) {
          var data = response.responseText;
          me.printSRBill(bill.get("ref"), data);
        }
      }
    };
    me.ajax(r);
  },

  printSRBill: function (ref, data) {
    var me = this;

    var lodop = getLodop();
    if (!lodop) {
      PSI.MsgBox.showInfo("Lodop打印控件没有正确安装");
      return;
    }

    lodop.PRINT_INIT("销售退货入库单" + ref);
    lodop.SET_PRINT_PAGESIZE(1, me.PRINT_PAGE_WIDTH, me.PRINT_PAGE_HEIGHT,
      "");
    lodop.ADD_PRINT_HTM("0mm", "0mm", "100%", "100%", data);
    var result = lodop.PRINT();
  }
});
