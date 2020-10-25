//
// 成品委托生产订单 - 主界面
//
Ext.define("PSI.DMO.DMOMainForm", {
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
          items: [me.getDetailGrid(),
          me.getDMWGrid()]
        }]
      }]
    });

    me.callParent();

    me.refreshMainGrid();
  },

  getToolbarCmp: function () {
    var me = this;
    return [{
      text: "新建成品委托生产订单",
      scope: me,
      handler: me.onAddBill,
      hidden: me.getPermission().add == "0",
      id: "buttonAdd"
    }, {
      hidden: me.getPermission().add == "0",
      xtype: "tbseparator"
    }, {
      text: "编辑成品委托生产订单",
      scope: me,
      handler: me.onEditBill,
      hidden: me.getPermission().edit == "0",
      id: "buttonEdit"
    }, {
      hidden: me.getPermission().edit == "0",
      xtype: "tbseparator"
    }, {
      text: "删除成品委托生产订单",
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
      hidden: me.getPermission().commit == "0",
      id: "buttonCommit"
    }, {
      text: "取消审核",
      scope: me,
      handler: me.onCancelConfirm,
      hidden: me.getPermission().commit == "0",
      id: "buttonCancelConfirm"
    }, {
      xtype: "tbseparator",
      hidden: me.getPermission().commit == "0",
      id: "tbseparator2"
    }, {
      text: "生成成品委托生产入库单",
      scope: me,
      handler: me.onGenDMWBill,
      hidden: me.getPermission().genDMWBill == "0",
      id: "buttonGenDMWBill"
    }, {
      hidden: me.getPermission().genDMWBill == "0",
      xtype: "tbseparator"
    }, {
      text: "关闭订单",
      hidden: me.getPermission().closeBill == "0",
      id: "buttonCloseBill",
      menu: [{
        text: "关闭成品委托生产订单",
        iconCls: "PSI-button-commit",
        scope: me,
        handler: me.onCloseDMO
      }, "-", {
        text: "取消成品委托生产订单关闭状态",
        iconCls: "PSI-button-cancelconfirm",
        scope: me,
        handler: me.onCancelClosedDMO
      }]
    }, {
      hidden: me.getPermission().closeBill == "0",
      xtype: "tbseparator"
    }, {
      text: "导出",
      hidden: me.getPermission().genPDF == "0",
      menu: [{
        text: "单据生成pdf",
        id: "buttonPDF",
        iconCls: "PSI-button-pdf",
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
        window.open(me.URL("Home/Help/index?t=dmobill"));
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
      id: "editQueryFactory",
      xtype: "psi_factoryfield",
      parentCmp: me,
      showModal: true,
      labelAlign: "right",
      labelSeparator: "",
      labelWidth: 60,
      margin: "5, 0, 0, 0",
      fieldLabel: "工厂"
    }, {
      id: "editQueryGoods",
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "物料",
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

  getMainGrid: function () {
    var me = this;
    if (me.__mainGrid) {
      return me.__mainGrid;
    }

    var modelName = "PSIDMOBill";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "ref", "factoryName", "contact", "tel",
        "fax", "inputUserName", "bizUserName",
        "billStatus", "goodsMoney", "dateCreated",
        "paymentType", "tax", "moneyWithTax", "dealDate",
        "dealAddress", "orgName", "confirmUserName",
        "confirmDate", "billMemo"]
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
        url: me.URL("Home/DM/dmobillList"),
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
          header: "成品委托生产订单号",
          dataIndex: "ref",
          width: 150
        }, {
          header: "交货日期",
          dataIndex: "dealDate"
        }, {
          header: "交货地址",
          dataIndex: "dealAddress"
        }, {
          header: "工厂",
          dataIndex: "factoryName",
          width: 300
        }, {
          header: "工厂联系人",
          dataIndex: "contact"
        }, {
          header: "工厂电话",
          dataIndex: "tel"
        }, {
          header: "工厂传真",
          dataIndex: "fax"
        }, {
          header: "金额",
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

  getDetailGrid: function () {
    var me = this;
    if (me.__detailGrid) {
      return me.__detailGrid;
    }

    var modelName = "PSIDMOBillDetail";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "goodsCode", "goodsName", "goodsSpec",
        "unitName", "goodsCount", "goodsMoney",
        "goodsPrice", "taxRate", "tax", "moneyWithTax",
        "dmwCount", "leftCount", "memo",
        "goodsPriceWithTax"]
    });
    var store = Ext.create("Ext.data.Store", {
      autoLoad: false,
      model: modelName,
      data: []
    });

    me.__detailGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      title: "生产订单明细",
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
          header: "生产数量",
          dataIndex: "goodsCount",
          align: "right"
        }, {
          header: "入库数量",
          dataIndex: "dmwCount",
          align: "right"
        }, {
          header: "未入库数量",
          dataIndex: "leftCount",
          align: "right",
          renderer: function (value) {
            if (value > 0) {
              return "<span style='color:red'>"
                + value + "</span>";
            } else {
              return value;
            }
          }
        }, {
          header: "单位",
          dataIndex: "unitName",
          width: 60
        }, {
          header: "加工单价",
          dataIndex: "goodsPrice",
          align: "right",
          xtype: "numbercolumn",
          width: 150
        }, {
          header: "金额",
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
          width: 150
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

  refreshMainGrid: function (id) {
    var me = this;

    Ext.getCmp("buttonEdit").setDisabled(true);
    Ext.getCmp("buttonDelete").setDisabled(true);
    Ext.getCmp("buttonCommit").setDisabled(true);
    Ext.getCmp("buttonCancelConfirm").setDisabled(true);
    Ext.getCmp("buttonGenDMWBill").setDisabled(true);

    var gridDetail = me.getDetailGrid();
    gridDetail.setTitle("生产订单明细");
    gridDetail.getStore().removeAll();

    Ext.getCmp("pagingToobar").doRefresh();
    me.__lastId = id;
  },

  onAddBill: function () {
    var me = this;

    var form = Ext.create("PSI.DMO.DMOEditForm", {
      parentForm: me,
      showAddGoodsButton: me.getPermission().showAddGoodsButton,
      showAddFactoryButton: me.getPermission().showAddFactoryButton
    });
    form.show();
  },

  onEditBill: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("没有选择要编辑的生产订单");
      return;
    }
    var bill = item[0];

    var form = Ext.create("PSI.DMO.DMOEditForm", {
      parentForm: me,
      entity: bill,
      showAddGoodsButton: me.getPermission().showAddGoodsButton,
      showAddFactoryButton: me.getPermission().showAddFactoryButton
    });
    form.show();
  },

  onDeleteBill: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要删除的生产订单");
      return;
    }

    var bill = item[0];

    if (bill.get("billStatus") > 0) {
      me.showInfo("当前生产订单已经审核，不能删除");
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

    var info = "请确认是否删除生产订单: <span style='color:red'>" + bill.get("ref")
      + "</span>";
    var funcConfirm = function () {
      var el = Ext.getBody();
      el.mask("正在删除中...");
      var r = {
        url: me.URL("Home/DM/deleteDMOBill"),
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
    me.getDetailGrid().setTitle("生产订单明细");
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      Ext.getCmp("buttonEdit").setDisabled(true);
      Ext.getCmp("buttonDelete").setDisabled(true);
      Ext.getCmp("buttonCommit").setDisabled(true);
      Ext.getCmp("buttonCancelConfirm").setDisabled(true);
      Ext.getCmp("buttonGenDMWBill").setDisabled(true);

      return;
    }
    var bill = item[0];
    var commited = bill.get("billStatus") >= 1000;

    var buttonEdit = Ext.getCmp("buttonEdit");
    buttonEdit.setDisabled(false);
    if (commited) {
      buttonEdit.setText("查看成品委托生产订单");
    } else {
      buttonEdit.setText("编辑成品委托生产订单");
    }

    Ext.getCmp("buttonDelete").setDisabled(commited);
    Ext.getCmp("buttonCommit").setDisabled(commited);
    Ext.getCmp("buttonCancelConfirm").setDisabled(!commited);
    Ext.getCmp("buttonGenDMWBill").setDisabled(!commited);

    me.refreshDetailGrid();
    me.refreshDMWGrid();
  },

  refreshDetailGrid: function (id) {
    var me = this;
    me.getDetailGrid().setTitle("生产订单明细");
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      return;
    }
    var bill = item[0];

    var grid = me.getDetailGrid();
    grid.setTitle("单号: " + bill.get("ref") + " 工厂: "
      + bill.get("factoryName"));
    var el = grid.getEl();
    el && el.mask(PSI.Const.LOADING);

    var r = {
      url: me.URL("Home/DM/dmoBillDetailList"),
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

        el && el.unmask();
      }
    };
    me.ajax(r);
  },

  onCommit: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("没有选择要审核的生产订单");
      return;
    }
    var bill = item[0];

    if (bill.get("billStatus") > 0) {
      me.showInfo("当前生产订单已经审核，不能再次审核");
      return;
    }

    var detailCount = me.getDetailGrid().getStore().getCount();
    if (detailCount == 0) {
      me.showInfo("当前生产订单没有录入物料明细，不能审核");
      return;
    }

    var info = "请确认是否审核单号: <span style='color:red'>" + bill.get("ref")
      + "</span> 的生产订单?";
    var id = bill.get("id");

    var funcConfirm = function () {
      var el = Ext.getBody();
      el.mask("正在提交中...");
      var r = {
        url: me.URL("Home/DM/commitDMOBill"),
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

  onCancelConfirm: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("没有选择要取消审核的生产订单");
      return;
    }
    var bill = item[0];

    if (bill.get("billStatus") == 0) {
      me.showInfo("当前生产订单还没有审核，无法取消审核");
      return;
    }

    var info = "请确认是否取消审核单号为 <span style='color:red'>" + bill.get("ref")
      + "</span> 的生产订单?";
    var id = bill.get("id");
    var funcConfirm = function () {
      var el = Ext.getBody();
      el.mask("正在提交中...");
      var r = {
        url: me.URL("Home/DM/cancelConfirmDMOBill"),
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
    Ext.getCmp("editQueryFactory").clearIdValue();
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

    var factoryId = Ext.getCmp("editQueryFactory").getIdValue();
    if (factoryId) {
      result.factoryId = factoryId;
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

  onGenDMWBill: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("没有选择要生成入库单的生产订单");
      return;
    }
    var bill = item[0];

    if (bill.get("billStatus") < 1000) {
      me.showInfo("当前生产订单还没有审核，无法生成成品委托生产入库单");
      return;
    }

    if (bill.get("billStatus") >= 4000) {
      me.showInfo("当前生产订单已经关闭，不能再生成成品委托生产入库单");
      return;
    }

    var form = Ext.create("PSI.DMW.DMWEditForm", {
      genBill: true,
      dmobillRef: bill.get("ref")
    });
    form.show();
  },

  onPDF: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("没有选择要生成pdf文件的生产订单");
      return;
    }
    var bill = item[0];

    var url = me.URL("Home/DM/dmoBillPdf?ref=" + bill.get("ref"));
    window.open(url);
  },

  getDMWGrid: function () {
    var me = this;
    if (me.__dmwGrid) {
      return me.__dmwGrid;
    }
    var modelName = "PSIDMOBill_DMWBill";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "ref", "bizDate", "factoryName",
        "warehouseName", "inputUserName", "bizUserName",
        "billStatus", "amount", "dateCreated",
        "paymentType"]
    });
    var store = Ext.create("Ext.data.Store", {
      autoLoad: false,
      model: modelName,
      data: []
    });

    me.__dmwGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      title: "生产订单入库详情",
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
          width: 60,
          renderer: function (value) {
            if (value == "待入库") {
              return "<span style='color:red'>" + value
                + "</span>";
            } else if (value == "已退货") {
              return "<span style='color:blue'>" + value
                + "</span>";
            } else {
              return value;
            }
          }
        }, {
          header: "入库单号",
          dataIndex: "ref",
          width: 130,
          renderer: function (value, md, record) {
            return "<a href='"
              + PSI.Const.BASE_URL
              + "Home/Bill/viewIndex?fid=2036&refType=成品委托生产入库&ref="
              + encodeURIComponent(record.get("ref"))
              + "' target='_blank'>" + value + "</a>";
          }
        }, {
          header: "业务日期",
          dataIndex: "bizDate"
        }, {
          header: "工厂",
          dataIndex: "factoryName",
          width: 300
        }, {
          header: "金额",
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

    return me.__dmwGrid;
  },

  refreshDMWGrid: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      return;
    }
    var bill = item[0];

    var grid = me.getDMWGrid();
    var el = grid.getEl();
    if (el) {
      el.mask(PSI.Const.LOADING);
    }

    var r = {
      url: me.URL("Home/DM/dmoBillDMWBillList"),
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

  onCloseDMO: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("没有选择要关闭的生产订单");
      return;
    }
    var bill = item[0];

    var info = "请确认是否关闭单号: <span style='color:red'>" + bill.get("ref")
      + "</span> 的生产订单?";
    var id = bill.get("id");

    var funcConfirm = function () {
      var el = Ext.getBody();
      el.mask("正在提交中...");
      var r = {
        url: me.URL("Home/DM/closeDMOBill"),
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

  onCancelClosedDMO: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("没有选择要取消关闭状态的生产订单");
      return;
    }
    var bill = item[0];

    var info = "请确认是否取消单号: <span style='color:red'>" + bill.get("ref")
      + "</span> 生产订单的关闭状态?";
    var id = bill.get("id");

    var funcConfirm = function () {
      var el = Ext.getBody();
      el.mask("正在提交中...");
      var r = {
        url: me.URL("Home/DM/cancelClosedDMOBill"),
        params: {
          id: id
        },
        callback: function (options, success, response) {
          el.unmask();

          if (success) {
            var data = me.decodeJSON(response.responseText);
            if (data.success) {
              me.showInfo("成功取消生产订单关闭状态", function () {
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

  onPrintPreview: function () {
    var lodop = getLodop();
    if (!lodop) {
      PSI.MsgBox.showInfo("没有安装Lodop控件，无法打印");
      return;
    }

    var me = this;

    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("没有选择要打印的成品委托生产订单");
      return;
    }
    var bill = item[0];

    var el = Ext.getBody();
    el.mask("数据加载中...");
    var r = {
      url: PSI.Const.BASE_URL + "Home/DM/genDMOBillPrintPage",
      params: {
        id: bill.get("id")
      },
      callback: function (options, success, response) {
        el.unmask();

        if (success) {
          var data = response.responseText;
          me.previewDMOBill(bill.get("ref"), data);
        }
      }
    };
    me.ajax(r);
  },

  PRINT_PAGE_WIDTH: "200mm",
  PRINT_PAGE_HEIGHT: "95mm",

  previewDMOBill: function (ref, data) {
    var me = this;

    var lodop = getLodop();
    if (!lodop) {
      PSI.MsgBox.showInfo("Lodop打印控件没有正确安装");
      return;
    }

    lodop.PRINT_INIT("成品委托生产订单" + ref);
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
      me.showInfo("没有选择要打印的成品委托生产订单");
      return;
    }
    var bill = item[0];

    var el = Ext.getBody();
    el.mask("数据加载中...");
    var r = {
      url: PSI.Const.BASE_URL + "Home/DM/genDMOBillPrintPage",
      params: {
        id: bill.get("id")
      },
      callback: function (options, success, response) {
        el.unmask();

        if (success) {
          var data = response.responseText;
          me.printDMOBill(bill.get("ref"), data);
        }
      }
    };
    me.ajax(r);
  },

  printDMOBill: function (ref, data) {
    var me = this;

    var lodop = getLodop();
    if (!lodop) {
      PSI.MsgBox.showInfo("Lodop打印控件没有正确安装");
      return;
    }

    lodop.PRINT_INIT("成品委托生产订单" + ref);
    lodop.SET_PRINT_PAGESIZE(1, me.PRINT_PAGE_WIDTH, me.PRINT_PAGE_HEIGHT,
      "");
    lodop.ADD_PRINT_HTM("0mm", "0mm", "100%", "100%", data);
    var result = lodop.PRINT();
  }
});
