/**
 * 首页
 */
Ext.define("PSI.Home.MainForm", {
  extend: "Ext.panel.Panel",

  config: {
    pSale: "",
    pInventory: "",
    pPurchase: "",
    pMoney: "",
    productionName: "PSI"
  },

  border: 0,
  bodyPadding: 5,

  initComponent: function () {
    var me = this;

    var items = [];

    // 销售看板
    if (me.getPSale() == "1") {
      items.push({
        width: "100%",
        layout: "hbox",
        border: 0,
        hidden: me.getPSale() != "1",
        items: [me.getSalePortal1(), me.getSalePortal2()]
      });
    }
    // 采购看板
    if (me.getPPurchase() == "1") {
      items.push({
        width: "100%",
        layout: "hbox",
        border: 0,
        items: [me.getPurchasePortal1(),
        me.getPurchasePortal2()]
      });
    }
    // 库存看板
    if (me.getPInventory() == "1") {
      items.push({
        width: "100%",
        layout: "hbox",
        border: 0,
        items: [me.getInventoryPortal()]
      });
    }
    // 资金看板
    if (me.getPMoney() == "1") {
      items.push({
        width: "100%",
        layout: "hbox",
        border: 0,
        items: [me.getMoneyPortal()]
      });
    }
    // 如果上述看板都没有权限，则显示默认信息
    if (items.length == 0) {
      items.push({
        width: "100%",
        layout: "hbox",
        border: 0,
        items: [me.getInfoPortal()]
      });
    }
    Ext.apply(me, {
      layout: "vbox",
      autoScroll: true,
      items: items
    });

    me.callParent(arguments);

    if (me.getPSale() == "1") {
      me.querySaleData();
    }

    if (me.getPPurchase() == "1") {
      me.queryPurchaseData();
    }

    if (me.getPInventory() == "1") {
      me.queryInventoryData();
    }

    if (me.getPMoney() == "1") {
      me.queryMoneyData();
    }
  },

  getSaleGrid: function () {
    var me = this;
    if (me.__saleGrid) {
      return me.__saleGrid;
    }

    var modelName = "PSIPortalSale";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["month", "saleMoney", "profit", "rate"]
    });

    me.__saleGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      columnLines: true,
      border: 0,
      columns: [{
        header: "月份",
        dataIndex: "month",
        width: 80,
        menuDisabled: true,
        sortable: false
      }, {
        header: "销售额(不含税)",
        dataIndex: "saleMoney",
        width: 120,
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn"
      }, {
        header: "毛利",
        dataIndex: "profit",
        width: 120,
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
      store: Ext.create("Ext.data.Store", {
        model: modelName,
        autoLoad: false,
        data: []
      })
    });

    return me.__saleGrid;
  },

  getPurchaseGrid: function () {
    var me = this;
    if (me.__purchaseGrid) {
      return me.__purchaseGrid;
    }

    var modelName = "PSIPortalPurchase";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["month", "purchaseMoney"]
    });

    me.__purchaseGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      columnLines: true,
      border: 0,
      columns: [{
        header: "月份",
        dataIndex: "month",
        width: 80,
        menuDisabled: true,
        sortable: false
      }, {
        header: "采购额",
        dataIndex: "purchaseMoney",
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

    return me.__purchaseGrid;
  },

  getInventoryGrid: function () {
    var me = this;
    if (me.__inventoryGrid) {
      return me.__inventoryGrid;
    }

    var modelName = "PSIPortalInventory";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["warehouseName", { name: "inventoryMoney", type: "float" },
        { name: "siCount", type: "float" },
        { name: "iuCount", type: "float" }]
    });

    me.__inventoryGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      features: [{
        ftype: "summary",
        dock: "bottom"
      }],
      columnLines: true,
      border: 0,
      columns: [{
        header: "仓库",
        dataIndex: "warehouseName",
        width: 160,
        menuDisabled: true,
        sortable: false,
        summaryRenderer: function () {
          return "合计";
        }
      }, {
        header: "存货金额",
        dataIndex: "inventoryMoney",
        width: 140,
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        summaryType: "sum"
      }, {
        header: "低于安全库存商品种类数",
        dataIndex: "siCount",
        width: 180,
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        format: "0",
        renderer: function (value) {
          return value > 0
            ? "<span style='color:red'>"
            + value + "</span>"
            : value;
        },
        summaryType: "sum"
      }, {
        header: "超过库存上限的商品种类数",
        dataIndex: "iuCount",
        width: 180,
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        format: "0",
        renderer: function (value) {
          return value > 0
            ? "<span style='color:red'>"
            + value + "</span>"
            : value;
        },
        summaryType: "sum"
      }],
      store: Ext.create("Ext.data.Store", {
        model: modelName,
        autoLoad: false,
        data: []
      })
    });

    return me.__inventoryGrid;
  },

  getMoneyGrid: function () {
    var me = this;
    if (me.__moneyGrid) {
      return me.__moneyGrid;
    }

    var modelName = "PSIPortalMoney";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["item", "balanceMoney", "money30", "money30to60",
        "money60to90", "money90"]
    });

    me.__moneyGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      columnLines: true,
      border: 0,
      columns: [{
        header: "款项",
        dataIndex: "item",
        width: 80,
        menuDisabled: true,
        sortable: false
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
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn"
      }, {
        header: "账龄60-90天",
        dataIndex: "money60to90",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn"
      }, {
        header: "账龄大于90天",
        dataIndex: "money90",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        width: 120
      }],
      store: Ext.create("Ext.data.Store", {
        model: modelName,
        autoLoad: false,
        data: []
      })
    });

    return me.__moneyGrid;
  },

  getSalePortal1: function () {
    var me = this;
    return {
      flex: 1,
      width: "100%",
      height: 270,
      margin: "5",
      header: {
        title: "<span style='font-size:120%;font-weight:normal;'>销售看板</span>",
        iconCls: "PSI-portal-sale",
        height: 40
      },
      layout: "fit",
      items: me.getSaleChart()
    };
  },

  getSalePortal2: function () {
    var me = this;
    return {
      flex: 1,
      width: "100%",
      height: 270,
      margin: "5",
      header: {
        title: "<span style='font-size:120%;font-weight:normal;'>销售看板</span>",
        iconCls: "PSI-portal-sale",
        height: 40
      },
      layout: "fit",
      items: me.getSaleGrid()
    };
  },

  getPurchasePortal1: function () {
    var me = this;
    return {
      header: {
        title: "<span style='font-size:120%;font-weight:normal;'>采购看板</span>",
        iconCls: "PSI-portal-purchase",
        height: 40
      },
      flex: 1,
      width: "100%",
      height: 270,
      margin: "5",
      layout: "fit",
      items: me.getPurchaseChart()
    };
  },

  getPurchasePortal2: function () {
    var me = this;
    return {
      header: {
        title: "<span style='font-size:120%;font-weight:normal;'>采购看板</span>",
        iconCls: "PSI-portal-purchase",
        height: 40
      },
      flex: 1,
      width: "100%",
      height: 270,
      margin: "5",
      layout: "fit",
      items: me.getPurchaseGrid()
    };
  },

  getInventoryPortal: function () {
    var me = this;
    return {
      header: {
        title: "<span style='font-size:120%;font-weight:normal;'>库存看板</span>",
        iconCls: "PSI-portal-inventory",
        height: 40
      },
      flex: 1,
      width: "100%",
      height: 270,
      margin: "5",
      layout: "fit",
      items: [me.getInventoryGrid()]
    };
  },

  getMoneyPortal: function () {
    var me = this;
    return {
      header: {
        title: "<span style='font-size:120%;font-weight:normal;'>资金看板</span>",
        iconCls: "PSI-portal-money",
        height: 40
      },
      flex: 1,
      width: "100%",
      height: 270,
      margin: "5",
      layout: "fit",
      items: [me.getMoneyGrid()]
    };
  },

  queryInventoryData: function () {
    var me = this;
    var grid = me.getInventoryGrid();
    var el = grid.getEl() || Ext.getBody();
    el.mask(PSI.Const.LOADING);
    Ext.Ajax.request({
      url: PSI.Const.BASE_URL + "Home/Portal/inventoryPortal",
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

  querySaleData: function () {
    var me = this;
    var grid = me.getSaleGrid();
    var el = grid.getEl() || Ext.getBody();
    el.mask(PSI.Const.LOADING);
    Ext.Ajax.request({
      url: PSI.Const.BASE_URL + "Home/Portal/salePortal",
      method: "POST",
      callback: function (options, success, response) {
        var store = grid.getStore();
        store.removeAll();

        if (success) {
          var data = Ext.JSON.decode(response.responseText);
          store.add(data);

          me.copyDataFromSaleGrid(data);
        }

        el.unmask();
      }
    });
  },

  copyDataFromSaleGrid: function (data) {
    var me = this;
    var store = me.getSaleChart().getStore();
    store.removeAll();
    var len = data.length;
    for (var i = len - 1; i >= 0; i--) {
      var d = data[i];
      store.add({
        month: d.month,
        "不含税销售额": d.saleMoney,
        "毛利": d.profit
      });
    }
  },

  queryPurchaseData: function () {
    var me = this;
    var grid = me.getPurchaseGrid();
    var el = grid.getEl() || Ext.getBody();
    el.mask(PSI.Const.LOADING);
    Ext.Ajax.request({
      url: PSI.Const.BASE_URL + "Home/Portal/purchasePortal",
      method: "POST",
      callback: function (options, success, response) {
        var store = grid.getStore();
        store.removeAll();

        if (success) {
          var data = Ext.JSON.decode(response.responseText);
          store.add(data);
          me.copyDataFromPurchaseGrid(data);
        }

        el.unmask();
      }
    });
  },

  copyDataFromPurchaseGrid: function (data) {
    var me = this;
    var store = me.getPurchaseChart().getStore();
    store.removeAll();
    var len = data.length;
    for (var i = len - 1; i >= 0; i--) {
      var d = data[i];
      store.add(d);
    }
  },

  queryMoneyData: function () {
    var me = this;
    var grid = me.getMoneyGrid();
    var el = grid.getEl() || Ext.getBody();
    el.mask(PSI.Const.LOADING);
    Ext.Ajax.request({
      url: PSI.Const.BASE_URL + "Home/Portal/moneyPortal",
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

  getInfoPortal: function () {
    var me = this;
    return {
      border: 0,
      html: "<h1>欢迎使用" + me.getProductionName() + "</h1>"
    }
  },

  getSaleChart: function () {
    var me = this;
    if (me.__saleChart) {
      return me.__saleChart;
    }

    var modelName = "saleChart";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["不含税销售额", "毛利", "month"]
    });
    var store = Ext.create("Ext.data.Store", {
      model: modelName,
      data: []
    });

    me.__saleChart = Ext.create("Ext.chart.Chart", {
      theme: "Category1",
      animate: true,
      legend: {
        position: "top"
      },
      store: store,
      axes: [{
        title: "金额",
        type: "Numeric",
        position: "left",
        grid: true,
        fields: ["不含税销售额", "毛利"],
        label: {
          renderer: function (v) {
            return me.formatMoney2(v);
          }
        }
      }, {
        type: "Category",
        position: "bottom",
        fields: ["month"]
      }],
      series: [{
        type: "line",
        xField: "month",
        yField: "不含税销售额",
        highlight: {
          size: 7,
          radius: 7
        },
        tips: {
          trackMouse: true,
          width: 120,
          height: 50,
          renderer: function (storeItem, item) {
            this.setTitle("不含税销售额");
            this.update(me
              .formatMoney(storeItem.get("不含税销售额")));
          }
        }
      }, {
        type: "line",
        xField: "month",
        yField: "毛利",
        highlight: {
          size: 7,
          radius: 7
        },
        highlight: {
          size: 7,
          radius: 7
        },
        tips: {
          trackMouse: true,
          width: 120,
          height: 50,
          renderer: function (storeItem, item) {
            this.setTitle("毛利");
            this
              .update(me.formatMoney(storeItem
                .get("毛利")));
          }
        }
      }]
    });
    return me.__saleChart;
  },

  getPurchaseChart: function () {
    var me = this;
    if (me.__purchaseChart) {
      return me.__purchaseChart;
    }

    var modelName = "purchaseChart";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["purchaseMoney", "month"]
    });
    var store = Ext.create("Ext.data.Store", {
      model: modelName,
      data: []
    });

    me.__purchaseChart = Ext.create("Ext.chart.Chart", {
      theme: "Green",
      animate: true,
      store: store,
      axes: [{
        title: "采购金额",
        type: "Numeric",
        position: "left",
        grid: true,
        fields: ["purchaseMoney"],
        label: {
          renderer: function (v) {
            return me.formatMoney2(v);
          }
        }
      }, {
        type: "Category",
        position: "bottom",
        fields: ["month"]
      }],
      series: [{
        type: "line",
        xField: "month",
        yField: "purchaseMoney",
        highlight: {
          size: 7,
          radius: 7
        },
        tips: {
          trackMouse: true,
          width: 120,
          height: 50,
          renderer: function (storeItem, item) {
            this.setTitle("采购金额");
            this.update(me.formatMoney(storeItem
              .get("purchaseMoney")));
          }
        }
      }]
    });
    return me.__purchaseChart;
  },

  formatMoney: function (value) {
    var value = parseFloat(value);
    var format = "0,000.00";
    if (value >= 0) {
      return Ext.util.Format.number(value, format);
    } else {
      return "-" + Ext.util.Format.number(Math.abs(value), format);
    }
  },

  formatMoney2: function (value) {
    var value = parseFloat(value);
    var format = "0,000";
    if (value >= 0) {
      return Ext.util.Format.number(value, format);
    } else {
      return "-" + Ext.util.Format.number(Math.abs(value), format);
    }
  }
});
