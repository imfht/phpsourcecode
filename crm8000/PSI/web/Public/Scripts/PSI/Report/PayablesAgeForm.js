/**
 * 应付账款账龄分析表
 */
Ext.define("PSI.Report.PayablesAgeForm", {
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
      }, "-", {
        text: "查询",
        iconCls: "PSI-button-refresh",
        handler: me.onQuery,
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
        layout: "fit",
        border: 0,
        items: [me.getMainGrid()]
      }, {
        region: "south",
        layout: "fit",
        border: 0,
        height: 90,
        items: [me.getSummaryGrid()]
      }]
    });

    me.callParent(arguments);
  },

  getMainGrid: function () {
    var me = this;
    if (me.__mainGrid) {
      return me.__mainGrid;
    }

    var modelName = "PSIReportPayablesAge";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["caType", "caCode", "caName", "balanceMoney",
        "money30", "money30to60", "money60to90", "money90"]
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
        url: PSI.Const.BASE_URL
          + "Home/Report/payablesAgeQueryData",
        reader: {
          root: 'dataList',
          totalProperty: 'totalCount'
        }
      }
    });

    me.__mainGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      border: 0,
      columnLines: true,
      columns: [{
        xtype: "rownumberer",
        width: 60
      }, {
        header: "往来单位性质",
        dataIndex: "caType",
        menuDisabled: true,
        sortable: false
      }, {
        header: "往来单位编码",
        dataIndex: "caCode",
        menuDisabled: true,
        sortable: false
      }, {
        header: "往来单位",
        dataIndex: "caName",
        menuDisabled: true,
        sortable: false,
        width: 200
      }, {
        header: "当期余额",
        dataIndex: "balanceMoney",
        width: 120,
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn"
      }, {
        header: "账龄30天内",
        dataIndex: "money30",
        width: 120,
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn"
      }, {
        header: "账龄30-60天",
        dataIndex: "money30to60",
        width: 120,
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn"
      }, {
        header: "账龄60-90天",
        dataIndex: "money60to90",
        width: 120,
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn"
      }, {
        header: "账龄大于90天",
        dataIndex: "money90",
        width: 120,
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn"
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

    var modelName = "PSIReceivablesSummary";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["balanceMoney", "money30", "money30to60",
        "money60to90", "money90"]
    });

    me.__summaryGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      header: {
        height: 30,
        title: me.formatGridHeaderTitle("应付账款汇总")
      },
      viewConfig: {
        enableTextSelection: true
      },
      columnLines: true,
      border: 1,
      columns: [{
        header: "当期余额",
        dataIndex: "balanceMoney",
        width: 120,
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn"
      }, {
        header: "账龄30天内",
        dataIndex: "money30",
        width: 120,
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn"
      }, {
        header: "账龄30-60天",
        dataIndex: "money30to60",
        width: 120,
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn"
      }, {
        header: "账龄60-90天",
        dataIndex: "money60to90",
        width: 120,
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn"
      }, {
        header: "账龄大于90天",
        dataIndex: "money90",
        width: 120,
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn"
      }],
      store: Ext.create("Ext.data.Store", {
        model: modelName,
        autoLoad: false,
        data: []
      })
    });

    return me.__summaryGrid;
  },

  onQuery: function () {
    this.refreshMainGrid();
    this.querySummaryData();
  },

  refreshMainGrid: function (id) {
    Ext.getCmp("pagingToobar").doRefresh();
  },

  querySummaryData: function () {
    var me = this;
    var grid = me.getSummaryGrid();
    var el = grid.getEl() || Ext.getBody();
    el.mask(PSI.Const.LOADING);
    Ext.Ajax.request({
      url: PSI.Const.BASE_URL
        + "Home/Report/payablesSummaryQueryData",
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

  onPrintPreview: function () {
    var lodop = getLodop();
    if (!lodop) {
      PSI.MsgBox.showInfo("没有安装Lodop控件，无法打印");
      return;
    }

    var me = this;

    var el = Ext.getBody();
    el.mask("数据加载中...");
    var r = {
      url: PSI.Const.BASE_URL + "Home/Report/genPayablesAgePrintPage",
      params: {
        limit: -1
      },
      callback: function (options, success, response) {
        el.unmask();

        if (success) {
          var data = response.responseText;
          me.previewReport("应付账款账龄分析表", data);
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

    var el = Ext.getBody();
    el.mask("数据加载中...");
    var r = {
      url: PSI.Const.BASE_URL + "Home/Report/genPayablesAgePrintPage",
      params: {
        limit: -1
      },
      callback: function (options, success, response) {
        el.unmask();

        if (success) {
          var data = response.responseText;
          me.printReport("应付账款账龄分析表", data);
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

    var url = "Home/Report/payablesAgePdf?limit=-1";
    window.open(me.URL(url));
  },

  onExcel: function () {
    var me = this;

    var url = "Home/Report/payablesAgeExcel?limit=-1";
    window.open(me.URL(url));
  }
});
