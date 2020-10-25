//
// 销售合同 - 主界面
//
Ext.define("PSI.SaleContract.SCMainForm", {
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
        height: 65,
        header: false,
        collapsible: true,
        collapseMode: "mini",
        border: 0,
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
          border: 0,
          xtype: "tabpanel",
          items: [me.getDetailGrid(),
          me.getClausePanel()]
        }]
      }]
    });

    me.callParent(arguments);

    me.editQualityClause = Ext
      .getCmp("PSI_SaleContract_SCMainForm_editQulityClause");
    me.editInsuranceClause = Ext
      .getCmp("PSI_SaleContract_SCMainForm_editInsuranceClause");
    me.editTransportClause = Ext
      .getCmp("PSI_SaleContract_SCMainForm_editTrasportClause");
    me.editOtherClause = Ext
      .getCmp("PSI_SaleContract_SCMainForm_editOtherClause");

    me.refreshMainGrid();
  },

	/**
	 * 工具栏
	 */
  getToolbarCmp: function () {
    var me = this;
    return [{
      text: "新建销售合同",
      id: "buttonAdd",
      hidden: me.getPermission().add == "0",
      scope: me,
      handler: me.onAddBill
    }, {
      hidden: me.getPermission().add == "0",
      xtype: "tbseparator"
    }, {
      text: "编辑销售合同",
      scope: me,
      hidden: me.getPermission().edit == "0",
      handler: me.onEditBill,
      id: "buttonEdit"
    }, {
      hidden: me.getPermission().edit == "0",
      xtype: "tbseparator"
    }, {
      text: "删除销售合同",
      scope: me,
      hidden: me.getPermission().del == "0",
      handler: me.onDeleteBill,
      id: "buttonDelete"
    }, {
      hidden: me.getPermission().del == "0",
      xtype: "tbseparator",
      id: "tbseparator1"
    }, {
      text: "审核",
      hidden: me.getPermission().commit == "0",
      scope: me,
      handler: me.onCommit,
      id: "buttonCommit"
    }, {
      text: "取消审核",
      hidden: me.getPermission().commit == "0",
      scope: me,
      handler: me.onCancelConfirm,
      id: "buttonCancelConfirm"
    }, {
      hidden: me.getPermission().commit == "0",
      xtype: "tbseparator",
      id: "tbseparator2"
    }, {
      text: "生成销售订单",
      hidden: me.getPermission().genSOBill == "0",
      scope: me,
      handler: me.onGenSOBill
    }, {
      hidden: me.getPermission().genSOBill == "0",
      xtype: "tbseparator"
    }, {
      hidden: me.getPermission().genPDF == "0",
      text: "导出",
      menu: [{
        text: "单据生成PDF",
        iconCls: "PSI-button-pdf",
        id: "buttonPDF",
        scope: me,
        handler: me.onPDF
      }, "-", {
        text: "单据生成Word",
        id: "buttonWord",
        iconCls: "PSI-button-word",
        scope: me,
        handler: me.onWord
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
        window.open(me.URL("Home/Help/index?t=scbill"));
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
        data: [[-1, "全部"], [0, "待审核"], [1000, "已审核"]]
      }),
      value: -1
    }, {
      id: "editQueryRef",
      labelWidth: 60,
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "合同号",
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
      id: "editQueryCustomer",
      xtype: "psi_customerfield",
      showModal: true,
      labelAlign: "right",
      labelSeparator: "",
      labelWidth: 60,
      margin: "5, 0, 0, 0",
      fieldLabel: "客户"
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

	/**
	 * 销售订单主表
	 */
  getMainGrid: function () {
    var me = this;
    if (me.__mainGrid) {
      return me.__mainGrid;
    }

    var modelName = "PSISOBill";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "ref", "customerName", "inputUserName",
        "bizUserName", "bizDT", "billStatus", "goodsMoney",
        "dateCreated", "tax", "moneyWithTax", "beginDT",
        "endDT", "dealDate", "dealAddress", "discount",
        "orgName", "confirmUserName", "confirmDate",
        "billMemo"]
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
        url: me.URL("Home/SaleContract/scbillList"),
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
          width: 80,
          renderer: function (value) {
            if (value == 0) {
              return "<span style='color:red'>待审核</span>";
            } else if (value == 1000) {
              return "已审核";
            } else {
              return "";
            }
          }
        }, {
          header: "销售合同号",
          dataIndex: "ref",
          width: 110
        }, {
          header: "客户(甲方)",
          dataIndex: "customerName",
          width: 300
        }, {
          header: "销售组织机构(乙方)",
          dataIndex: "orgName",
          width: 200
        }, {
          header: "合同开始日期",
          dataIndex: "beginDT"
        }, {
          header: "合同结束日期",
          dataIndex: "endDT"
        }, {
          header: "销售金额",
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
          header: "交货日期",
          dataIndex: "dealDate"
        }, {
          header: "交货地点",
          dataIndex: "dealAddress"
        }, {
          header: "折扣率(%)",
          dataIndex: "discount",
          align: "right"
        }, {
          header: "业务员",
          dataIndex: "bizUserName"
        }, {
          header: "合同签订日期",
          dataIndex: "bizDT"
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
          dataIndex: "billMemo",
          width: 200
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
          fn: me.getPermission().genPDF == "0"
            ? Ext.emptyFn
            : me.onEditBill,
          scope: me
        }
      }
    });

    return me.__mainGrid;
  },

  getClausePanel: function () {
    var me = this;
    if (me.__clausePanel) {
      return me.__clausePanel;
    }

    me.__clausePanel = Ext.create("Ext.panel.Panel", {
      title: "合同条款",
      autoScroll: true,
      border: 0,
      layout: "form",
      bodyPadding: 5,
      defaults: {
        readOnly: true,
        hideLabel: true,
        labelSeparator: "",
        rows: 3
      },
      cls: "PSI-SCBill",
      items: [{
        xtype: "displayfield",
        value: "品质条款"
      }, {
        xtype: "textareafield",
        id: "PSI_SaleContract_SCMainForm_editQulityClause"
      }, {
        xtype: "displayfield",
        value: "保险条款"
      }, {
        xtype: "textareafield",
        id: "PSI_SaleContract_SCMainForm_editInsuranceClause"
      }, {
        xtype: "displayfield",
        value: "运输条款"
      }, {
        xtype: "textareafield",
        id: "PSI_SaleContract_SCMainForm_editTrasportClause"
      }, {
        xtype: "displayfield",
        value: "其他条款"
      }, {
        xtype: "textareafield",
        id: "PSI_SaleContract_SCMainForm_editOtherClause"
      }]
    });

    return me.__clausePanel;
  },

	/**
	 * 销售订单明细记录
	 */
  getDetailGrid: function () {
    var me = this;
    if (me.__detailGrid) {
      return me.__detailGrid;
    }

    var modelName = "PSISOBillDetail";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "goodsCode", "goodsName", "goodsSpec",
        "unitName", "goodsCount", "goodsMoney",
        "goodsPrice", "taxRate", "tax", "moneyWithTax",
        "soCount", "leftCount", "memo", "goodsPriceWithTax"]
    });
    var store = Ext.create("Ext.data.Store", {
      autoLoad: false,
      model: modelName,
      data: []
    });

    me.__detailGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      title: "销售订单明细",
      viewConfig: {
        enableTextSelection: true
      },
      border: 0,
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
        header: "合同销售数量",
        dataIndex: "goodsCount",
        menuDisabled: true,
        sortable: false,
        align: "right",
        width: 120
      }, {
        header: "销售订单已执行数量",
        dataIndex: "soCount",
        menuDisabled: true,
        sortable: false,
        align: "right",
        width: 140
      }, {
        header: "合同未执行数量",
        dataIndex: "leftCount",
        menuDisabled: true,
        sortable: false,
        align: "right",
        renderer: function (value) {
          if (value > 0) {
            return "<span style='color:red'>"
              + value + "</span>";
          } else {
            return value;
          }
        },
        width: 120
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
        width: 150
      }, {
        header: "销售金额",
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
        xtype: "numbercolumn",
        format: "0",
        align: "right"
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
        header: "备注",
        dataIndex: "memo",
        menuDisabled: true,
        sortable: false,
        width: 120
      }],
      store: store
    });

    return me.__detailGrid;
  },

  clearClauseEditor: function () {
    var me = this;
    me.editQualityClause.setValue(null);
    me.editInsuranceClause.setValue(null);
    me.editTransportClause.setValue(null);
    me.editOtherClause.setValue(null);
  },

	/**
	 * 刷新销售订单主表记录
	 */
  refreshMainGrid: function (id) {
    var me = this;

    Ext.getCmp("buttonEdit").setDisabled(true);
    Ext.getCmp("buttonDelete").setDisabled(true);
    Ext.getCmp("buttonCommit").setDisabled(true);
    Ext.getCmp("buttonCancelConfirm").setDisabled(true);

    me.clearClauseEditor();

    var gridDetail = me.getDetailGrid();
    gridDetail.setTitle("销售合同明细");
    gridDetail.getStore().removeAll();

    Ext.getCmp("pagingToobar").doRefresh();
    me.__lastId = id;
  },

  onAddBill: function () {
    var me = this;

    var form = Ext.create("PSI.SaleContract.SCEditForm", {
      parentForm: me,
      showAddGoodsButton: me.getPermission().showAddGoodsButton
    });
    form.show();
  },

  onEditBill: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("没有选择要编辑的销售合同");
      return;
    }
    var bill = item[0];

    var form = Ext.create("PSI.SaleContract.SCEditForm", {
      parentForm: me,
      showAddGoodsButton: me.getPermission().showAddGoodsButton,
      entity: bill
    });
    form.show();
  },

  onDeleteBill: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要删除的销售合同");
      return;
    }

    var bill = item[0];

    if (bill.get("billStatus") > 0) {
      me.showInfo("当前销售合同已经审核，不能删除");
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

    var info = "请确认是否删除销售合同: <span style='color:red'>" + bill.get("ref")
      + "</span>";
    var funcConfirm = function () {
      var el = Ext.getBody();
      el.mask("正在删除中...");
      var r = {
        url: me.URL("Home/SaleContract/deleteSCBill"),
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
    me.getDetailGrid().setTitle("销售合同明细");
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      Ext.getCmp("buttonEdit").setDisabled(true);
      Ext.getCmp("buttonDelete").setDisabled(true);
      Ext.getCmp("buttonCommit").setDisabled(true);
      Ext.getCmp("buttonCancelConfirm").setDisabled(true);

      return;
    }
    var bill = item[0];
    var commited = bill.get("billStatus") >= 1000;

    var buttonEdit = Ext.getCmp("buttonEdit");
    buttonEdit.setDisabled(false);
    if (commited) {
      buttonEdit.setText("查看销售合同");
    } else {
      buttonEdit.setText("编辑销售合同");
    }

    Ext.getCmp("buttonDelete").setDisabled(commited);
    Ext.getCmp("buttonCommit").setDisabled(commited);
    Ext.getCmp("buttonCancelConfirm").setDisabled(!commited);

    me.refreshDetailGrid();
  },

  refreshDetailGrid: function (id) {
    var me = this;
    me.getDetailGrid().setTitle("销售合同明细");
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      return;
    }
    var bill = item[0];

    var grid = me.getDetailGrid();
    grid.setTitle("合同号: " + bill.get("ref") + " 客户: "
      + bill.get("customerName"));
    var el = grid.getEl();
    el.mask(PSI.Const.LOADING);

    var r = {
      url: me.URL("Home/SaleContract/scBillDetailList"),
      params: {
        id: bill.get("id")
      },
      callback: function (options, success, response) {
        var store = grid.getStore();

        store.removeAll();

        if (success) {
          var data = me.decodeJSON(response.responseText);
          store.add(data.items);
          me.editQualityClause.setValue(data.qualityClause);
          me.editInsuranceClause.setValue(data.insuranceClause);
          me.editTransportClause.setValue(data.transportClause);
          me.editOtherClause.setValue(data.otherClause);

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

  // 审核
  onCommit: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("没有选择要审核的销售合同");
      return;
    }
    var bill = item[0];

    if (bill.get("billStatus") > 0) {
      me.showInfo("当前销售合同已经审核，不能再次审核");
      return;
    }

    var detailCount = me.getDetailGrid().getStore().getCount();
    if (detailCount == 0) {
      me.showInfo("当前销售合同没有录入商品明细，不能审核");
      return;
    }

    var info = "请确认是否审核编号: <span style='color:red'>" + bill.get("ref")
      + "</span> 的销售合同?";
    var id = bill.get("id");

    var funcConfirm = function () {
      var el = Ext.getBody();
      el.mask("正在操作中...");
      var r = {
        url: me.URL("Home/SaleContract/commitSCBill"),
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

  // 取消审核
  onCancelConfirm: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("没有选择要取消审核的销售合同");
      return;
    }
    var bill = item[0];

    if (bill.get("billStatus") == 0) {
      me.showInfo("当前销售合同还没有审核，不需要取消审核");
      return;
    }

    var info = "请确认是否取消审核编号: <span style='color:red'>" + bill.get("ref")
      + "</span> 的销售合同?";
    var id = bill.get("id");

    var funcConfirm = function () {
      var el = Ext.getBody();
      el.mask("正在操作中...");
      var r = {
        url: me.URL("Home/SaleContract/cancelConfirmSCBill"),
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

    var fromDT = Ext.getCmp("editQueryFromDT").getValue();
    if (fromDT) {
      result.fromDT = Ext.Date.format(fromDT, "Y-m-d");
    }

    var toDT = Ext.getCmp("editQueryToDT").getValue();
    if (toDT) {
      result.toDT = Ext.Date.format(toDT, "Y-m-d");
    }

    var goodsId = Ext.getCmp("editQueryGoods").getIdValue();
    if (goodsId) {
      result.goodsId = goodsId;
    }

    return result;
  },

  onGenSOBill: function () {
    var me = this;

    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("没有选择要生成销售订单的销售合同");
      return;
    }
    var bill = item[0];

    if (bill.get("billStatus") < 1000) {
      me.showInfo("当前销售合同还没有审核，无法生成销售订单");
      return;
    }

    var form = Ext.create("PSI.SaleOrder.SOEditForm", {
      genBill: true,
      scbillRef: bill.get("ref")
    });
    form.show();
  },

  onPDF: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("没有选择要生成pdf文件的销售合同");
      return;
    }
    var bill = item[0];

    var url = me.URL("Home/SaleContract/scBillPdf?ref=" + bill.get("ref"));
    window.open(url);
  },

  onWord: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("没有选择要生成Word文件的销售合同");
      return;
    }
    var bill = item[0];

    var url = me.URL("Home/SaleContract/scBillWord?ref=" + bill.get("ref"));
    window.open(url);
  },

  onPrintPreview: function () {
    var lodop = getLodop();
    if (!lodop) {
      PSI.MsgBox.showInfo("没有安装Lodop控件，无法打印");
      return;
    }

    var me = this;

    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("没有选择要打印的销售合同");
      return;
    }
    var bill = item[0];

    var el = Ext.getBody();
    el.mask("数据加载中...");
    var r = {
      url: me.URL("Home/SaleContract/genSCBillPrintPage"),
      params: {
        id: bill.get("id")
      },
      callback: function (options, success, response) {
        el.unmask();

        if (success) {
          var data = response.responseText;
          me.previewSCBill(bill.get("ref"), data);
        } else {
          me.showInfo("网络错误");
        }
      }
    };
    me.ajax(r);
  },

  PRINT_PAGE_WIDTH: "200mm",
  PRINT_PAGE_HEIGHT: "95mm",

  previewSCBill: function (ref, data) {
    var me = this;

    var lodop = getLodop();
    if (!lodop) {
      PSI.MsgBox.showInfo("Lodop打印控件没有正确安装");
      return;
    }

    lodop.PRINT_INIT("销售合同" + ref);
    lodop.SET_PRINT_PAGESIZE(1, me.PRINT_PAGE_WIDTH, me.PRINT_PAGE_HEIGHT,
      "");
    lodop.ADD_PRINT_HTM("0mm", "0mm", "100%", "100%", data);
    var result = lodop.PREVIEW("_blank");
  },

  onPrint: function () {
    var lodop = getLodop();
    if (!lodop) {
      PSI.MsgBox.showInfo("没有安装Lodop控件，无法打印");
      return;
    }

    var me = this;

    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("没有选择要打印的销售合同");
      return;
    }
    var bill = item[0];

    var el = Ext.getBody();
    el.mask("数据加载中...");
    var r = {
      url: PSI.Const.BASE_URL + "Home/SaleContract/genSCBillPrintPage",
      params: {
        id: bill.get("id")
      },
      callback: function (options, success, response) {
        el.unmask();

        if (success) {
          var data = response.responseText;
          me.printSCBill(bill.get("ref"), data);
        }
      }
    };
    me.ajax(r);
  },

  printSCBill: function (ref, data) {
    var me = this;

    var lodop = getLodop();
    if (!lodop) {
      PSI.MsgBox.showInfo("Lodop打印控件没有正确安装");
      return;
    }

    lodop.PRINT_INIT("销售合同" + ref);
    lodop.SET_PRINT_PAGESIZE(1, me.PRINT_PAGE_WIDTH, me.PRINT_PAGE_HEIGHT,
      "");
    lodop.ADD_PRINT_HTM("0mm", "0mm", "100%", "100%", data);
    var result = lodop.PRINT();
  }
});
