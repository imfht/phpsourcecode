/**
 * 库存建账 - 主界面
 */
Ext.define("PSI.Inventory.InitInventoryMainForm", {
  extend: "PSI.AFX.BaseMainExForm",
  initComponent: function () {
    var me = this;

    Ext.define("PSIInitInv", {
      extend: "Ext.data.Model",
      fields: ["id", "goodsCode", "goodsName", "goodsSpec",
        "goodsCount", "goodsUnit", "goodsMoney",
        "goodsPrice", "initDate"]
    });
    var store = Ext.create("Ext.data.Store", {
      autoLoad: false,
      model: "PSIInitInv",
      data: [],
      pageSize: 20,
      proxy: {
        type: "ajax",
        actionMethods: {
          read: "POST"
        },
        url: PSI.Const.BASE_URL
          + "Home/InitInventory/initInfoList",
        reader: {
          root: 'initInfoList',
          totalProperty: 'totalCount'
        }
      },
      listeners: {
        beforeload: {
          fn: function () {
            var item = me.gridWarehouse.getSelectionModel()
              .getSelection();
            var warehouseId;
            if (item == null || item.length != 1) {
              warehouseId = null;
            }

            warehouseId = item[0].get("id");

            Ext.apply(store.proxy.extraParams, {
              warehouseId: warehouseId
            });
          },
          scope: me
        },
        load: {
          fn: function (e, records, successful) {
            if (successful) {
              me.gotoInitInvGridRecord(me.__lastId);
            }
          },
          scope: me
        }
      }
    });

    var gridInitInv = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      header: {
        height: 30,
        title: me.formatGridHeaderTitle("请选择一个仓库")
      },
      columnLines: true,
      columns: [{
        header: "物料编码",
        dataIndex: "goodsCode",
        menuDisabled: true,
        sortable: false
      }, {
        header: "品名",
        dataIndex: "goodsName",
        menuDisabled: true,
        sortable: false,
        width: 300
      }, {
        header: "规格型号",
        dataIndex: "goodsSpec",
        menuDisabled: true,
        sortable: false,
        width: 200
      }, {
        header: "期初数量",
        dataIndex: "goodsCount",
        menuDisabled: true,
        sortable: false,
        align: "right"
      }, {
        header: "单位",
        dataIndex: "goodsUnit",
        menuDisabled: true,
        sortable: false,
        width: 50
      }, {
        header: "期初金额",
        dataIndex: "goodsMoney",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn"
      }, {
        header: "期初单价",
        dataIndex: "goodsPrice",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn"
      }, {
        header: "建账日期",
        dataIndex: "initDate",
        menuDisabled: true,
        sortable: false,
        width: 80
      }],
      store: store,
      bbar: ["->", {
        id: "pagingToolbar",
        border: 0,
        xtype: "pagingtoolbar",
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
                Ext.getCmp("pagingToolbar")
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
    this.gridInitInv = gridInitInv;

    Ext.define("PSIWarehouse", {
      extend: "Ext.data.Model",
      fields: ["id", "code", "name", "inited"]
    });

    var gridWarehouse = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      header: {
        height: 30,
        title: me.formatGridHeaderTitle("仓库")
      },
      forceFit: true,
      columnLines: true,
      columns: [{
        header: "仓库编码",
        dataIndex: "code",
        menuDisabled: true,
        sortable: false,
        width: 70
      }, {
        header: "仓库名称",
        dataIndex: "name",
        flex: 1,
        menuDisabled: true,
        sortable: false
      }, {
        header: "建账完毕",
        dataIndex: "inited",
        menuDisabled: true,
        sortable: false,
        width: 80,
        renderer: function (value) {
          return value == 1
            ? "完毕"
            : "<span style='color:red'>未完</span>";
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
    me.gridWarehouse = gridWarehouse;

    Ext.apply(me, {
      tbar: [{
        text: "录入建账数据",
        scope: me,
        handler: me.onInitInv
      }, "-", {
        text: "刷新",
        scope: me,
        handler: function () {
          me.freshInvGrid();
        }
      }, "-", {
        text: "标记建账完毕",
        scope: me,
        handler: me.onFinish
      }, "-", {
        text: "取消建账完毕标记",
        scope: me,
        handler: me.onCancel
      }, "-", {
        text: "帮助",
        handler: function () {
          window.open(me
            .URL("/Home/Help/index?t=initInv"));
        }
      }, "-", {
        text: "关闭",
        handler: function () {
          me.closeWindow();
        }
      }],
      items: [{
        region: "north",
        height: 2,
        border: 0,
        bodyStyle: "background-color:#f5f5f5"
      }, {
        region: "west",
        xtype: "panel",
        layout: "fit",
        border: 0,
        width: 300,
        minWidth: 200,
        split: true,
        items: [gridWarehouse]
      }, {
        region: "center",
        xtype: "panel",
        layout: "fit",
        border: 0,
        items: [gridInitInv]
      }]
    });

    me.callParent(arguments);

    me.freshWarehouseGrid();
  },
  freshWarehouseGrid: function () {
    var grid = this.gridWarehouse;
    var el = grid.getEl() || Ext.getBody();
    el.mask(PSI.Const.LOADING);
    Ext.Ajax.request({
      url: PSI.Const.BASE_URL
        + "Home/InitInventory/warehouseList",
      method: "POST",
      callback: function (options, success, response) {
        var store = grid.getStore();

        store.removeAll();

        if (success) {
          var data = Ext.JSON.decode(response.responseText);
          store.add(data);
          grid.getSelectionModel().select(0);
        }

        el.unmask();
      }
    });
  },
  onWarehouseGridSelect: function () {
    this.freshInvGrid();
  },
  freshInvGrid: function (id) {
    var me = this;
    var grid = me.gridInitInv;
    var item = me.gridWarehouse.getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      grid.setTitle(me.formatGridHeaderTitle("请选一个仓库"));
      return;
    }

    var warehouse = item[0];
    grid.setTitle(me.formatGridHeaderTitle("仓库: " + warehouse.get("name")));

    me.__lastId = id;
    Ext.getCmp("pagingToolbar").doRefresh()
  },

  onInitInv: function () {
    var item = this.gridWarehouse.getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      PSI.MsgBox.showInfo("请选择要建账的仓库");
      return;
    }
    var warehouse = item[0];

    if (warehouse.get("inited") == 1) {
      PSI.MsgBox.showInfo("仓库[" + warehouse.get("name") + "]已经建账完毕");
      return;
    }

    var form = Ext.create("PSI.Inventory.InitInventoryEditForm", {
      warehouse: warehouse
    });
    form.show();
  },
  onFinish: function () {
    var me = this;
    var item = this.gridWarehouse.getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      PSI.MsgBox.showInfo("请选择要标记的仓库");
      return;
    }
    var warehouse = item[0];
    if (warehouse.get("inited") == 1) {
      PSI.MsgBox.showInfo("仓库[" + warehouse.get("name") + "]已经标记建账完毕");
      return;
    }

    PSI.MsgBox.confirm("请确认是否给仓库[" + warehouse.get("name") + "]标记建账完毕?",
      function () {
        var el = Ext.getBody();
        el.mask(PSI.Const.SAVING);
        Ext.Ajax.request({
          url: PSI.Const.BASE_URL
            + "Home/InitInventory/finish",
          params: {
            warehouseId: warehouse.get("id")
          },
          method: "POST",
          callback: function (options, success, response) {
            el.unmask();
            if (success) {
              var data = Ext.JSON
                .decode(response.responseText);
              if (data.success) {
                PSI.MsgBox.showInfo("成功标记建账完毕",
                  function () {
                    me.freshWarehouseGrid();
                  });
              } else {
                PSI.MsgBox.showInfo(data.msg);
              }
            } else {
              PSI.MsgBox.showInfo("网络错误");
            }
          }
        });
      });
  },
  onCancel: function () {
    var me = this;
    var item = this.gridWarehouse.getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      PSI.MsgBox.showInfo("请选择要取消建账的仓库");
      return;
    }
    var warehouse = item[0];

    if (warehouse.get("inited") == 0) {
      PSI.MsgBox.showInfo("仓库[" + warehouse.get("name") + "]没有标记建账完毕");
      return;
    }

    PSI.MsgBox.confirm("请确认是否取消仓库[" + warehouse.get("name") + "]的建账完毕标志?",
      function () {
        var el = Ext.getBody();
        el.mask(PSI.Const.SAVING);
        Ext.Ajax.request({
          url: PSI.Const.BASE_URL
            + "Home/InitInventory/cancel",
          params: {
            warehouseId: warehouse.get("id")
          },
          method: "POST",
          callback: function (options, success, response) {
            el.unmask();
            if (success) {
              var data = Ext.JSON
                .decode(response.responseText);
              if (data.success) {
                PSI.MsgBox.showInfo("成功取消建账标志",
                  function () {
                    me.freshWarehouseGrid();
                  });
              } else {
                PSI.MsgBox.showInfo(data.msg);
              }
            } else {
              PSI.MsgBox.showInfo("网络错误");
            }
          }
        });
      });
  },
  gotoInitInvGridRecord: function (id) {
    var me = this;
    var grid = me.gridInitInv;
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
  }
});
