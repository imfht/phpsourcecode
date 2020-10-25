/**
 * 库存建账 - 编辑界面
 */
Ext.define("PSI.Inventory.InitInventoryEditForm", {
  extend: "Ext.window.Window",
  config: {
    warehouse: null
  },

  initComponent: function () {
    var me = this;
    var warehouse = me.getWarehouse();
    Ext.define("PSIGoodsCategory", {
      extend: "Ext.data.Model",
      fields: ["id", "name"]
    });
    var storeGoodsCategory = Ext.create("Ext.data.Store", {
      autoLoad: false,
      model: "PSIGoodsCategory",
      data: []
    });
    me.storeGoodsCategory = storeGoodsCategory;

    var logoHtml = "<img style='float:left;margin:10px 10px 0px 10px;width:48px;height:48px;' src='"
      + PSI.Const.BASE_URL
      + "Public/Images/edit-form-update.png'></img>"
      + "<h2 style='margin:20px'>建账仓库：<span style='color:#cf1322'>"
      + warehouse.get("name") + "</span></h2>";

    Ext.apply(me, {
      title: PSI.Const.PROD_NAME,
      modal: true,
      onEsc: Ext.emptyFn,
      width: 1000,
      height: 600,
      maximized: true,
      layout: "fit",
      items: [{
        id: "editForm",
        layout: "border",
        border: 0,
        bodyPadding: 5,
        items: [{
          xtype: "panel",
          region: "north",
          height: 70,
          border: 0,
          html: logoHtml
        }, {
          xtype: "panel",
          region: "center",
          layout: "border",
          border: 0,
          items: [{
            xtype: "panel",
            region: "center",
            layout: "border",
            border: 0,
            items: [{
              xtype: "panel",
              region: "north",
              height: 40,
              layout: "hbox",
              border: 0,
              items: [{
                xtype: "displayfield",
                margin: 5,
                value: "物料分类"
              }, {
                id: "comboboxGoodsCategory",
                cls: "PSI-toolbox",
                margin: 5,
                xtype: "combobox",
                flex: 1,
                store: storeGoodsCategory,
                editable: false,
                displayField: "name",
                valueField: "id",
                queryMode: "local",
                listeners: {
                  change: function () {
                    me.getGoods();
                  }
                }
              }]
            }, {
              xtype: "panel",
              region: "center",
              layout: "fit",
              items: [this.getGoodsGrid()]
            }]
          }, {
            xtype: "panel",
            region: "east",
            width: 400,
            split: true,
            items: [{
              xtype: "form",
              layout: "form",
              border: 0,
              fieldDefaults: {
                labelWidth: 60,
                labelAlign: "right",
                labelSeparator: "",
                msgTarget: 'side'
              },
              bodyPadding: 5,
              defaultType: 'textfield',
              items: [{
                id: "editGoodsCode",
                fieldLabel: "物料编码",
                xtype: "displayfield"
              }, {
                id: "editGoodsName",
                fieldLabel: "品名",
                xtype: "displayfield"
              }, {
                id: "editGoodsSpec",
                fieldLabel: "规格型号",
                xtype: "displayfield"
              }, {
                id: "editGoodsCount",
                fieldLabel: "期初数量",
                beforeLabelTextTpl: PSI.Const.REQUIRED,
                xtype: "numberfield",
                allowDecimals: PSI.Const.GC_DEC_NUMBER > 0,
                decimalPrecision: PSI.Const.GC_DEC_NUMBER,
                minValue: 0,
                hideTrigger: true
              }, {
                id: "editUnit",
                xtype: "displayfield",
                fieldLabel: "计量单位",
                value: ""
              }, {
                id: "editGoodsMoney",
                fieldLabel: "期初金额",
                xtype: "numberfield",
                allowDecimals: true,
                hideTrigger: true,
                beforeLabelTextTpl: PSI.Const.REQUIRED
              }, {
                id: "editGoodsPrice",
                fieldLabel: "期初单价",
                xtype: "displayfield"
              }]
            }, {
              xtype: "container",
              layout: "hbox",
              items: [{
                xtype: "container",
                flex: 1
              }, {
                id: "buttonSubmit",
                xtype: "button",
                height: 36,
                text: "保存当前物料的建账信息",
                iconCls: "PSI-button-ok",
                flex: 2,
                handler: me.onSave,
                scope: me
              }, {
                xtype: "container",
                flex: 1
              }]
            }, {
              xtype: "container",
              layout: "hbox",
              margin: 10,
              items: [{
                xtype: "container",
                flex: 1
              }, {
                xtype: "checkbox",
                id: "checkboxGotoNext",
                checked: true,
                fieldLabel: "保存后自动跳转到下一条记录",
                labelWidth: 180,
                labelSeparator: ""
              }, {
                xtype: "container",
                flex: 1
              }]
            }, {
              fieldLabel: "说明",
              xtype: "displayfield",
              labelWidth: 60,
              labelAlign: "right",
              labelSeparator: "",
              value: "如果期初数量设置为0，就会清除该物料的建账记录"
            }]
          }]
        }]
      }],
      buttons: [{
        text: "关闭",
        handler: function () {
          me.close();
        }
      }],
      listeners: {
        show: {
          fn: me.onWndShow,
          scope: me
        },
        close: {
          fn: me.onWndClose,
          scope: me
        }
      }
    });
    me.callParent(arguments);
    Ext.getCmp("editGoodsCount").on("specialkey", function (field, e) {
      if (e.getKey() == e.ENTER) {
        Ext.getCmp("editGoodsMoney").focus();
      }
    });
    Ext.getCmp("editGoodsMoney").on("specialkey", function (field, e) {
      if (e.getKey() == e.ENTER) {
        Ext.getCmp("buttonSubmit").focus();
      }
    });
    me.getGoodsCategories();
  },

  onWindowBeforeUnload: function (e) {
    return (window.event.returnValue = e.returnValue = '确认离开当前页面？');
  },

  onWndClose: function () {
    var me = this;

    Ext.get(window).un('beforeunload', me.onWindowBeforeUnload);
  },

  onWndShow: function () {
    var me = this;

    Ext.get(window).on('beforeunload', me.onWindowBeforeUnload);
  },

  getGoodsGrid: function () {
    var me = this;
    if (me.__gridGoods) {
      return me.__gridGoods;
    }

    Ext.define("PSIInitInvGoods", {
      extend: "Ext.data.Model",
      fields: ["id", "goodsCode", "goodsName", "goodsSpec",
        "goodsCount", "unitName", "goodsMoney",
        "goodsPrice", "initDate"]
    });
    var store = Ext.create("Ext.data.Store", {
      autoLoad: false,
      model: "PSIInitInvGoods",
      data: [],
      pageSize: 20,
      proxy: {
        type: "ajax",
        actionMethods: {
          read: "POST"
        },
        url: PSI.Const.BASE_URL
          + "Home/InitInventory/goodsList",
        reader: {
          root: 'goodsList',
          totalProperty: 'totalCount'
        }
      },
      listeners: {
        beforeload: {
          fn: function () {
            var comboboxGoodsCategory = Ext
              .getCmp("comboboxGoodsCategory");
            var categoryId = comboboxGoodsCategory
              .getValue();
            var warehouseId = this.getWarehouse().get("id");
            Ext.apply(store.proxy.extraParams, {
              categoryId: categoryId,
              warehouseId: warehouseId
            });
          },
          scope: me
        },
        load: {
          fn: function (e, records, successful) {
            if (successful) {
              me.getGoodsGrid().getSelectionModel()
                .select(0);
              Ext.getCmp("editGoodsCount").focus();
            }
          },
          scope: me
        }
      }
    });
    me.__gridGoods = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      border: 0,
      columnLines: true,
      columns: [Ext.create("Ext.grid.RowNumberer", {
        text: "序号",
        width: 50
      }), {
        header: "物料编码",
        dataIndex: "goodsCode",
        menuDisabled: true,
        sortable: false,
        width: 80
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
        width: 200
      }, {
        header: "期初数量",
        dataIndex: "goodsCount",
        menuDisabled: true,
        sortable: false,
        align: "right"
      }, {
        header: "单位",
        dataIndex: "unitName",
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
      }],
      bbar: [{
        id: "_pagingToolbar",
        border: 0,
        xtype: "pagingtoolbar",
        store: store
      }, "-", {
        xtype: "displayfield",
        value: "每页显示"
      }, {
        id: "_comboCountPerPage",
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
                .getCmp("_comboCountPerPage")
                .getValue();
              store.currentPage = 1;
              Ext.getCmp("_pagingToolbar")
                .doRefresh();
            },
            scope: me
          }
        }
      }, {
        xtype: "displayfield",
        value: "条记录"
      }],
      store: store,
      listeners: {
        select: {
          fn: me.onGoodsGridSelect,
          scope: me
        }
      }
    });
    return me.__gridGoods;
  },
  getGoodsCategories: function () {
    var store = this.storeGoodsCategory;
    var el = Ext.getBody();
    el.mask(PSI.Const.LOADING);
    Ext.Ajax.request({
      url: PSI.Const.BASE_URL
        + "Home/InitInventory/goodsCategoryList",
      method: "POST",
      callback: function (options, success, response) {
        store.removeAll();
        if (success) {
          var data = Ext.JSON.decode(response.responseText);
          store.add(data);
        }

        el.unmask();
      }
    });
  },
  getGoods: function () {
    var me = this;
    me.getGoodsGrid().getStore().currentPage = 1;
    Ext.getCmp("_pagingToolbar").doRefresh();
  },
  onGoodsGridSelect: function () {
    var me = this;
    var grid = me.getGoodsGrid();
    var item = grid.getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      return;
    }

    var goods = item[0];
    Ext.getCmp("editGoodsCode").setValue(goods.get("goodsCode"));
    Ext.getCmp("editGoodsName").setValue(goods.get("goodsName"));
    Ext.getCmp("editGoodsSpec").setValue(goods.get("goodsSpec"));
    Ext.getCmp("editUnit").setValue("<strong>" + goods.get("unitName")
      + "</strong>");
    var goodsCount = goods.get("goodsCount");
    if (goodsCount == "0") {
      Ext.getCmp("editGoodsCount").setValue(null);
      Ext.getCmp("editGoodsMoney").setValue(null);
      Ext.getCmp("editGoodsPrice").setValue(null);
    } else {
      Ext.getCmp("editGoodsCount").setValue(goods.get("goodsCount"));
      Ext.getCmp("editGoodsMoney").setValue(goods.get("goodsMoney"));
      Ext.getCmp("editGoodsPrice").setValue(goods.get("goodsPrice"));
    }
  },
  updateAfterSave: function (goods) {
    goods.set("goodsCount", Ext.getCmp("editGoodsCount").getValue());
    goods.set("goodsMoney", Ext.getCmp("editGoodsMoney").getValue());
    var cnt = Ext.getCmp("editGoodsCount").getValue();
    if (cnt == 0) {
      goods.set("goodsPrice", null);
    } else {
      var p = Ext.getCmp("editGoodsMoney").getValue()
        / Ext.getCmp("editGoodsCount").getValue();
      p = Ext.Number.toFixed(p, 2);
      goods.set("goodsPrice", p);
    }
    this.getGoodsGrid().getStore().commitChanges();
    Ext.getCmp("editGoodsPrice").setValue(goods.get("goodsPrice"));
  },
  onSave: function () {
    var me = this;
    var grid = me.getGoodsGrid();
    var item = grid.getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      PSI.MsgBox.showInfo("请选择一个建账物料");
      return;
    }

    var goods = item[0];
    var goodsCount = Ext.getCmp("editGoodsCount").getValue();
    var goodsMoney = Ext.getCmp("editGoodsMoney").getValue();
    var el = Ext.getBody();
    el.mask(PSI.Const.SAVING);
    Ext.Ajax.request({
      url: PSI.Const.BASE_URL
        + "Home/InitInventory/commitInitInventoryGoods",
      params: {
        goodsId: goods.get("id"),
        goodsCount: goodsCount,
        goodsMoney: goodsMoney,
        warehouseId: me.getWarehouse().get("id")
      },
      method: "POST",
      callback: function (options, success, response) {
        el.unmask();
        if (success) {
          var result = Ext.JSON.decode(response.responseText);
          if (result.success) {
            me.updateAfterSave(goods);
            if (!Ext.getCmp("checkboxGotoNext").getValue()) {
              PSI.MsgBox.showInfo("数据成功保存");
            } else {
              me.gotoNext();
            }
          } else {
            PSI.MsgBox.showInfo(result.msg, function () {
              Ext.getCmp("editGoodsCount")
                .focus();
            });
          }
        }
      }
    });
  },
  gotoNext: function () {
    var me = this;
    if (!Ext.getCmp("checkboxGotoNext").getValue()) {
      return;
    }
    var grid = me.getGoodsGrid();
    var hasNext = grid.getSelectionModel().selectNext();
    if (!hasNext) {
      Ext.getCmp("_pagingToolbar").moveNext();
    }
    var editCount = Ext.getCmp("editGoodsCount");
    editCount.focus();
  }
});
