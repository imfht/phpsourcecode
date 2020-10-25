//
// 拆分单 - 新建或编辑页面
//
Ext.define("PSI.WSP.WSPEditForm", {
  extend: "PSI.AFX.BaseDialogForm",

  initComponent: function () {
    var me = this;
    me.__readonly = false;
    var entity = me.getEntity();
    me.adding = entity == null;

    var title = entity == null ? "新建拆分单" : "编辑拆分单";
    title = me.formatTitle(title);
    var iconCls = entity == null ? "PSI-button-add" : "PSI-button-edit";

    Ext.apply(me, {
      header: {
        title: title,
        height: 40,
        iconCls: iconCls
      },
      maximized: true,
      width: 1000,
      height: 600,
      layout: "border",
      tbar: [{
        text: "保存",
        iconCls: "PSI-button-ok",
        handler: me.onOK,
        scope: me,
        id: "PSI_WSP_WSPEditForm_buttonSave"
      }, "-", {
        text: "取消",
        handler: function () {
          if (me.__readonly) {
            me.close();
            return;
          }
          PSI.MsgBox.confirm("请确认是否取消当前操作?", function () {
            me.close();
          });
        },
        scope: me,
        id: "PSI_WSP_WSPEditForm_buttonCancel"
      }, "->", {
        text: "表单通用操作帮助",
        iconCls: "PSI-help",
        handler: function () {
          window.open(me.URL("Home/Help/index?t=commBill"));
        }
      }, "-", {
        fieldLabel: "快捷访问",
        labelSeparator: "",
        margin: "5 5 5 0",
        cls: "PSI-toolbox",
        labelAlign: "right",
        labelWidth: 50,
        emptyText: "双击此处弹出选择框",
        xtype: "psi_mainmenushortcutfield"
      }],
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
          columns: 4
        },
        height: 60,
        bodyPadding: 10,
        items: [{
          xtype: "hidden",
          id: "PSI_WSP_WSPEditForm_hiddenId",
          name: "id",
          value: entity == null ? null : entity
            .get("id")
        }, {
          id: "PSI_WSP_WSPEditForm_editRef",
          fieldLabel: "单号",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: "",
          xtype: "displayfield",
          value: "<span style='color:red'>保存后自动生成</span>"
        }, {
          id: "PSI_WSP_WSPEditForm_editBizDT",
          fieldLabel: "业务日期",
          allowBlank: false,
          blankText: "没有输入业务日期",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: "",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          xtype: "datefield",
          format: "Y-m-d",
          value: new Date(),
          name: "bizDT",
          listeners: {
            specialkey: {
              fn: me.onEditBizDTSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_WSP_WSPEditForm_editFromWarehouse",
          fieldLabel: "仓库",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: "",
          xtype: "psi_warehousefield",
          fid: "2033",
          allowBlank: false,
          blankText: "没有输入仓库",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          listeners: {
            specialkey: {
              fn: me.onEditFromWarehouseSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_WSP_WSPEditForm_editToWarehouse",
          fieldLabel: "拆分后调入仓库",
          labelWidth: 120,
          labelAlign: "right",
          labelSeparator: "",
          xtype: "psi_warehousefield",
          fid: "2033",
          allowBlank: false,
          blankText: "没有输入拆分后调入仓库",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          listeners: {
            specialkey: {
              fn: me.onEditToWarehouseSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_WSP_WSPEditForm_editBizUser",
          fieldLabel: "业务员",
          xtype: "psi_userfield",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: "",
          allowBlank: false,
          blankText: "没有输入业务员",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          listeners: {
            specialkey: {
              fn: me.onEditBizUserSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_WSP_WSPEditForm_editBillMemo",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: "",
          fieldLabel: "备注",
          xtype: "textfield",
          colspan: 3,
          width: 710,
          listeners: {
            specialkey: {
              fn: me.onEditBillMemoSpecialKey,
              scope: me
            }
          }
        }]
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

    me.hiddenId = Ext.getCmp("PSI_WSP_WSPEditForm_hiddenId");
    me.editRef = Ext.getCmp("PSI_WSP_WSPEditForm_editRef");
    me.editBizDT = Ext.getCmp("PSI_WSP_WSPEditForm_editBizDT");
    me.editFromWarehouse = Ext
      .getCmp("PSI_WSP_WSPEditForm_editFromWarehouse");
    me.editToWarehouse = Ext.getCmp("PSI_WSP_WSPEditForm_editToWarehouse");
    me.editBizUser = Ext.getCmp("PSI_WSP_WSPEditForm_editBizUser");
    me.editBillMemo = Ext.getCmp("PSI_WSP_WSPEditForm_editBillMemo");

    me.buttonSave = Ext.getCmp("PSI_WSP_WSPEditForm_buttonSave");
    me.buttonCancel = Ext.getCmp("PSI_WSP_WSPEditForm_buttonCancel");
    me.columnActionDelete = Ext
      .getCmp("PSI_WSP_WSPEditForm_columnActionDelete");
    me.columnActionAdd = Ext.getCmp("PSI_WSP_WSPEditForm_columnActionAdd");
    me.columnActionAppend = Ext
      .getCmp("PSI_WSP_WSPEditForm_columnActionAppend");
  },

  onWindowBeforeUnload: function (e) {
    return (window.event.returnValue = e.returnValue = '确认离开当前页面？');
  },

  onWndClose: function () {
    // 加上这个调用是为了解决 #IMQB2 - https://gitee.com/crm8000/PSI/issues/IMQB2
    // 这个只是目前的临时应急方法，实现的太丑陋了
    Ext.WindowManager.hideAll();

    Ext.get(window).un('beforeunload', this.onWindowBeforeUnload);
  },

  onWndShow: function () {
    Ext.get(window).on('beforeunload', this.onWindowBeforeUnload);

    var me = this;
    var el = me.getEl() || Ext.getBody();
    el.mask(PSI.Const.LOADING);
    Ext.Ajax.request({
      url: me.URL("Home/WSP/wspBillInfo"),
      params: {
        id: me.hiddenId.getValue()
      },
      method: "POST",
      callback: function (options, success, response) {
        el.unmask();

        if (success) {
          var data = me.decodeJSON(response.responseText);

          if (data.ref) {
            me.editRef.setValue(data.ref);
            me.editBillMemo.setValue(data.billMemo);
          }

          me.editBizUser.setIdValue(data.bizUserId);
          me.editBizUser.setValue(data.bizUserName);
          if (data.bizDT) {
            me.editBizDT.setValue(data.bizDT);
          }
          if (data.fromWarehouseId) {
            me.editFromWarehouse
              .setIdValue(data.fromWarehouseId);
            me.editFromWarehouse
              .setValue(data.fromWarehouseName);
          }
          if (data.toWarehouseId) {
            me.editToWarehouse
              .setIdValue(data.toWarehouseId);
            me.editToWarehouse
              .setValue(data.toWarehouseName);
          }

          var store = me.getGoodsGrid().getStore();
          store.removeAll();
          if (data.items) {
            store.add(data.items);
          }
          if (store.getCount() == 0) {
            store.add({});
          }

          if (data.billStatus && data.billStatus != 0) {
            me.setBillReadonly();
          }

          me.editFromWarehouse.focus();

        } else {
          me.showInfo("网络错误")
        }
      }
    });
  },

  onOK: function () {
    var me = this;

    var fromWarehouseId = me.editFromWarehouse.getIdValue();
    if (!fromWarehouseId) {
      me.showInfo("没有输入仓库", function () {
        me.editFromWarehouse.focus();
      });
      return;
    }

    var toWarehouseId = me.editToWarehouse.getIdValue();
    if (!toWarehouseId) {
      me.showInfo("没有输入拆分后调入仓库", function () {
        me.editToWarehouse.focus();
      });
      return;
    }

    Ext.getBody().mask("正在保存中...");
    Ext.Ajax.request({
      url: PSI.Const.BASE_URL + "Home/WSP/editWSPBill",
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
              me.getParentForm().refreshMainGrid(data.id);
            });
          } else {
            PSI.MsgBox.showInfo(data.msg);
          }
        }
      }
    });
  },

  onEditBizDTSpecialKey: function (field, e) {
    var me = this;
    if (e.getKey() == e.ENTER) {
      me.editFromWarehouse.focus();
    }
  },

  onEditFromWarehouseSpecialKey: function (field, e) {
    var me = this;
    if (e.getKey() == e.ENTER) {
      me.editToWarehouse.focus();
    }
  },

  onEditToWarehouseSpecialKey: function (field, e) {
    var me = this;
    if (e.getKey() == e.ENTER) {
      me.editBizUser.focus();
    }
  },

  onEditBizUserSpecialKey: function (field, e) {
    var me = this;
    if (e.getKey() == e.ENTER) {
      me.editBillMemo.focus();
    }
  },

  onEditBillMemoSpecialKey: function (field, e) {
    if (this.__readonly) {
      return;
    }

    if (e.getKey() == e.ENTER) {
      var me = this;
      var store = me.getGoodsGrid().getStore();
      if (store.getCount() == 0) {
        store.add({});
      }
      me.getGoodsGrid().focus();
      me.__cellEditing.startEdit(0, 1);
    }
  },

  getGoodsGrid: function () {
    var me = this;
    if (me.__goodsGrid) {
      return me.__goodsGrid;
    }
    var modelName = "PSIWSPBillDetail_EditForm";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "goodsId", "goodsCode", "goodsName",
        "goodsSpec", "unitName", "goodsCount", "memo"]
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
        enableTextSelection: true,
        markDirty: !me.adding
      },
      plugins: [me.__cellEditing],
      columnLines: true,
      columns: {
        defaults: {
          menuDisabled: true,
          draggable: false,
          sortable: false
        },
        items: [Ext.create("Ext.grid.RowNumberer", {
          text: "",
          width: 30
        }), {
          header: "物料编码",
          dataIndex: "goodsCode",
          editor: {
            xtype: "psi_goodsfieldforbom",
            parentCmp: me
          }
        }, {
          header: "品名",
          dataIndex: "goodsName",
          width: 200
        }, {
          header: "规格型号",
          dataIndex: "goodsSpec",
          width: 200
        }, {
          header: "拆分数量",
          dataIndex: "goodsCount",
          align: "right",
          width: 100,
          editor: {
            xtype: "numberfield",
            allowDecimals: PSI.Const.GC_DEC_NUMBER > 0,
            decimalPrecision: PSI.Const.GC_DEC_NUMBER,
            minValue: 0,
            hideTrigger: true
          }
        }, {
          header: "单位",
          dataIndex: "unitName",
          width: 60
        }, {
          header: "备注",
          dataIndex: "memo",
          editor: {
            xtype: "textfield"
          }
        }, {
          header: "",
          align: "center",
          width: 50,
          xtype: "actioncolumn",
          id: "PSI_WSP_WSPEditForm_columnActionDelete",
          items: [{
            icon: PSI.Const.BASE_URL
              + "Public/Images/icons/delete.png",
            tooltip: "删除当前记录",
            handler: function (grid, row) {
              var store = grid.getStore();
              store.remove(store.getAt(row));
              if (store.getCount() == 0) {
                store.add({});
              }
            },
            scope: me
          }]
        }, {
          header: "",
          id: "PSI_WSP_WSPEditForm_columnActionAdd",
          align: "center",
          width: 50,
          xtype: "actioncolumn",
          items: [{
            icon: PSI.Const.BASE_URL
              + "Public/Images/icons/insert.png",
            tooltip: "在当前记录之前插入新记录",
            handler: function (grid, row) {
              var store = grid.getStore();
              store.insert(row, [{}]);
            },
            scope: me
          }]
        }, {
          header: "",
          id: "PSI_WSP_WSPEditForm_columnActionAppend",
          align: "center",
          width: 50,
          xtype: "actioncolumn",
          items: [{
            icon: PSI.Const.BASE_URL
              + "Public/Images/icons/add.png",
            tooltip: "在当前记录之后新增记录",
            handler: function (grid, row) {
              var store = grid.getStore();
              store.insert(row + 1, [{}]);
            },
            scope: me
          }]
        }]
      },
      store: store,
      listeners: {
        cellclick: function () {
          return !me.__readonly;
        }
      }
    });

    return me.__goodsGrid;
  },

  cellEditingAfterEdit: function (editor, e) {
    var me = this;
    if (e.colIdx == 6) {
      var store = me.getGoodsGrid().getStore();
      if (e.rowIdx == store.getCount() - 1) {
        store.add({});

        var row = e.rowIdx + 1;
        me.getGoodsGrid().getSelectionModel().select(row);
        me.__cellEditing.startEdit(row, 1);
      }
    }
  },

  // xtype:psi_goodsfield回调本方法
  // 参见PSI.Goods.GoodsField的onOK代码
  __setGoodsInfo: function (data) {
    var me = this;
    var item = me.getGoodsGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      return;
    }
    var goods = item[0];

    goods.set("goodsId", data.id);
    goods.set("goodsCode", data.code);
    goods.set("goodsName", data.name);
    goods.set("unitName", data.unitName);
    goods.set("goodsSpec", data.spec);
  },

  getSaveData: function () {
    var me = this;
    var result = {
      id: me.hiddenId.getValue(),
      bizDT: Ext.Date.format(me.editBizDT.getValue(), "Y-m-d"),
      fromWarehouseId: me.editFromWarehouse.getIdValue(),
      toWarehouseId: me.editToWarehouse.getIdValue(),
      bizUserId: me.editBizUser.getIdValue(),
      billMemo: me.editBillMemo.getValue(),
      items: []
    };

    var store = me.getGoodsGrid().getStore();
    for (var i = 0; i < store.getCount(); i++) {
      var item = store.getAt(i);
      result.items.push({
        id: item.get("id"),
        goodsId: item.get("goodsId"),
        goodsCount: item.get("goodsCount"),
        memo: item.get("memo")
      });
    }

    return Ext.JSON.encode(result);
  },

  setBillReadonly: function () {
    var me = this;
    me.__readonly = true;
    me.setTitle("<span style='font-size:160%'>查看拆分单</span>");
    me.buttonSave.setDisabled(true);
    me.buttonCancel.setText("关闭");
    me.editBizDT.setReadOnly(true);
    me.editFromWarehouse.setReadOnly(true);
    me.editToWarehouse.setReadOnly(true);
    me.editBizUser.setReadOnly(true);
    me.columnActionDelete.hide();
    me.columnActionAdd.hide();
    me.columnActionAppend.hide();
  }
});
