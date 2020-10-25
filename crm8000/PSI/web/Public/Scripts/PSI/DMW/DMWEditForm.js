//
// 成品委托生产入库单 - 新增或编辑界面
//
Ext.define("PSI.DMW.DMWEditForm", {
  extend: "PSI.AFX.BaseDialogForm",
  config: {
    genBill: false,
    dmobillRef: null,
    showAddGoodsButton: "0"
  },

  mixins: ["PSI.Mix.GoodsPrice"],

  initComponent: function () {
    var me = this;
    me.__readOnly = false;
    var entity = me.getEntity();
    me.adding = entity == null;

    var title = entity == null ? "新建成品委托生产入库单" : "编辑成品委托生产入库单";
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
      defaultFocus: "editSupplier",
      tbar: [{
        text: "保存",
        id: "buttonSave",
        iconCls: "PSI-button-ok",
        handler: me.onOK,
        scope: me
      }, "-", {
        text: "取消",
        id: "buttonCancel",
        handler: function () {
          if (me.__readonly) {
            me.close();
            return;
          }

          me.confirm("请确认是否取消当前操作？", function () {
            me.close();
          });
        },
        scope: me
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
        layout: "fit",
        border: 0,
        bodyPadding: 10,
        items: [me.getGoodsGrid()]
      }, {
        region: "north",
        id: "editForm",
        layout: {
          type: "table",
          columns: 4
        },
        height: 90,
        bodyPadding: 10,
        border: 0,
        items: [{
          xtype: "hidden",
          id: "hiddenId",
          name: "id",
          value: entity == null ? null : entity
            .get("id")
        }, {
          id: "editRef",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: "",
          fieldLabel: "单号",
          xtype: "displayfield",
          value: "<span style='color:red'>保存后自动生成</span>"
        }, {
          id: "editBizDT",
          fieldLabel: "业务日期",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: "",
          allowBlank: false,
          blankText: "没有输入业务日期",
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
          id: "editFactory",
          colspan: 2,
          width: 430,
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: "",
          xtype: "psi_factoryfield",
          fieldLabel: "工厂",
          allowBlank: false,
          blankText: "没有输入工厂",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          listeners: {
            specialkey: {
              fn: me.onEditFactorySpecialKey,
              scope: me
            }
          },
          showAddButton: true
        }, {
          id: "editWarehouse",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: "",
          fieldLabel: "入库仓库",
          xtype: "psi_warehousefield",
          fid: "2036",
          allowBlank: false,
          blankText: "没有输入入库仓库",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          listeners: {
            specialkey: {
              fn: me.onEditWarehouseSpecialKey,
              scope: me
            }
          }
        }, {
          id: "editBizUser",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: "",
          fieldLabel: "业务员",
          xtype: "psi_userfield",
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
          id: "editPaymentType",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: "",
          fieldLabel: "付款方式",
          xtype: "combo",
          queryMode: "local",
          editable: false,
          valueField: "id",
          store: Ext.create("Ext.data.ArrayStore", {
            fields: ["id", "text"],
            data: [["0", "记应付账款"]]
          }),
          value: "0",
          listeners: {
            specialkey: {
              fn: me.onEditPaymentTypeSpecialKey,
              scope: me
            }
          },
          colspan: 2
        }, {
          id: "editBillMemo",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: "",
          fieldLabel: "备注",
          xtype: "textfield",
          colspan: 4,
          width: 860,
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

    me.editRef = Ext.getCmp("editRef");
    me.editBizDT = Ext.getCmp("editBizDT");
    me.editFactory = Ext.getCmp("editFactory");
    me.editWarehouse = Ext.getCmp("editWarehouse");
    me.editBizUser = Ext.getCmp("editBizUser");
    me.editPaymentType = Ext.getCmp("editPaymentType");
    me.editBillMemo = Ext.getCmp("editBillMemo");

    me.editHiddenId = Ext.getCmp("hiddenId");

    me.columnActionDelete = Ext.getCmp("columnActionDelete");
    me.columnActionAdd = Ext.getCmp("columnActionAdd");
    me.columnActionAppend = Ext.getCmp("columnActionAppend");

    me.columnGoodsCode = Ext.getCmp("columnGoodsCode");
    me.columnGoodsPrice = Ext.getCmp("columnGoodsPrice");
    me.columnGoodsMoney = Ext.getCmp("columnGoodsMoney");

    me.buttonSave = Ext.getCmp("buttonSave");
    me.buttonCancel = Ext.getCmp("buttonCancel");
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
    me.ajax({
      url: me.URL("Home/DM/dmwBillInfo"),
      params: {
        id: me.editHiddenId.getValue(),
        dmobillRef: me.getDmobillRef()
      },
      callback: function (options, success, response) {
        el.unmask();

        if (success) {
          me.editFactory.focus();

          var data = me.decodeJSON(response.responseText);
          me.editBillMemo.setValue(data.billMemo);

          if (me.getGenBill()) {
            // 从成品委托生产订单生成成品委托生产入库单
            me.editFactory.setIdValue(data.factoryId);
            me.editFactory.setValue(data.factoryName);
            me.editBizUser.setIdValue(data.bizUserId);
            me.editBizUser.setValue(data.bizUserName);
            me.editBizDT.setValue(data.dealDate);
            me.editPaymentType.setValue(data.paymentType);
            var store = me.getGoodsGrid().getStore();
            store.removeAll();
            store.add(data.items);

            me.editFactory.setReadOnly(true);
            me.columnActionDelete.hide();
            me.columnActionAdd.hide();
            me.columnActionAppend.hide();
          } else {
            if (!data.genBill) {
              me.columnGoodsCode.setEditor({
                xtype: "psi_goodsfield",
                parentCmp: me,
                showAddButton: me.getShowAddGoodsButton() == "1"
              });
              me.columnGoodsPrice.setEditor({
                xtype: "numberfield",
                hideTrigger: true
              });
              me.columnGoodsMoney.setEditor({
                xtype: "numberfield",
                hideTrigger: true
              });
            } else {
              me.editFactory.setReadOnly(true);
              me.columnActionDelete.hide();
              me.columnActionAdd.hide();
              me.columnActionAppend.hide();
            }

            if (data.ref) {
              me.editRef.setValue(data.ref);
            }

            me.editFactory.setIdValue(data.factoryId);
            me.editFactory.setValue(data.factoryName);

            me.editWarehouse.setIdValue(data.warehouseId);
            me.editWarehouse.setValue(data.warehouseName);

            me.editBizUser.setIdValue(data.bizUserId);
            me.editBizUser.setValue(data.bizUserName);
            if (data.bizDT) {
              me.editBizDT.setValue(data.bizDT);
            }
            if (data.paymentType) {
              me.editPaymentType.setValue(data.paymentType);
            }
            if (data.expandByBOM) {
              me.editExpand.setValue(data.expandByBOM);
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
          }
        }
      }
    });
  },

  onOK: function () {
    var me = this;
    Ext.getBody().mask("正在保存中...");
    var r = {
      url: me.URL("Home/DM/editDMWBill"),
      params: {
        jsonStr: me.getSaveData()
      },
      callback: function (options, success, response) {
        Ext.getBody().unmask();

        if (success) {
          var data = me.decodeJSON(response.responseText);
          if (data.success) {
            me.showInfo("成功保存数据", function () {
              me.close();
              var pf = me.getParentForm();
              if (pf) {
                pf.refreshMainGrid(data.id);
              }
            });
          } else {
            me.showInfo(data.msg);
          }
        }
      }
    };
    me.ajax(r);
  },

  onEditBizDTSpecialKey: function (field, e) {
    var me = this;

    if (e.getKey() == e.ENTER) {
      me.editFactory.focus();
    }
  },

  onEditFactorySpecialKey: function (field, e) {
    var me = this;

    if (e.getKey() == e.ENTER) {
      me.editWarehouse.focus();
    }
  },

  onEditWarehouseSpecialKey: function (field, e) {
    var me = this;

    if (e.getKey() == e.ENTER) {
      me.editBizUser.focus();
    }
  },

  onEditBizUserSpecialKey: function (field, e) {
    var me = this;

    if (me.__readonly) {
      return;
    }

    if (e.getKey() == e.ENTER) {
      me.editPaymentType.focus();
    }
  },

  onEditBillMemoSpecialKey: function (field, e) {
    var me = this;

    if (me.__readonly) {
      return;
    }

    if (e.getKey() == e.ENTER) {
      var store = me.getGoodsGrid().getStore();
      if (store.getCount() == 0) {
        store.add({});
      }
      me.getGoodsGrid().focus();
      me.__cellEditing.startEdit(0, 1);
    }
  },

  onEditPaymentTypeSpecialKey: function (field, e) {
    var me = this;

    if (me.__readonly) {
      return;
    }

    if (e.getKey() == e.ENTER) {
      me.editBillMemo.focus();
    }
  },

  getGoodsGrid: function () {
    var me = this;
    if (me.__goodsGrid) {
      return me.__goodsGrid;
    }
    var modelName = "PSIDMWBillDetail_EditForm";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "goodsId", "goodsCode", "goodsName",
        "goodsSpec", "unitName", "goodsCount", {
          name: "goodsMoney",
          type: "float"
        }, "goodsPrice", "memo", "dmoBillDetailId", {
          name: "taxRate",
          type: "int"
        }, {
          name: "tax",
          type: "float"
        }, {
          name: "moneyWithTax",
          type: "float"
        }, "goodsPriceWithTax"]
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
      features: [{
        ftype: "summary"
      }],
      plugins: [me.__cellEditing],
      columnLines: true,
      columns: {
        defaults: {
          menuDisabled: true,
          sortable: false,
          draggable: false
        },
        items: [{
          xtype: "rownumberer"
        }, {
          header: "物料编码",
          dataIndex: "goodsCode",
          id: "columnGoodsCode"
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
          header: "单价",
          dataIndex: "goodsPrice",
          align: "right",
          xtype: "numbercolumn",
          width: 100,
          id: "columnGoodsPrice",
          summaryRenderer: function () {
            return "金额合计";
          }
        }, {
          header: "金额",
          dataIndex: "goodsMoney",
          align: "right",
          xtype: "numbercolumn",
          width: 120,
          id: "columnGoodsMoney",
          summaryType: "sum"
        }, {
          header: "含税价",
          dataIndex: "goodsPriceWithTax",
          align: "right",
          xtype: "numbercolumn",
          width: 100,
          editor: {
            xtype: "numberfield",
            hideTrigger: true
          }
        }, {
          header: "税率(%)",
          dataIndex: "taxRate",
          menuDisabled: true,
          sortable: false,
          draggable: false,
          align: "right",
          format: "0",
          width: 80
        }, {
          header: "税金",
          dataIndex: "tax",
          menuDisabled: true,
          sortable: false,
          draggable: false,
          align: "right",
          xtype: "numbercolumn",
          width: 100,
          editor: {
            xtype: "numberfield",
            hideTrigger: true
          },
          summaryType: "sum"
        }, {
          header: "价税合计",
          dataIndex: "moneyWithTax",
          menuDisabled: true,
          sortable: false,
          draggable: false,
          align: "right",
          xtype: "numbercolumn",
          width: 120,
          editor: {
            xtype: "numberfield",
            hideTrigger: true
          },
          summaryType: "sum"
        }, {
          header: "备注",
          dataIndex: "memo",
          width: 200,
          editor: {
            xtype: "textfield"
          }
        }, {
          header: "",
          id: "columnActionDelete",
          align: "center",
          width: 50,
          xtype: "actioncolumn",
          items: [{
            icon: me.URL("Public/Images/icons/delete.png"),
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
          id: "columnActionAdd",
          align: "center",
          width: 50,
          xtype: "actioncolumn",
          items: [{
            icon: me.URL("Public/Images/icons/insert.png"),
            tooltip: "在当前记录之前插入新记录",
            handler: function (grid, row) {
              var store = grid.getStore();
              store.insert(row, [{}]);
            },
            scope: me
          }]
        }, {
          header: "",
          id: "columnActionAppend",
          align: "center",
          width: 50,
          xtype: "actioncolumn",
          items: [{
            icon: me
              .URL("Public/Images/icons/add.png"),
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
    goods.set("taxRate", data.taxRate);

    me.calcMoney(goods);
  },

  cellEditingAfterEdit: function (editor, e) {
    var me = this;

    if (me.__readonly) {
      return;
    }

    var fieldName = e.field;
    var goods = e.record;
    var oldValue = e.originalValue;
    if (fieldName == "memo") {
      var store = me.getGoodsGrid().getStore();
      if (e.rowIdx == store.getCount() - 1) {
        store.add({});
        var row = e.rowIdx + 1;
        me.getGoodsGrid().getSelectionModel().select(row);
        me.__cellEditing.startEdit(row, 1);
      }
    } else if (fieldName == "goodsMoney") {
      if (goods.get(fieldName) != (new Number(oldValue)).toFixed(2)) {
        me.calcPrice(goods);
      }
    } else if (fieldName == "goodsCount") {
      if (goods.get(fieldName) != oldValue) {
        me.calcMoney(goods);
      }
    } else if (fieldName == "goodsPrice") {
      if (goods.get(fieldName) != (new Number(oldValue)).toFixed(2)) {
        me.calcMoney(goods);
      }
    } else if (fieldName == "moneyWithTax") {
      if (goods.get(fieldName) != (new Number(oldValue)).toFixed(2)) {
        me.calcTax(goods);
      }
    } else if (fieldName == "tax") {
      if (goods.get(fieldName) != (new Number(oldValue)).toFixed(2)) {
        me.calcMoneyWithTax(goods);
      }
    } else if (fieldName == "goodsPriceWithTax") {
      if (goods.get(fieldName) != (new Number(oldValue)).toFixed(2)) {
        me.calcMoney2(goods);
      }
    }

    // 上述代码的技术说明
    // 各个calcXXXX函数实现在PSI.Mix.GoodsPrice中
    // 这是利用ExtJS的mix技术
  },

  getSaveData: function () {
    var me = this;

    var result = {
      id: me.editHiddenId.getValue(),
      bizDT: Ext.Date.format(me.editBizDT.getValue(), "Y-m-d"),
      factoryId: me.editFactory.getIdValue(),
      warehouseId: me.editWarehouse.getIdValue(),
      bizUserId: me.editBizUser.getIdValue(),
      paymentType: me.editPaymentType.getValue(),
      dmobillRef: me.getDmobillRef(),
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
        goodsPrice: item.get("goodsPrice"),
        goodsMoney: item.get("goodsMoney"),
        memo: item.get("memo"),
        dmoBillDetailId: item.get("dmoBillDetailId"),
        taxRate: item.get("taxRate"),
        tax: item.get("tax"),
        moneyWithTax: item.get("moneyWithTax"),
        goodsPriceWithTax: item.get("goodsPriceWithTax")
      });
    }

    return Ext.JSON.encode(result);
  },

  setBillReadonly: function () {
    var me = this;
    me.__readonly = true;
    me.setTitle("<span style='font-size:160%;'>查看成品委托生产入库单</span>");
    me.buttonSave.setDisabled(true);
    me.buttonCancel.setText("关闭");
    me.editBizDT.setReadOnly(true);
    me.editFactory.setReadOnly(true);
    me.editWarehouse.setReadOnly(true);
    me.editBizUser.setReadOnly(true);
    me.editPaymentType.setReadOnly(true);
    me.editBillMemo.setReadOnly(true);
    me.columnActionDelete.hide();
    me.columnActionAdd.hide();
    me.columnActionAppend.hide();
  }
});
