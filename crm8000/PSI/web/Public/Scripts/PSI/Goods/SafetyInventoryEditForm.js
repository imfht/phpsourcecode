/**
 * 商品安全库存设置界面
 */
Ext.define("PSI.Goods.SafetyInventoryEditForm", {
  extend: "PSI.AFX.BaseDialogForm",

	/**
	 * 初始化组件
	 */
  initComponent: function () {
    var me = this;
    var entity = me.getEntity();

    Ext.apply(me, {
      header: {
        title: me.formatTitle("设置商品安全库存"),
        height: 40,
        iconCls: "PSI-button-commit"
      },
      modal: true,
      onEsc: Ext.emptyFn,
      width: 620,
      height: 400,
      layout: "border",
      items: [{
        region: "center",
        border: 0,
        bodyPadding: 10,
        layout: "fit",
        items: [me.getGoodsGrid()]
      }, {
        region: "north",
        border: 0,
        layout: {
          type: "table",
          columns: 2
        },
        height: 40,
        bodyPadding: 10,
        items: [{
          xtype: "hidden",
          id: "hiddenId",
          name: "id",
          value: entity.get("id")
        }, {
          id: "editRef",
          fieldLabel: "商品",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: ":",
          xtype: "displayfield",
          value: entity.get("code") + " "
            + entity.get("name") + " "
            + entity.get("spec")
        }]
      }],
      buttons: [{
        text: "保存",
        iconCls: "PSI-button-ok",
        handler: me.onOK,
        scope: me,
        id: "buttonSave"
      }, {
        text: "取消",
        handler: function () {
          PSI.MsgBox.confirm("请确认是否取消当前操作？",
            function () {
              me.close();
            });
        },
        scope: me,
        id: "buttonCancel"
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

    var el = me.getEl() || Ext.getBody();
    el.mask(PSI.Const.LOADING);
    Ext.Ajax.request({
      url: PSI.Const.BASE_URL + "Home/Goods/siInfo",
      params: {
        id: Ext.getCmp("hiddenId").getValue()
      },
      method: "POST",
      callback: function (options, success, response) {
        el.unmask();

        if (success) {
          var data = Ext.JSON.decode(response.responseText);

          var store = me.getGoodsGrid().getStore();
          store.removeAll();
          store.add(data);
        } else {
          PSI.MsgBox.showInfo("网络错误")
        }
      }
    });
  },

  onOK: function () {
    var me = this;
    Ext.getBody().mask("正在保存中...");
    Ext.Ajax.request({
      url: PSI.Const.BASE_URL + "Home/Goods/editSafetyInventory",
      method: "POST",
      params: {
        jsonStr: me.getSaveData()
      },
      callback: function (options, success, response) {
        Ext.getBody().unmask();

        if (success) {
          var data = Ext.JSON.decode(response.responseText);
          if (data.success) {
            PSI.MsgBox.showInfo("成功保存数据", function () {
              me.close();
              me.getParentForm().onGoodsSelect();
            });
          } else {
            PSI.MsgBox.showInfo(data.msg);
          }
        }
      }
    });
  },

  getGoodsGrid: function () {
    var me = this;
    if (me.__goodsGrid) {
      return me.__goodsGrid;
    }
    var modelName = "PSIGoodsSafetyInventory_EditForm";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["warehouseId", "warehouseCode", "warehouseName",
        "safetyInventory", "unitName", "inventoryUpper"]
    });
    var store = Ext.create("Ext.data.Store", {
      autoLoad: false,
      model: modelName,
      data: []
    });

    me.__cellEditing = Ext.create("PSI.UX.CellEditing", {
      clicksToEdit: 1,
      listeners: {
        edit: {
          fn: me.cellEditingAfterEdit,
          scope: me
        }
      }
    });

    me.__goodsGrid = Ext.create("Ext.grid.Panel", {
      viewConfig: {
        enableTextSelection: true
      },
      plugins: [me.__cellEditing],
      columnLines: true,
      columns: [{
        header: "仓库编码",
        dataIndex: "warehouseCode",
        width: 100,
        menuDisabled: true,
        sortable: false
      }, {
        header: "仓库名称",
        dataIndex: "warehouseName",
        width: 120,
        menuDisabled: true,
        sortable: false
      }, {
        header: "库存上限",
        dataIndex: "inventoryUpper",
        width: 120,
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        format: "0",
        editor: {
          xtype: "numberfield",
          allowDecimals: false,
          hideTrigger: true
        }
      }, {
        header: "安全库存量",
        dataIndex: "safetyInventory",
        width: 120,
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        format: "0",
        editor: {
          xtype: "numberfield",
          allowDecimals: false,
          hideTrigger: true
        }
      }, {
        header: "计量单位",
        dataIndex: "unitName",
        width: 80,
        menuDisabled: true,
        sortable: false
      }],
      store: store
    });

    return me.__goodsGrid;
  },

  cellEditingAfterEdit: function (editor, e) {
  },

  getSaveData: function () {
    var result = {
      id: Ext.getCmp("hiddenId").getValue(),
      items: []
    };

    var store = this.getGoodsGrid().getStore();
    for (var i = 0; i < store.getCount(); i++) {
      var item = store.getAt(i);
      result.items.push({
        warehouseId: item.get("warehouseId"),
        invUpper: item.get("inventoryUpper"),
        si: item.get("safetyInventory")
      });
    }

    return Ext.JSON.encode(result);
  }
});
