/**
 * 采购订单 - 主界面
 * 
 * @author 李静波
 */
Ext.define("PSI.PurchaseOrder.POMainForm", {
  extend: "PSI.AFX.BaseMainExForm",

  config: {
    permission: null
  },

  /**
   * 初始化组件
   */
  initComponent: function () {
    var me = this;

    Ext.apply(me, {
      tbar: me.getToolbarCmp(),
      items: [{
        id: "panelQueryCmp",
        region: "north",
        height: 65,
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
          xtype: "tabpanel",
          items: [me.getDetailGrid(), me.getPWGrid()]
        }]
      }]
    });

    me.callParent(arguments);

    me.refreshMainGrid();
  },

  /**
   * 工具栏
   */
  getToolbarCmp: function () {
    var me = this;
    return [{
      text: "新建采购订单",
      scope: me,
      handler: me.onAddBill,
      hidden: me.getPermission().add == "0",
      id: "buttonAdd"
    }, {
      hidden: me.getPermission().add == "0",
      xtype: "tbseparator"
    }, {
      text: "编辑采购订单",
      scope: me,
      handler: me.onEditBill,
      hidden: me.getPermission().edit == "0",
      id: "buttonEdit"
    }, {
      hidden: me.getPermission().edit == "0",
      xtype: "tbseparator"
    }, {
      text: "删除采购订单",
      scope: me,
      handler: me.onDeleteBill,
      hidden: me.getPermission().del == "0",
      id: "buttonDelete"
    }, {
      xtype: "tbseparator",
      hidden: me.getPermission().del == "0",
      id: "tbseparator1"
    }, {
      text: "审核",
      scope: me,
      handler: me.onCommit,
      hidden: me.getPermission().confirm == "0",
      id: "buttonCommit"
    }, {
      text: "取消审核",
      scope: me,
      handler: me.onCancelConfirm,
      hidden: me.getPermission().confirm == "0",
      id: "buttonCancelConfirm"
    }, {
      xtype: "tbseparator",
      hidden: me.getPermission().confirm == "0",
      id: "tbseparator2"
    }, {
      text: "生成采购入库单",
      scope: me,
      handler: me.onGenPWBill,
      hidden: me.getPermission().genPWBill == "0",
      id: "buttonGenPWBill"
    }, {
      hidden: me.getPermission().genPWBill == "0",
      xtype: "tbseparator"
    }, {
      text: "关闭订单",
      hidden: me.getPermission().closeBill == "0",
      id: "buttonCloseBill",
      menu: [{
        text: "关闭采购订单",
        iconCls: "PSI-button-commit",
        scope: me,
        handler: me.onClosePO
      }, "-", {
        text: "取消采购订单关闭状态",
        iconCls: "PSI-button-cancelconfirm",
        scope: me,
        handler: me.onCancelClosedPO
      }]
    }, {
      hidden: me.getPermission().closeBill == "0",
      xtype: "tbseparator"
    }, {
      text: "导出",
      hidden: me.getPermission().genPDF == "0" &&
        me.getPermission().genExcel == "0",
      menu: [{
        text: "单据生成pdf",
        id: "buttonPDF",
        iconCls: "PSI-button-pdf",
        scope: me,
        handler: me.onPDF
      }, {
        text: "单据生成Excel",
        id: "buttonExcel",
        iconCls: "PSI-button-excel",
        scope: me,
        handler: me.onExcel
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
        window.open(me.URL("Home/Help/index?t=pobill"));
      }
    }, "-", {
      text: "关闭",
      handler: function () {
        me.closeWindow();
      }
    }];
  },

  /**
   * 查询条件
   */
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
        data: [[-1, "全部"], [0, "待审核"], [1000, "已审核"],
        [2000, "部分入库"], [3000, "全部入库"], [4000, "订单关闭"]]
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
      fieldLabel: "交货日期（起）"
    }, {
      id: "editQueryToDT",
      xtype: "datefield",
      margin: "5, 0, 0, 0",
      format: "Y-m-d",
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "交货日期（止）"
    }, {
      id: "editQuerySupplier",
      xtype: "psi_supplierfield",
      parentCmp: me,
      showModal: true,
      labelAlign: "right",
      labelSeparator: "",
      labelWidth: 60,
      margin: "5, 0, 0, 0",
      fieldLabel: "供应商"
    }, {
      id: "editQueryPaymentType",
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "付款方式",
      labelWidth: 60,
      margin: "5, 0, 0, 0",
      xtype: "combo",
      queryMode: "local",
      editable: false,
      valueField: "id",
      store: Ext.create("Ext.data.ArrayStore", {
        fields: ["id", "text"],
        data: [[-1, "全部"], [0, "记应付账款"], [1, "现金付款"],
        [2, "预付款"]]
      }),
      value: -1
    }, {
      id: "editQueryGoods",
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "物料",
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
      }, {
        xtype: "button",
        iconCls: "PSI-button-hide",
        text: "隐藏查询条件栏",
        width: 130,
        height: 26,
        margin: "5 0 0 10",
        handler: function () {
          Ext.getCmp("panelQueryCmp").collapse();
        },
        scope: me
      }]
    }];
  },

  /**
   * 采购订单主表
   */
  getMainGrid: function () {
    var me = this;
    if (me.__mainGrid) {
      return me.__mainGrid;
    }

    var modelName = "PSIPOBill";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "ref", "supplierName", "contact", "tel",
        "fax", "inputUserName", "bizUserName",
        "billStatus", "goodsMoney", "dateCreated",
        "paymentType", "tax", "moneyWithTax", "dealDate",
        "dealAddress", "orgName", "confirmUserName",
        "confirmDate", "billMemo", "genPWBill"]
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
        url: me.URL("Home/Purchase/pobillList"),
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
      columns: {
        defaults: {
          menuDisabled: true,
          sortable: false
        },
        items: [{
          xtype: "rownumberer",
          width: 50
        }, {
          header: "状态",
          dataIndex: "billStatus",
          width: 100,
          renderer: function (value) {
            if (value == 0) {
              return "<span style='color:red'>待审核</span>";
            } else if (value == 1000) {
              return "已审核";
            } else if (value == 2000) {
              return "<span style='color:green'>部分入库</span>";
            } else if (value == 3000) {
              return "全部入库";
            } else if (value == 4000) {
              return "关闭(未入库)";
            } else if (value == 4001) {
              return "关闭(部分入库)";
            } else if (value == 4002) {
              return "关闭(全部入库)";
            } else {
              return "";
            }
          }
        }, {
          header: "采购订单号",
          dataIndex: "ref",
          width: 110
        }, {
          header: "入库单?",
          dataIndex: "genPWBill",
          width: 70,
          align: "center",
          menuDisabled: true,
          sortable: false
        }, {
          header: "交货日期",
          dataIndex: "dealDate"
        }, {
          header: "交货地址",
          dataIndex: "dealAddress"
        }, {
          header: "供应商",
          dataIndex: "supplierName",
          width: 300
        }, {
          header: "供应商联系人",
          dataIndex: "contact"
        }, {
          header: "供应商电话",
          dataIndex: "tel"
        }, {
          header: "供应商传真",
          dataIndex: "fax"
        }, {
          header: "采购金额",
          dataIndex: "goodsMoney",
          align: "right",
          xtype: "numbercolumn",
          width: 150
        }, {
          header: "税金",
          dataIndex: "tax",
          align: "right",
          xtype: "numbercolumn",
          width: 150
        }, {
          header: "价税合计",
          dataIndex: "moneyWithTax",
          align: "right",
          xtype: "numbercolumn",
          width: 150
        }, {
          header: "付款方式",
          dataIndex: "paymentType",
          width: 100,
          renderer: function (value) {
            if (value == 0) {
              return "记应付账款";
            } else if (value == 1) {
              return "现金付款";
            } else if (value == 2) {
              return "预付款";
            } else {
              return "";
            }
          }
        }, {
          header: "业务员",
          dataIndex: "bizUserName"
        }, {
          header: "组织机构",
          dataIndex: "orgName"
        }, {
          header: "制单人",
          dataIndex: "inputUserName"
        }, {
          header: "制单时间",
          dataIndex: "dateCreated",
          width: 140
        }, {
          header: "审核人",
          dataIndex: "confirmUserName"
        }, {
          header: "审核时间",
          dataIndex: "confirmDate",
          width: 140
        }, {
          header: "备注",
          dataIndex: "billMemo"
        }]
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
            data: [["20"], ["50"], ["100"], ["300"],
            ["1000"]]
          }),
          value: 20,
          listeners: {
            change: {
              fn: function () {
                store.pageSize = Ext
                  .getCmp("comboCountPerPage")
                  .getValue();
                store.currentPage = 1;
                Ext.getCmp("pagingToobar").doRefresh();
              },
              scope: me
            }
          }
        }, {
          xtype: "displayfield",
          value: "条记录"
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
      }
    });

    return me.__mainGrid;
  },

  /**
   * 采购订单明细记录
   */
  getDetailGrid: function () {
    var me = this;
    if (me.__detailGrid) {
      return me.__detailGrid;
    }

    var modelName = "PSIPOBillDetail";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "goodsCode", "goodsName", "goodsSpec",
        "unitName", "goodsCount", "goodsMoney",
        "goodsPrice", "taxRate", "tax", "moneyWithTax",
        "pwCount", "leftCount", "memo", "goodsPriceWithTax",
        "rejCount", "realCount"]
    });
    var store = Ext.create("Ext.data.Store", {
      autoLoad: false,
      model: modelName,
      data: []
    });

    me.__detailGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      title: "采购订单明细",
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
          header: "",
          id: "columnActionChangeOrder",
          align: "center",
          menuDisabled: true,
          draggable: false,
          hidden: true,
          width: 40,
          xtype: "actioncolumn",
          items: [{
            icon: me
              .URL("Public/Images/icons/edit.png"),
            tooltip: "订单变更",
            handler: me.onChangeOrder,
            scope: me
          }]
        }, {
          header: "物料编码",
          dataIndex: "goodsCode",
          width: 120
        }, {
          header: "品名",
          dataIndex: "goodsName",
          width: 200
        }, {
          header: "规格型号",
          dataIndex: "goodsSpec",
          width: 200
        }, {
          header: "采购数量",
          dataIndex: "goodsCount",
          align: "right"
        }, {
          header: "入库数量",
          dataIndex: "pwCount",
          align: "right"
        }, {
          header: "退货数量",
          dataIndex: "rejCount",
          align: "right"
        }, {
          header: "实际入库数量",
          dataIndex: "realCount",
          align: "right"
        }, {
          header: "未入库数量",
          dataIndex: "leftCount",
          align: "right",
          renderer: function (value) {
            if (value > 0) {
              return "<span style='color:red'>" + value
                + "</span>";
            } else {
              return value;
            }
          }
        }, {
          header: "单位",
          dataIndex: "unitName",
          width: 60
        }, {
          header: "采购单价",
          dataIndex: "goodsPrice",
          align: "right",
          xtype: "numbercolumn",
          width: 150
        }, {
          header: "采购金额",
          dataIndex: "goodsMoney",
          align: "right",
          xtype: "numbercolumn",
          width: 150
        }, {
          header: "税率(%)",
          dataIndex: "taxRate",
          align: "right",
          xtype: "numbercolumn",
          format: "0"
        }, {
          header: "税金",
          dataIndex: "tax",
          align: "right",
          xtype: "numbercolumn",
          width: 150
        }, {
          header: "价税合计",
          dataIndex: "moneyWithTax",
          align: "right",
          xtype: "numbercolumn",
          width: 150
        }, {
          header: "含税价",
          dataIndex: "goodsPriceWithTax",
          align: "right",
          xtype: "numbercolumn",
          width: 100
        }, {
          header: "备注",
          dataIndex: "memo",
          width: 120
        }]
      },
      store: store
    });

    return me.__detailGrid;
  },

  onChangeOrder: function (grid, row) {
    var me = this;

    if (me.getPermission().confirm == "0") {
      me.showInfo("您没有订单变更的权限");
      return;
    }

    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("没有选择要变更的采购订单");
      return;
    }
    var bill = item[0];
    if (parseInt(bill.get("billStatus")) >= 4000) {
      me.showInfo("订单已经关闭，不能再做变更操作");
      return;
    }

    var entity = grid.getStore().getAt(row);
    if (!entity) {
      me.showInfo("请选择要变更的明细记录");
      return;
    }

    var form = Ext.create("PSI.PurchaseOrder.ChangeOrderEditForm", {
      entity: entity,
      parentForm: me
    });
    form.show();
  },

  /**
   * 刷新采购订单主表记录
   */
  refreshMainGrid: function (id) {
    var me = this;

    Ext.getCmp("buttonEdit").setDisabled(true);
    Ext.getCmp("buttonDelete").setDisabled(true);
    Ext.getCmp("buttonCommit").setDisabled(true);
    Ext.getCmp("buttonCancelConfirm").setDisabled(true);
    Ext.getCmp("buttonGenPWBill").setDisabled(true);

    var gridDetail = me.getDetailGrid();
    gridDetail.setTitle("采购订单明细");
    gridDetail.getStore().removeAll();

    Ext.getCmp("pagingToobar").doRefresh();
    me.__lastId = id;
  },

  /**
   * 新增采购订单
   */
  onAddBill: function () {
    var me = this;

    var form = Ext.create("PSI.PurchaseOrder.POEditForm", {
      parentForm: me,
      showAddGoodsButton: me.getPermission().showAddGoodsButton
    });
    form.show();
  },

  /**
   * 编辑采购订单
   */
  onEditBill: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("没有选择要编辑的采购订单");
      return;
    }
    var bill = item[0];

    var form = Ext.create("PSI.PurchaseOrder.POEditForm", {
      parentForm: me,
      entity: bill,
      showAddGoodsButton: me.getPermission().showAddGoodsButton
    });
    form.show();
  },

  /**
   * 删除采购订单
   */
  onDeleteBill: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要删除的采购订单");
      return;
    }

    var bill = item[0];

    if (bill.get("billStatus") > 0) {
      me.showInfo("当前采购订单已经审核，不能删除");
      return;
    }

    var store = me.getMainGrid().getStore();
    var index = store.findExact("id", bill.get("id"));
    index--;
    var preIndex = null;
    var preItem = store.getAt(index);
    if (preItem) {
      preIndex = preItem.get("id");
    }

    var info = "请确认是否删除采购订单: <span style='color:red'>" + bill.get("ref")
      + "</span>";
    var funcConfirm = function () {
      var el = Ext.getBody();
      el.mask("正在删除中...");
      var r = {
        url: me.URL("Home/Purchase/deletePOBill"),
        params: {
          id: bill.get("id")
        },
        callback: function (options, success, response) {
          el.unmask();

          if (success) {
            var data = me.decodeJSON(response.responseText);
            if (data.success) {
              me.showInfo("成功完成删除操作", function () {
                me.refreshMainGrid(preIndex);
              });
            } else {
              me.showInfo(data.msg);
            }
          } else {
            me.showInfo("网络错误");
          }
        }
      };
      me.ajax(r);
    };

    me.confirm(info, funcConfirm);
  },

  onMainGridSelect: function () {
    var me = this;
    me.getDetailGrid().setTitle("采购订单明细");
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      Ext.getCmp("buttonEdit").setDisabled(true);
      Ext.getCmp("buttonDelete").setDisabled(true);
      Ext.getCmp("buttonCommit").setDisabled(true);
      Ext.getCmp("buttonCancelConfirm").setDisabled(true);
      Ext.getCmp("buttonGenPWBill").setDisabled(true);

      return;
    }
    var bill = item[0];
    var commited = bill.get("billStatus") >= 1000;

    var buttonEdit = Ext.getCmp("buttonEdit");
    buttonEdit.setDisabled(false);
    if (commited) {
      buttonEdit.setText("查看采购订单");
      Ext.getCmp("columnActionChangeOrder").show();
    } else {
      buttonEdit.setText("编辑采购订单");
      Ext.getCmp("columnActionChangeOrder").hide();
    }
    if (me.getPermission().confirm == "0") {
      // 没有审核权限就不能做订单变更
      Ext.getCmp("columnActionChangeOrder").hide();
    }

    Ext.getCmp("buttonDelete").setDisabled(commited);
    Ext.getCmp("buttonCommit").setDisabled(commited);
    Ext.getCmp("buttonCancelConfirm").setDisabled(!commited);
    Ext.getCmp("buttonGenPWBill").setDisabled(!commited);

    me.refreshDetailGrid();
    me.refreshPWGrid();
  },

  /**
   * 刷新采购订单明细记录
   */
  refreshDetailGrid: function (id) {
    var me = this;
    me.getDetailGrid().setTitle("采购订单明细");
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      return;
    }
    var bill = item[0];

    var grid = me.getDetailGrid();
    grid.setTitle("单号: " + bill.get("ref") + " 供应商: "
      + bill.get("supplierName"));
    var el = grid.getEl();
    el.mask(PSI.Const.LOADING);

    var r = {
      url: me.URL("Home/Purchase/poBillDetailList"),
      params: {
        id: bill.get("id")
      },
      callback: function (options, success, response) {
        var store = grid.getStore();

        store.removeAll();

        if (success) {
          var data = me.decodeJSON(response.responseText);
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
    };
    me.ajax(r);
  },

  /**
   * 审核采购订单
   */
  onCommit: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("没有选择要审核的采购订单");
      return;
    }
    var bill = item[0];

    if (bill.get("billStatus") > 0) {
      me.showInfo("当前采购订单已经审核，不能再次审核");
      return;
    }

    var detailCount = me.getDetailGrid().getStore().getCount();
    if (detailCount == 0) {
      me.showInfo("当前采购订单没有录入物料明细，不能审核");
      return;
    }

    var info = "请确认是否审核单号: <span style='color:red'>" + bill.get("ref")
      + "</span> 的采购订单?";
    var id = bill.get("id");

    var funcConfirm = function () {
      var el = Ext.getBody();
      el.mask("正在提交中...");
      var r = {
        url: me.URL("Home/Purchase/commitPOBill"),
        params: {
          id: id
        },
        callback: function (options, success, response) {
          el.unmask();

          if (success) {
            var data = me.decodeJSON(response.responseText);
            if (data.success) {
              me.showInfo("成功完成审核操作", function () {
                me.refreshMainGrid(id);
              });
            } else {
              me.showInfo(data.msg);
            }
          } else {
            me.showInfo("网络错误");
          }
        }
      };
      me.ajax(r);
    };
    me.confirm(info, funcConfirm);
  },

  /**
   * 取消审核
   */
  onCancelConfirm: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("没有选择要取消审核的采购订单");
      return;
    }
    var bill = item[0];

    if (bill.get("billStatus") == 0) {
      me.showInfo("当前采购订单还没有审核，无法取消审核");
      return;
    }

    var info = "请确认是否取消审核单号为 <span style='color:red'>" + bill.get("ref")
      + "</span> 的采购订单?";
    var id = bill.get("id");
    var funcConfirm = function () {
      var el = Ext.getBody();
      el.mask("正在提交中...");
      var r = {
        url: me.URL("Home/Purchase/cancelConfirmPOBill"),
        params: {
          id: id
        },
        callback: function (options, success, response) {
          el.unmask();

          if (success) {
            var data = me.decodeJSON(response.responseText);
            if (data.success) {
              me.showInfo("成功完成取消审核操作", function () {
                me.refreshMainGrid(id);
              });
            } else {
              me.showInfo(data.msg);
            }
          } else {
            me.showInfo("网络错误");
          }
        }
      };
      me.ajax(r);
    };
    me.confirm(info, funcConfirm);
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

  /**
   * 查询
   */
  onQuery: function () {
    var me = this;

    me.getMainGrid().getStore().currentPage = 1;
    me.refreshMainGrid();
  },

  /**
   * 清除查询条件
   */
  onClearQuery: function () {
    var me = this;

    Ext.getCmp("editQueryBillStatus").setValue(-1);
    Ext.getCmp("editQueryRef").setValue(null);
    Ext.getCmp("editQueryFromDT").setValue(null);
    Ext.getCmp("editQueryToDT").setValue(null);
    Ext.getCmp("editQuerySupplier").clearIdValue();
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

    var supplierId = Ext.getCmp("editQuerySupplier").getIdValue();
    if (supplierId) {
      result.supplierId = supplierId;
    }

    var fromDT = Ext.getCmp("editQueryFromDT").getValue();
    if (fromDT) {
      result.fromDT = Ext.Date.format(fromDT, "Y-m-d");
    }

    var toDT = Ext.getCmp("editQueryToDT").getValue();
    if (toDT) {
      result.toDT = Ext.Date.format(toDT, "Y-m-d");
    }

    var paymentType = Ext.getCmp("editQueryPaymentType").getValue();
    result.paymentType = paymentType;

    var goodsId = Ext.getCmp("editQueryGoods").getIdValue();
    if (goodsId) {
      result.goodsId = goodsId;
    }

    return result;
  },

  /**
   * 生成采购入库单
   */
  onGenPWBill: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("没有选择要生成入库单的采购订单");
      return;
    }
    var bill = item[0];

    if (bill.get("billStatus") < 1000) {
      me.showInfo("当前采购订单还没有审核，无法生成采购入库单");
      return;
    }

    if (bill.get("billStatus") >= 4000) {
      me.showInfo("当前采购订单已经关闭，不能再生成采购入库单");
      return;
    }

    var form = Ext.create("PSI.Purchase.PWEditForm", {
      genBill: true,
      pobillRef: bill.get("ref")
    });
    form.show();
  },

  onPDF: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("没有选择要生成pdf文件的采购订单");
      return;
    }
    var bill = item[0];

    var url = me.URL("Home/Purchase/poBillPdf?ref=" + bill.get("ref"));
    window.open(url);
  },

  onExcel: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("没有选择要生成Excel文件的采购订单");
      return;
    }
    var bill = item[0];

    var url = me.URL("Home/Purchase/poBillExcel?ref=" + bill.get("ref"));
    window.open(url);
  },

  getPWGrid: function () {
    var me = this;
    if (me.__pwGrid) {
      return me.__pwGrid;
    }
    var modelName = "PSIPOBill_PWBill";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "ref", "bizDate", "supplierName",
        "warehouseName", "inputUserName", "bizUserName",
        "billStatus", "amount", "dateCreated",
        "paymentType"]
    });
    var store = Ext.create("Ext.data.Store", {
      autoLoad: false,
      model: modelName,
      data: []
    });

    me.__pwGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      title: "采购订单入库详情",
      viewConfig: {
        enableTextSelection: true
      },
      columnLines: true,
      columns: {
        defaults: {
          menuDisabled: true,
          sortable: false
        },
        items: [{
          xtype: "rownumberer",
          width: 50
        }, {
          header: "状态",
          dataIndex: "billStatus",
          width: 80,
          renderer: function (value) {
            if (value == "待入库") {
              return "<span style='color:red'>" + value
                + "</span>";
            } else if (value == "部分退货") {
              return "<span style='color:blue'>" + value
                + "</span>";
            } else if (value == "全部退货") {
              return "<span style='color:green'>" + value
                + "</span>";
            } else {
              return value;
            }
          }
        }, {
          header: "入库单号",
          dataIndex: "ref",
          width: 110,
          renderer: function (value, md, record) {
            return "<a href='"
              + PSI.Const.BASE_URL
              + "Home/Bill/viewIndex?fid=2027&refType=采购入库&ref="
              + encodeURIComponent(record.get("ref"))
              + "' target='_blank'>" + value + "</a>";
          }
        }, {
          header: "业务日期",
          dataIndex: "bizDate"
        }, {
          header: "供应商",
          dataIndex: "supplierName",
          width: 300
        }, {
          header: "采购金额",
          dataIndex: "amount",
          align: "right",
          xtype: "numbercolumn",
          width: 150
        }, {
          header: "付款方式",
          dataIndex: "paymentType",
          width: 100,
          renderer: function (value) {
            if (value == 0) {
              return "记应付账款";
            } else if (value == 1) {
              return "现金付款";
            } else if (value == 2) {
              return "预付款";
            } else {
              return "";
            }
          }
        }, {
          header: "入库仓库",
          dataIndex: "warehouseName"
        }, {
          header: "业务员",
          dataIndex: "bizUserName"
        }, {
          header: "制单人",
          dataIndex: "inputUserName"
        }, {
          header: "制单时间",
          dataIndex: "dateCreated",
          width: 140
        }]
      },
      store: store
    });

    return me.__pwGrid;
  },

  refreshPWGrid: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      return;
    }
    var bill = item[0];

    var grid = me.getPWGrid();
    var el = grid.getEl();
    if (el) {
      el.mask(PSI.Const.LOADING);
    }

    var r = {
      url: me.URL("Home/Purchase/poBillPWBillList"),
      params: {
        id: bill.get("id")
      },
      callback: function (options, success, response) {
        var store = grid.getStore();

        store.removeAll();

        if (success) {
          var data = me.decodeJSON(response.responseText);
          store.add(data);
        }

        if (el) {
          el.unmask();
        }
      }
    };
    me.ajax(r);
  },

  onClosePO: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("没有选择要关闭的采购订单");
      return;
    }
    var bill = item[0];

    // if (bill.get("billStatus") > 0) {
    // me.showInfo("当前采购订单已经审核，不能再次审核");
    // return;
    // }

    var info = "请确认是否关闭单号: <span style='color:red'>" + bill.get("ref")
      + "</span> 的采购订单?";
    var id = bill.get("id");

    var funcConfirm = function () {
      var el = Ext.getBody();
      el.mask("正在提交中...");
      var r = {
        url: me.URL("Home/Purchase/closePOBill"),
        params: {
          id: id
        },
        callback: function (options, success, response) {
          el.unmask();

          if (success) {
            var data = me.decodeJSON(response.responseText);
            if (data.success) {
              me.showInfo("成功关闭采购订单", function () {
                me.refreshMainGrid(id);
              });
            } else {
              me.showInfo(data.msg);
            }
          } else {
            me.showInfo("网络错误");
          }
        }
      };
      me.ajax(r);
    };
    me.confirm(info, funcConfirm);
  },

  onCancelClosedPO: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("没有选择要取消关闭状态的采购订单");
      return;
    }
    var bill = item[0];

    // if (bill.get("billStatus") > 0) {
    // me.showInfo("当前采购订单已经审核，不能再次审核");
    // return;
    // }

    var info = "请确认是否取消单号: <span style='color:red'>" + bill.get("ref")
      + "</span> 采购订单的关闭状态?";
    var id = bill.get("id");

    var funcConfirm = function () {
      var el = Ext.getBody();
      el.mask("正在提交中...");
      var r = {
        url: me.URL("Home/Purchase/cancelClosedPOBill"),
        params: {
          id: id
        },
        callback: function (options, success, response) {
          el.unmask();

          if (success) {
            var data = me.decodeJSON(response.responseText);
            if (data.success) {
              me.showInfo("成功取消采购订单关闭状态", function () {
                me.refreshMainGrid(id);
              });
            } else {
              me.showInfo(data.msg);
            }
          } else {
            me.showInfo("网络错误");
          }
        }
      };
      me.ajax(r);
    };
    me.confirm(info, funcConfirm);
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
      me.showInfo("没有选择要打印的采购订单");
      return;
    }
    var bill = item[0];

    var el = Ext.getBody();
    el.mask("数据加载中...");
    var r = {
      url: PSI.Const.BASE_URL + "Home/Purchase/genPOBillPrintPage",
      params: {
        id: bill.get("id")
      },
      callback: function (options, success, response) {
        el.unmask();

        if (success) {
          var data = response.responseText;
          me.previewPOBill(bill.get("ref"), data);
        }
      }
    };
    me.ajax(r);
  },

  PRINT_PAGE_WIDTH: "200mm",
  PRINT_PAGE_HEIGHT: "95mm",

  previewPOBill: function (ref, data) {
    var me = this;

    var lodop = getLodop();
    if (!lodop) {
      PSI.MsgBox.showInfo("Lodop打印控件没有正确安装");
      return;
    }

    lodop.PRINT_INIT("采购订单" + ref);
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
      me.showInfo("没有选择要打印的采购订单");
      return;
    }
    var bill = item[0];

    var el = Ext.getBody();
    el.mask("数据加载中...");
    var r = {
      url: PSI.Const.BASE_URL + "Home/Purchase/genPOBillPrintPage",
      params: {
        id: bill.get("id")
      },
      callback: function (options, success, response) {
        el.unmask();

        if (success) {
          var data = response.responseText;
          me.printPOBill(bill.get("ref"), data);
        }
      }
    };
    me.ajax(r);
  },

  printPOBill: function (ref, data) {
    var me = this;

    var lodop = getLodop();
    if (!lodop) {
      PSI.MsgBox.showInfo("Lodop打印控件没有正确安装");
      return;
    }

    lodop.PRINT_INIT("采购订单" + ref);
    lodop.SET_PRINT_PAGESIZE(1, me.PRINT_PAGE_WIDTH, me.PRINT_PAGE_HEIGHT,
      "");
    lodop.ADD_PRINT_HTM("0mm", "0mm", "100%", "100%", data);
    var result = lodop.PRINT();
  },

  // 订单变更后刷新Grid
  refreshAterChangeOrder: function (detailId) {
    var me = this;

    me.refreshDetailGrid(detailId);

    // 刷新主表中金额相关字段
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      return;
    }
    var bill = item[0];

    var r = {
      url: me.URL("Home/Purchase/getPOBillDataAterChangeOrder"),
      params: {
        id: bill.get("id")
      },
      callback: function (options, success, response) {

        if (success) {
          var data = me.decodeJSON(response.responseText);
          if (data.goodsMoney) {
            bill.set("goodsMoney", data.goodsMoney);
            bill.set("tax", data.tax);
            bill.set("moneyWithTax", data.moneyWithTax);
            me.getMainGrid().getStore().commitChanges();
          }
        }
      }
    };
    me.ajax(r);
  }
});
