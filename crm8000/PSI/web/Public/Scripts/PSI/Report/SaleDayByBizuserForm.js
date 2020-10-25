/**
 * 销售日报表(按业务员汇总)
 */
Ext.define("PSI.Report.SaleDayByBizuserForm", {
  extend: "PSI.AFX.BaseMainExForm",

  initComponent: function () {
    var me = this;

    var store = me.getMainGrid().getStore();

    Ext.apply(me, {
      tbar: [{
        id: "pagingToobar",
        cls: "PSI-toolbox",
        xtype: "pagingtoolbar",
        border: 0,
        store: store
      }, "-", {
        xtype: "displayfield",
        value: "每页显示"
      }, {
        id: "comboCountPerPage",
        cls: "PSI-toolbox",
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
      }, {
        id: "editQueryDT",
        cls: "PSI-toolbox",
        xtype: "datefield",
        format: "Y-m-d",
        labelAlign: "right",
        labelSeparator: "",
        labelWidth: 60,
        fieldLabel: "业务日期",
        value: new Date()
      }, " ", {
        text: "前一天",
        handler: me.onPreDay,
        scope: me
      }, {
        text: "后一天",
        handler: me.onNextDay,
        scope: me
      }, " ", {
        text: "查询",
        iconCls: "PSI-button-refresh",
        handler: me.onQuery,
        scope: me
      }, {
        text: "重置查询条件",
        handler: me.onClearQuery,
        scope: me
      }, "-", {
        text: "打印",
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
        text: "导出",
        menu: [{
          text: "导出PDF",
          iconCls: "PSI-button-pdf",
          scope: me,
          handler: me.onPDF
        }, "-", {
          text: "导出Excel",
          iconCls: "PSI-button-excel",
          scope: me,
          handler: me.onExcel
        }]
      }, "-", {
        text: "关闭",
        handler: function () {
          me.closeWindow();
        }
      }],
      items: [{
        region: "center",
        layout: "border",
        border: 0,
        items: [{
          region: "center",
          layout: "fit",
          border: 0,
          items: [me.getMainGrid()]
        }, {
          region: "south",
          layout: "fit",
          height: 100,
          items: [me.getSummaryGrid()]
        }]
      }]
    });

    me.callParent(arguments);
  },

  getMainGrid: function () {
    var me = this;
    if (me.__mainGrid) {
      return me.__mainGrid;
    }

    var modelName = "PSIReportSaleDayByBizuser";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["bizDT", "userCode", "userName", "saleMoney",
        "rejMoney", "m", "profit", "rate"]
    });
    var store = Ext.create("Ext.data.Store", {
      autoLoad: false,
      model: modelName,
      data: [],
      pageSize: 20,
      remoteSort: true,
      proxy: {
        type: "ajax",
        actionMethods: {
          read: "POST"
        },
        url: PSI.Const.BASE_URL
          + "Home/Report/saleDayByBizuserQueryData",
        reader: {
          root: 'dataList',
          totalProperty: 'totalCount'
        }
      }
    });
    store.on("beforeload", function () {
      store.proxy.extraParams = me.getQueryParam();
    });

    me.__mainGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      border: 0,
      columnLines: true,
      columns: [{
        xtype: "rownumberer"
      }, {
        header: "业务日期",
        dataIndex: "bizDT",
        menuDisabled: true,
        sortable: false,
        width: 80
      }, {
        header: "业务员编码",
        dataIndex: "userCode",
        menuDisabled: true,
        sortable: true,
        width: 100
      }, {
        header: "业务员",
        dataIndex: "userName",
        menuDisabled: true,
        sortable: false,
        width: 100
      }, {
        header: "销售出库金额",
        dataIndex: "saleMoney",
        menuDisabled: true,
        sortable: true,
        align: "right",
        xtype: "numbercolumn"
      }, {
        header: "退货入库金额",
        dataIndex: "rejMoney",
        menuDisabled: true,
        sortable: true,
        align: "right",
        xtype: "numbercolumn"
      }, {
        header: "净销售金额",
        dataIndex: "m",
        menuDisabled: true,
        sortable: true,
        align: "right",
        xtype: "numbercolumn"
      }, {
        header: "毛利",
        dataIndex: "profit",
        menuDisabled: true,
        sortable: true,
        align: "right",
        xtype: "numbercolumn"
      }, {
        header: "毛利率",
        dataIndex: "rate",
        menuDisabled: true,
        sortable: true,
        align: "right"
      }],
      store: store
    });

    return me.__mainGrid;
  },

  getSummaryGrid: function () {
    var me = this;
    if (me.__summaryGrid) {
      return me.__summaryGrid;
    }

    var modelName = "PSIReportSaleDayByBizuserSummary";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["bizDT", "saleMoney", "rejMoney", "m", "profit",
        "rate"]
    });
    var store = Ext.create("Ext.data.Store", {
      autoLoad: false,
      model: modelName,
      data: []
    });

    me.__summaryGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      header: {
        height: 30,
        title: me.formatGridHeaderTitle("日销售汇总")
      },
      viewConfig: {
        enableTextSelection: true
      },
      border: 0,
      columnLines: true,
      columns: [{
        header: "业务日期",
        dataIndex: "bizDT",
        menuDisabled: true,
        sortable: false,
        width: 80
      }, {
        header: "销售出库金额",
        dataIndex: "saleMoney",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn"
      }, {
        header: "退货入库金额",
        dataIndex: "rejMoney",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn"
      }, {
        header: "净销售金额",
        dataIndex: "m",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn"
      }, {
        header: "毛利",
        dataIndex: "profit",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn"
      }, {
        header: "毛利率",
        dataIndex: "rate",
        menuDisabled: true,
        sortable: false,
        align: "right"
      }],
      store: store
    });

    return me.__summaryGrid;
  },

  onQuery: function () {
    this.refreshMainGrid();
    this.refreshSummaryGrid();
  },

  refreshSummaryGrid: function () {
    var me = this;
    var grid = me.getSummaryGrid();
    var el = grid.getEl() || Ext.getBody();
    el.mask(PSI.Const.LOADING);
    Ext.Ajax.request({
      url: PSI.Const.BASE_URL
        + "Home/Report/saleDayByBizuserSummaryQueryData",
      params: me.getQueryParam(),
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
    });
  },

  onClearQuery: function () {
    var me = this;

    Ext.getCmp("editQueryDT").setValue(new Date());

    me.onQuery();
  },

  getQueryParam: function () {
    var me = this;

    var result = {};

    var dt = Ext.getCmp("editQueryDT").getValue();
    if (dt) {
      result.dt = Ext.Date.format(dt, "Y-m-d");
    }

    return result;
  },

  refreshMainGrid: function (id) {
    Ext.getCmp("pagingToobar").doRefresh();
  },

  onPreDay: function () {
    var me = this;

    var editQueryDT = Ext.getCmp("editQueryDT");
    var day = Ext.Date.add(editQueryDT.getValue(), Ext.Date.DAY, -1);
    editQueryDT.setValue(day);

    me.onQuery();
  },

  onNextDay: function () {
    var me = this;

    var editQueryDT = Ext.getCmp("editQueryDT");
    var day = Ext.Date.add(editQueryDT.getValue(), Ext.Date.DAY, 1);
    editQueryDT.setValue(day);

    me.onQuery();
  },

  onPrintPreview: function () {
    var lodop = getLodop();
    if (!lodop) {
      PSI.MsgBox.showInfo("没有安装Lodop控件，无法打印");
      return;
    }

    var me = this;

    var store = me.getMainGrid().getStore();
    var sorter = null;
    if (store.sorters.getCount() > 0) {
      sorter = Ext.JSON.encode([store.sorters.getAt(0)]);
    }

    var el = Ext.getBody();
    el.mask("数据加载中...");
    var r = {
      url: PSI.Const.BASE_URL
        + "Home/Report/genSaleDayByBizuserPrintPage",
      params: {
        dt: Ext.Date.format(Ext.getCmp("editQueryDT").getValue(),
          "Y-m-d"),
        sort: sorter,
        limit: -1
      },
      callback: function (options, success, response) {
        el.unmask();

        if (success) {
          var data = response.responseText;
          me.previewReport("销售日报表(按客户汇总)", data);
        }
      }
    };
    me.ajax(r);
  },

  PRINT_PAGE_WIDTH: "200mm",
  PRINT_PAGE_HEIGHT: "95mm",

  previewReport: function (ref, data) {
    var me = this;

    var lodop = getLodop();
    if (!lodop) {
      PSI.MsgBox.showInfo("Lodop打印控件没有正确安装");
      return;
    }

    lodop.PRINT_INIT(ref);
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

    var store = me.getMainGrid().getStore();
    var sorter = null;
    if (store.sorters.getCount() > 0) {
      sorter = Ext.JSON.encode([store.sorters.getAt(0)]);
    }

    var el = Ext.getBody();
    el.mask("数据加载中...");
    var r = {
      url: PSI.Const.BASE_URL
        + "Home/Report/genSaleDayByBizuserPrintPage",
      params: {
        dt: Ext.Date.format(Ext.getCmp("editQueryDT").getValue(),
          "Y-m-d"),
        sort: sorter,
        limit: -1
      },
      callback: function (options, success, response) {
        el.unmask();

        if (success) {
          var data = response.responseText;
          me.printReport("销售日报表(按客户汇总)", data);
        }
      }
    };
    me.ajax(r);
  },

  printReport: function (ref, data) {
    var me = this;

    var lodop = getLodop();
    if (!lodop) {
      PSI.MsgBox.showInfo("Lodop打印控件没有正确安装");
      return;
    }

    lodop.PRINT_INIT(ref);
    lodop.SET_PRINT_PAGESIZE(1, me.PRINT_PAGE_WIDTH, me.PRINT_PAGE_HEIGHT,
      "");
    lodop.ADD_PRINT_HTM("0mm", "0mm", "100%", "100%", data);
    var result = lodop.PRINT();
  },

  onPDF: function () {
    var me = this;

    var store = me.getMainGrid().getStore();
    var sorter = null;
    if (store.sorters.getCount() > 0) {
      sorter = Ext.JSON.encode([store.sorters.getAt(0)]);
    }

    var dt = Ext.Date.format(Ext.getCmp("editQueryDT").getValue(), "Y-m-d");

    var url = "Home/Report/saleDayByBizuserPdf?limit=-1&dt=" + dt;
    if (sorter) {
      url += "&sort=" + sorter;
    }

    window.open(me.URL(url));
  },

  onExcel: function () {
    var me = this;

    var store = me.getMainGrid().getStore();
    var sorter = null;
    if (store.sorters.getCount() > 0) {
      sorter = Ext.JSON.encode([store.sorters.getAt(0)]);
    }

    var dt = Ext.Date.format(Ext.getCmp("editQueryDT").getValue(), "Y-m-d");

    var url = "Home/Report/saleDayByBizuserExcel?limit=-1&dt=" + dt;
    if (sorter) {
      url += "&sort=" + sorter;
    }

    window.open(me.URL(url));
  }
});
