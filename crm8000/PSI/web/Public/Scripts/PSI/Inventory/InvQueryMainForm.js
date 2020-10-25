/**
 * 库存账查询 - 主界面
 */
Ext.define("PSI.Inventory.InvQueryMainForm", {
  extend: "PSI.AFX.BaseMainExForm",

  config: {
    pExcel: null
  },

  initComponent: function () {
    var me = this;

    Ext.define("PSIWarehouse", {
      extend: "Ext.data.Model",
      fields: ["id", "code", "name", "enabled"]
    });

    Ext.define("PSIInventory", {
      extend: "Ext.data.Model",
      fields: ["id", "goodsId", "goodsCode", "goodsName", "goodsSpec",
        "unitName", "inCount", "inPrice", "inMoney", "outCount",
        "outPrice", "outMoney", "balanceCount", "balancePrice",
        "balanceMoney", "afloatCount", "afloatMoney", "afloatPrice"]
    });

    Ext.define("PSIInventoryDetail", {
      extend: "Ext.data.Model",
      fields: ["id", "goodsCode", "goodsName", "goodsSpec",
        "unitName", "inCount", "inPrice", "inMoney",
        "outCount", "outPrice", "outMoney", "balanceCount",
        "balancePrice", "balanceMoney", "bizDT",
        "bizUserName", "refType", "refNumber"]
    });

    Ext.apply(me, {
      tbar: [{
        text: "总账导出Excel",
        disabled: me.getPExcel() == "0",
        handler: me.onExcel,
        scope: me
      }, "-", {
        text: "关闭",
        handler: function () {
          me.closeWindow();
        }
      }],
      items: [{
        id: "panelQueryCmp",
        region: "north",
        border: 0,
        height: 65,
        header: false,
        collapsible: true,
        collapseMode: "mini",
        layout: {
          type: "table",
          columns: 4
        },
        items: me.getQueryCmp()
      }, {
        id: "panelWarehouse",
        region: "west",
        layout: "fit",
        border: 0,
        width: 200,
        split: true,
        collapsible: true,
        header: false,
        items: [me.getWarehouseGrid()]
      }, {
        region: "center",
        layout: "border",
        border: 0,
        items: [{
          region: "center",
          layout: "fit",
          border: 0,
          items: [me.getInventoryGrid()]
        }, {
          id: "panelDetail",
          header: {
            height: 30,
            title: me
              .formatGridHeaderTitle("明细账")
          },
          cls: "PSI",
          tools: [{
            type: "close",
            handler: function () {
              Ext.getCmp("panelDetail")
                .collapse();
            }
          }],
          region: "south",
          height: "50%",
          split: true,
          layout: "fit",
          border: 1,
          items: [me.getInventoryDetailGrid()]
        }]
      }]
    });

    me.callParent(arguments);

    me.__queryEditNameList = ["editQueryCode", "editQueryName",
      "editQuerySpec", "editQueryBrand"];

    me.refreshWarehouseGrid();
  },

  getQueryCmp: function () {
    var me = this;

    return [{
      labelWidth: 60,
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "物料编码",
      margin: "5, 0, 0, 0",
      xtype: "textfield",
      id: "editQueryCode",
      listeners: {
        specialkey: {
          fn: me.onQueryEditSpecialKey,
          scope: me
        }
      }
    }, {
      labelWidth: 60,
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "品名",
      margin: "5, 0, 0, 0",
      xtype: "textfield",
      id: "editQueryName",
      listeners: {
        specialkey: {
          fn: me.onQueryEditSpecialKey,
          scope: me
        }
      }
    }, {
      labelWidth: 60,
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "规格型号",
      margin: "5, 0, 0, 0",
      xtype: "textfield",
      id: "editQuerySpec",
      listeners: {
        specialkey: {
          fn: me.onQueryEditSpecialKey,
          scope: me
        }
      }
    }, {
      xtype: "container",
      items: [{
        text: "查询",
        iconCls: "PSI-button-refresh",
        handler: me.onQueryGoods,
        scope: me,
        width: 100,
        height: 26,
        margin: "5, 0, 0, 20",
        xtype: "button"
      }, {
        text: "清空查询条件",
        handler: me.onClearQuery,
        scope: me,
        width: 100,
        height: 26,
        margin: "5, 0, 0, 20",
        xtype: "button"
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
    }, {
      labelWidth: 60,
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "品牌",
      margin: "5, 0, 0, 0",
      xtype: "PSI_goods_brand_field",
      showModal: true,
      id: "editQueryBrand",
      listeners: {
        specialkey: {
          fn: me.onLastQueryEditSpecialKey,
          scope: me
        }
      }
    }, {
      xtype: "checkbox",
      boxLabel: "只显示有库存的物料",
      inputValue: "1",
      margin: "5 0 0 50",
      id: "editQueryHasInv",
      listeners: {
        change: {
          fn: function () {
            me.onQueryGoods();
          },
          scoep: me
        }
      }
    }];
  },

  getWarehouseGrid: function () {
    var me = this;
    if (me.__warehouseGrid) {
      return me.__warehouseGrid;
    }

    me.__warehouseGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      header: {
        height: 30,
        title: me.formatGridHeaderTitle("已经建账的仓库")
      },
      tools: [{
        type: "close",
        handler: function () {
          Ext.getCmp("panelWarehouse").collapse();
        }
      }],
      columnLines: true,
      columns: [{
        header: "仓库编码",
        dataIndex: "code",
        menuDisabled: true,
        sortable: false,
        width: 80,
        renderer: function (value, metaData, record) {
          if (parseInt(record.get("enabled")) == 1) {
            return value;
          } else {
            return "<span style='color:gray;text-decoration:line-through;'>"
              + value + "</span>";
          }
        }
      }, {
        header: "仓库名称",
        dataIndex: "name",
        menuDisabled: true,
        sortable: false,
        flex: 1,
        renderer: function (value, metaData, record) {
          if (parseInt(record.get("enabled")) == 1) {
            return value;
          } else {
            return "<span style='color:gray;text-decoration:line-through;'>"
              + value
              + "</span>"
              + "<span style='color:red;'>(已停用)</span>";
          }
        }
      }],
      store: Ext.create("Ext.data.Store", {
        model: "PSIWarehouse",
        autoLoad: false,
        data: []
      }),
      listeners: {
        select: {
          fn: me.onWarehouseGridSelect,
          scope: me
        }
      }
    });

    return me.__warehouseGrid;
  },

  refreshWarehouseGrid: function () {
    var grid = this.getWarehouseGrid();
    var el = grid.getEl() || Ext.getBody();
    el.mask(PSI.Const.LOADING);
    Ext.Ajax.request({
      url: PSI.Const.BASE_URL + "Home/Inventory/warehouseList",
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

  getInventoryGrid: function () {
    var me = this;
    if (me.__inventoryGrid) {
      return me.__inventoryGrid;
    }

    var store = Ext.create("Ext.data.Store", {
      model: "PSIInventory",
      pageSize: 20,
      remoteSort: true,
      proxy: {
        type: "ajax",
        actionMethods: {
          read: "POST"
        },
        url: PSI.Const.BASE_URL
          + "Home/Inventory/inventoryList",
        reader: {
          root: 'dataList',
          totalProperty: 'totalCount'
        }
      },
      autoLoad: false,
      data: []
    });

    store.on("beforeload", function () {
      store.proxy.extraParams = me.getInventoryGridParam();
    });

    me.__inventoryGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      header: {
        height: 30,
        title: me.formatGridHeaderTitle("总账")
      },
      viewConfig: {
        enableTextSelection: true
      },
      bbar: ["->", {
        xtype: "pagingtoolbar",
        id: "pagingToolbarInv",
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
                Ext.getCmp("pagingToolbarInv")
                  .doRefresh();
              },
              scope: me
            }
          }
        }, {
          xtype: "displayfield",
          value: "条记录"
        }],
      columnLines: true,
      columns: [{
        header: "物料编码",
        dataIndex: "goodsCode",
        menuDisabled: true,
        sortable: true
      }, {
        header: "品名",
        dataIndex: "goodsName",
        menuDisabled: true,
        sortable: false
      }, {
        header: "规格型号",
        dataIndex: "goodsSpec",
        menuDisabled: true,
        sortable: false
      }, {
        header: "单位",
        dataIndex: "unitName",
        menuDisabled: true,
        sortable: false,
        width: 80
      }, {
        header: "在途数量",
        align: "right",
        dataIndex: "afloatCount",
        menuDisabled: true,
        sortable: true
      }, {
        header: "在途单价",
        align: "right",
        xtype: "numbercolumn",
        dataIndex: "afloatPrice",
        menuDisabled: true,
        sortable: true
      }, {
        header: "在途金额",
        align: "right",
        xtype: "numbercolumn",
        dataIndex: "afloatMoney",
        menuDisabled: true,
        sortable: true
      }, {
        header: "入库数量",
        align: "right",
        dataIndex: "inCount",
        menuDisabled: true,
        sortable: true
      }, {
        header: "平均入库成本单价",
        align: "right",
        xtype: "numbercolumn",
        dataIndex: "inPrice",
        menuDisabled: true,
        sortable: true,
        width: 130
      }, {
        header: "入库成本总金额",
        align: "right",
        xtype: "numbercolumn",
        dataIndex: "inMoney",
        menuDisabled: true,
        sortable: true,
        width: 120
      }, {
        header: "出库数量",
        align: "right",
        dataIndex: "outCount",
        menuDisabled: true,
        sortable: true
      }, {
        header: "平均出库成本单价",
        align: "right",
        xtype: "numbercolumn",
        dataIndex: "outPrice",
        menuDisabled: true,
        sortable: true,
        width: 130
      }, {
        header: "出库成本总金额",
        align: "right",
        xtype: "numbercolumn",
        dataIndex: "outMoney",
        menuDisabled: true,
        sortable: true,
        width: 120
      }, {
        header: "余额数量",
        align: "right",
        dataIndex: "balanceCount",
        menuDisabled: true,
        sortable: true
      }, {
        header: "余额平均单价",
        align: "right",
        xtype: "numbercolumn",
        dataIndex: "balancePrice",
        menuDisabled: true,
        sortable: true
      }, {
        header: "余额总金额",
        align: "right",
        xtype: "numbercolumn",
        dataIndex: "balanceMoney",
        menuDisabled: true,
        sortable: true
      }],
      store: store,
      listeners: {
        select: {
          fn: me.onInventoryGridSelect,
          scope: me
        }
      }
    });

    return me.__inventoryGrid;
  },

  getWarehouseIdParam: function () {
    var item = this.getWarehouseGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      return null;
    }

    var warehouse = item[0];
    return warehouse.get("id");
  },

  getGoodsIdParam: function () {
    var item = this.getInventoryGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      return null;
    }

    var inventory = item[0];
    return inventory.get("goodsId");
  },

  getInventoryDetailGrid: function () {
    var me = this;
    if (me.__inventoryDetailGrid) {
      return me.__inventoryDetailGrid;
    }

    var store = Ext.create("Ext.data.Store", {
      model: "PSIInventoryDetail",
      pageSize: 20,
      proxy: {
        type: "ajax",
        actionMethods: {
          read: "POST"
        },
        url: PSI.Const.BASE_URL + "Home/Inventory/inventoryDetailList",
        reader: {
          root: 'details',
          totalProperty: 'totalCount'
        }
      },
      autoLoad: false,
      data: []
    });

    store.on("beforeload", function () {
      Ext.apply(store.proxy.extraParams, {
        warehouseId: me.getWarehouseIdParam(),
        goodsId: me.getGoodsIdParam(),
        dtFrom: Ext.Date.format(Ext.getCmp("dtFrom")
          .getValue(), "Y-m-d"),
        dtTo: Ext.Date.format(Ext.getCmp("dtTo")
          .getValue(), "Y-m-d")
      });
    });

    me.__inventoryDetailGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      border: 0,
      tbar: [{
        xtype: "displayfield",
        value: "业务日期 从"
      }, {
        id: "dtFrom",
        xtype: "datefield",
        format: "Y-m-d",
        width: 90
      }, {
        xtype: "displayfield",
        value: " 到 "
      }, {
        id: "dtTo",
        xtype: "datefield",
        format: "Y-m-d",
        width: 90,
        value: new Date()
      }, " ", {
        text: "查询",
        iconCls: "PSI-button-refresh",
        handler: me.onQuery,
        scope: me
      }, "->", {
        xtype: "pagingtoolbar",
        id: "pagingtoolbarDetail",
        border: 0,
        store: store
      }, "-", {
        xtype: "displayfield",
        value: "每页显示"
      }, {
        id: "comboCountPerPageDetail",
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
                .getCmp("comboCountPerPageDetail")
                .getValue();
              store.currentPage = 1;
              Ext.getCmp("pagingtoolbarDetail")
                .doRefresh();
            },
            scope: me
          }
        }
      }, {
        xtype: "displayfield",
        value: "条记录"
      }],
      columnLines: true,
      columns: [Ext.create("Ext.grid.RowNumberer", {
        text: "序号",
        width: 40
      }), {
        header: "物料编码",
        dataIndex: "goodsCode",
        menuDisabled: true,
        sortable: false
      }, {
        header: "品名",
        dataIndex: "goodsName",
        menuDisabled: true,
        sortable: false
      }, {
        header: "规格型号",
        dataIndex: "goodsSpec",
        menuDisabled: true,
        sortable: false
      }, {
        header: "单位",
        dataIndex: "unitName",
        menuDisabled: true,
        sortable: false,
        width: 80
      }, {
        header: "入库数量",
        dataIndex: "inCount",
        align: "right",
        menuDisabled: true,
        sortable: false
      }, {
        header: "入库成本单价",
        dataIndex: "inPrice",
        align: "right",
        xtype: "numbercolumn",
        menuDisabled: true,
        sortable: false
      }, {
        header: "入库成本金额",
        dataIndex: "inMoney",
        align: "right",
        xtype: "numbercolumn",
        menuDisabled: true,
        sortable: false
      }, {
        header: "出库数量",
        dataIndex: "outCount",
        align: "right",
        menuDisabled: true,
        sortable: false
      }, {
        header: "出库成本单价",
        dataIndex: "outPrice",
        align: "right",
        xtype: "numbercolumn",
        menuDisabled: true,
        sortable: false
      }, {
        header: "出库成本金额",
        dataIndex: "outMoney",
        align: "right",
        xtype: "numbercolumn",
        menuDisabled: true,
        sortable: false
      }, {
        header: "余额数量",
        dataIndex: "balanceCount",
        align: "right",
        menuDisabled: true,
        sortable: false
      }, {
        header: "余额单价",
        dataIndex: "balancePrice",
        align: "right",
        xtype: "numbercolumn",
        menuDisabled: true,
        sortable: false
      }, {
        header: "余额金额",
        dataIndex: "balanceMoney",
        align: "right",
        xtype: "numbercolumn",
        menuDisabled: true,
        sortable: false
      }, {
        header: "业务日期",
        dataIndex: "bizDT",
        menuDisabled: true,
        sortable: false,
        width: 80
      }, {
        header: "业务员",
        dataIndex: "bizUserName",
        menuDisabled: true,
        sortable: false,
        width: 80
      }, {
        header: "业务类型",
        dataIndex: "refType",
        menuDisabled: true,
        sortable: false,
        width: 120
      }, {
        header: "业务单号",
        dataIndex: "refNumber",
        menuDisabled: true,
        sortable: false,
        width: 120,
        renderer: function (value, md, record) {
          return "<a href='"
            + PSI.Const.BASE_URL
            + "Home/Bill/viewIndex?fid=2003&refType="
            + encodeURIComponent(record
              .get("refType"))
            + "&ref="
            + encodeURIComponent(record
              .get("refNumber"))
            + "' target='_blank'>" + value
            + "</a>";
        }
      }],
      store: store
    });

    var dt = new Date();
    dt.setDate(dt.getDate() - 7);
    Ext.getCmp("dtFrom").setValue(dt);

    return me.__inventoryDetailGrid;
  },

  onWarehouseGridSelect: function () {
    this.refreshInventoryGrid()
  },

  getInventoryGridParam: function () {
    var me = this;
    var item = me.getWarehouseGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      return {};
    }

    var warehouse = item[0];
    var result = {
      warehouseId: warehouse.get("id")
    };

    var code = Ext.getCmp("editQueryCode").getValue();
    if (code) {
      result.code = code;
    }

    var name = Ext.getCmp("editQueryName").getValue();
    if (name) {
      result.name = name;
    }

    var spec = Ext.getCmp("editQuerySpec").getValue();
    if (spec) {
      result.spec = spec;
    }

    var hasInv = Ext.getCmp("editQueryHasInv").getValue();
    if (hasInv) {
      result.hasInv = hasInv ? 1 : 0;
    }

    var brandId = Ext.getCmp("editQueryBrand").getIdValue();
    if (brandId) {
      result.brandId = brandId;
    }

    return result;
  },

  refreshInventoryGrid: function () {
    var me = this;
    me.getInventoryDetailGrid().getStore().removeAll();

    var item = me.getWarehouseGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      return;
    }

    var warehouse = item[0];

    var grid = me.getInventoryGrid();
    grid.setTitle(me.formatGridHeaderTitle("仓库 [" + warehouse.get("name")
      + "] 的总账"));

    grid.getStore().loadPage(1);
  },

  onInventoryGridSelect: function () {
    this.getInventoryDetailGrid().getStore().loadPage(1);
  },

  onQuery: function () {
    var dtTo = Ext.getCmp("dtTo").getValue();
    if (dtTo == null) {
      Ext.getCmp("dtTo").setValue(new Date());
    }

    var dtFrom = Ext.getCmp("dtFrom").getValue();
    if (dtFrom == null) {
      var dt = new Date();
      dt.setDate(dt.getDate() - 7);
      Ext.getCmp("dtFrom").setValue(dt);
    }

    this.getInventoryDetailGrid().getStore().loadPage(1);
  },

  onQueryEditSpecialKey: function (field, e) {
    if (e.getKey() === e.ENTER) {
      var me = this;
      var id = field.getId();
      for (var i = 0; i < me.__queryEditNameList.length - 1; i++) {
        var editorId = me.__queryEditNameList[i];
        if (id === editorId) {
          var edit = Ext.getCmp(me.__queryEditNameList[i + 1]);
          edit.focus();
          edit.setValue(edit.getValue());
        }
      }
    }
  },

  onLastQueryEditSpecialKey: function (field, e) {
    if (e.getKey() === e.ENTER) {
      this.onQueryGoods();
    }
  },

  onClearQuery: function () {
    var nameList = this.__queryEditNameList;
    for (var i = 0; i < nameList.length; i++) {
      var name = nameList[i];
      var edit = Ext.getCmp(name);
      if (edit) {
        edit.setValue(null);
      }
    }

    Ext.getCmp("editQueryHasInv").setValue(false);
    Ext.getCmp("editQueryBrand").clearIdValue();

    this.onQueryGoods();
  },

  onQueryGoods: function () {
    this.refreshInventoryGrid();
  },

  // 导出Excel
  onExcel: function () {
    var me = this;

    me.confirm("请确认是否把库存总账导出为Excel文件？<br/><br/>(数据是根据当前查询条件生成)", function () {
      var url = "Home/Inventory/exportExcel";

      var code = Ext.getCmp("editQueryCode").getValue();
      url += "?code=" + code;

      var name = Ext.getCmp("editQueryName").getValue();
      url += "&name=" + name;

      var spec = Ext.getCmp("editQuerySpec").getValue();
      url += "&spec=" + spec;

      var hasInv = Ext.getCmp("editQueryHasInv").getValue();
      url += "&hasInv=" + (hasInv ? "1" : "0");

      var brandId = Ext.getCmp("editQueryBrand").getIdValue();
      url += "&brandId=" + (brandId ? brandId : "");
      window.open(me.URL(url));
    });
  }
});
