//
// 成品委托生产入库 - 主界面
// 
Ext.define("PSI.DMW.DMWMainForm", {
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
      text: "新建成品委托生产入库单",
      hidden: me.getPermission().add == "0",
      id: "buttonAdd",
      scope: me,
      handler: me.onAddBill
    }, {
      hidden: me.getPermission().add == "0",
      xtype: "tbseparator"
    }, {
      text: "编辑成品委托生产入库单",
      hidden: me.getPermission().edit == "0",
      scope: me,
      handler: me.onEditBill,
      id: "buttonEdit"
    }, {
      hidden: me.getPermission().edit == "0",
      xtype: "tbseparator"
    }, {
      text: "删除成品委托生产入库单",
      hidden: me.getPermission().del == "0",
      scope: me,
      handler: me.onDeleteBill,
      id: "buttonDelete"
    }, {
      hidden: me.getPermission().del == "0",
      xtype: "tbseparator"
    }, {
      text: "提交入库",
      hidden: me.getPermission().commit == "0",
      scope: me,
      handler: me.onCommit,
      id: "buttonCommit"
    }, {
      hidden: me.getPermission().commit == "0",
      xtype: "tbseparator"
    }, {
      hidden: me.getPermission().genPDF == "0",
      text: "导出",
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
      hidden: me.getPermission().print == "0",
      xtype: "tbseparator"
    }, {
      text: "帮助",
      handler: function () {
        window.open(me.URL("Home/Help/index?t=dmwbill"));
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
        data: [[-1, "全部"], [0, "待入库"], [1000, "已入库"],
        [2000, "已退货"]]
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
      id: "editQueryWarehouse",
      xtype: "psi_warehousefield",
      parentCmp: me,
      showModal: true,
      labelAlign: "right",
      labelSeparator: "",
      labelWidth: 60,
      margin: "5, 0, 0, 0",
      fieldLabel: "仓库"
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

  getMainGrid: function () {
    var me = this;
    if (me.__mainGrid) {
      return me.__mainGrid;
    }

    var modelName = "PSIDMWBill";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "ref", "bizDate", "factoryName",
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
        url: me.URL("Home/DM/dmwbillList"),
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
          width: 60,
          renderer: function (value) {
            if (value == "待入库") {
              return "<span style='color:red'>"
                + value + "</span>";
            } else if (value == "已退货") {
              return "<span style='color:blue'>"
                + value + "</span>";
            } else {
              return value;
            }
          }
        }, {
          header: "入库单号",
          dataIndex: "ref",
          width: 130
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
        }, {
          header: "备注",
          dataIndex: "billMemo",
          width: 150
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
        }],
      listeners: {
        select: {
          fn: me.onMainGridSelect,
          scope: me
        },
        itemdblclick: {
          fn: me.onEditBill,
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

    var modelName = "PSIPWBillDetail";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "goodsCode", "goodsName", "goodsSpec",
        "unitName", "goodsCount", "goodsMoney",
        "goodsPrice", "memo", "taxRate", "tax",
        "moneyWithTax", "goodsPriceWithTax"]
    });
    var store = Ext.create("Ext.data.Store", {
      autoLoad: false,
      model: modelName,
      data: []
    });

    me.__detailGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      header: {
        height: 30,
        title: me.formatGridHeaderTitle("成品委托生产入库单明细")
      },
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
          header: "入库数量",
          width: 120,
          dataIndex: "goodsCount",
          align: "right"
        }, {
          header: "单位",
          dataIndex: "unitName",
          width: 60
        }, {
          header: "单价",
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
          format: "#",
          xtype: "numbercolumn"
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
          width: 200
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

    var gridDetail = me.getDetailGrid();
    gridDetail.setTitle(me.formatGridHeaderTitle("成品委托生产入库单明细"));
    gridDetail.getStore().removeAll();

    Ext.getCmp("pagingToobar").doRefresh();
    me.__lastId = id;
  },

  onAddBill: function () {
    var me = this;

    var form = Ext.create("PSI.DMW.DMWEditForm", {
      parentForm: me
    });
    form.show();
  },

  onEditBill: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("没有选择要编辑的成品委托生产入库单");
      return;
    }
    var bill = item[0];

    var form = Ext.create("PSI.DMW.DMWEditForm", {
      parentForm: me,
      entity: bill
    });
    form.show();
  },

  onDeleteBill: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要删除的成品委托生产入库单");
      return;
    }

    var bill = item[0];

    if (bill.get("billStatus") == "已入库") {
      me.showInfo("当前成品委托生产入库单已经提交入库，不能删除");
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

    var info = "请确认是否删除成品委托生产入库单: <span style='color:red'>"
      + bill.get("ref") + "</span>";
    var confirmFunc = function () {
      var el = Ext.getBody();
      el.mask("正在删除中...");

      var r = {
        url: me.URL("Home/DM/deleteDMWBill"),
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
    me.confirm(info, confirmFunc);
  },

  onMainGridSelect: function () {
    var me = this;
    me.getDetailGrid().setTitle(me.formatGridHeaderTitle("成品委托生产入库单明细"));
    var item = me.getMainGrid().getSelectionModel().getSelection();
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
      buttonEdit.setText("查看成品委托生产入库单");
    } else {
      buttonEdit.setText("编辑成品委托生产入库单");
    }

    Ext.getCmp("buttonDelete").setDisabled(commited);
    Ext.getCmp("buttonCommit").setDisabled(commited);

    me.refreshDetailGrid();
  },

  refreshDetailGrid: function (id) {
    var me = this;
    me.getDetailGrid().setTitle(me.formatGridHeaderTitle("成品委托生产入库单明细"));
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      return;
    }
    var bill = item[0];

    var grid = me.getDetailGrid();
    grid.setTitle(me.formatGridHeaderTitle("单号: " + bill.get("ref")
      + " 工厂: " + bill.get("factoryName") + " 入库仓库: "
      + bill.get("warehouseName")));
    var el = grid.getEl();
    el.mask(PSI.Const.LOADING);
    me.ajax({
      url: me.URL("Home/DM/dmwBillDetailList"),
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
    });
  },

  onCommit: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("没有选择要提交的成品委托生产入库单");
      return;
    }
    var bill = item[0];

    if (bill.get("billStatus") == "已入库") {
      me.showInfo("当前成品委托生产入库单已经提交入库，不能再次提交");
      return;
    }

    var detailCount = me.getDetailGrid().getStore().getCount();
    if (detailCount == 0) {
      me.showInfo("当前成品委托生产入库单没有录入物料明细，不能提交");
      return;
    }

    var info = "请确认是否提交单号: <span style='color:red'>" + bill.get("ref")
      + "</span> 的成品委托生产入库单?";
    var id = bill.get("id");
    var confirmFunc = function () {
      var el = Ext.getBody();
      el.mask("正在提交中...");
      var r = {
        url: me.URL("Home/DM/commitDMWBill"),
        params: {
          id: id
        },
        callback: function (options, success, response) {
          el.unmask();

          if (success) {
            var data = me.decodeJSON(response.responseText);
            if (data.success) {
              me.showInfo("成功完成提交操作", function () {
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
    me.confirm(info, confirmFunc);
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
    Ext.getCmp("editQueryWarehouse").clearIdValue();
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
      me.showInfo("没有选择要生成pdf文件的成品委托生产入库单");
      return;
    }
    var bill = item[0];

    var url = me.URL("Home/DM/dmwBillPdf?ref=" + bill.get("ref"));
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
      me.showInfo("没有选择要打印的成品委托生产入库单");
      return;
    }
    var bill = item[0];

    var el = Ext.getBody();
    el.mask("数据加载中...");
    var r = {
      url: PSI.Const.BASE_URL + "Home/DM/genDMWBillPrintPage",
      params: {
        id: bill.get("id")
      },
      callback: function (options, success, response) {
        el.unmask();

        if (success) {
          var data = response.responseText;
          me.previewDMWBill(bill.get("ref"), data);
        }
      }
    };
    me.ajax(r);
  },

  PRINT_PAGE_WIDTH: "200mm",
  PRINT_PAGE_HEIGHT: "95mm",

  previewDMWBill: function (ref, data) {
    var me = this;

    var lodop = getLodop();
    if (!lodop) {
      PSI.MsgBox.showInfo("Lodop打印控件没有正确安装");
      return;
    }

    lodop.PRINT_INIT("成品委托生产入库单" + ref);
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
      me.showInfo("没有选择要打印的成品委托生产入库单");
      return;
    }
    var bill = item[0];

    var el = Ext.getBody();
    el.mask("数据加载中...");
    var r = {
      url: PSI.Const.BASE_URL + "Home/DM/genDMWBillPrintPage",
      params: {
        id: bill.get("id")
      },
      callback: function (options, success, response) {
        el.unmask();

        if (success) {
          var data = response.responseText;
          me.printDMWBill(bill.get("ref"), data);
        }
      }
    };
    me.ajax(r);
  },

  printDMWBill: function (ref, data) {
    var me = this;

    var lodop = getLodop();
    if (!lodop) {
      PSI.MsgBox.showInfo("Lodop打印控件没有正确安装");
      return;
    }

    lodop.PRINT_INIT("成品委托生产入库单" + ref);
    lodop.SET_PRINT_PAGESIZE(1, me.PRINT_PAGE_WIDTH, me.PRINT_PAGE_HEIGHT,
      "");
    lodop.ADD_PRINT_HTM("0mm", "0mm", "100%", "100%", data);
    var result = lodop.PRINT();
  }
});
