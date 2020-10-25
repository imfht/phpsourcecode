/**
 * 库存超上限明细表
 */
Ext.define("PSI.Report.InventoryUpperForm", {
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
      }]
    });

    me.callParent(arguments);
  },

  getMainGrid: function () {
    var me = this;
    if (me.__mainGrid) {
      return me.__mainGrid;
    }

    var modelName = "PSIReportInventoryUpper";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["warehouseCode", "warehouseName", "iuCount",
        "invCount", "goodsCode", "goodsName", "goodsSpec",
        "unitName", "delta"]
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
          + "Home/Report/inventoryUpperQueryData",
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
        xtype: "rownumberer"
      }, {
        header: "仓库编码",
        dataIndex: "warehouseCode",
        menuDisabled: true,
        sortable: false
      }, {
        header: "仓库",
        dataIndex: "warehouseName",
        menuDisabled: true,
        sortable: false,
        width: 200
      }, {
        header: "物料编码",
        dataIndex: "goodsCode",
        menuDisabled: true,
        sortable: false
      }, {
        header: "品名",
        dataIndex: "goodsName",
        menuDisabled: true,
        sortable: false,
        width: 200
      }, {
        header: "规格型号",
        dataIndex: "goodsSpec",
        menuDisabled: true,
        sortable: false,
        width: 160
      }, {
        header: "库存上限",
        dataIndex: "iuCount",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        format: "0"
      }, {
        header: "当前库存",
        dataIndex: "invCount",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        format: "0"
      }, {
        header: "存货超量",
        dataIndex: "delta",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        format: "0",
        renderer: function (value) {
          return "<span style='color:red'>" + value
            + "</span>";
        }
      }, {
        header: "计量单位",
        dataIndex: "unitName",
        menuDisabled: true,
        sortable: false
      }],
      store: store
    });

    return me.__mainGrid;
  },

  onQuery: function () {
    this.refreshMainGrid();
  },

  refreshMainGrid: function (id) {
    Ext.getCmp("pagingToobar").doRefresh();
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
      url: PSI.Const.BASE_URL + "Home/Report/genInventoryUpperPrintPage",
      params: {
        limit: -1
      },
      callback: function (options, success, response) {
        el.unmask();

        if (success) {
          var data = response.responseText;
          me.previewReport("库存超上限明细表", data);
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
      url: PSI.Const.BASE_URL + "Home/Report/genInventoryUpperPrintPage",
      params: {
        limit: -1
      },
      callback: function (options, success, response) {
        el.unmask();

        if (success) {
          var data = response.responseText;
          me.printReport("库存超上限明细表", data);
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

    var url = "Home/Report/inventoryUpperPdf?limit=-1";
    window.open(me.URL(url));
  },

  onExcel: function () {
    var me = this;

    var url = "Home/Report/inventoryUpperExcel?limit=-1";
    window.open(me.URL(url));
  }
});
