/**
 * 采购订单 - 新增或编辑界面
 * 
 * @author 李静波
 */
Ext.define("PSI.PurchaseOrder.POEditForm", {
  extend: "PSI.AFX.BaseDialogForm",

  config: {
    showAddGoodsButton: "0",
    genBill: false,
    sobillRef: null
  },

  mixins: ["PSI.Mix.GoodsPrice"],

  /**
   * 初始化组件
   */
  initComponent: function () {
    var me = this;
    me.__readOnly = false;
    var entity = me.getEntity();
    this.adding = entity == null;

    var title = entity == null ? "新建采购订单" : "编辑采购订单";
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

          PSI.MsgBox.confirm("请确认是否取消当前操作？", function () {
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
        height: 120,
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
          id: "editDealDate",
          fieldLabel: "交货日期",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: "",
          allowBlank: false,
          blankText: "没有输入交货日期",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          xtype: "datefield",
          format: "Y-m-d",
          value: new Date(),
          name: "bizDT",
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "editSupplier",
          colspan: 2,
          width: 430,
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: "",
          xtype: "psi_supplierfield",
          fieldLabel: "供应商",
          allowBlank: false,
          blankText: "没有输入供应商",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          },
          showAddButton: true,
          callbackFunc: me.__setSupplierExtData,
          callbackScope: me
        }, {
          id: "editDealAddress",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: "",
          fieldLabel: "交货地址",
          colspan: 2,
          width: 430,
          xtype: "textfield",
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "editContact",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: "",
          fieldLabel: "联系人",
          xtype: "textfield",
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "editTel",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: "",
          fieldLabel: "电话",
          xtype: "textfield",
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "editFax",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: "",
          fieldLabel: "传真",
          xtype: "textfield",
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "editOrg",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: "",
          fieldLabel: "组织机构",
          xtype: "psi_orgwithdataorgfield",
          colspan: 2,
          width: 430,
          allowBlank: false,
          blankText: "没有输入组织机构",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
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
              fn: me.onEditSpecialKey,
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
            data: [["0", "记应付账款"],
            ["1", "现金付款"],
            ["2", "预付款"]]
          }),
          value: "0",
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "editBillMemo",
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: "",
          fieldLabel: "备注",
          xtype: "textfield",
          colspan: 3,
          width: 645,
          listeners: {
            specialkey: {
              fn: me.onLastEditSpecialKey,
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

    me.__editorList = ["editDealDate", "editSupplier", "editDealAddress",
      "editContact", "editTel", "editFax", "editOrg", "editBizUser",
      "editPaymentType", "editBillMemo"];
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
      url: PSI.Const.BASE_URL + "Home/Purchase/poBillInfo",
      params: {
        id: Ext.getCmp("hiddenId").getValue(),
        genBill: me.getGenBill(),
        sobillRef: me.getSobillRef()
      },
      method: "POST",
      callback: function (options, success, response) {
        el.unmask();

        if (success) {
          var data = Ext.JSON.decode(response.responseText);

          if (data.ref) {
            Ext.getCmp("editRef").setValue(data.ref);
            Ext.getCmp("editSupplier")
              .setIdValue(data.supplierId);
            Ext.getCmp("editSupplier")
              .setValue(data.supplierName);
            Ext.getCmp("editBillMemo")
              .setValue(data.billMemo);
            Ext.getCmp("editDealDate")
              .setValue(data.dealDate);
            Ext.getCmp("editDealAddress")
              .setValue(data.dealAddress);
            Ext.getCmp("editContact")
              .setValue(data.contact);
            Ext.getCmp("editTel").setValue(data.tel);
            Ext.getCmp("editFax").setValue(data.fax);
          }

          Ext.getCmp("editBizUser")
            .setIdValue(data.bizUserId);
          Ext.getCmp("editBizUser")
            .setValue(data.bizUserName);
          if (data.orgId) {
            Ext.getCmp("editOrg").setIdValue(data.orgId);
            Ext.getCmp("editOrg")
              .setValue(data.orgFullName);
          }

          if (data.paymentType) {
            Ext.getCmp("editPaymentType")
              .setValue(data.paymentType);
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
    });
  },

  onOK: function () {
    var me = this;
    Ext.getBody().mask("正在保存中...");
    Ext.Ajax.request({
      url: PSI.Const.BASE_URL + "Home/Purchase/editPOBill",
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
              if (!me.getGenBill()) {
                me
                  .getParentForm()
                  .refreshMainGrid(data.id);
              }
            });
          } else {
            PSI.MsgBox.showInfo(data.msg);
          }
        }
      }
    });
  },

  onEditSpecialKey: function (field, e) {
    if (e.getKey() === e.ENTER) {
      var me = this;
      var id = field.getId();
      for (var i = 0; i < me.__editorList.length; i++) {
        var editorId = me.__editorList[i];
        if (id === editorId) {
          var edit = Ext.getCmp(me.__editorList[i + 1]);
          edit.focus();
          edit.setValue(edit.getValue());
        }
      }
    }
  },

  onLastEditSpecialKey: function (field, e) {
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
    var modelName = "PSIPOBill_EditForm";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "goodsId", "goodsCode", "goodsName",
        "goodsSpec", "unitName", "goodsCount", {
          name: "goodsMoney",
          type: "float"
        }, "goodsPrice", {
          name: "taxRate",
          type: "int"
        }, {
          name: "tax",
          type: "float"
        }, {
          name: "moneyWithTax",
          type: "float"
        }, "memo", "goodsPriceWithTax"]
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
      columns: [{
        xtype: "rownumberer"
      }, {
        header: "物料编码",
        dataIndex: "goodsCode",
        menuDisabled: true,
        sortable: false,
        draggable: false,
        editor: {
          xtype: "psi_goods_with_purchaseprice_field",
          parentCmp: me,
          showAddButton: me.getShowAddGoodsButton() == "1",
          supplierIdFunc: me.__supplierIdFunc,
          supplierIdScope: me
        }
      }, {
        header: "品名",
        dataIndex: "goodsName",
        menuDisabled: true,
        sortable: false,
        draggable: false,
        width: 200
      }, {
        header: "规格型号",
        dataIndex: "goodsSpec",
        menuDisabled: true,
        sortable: false,
        draggable: false,
        width: 200
      }, {
        header: "采购数量",
        dataIndex: "goodsCount",
        menuDisabled: true,
        sortable: false,
        draggable: false,
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
        menuDisabled: true,
        sortable: false,
        draggable: false,
        width: 60
      }, {
        header: "采购单价",
        dataIndex: "goodsPrice",
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
        summaryRenderer: function () {
          return "采购金额合计";
        }
      }, {
        header: "采购金额",
        dataIndex: "goodsMoney",
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
        header: "含税价",
        dataIndex: "goodsPriceWithTax",
        menuDisabled: true,
        sortable: false,
        draggable: false,
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
        menuDisabled: true,
        sortable: false,
        draggable: false,
        editor: {
          xtype: "textfield"
        }
      }, {
        header: "",
        id: "columnActionDelete",
        align: "center",
        menuDisabled: true,
        draggable: false,
        width: 40,
        xtype: "actioncolumn",
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
        id: "columnActionAdd",
        align: "center",
        menuDisabled: true,
        draggable: false,
        width: 40,
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
        id: "columnActionAppend",
        align: "center",
        menuDisabled: true,
        draggable: false,
        width: 40,
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
      }],
      store: store,
      listeners: {
        cellclick: function () {
          return !me.__readonly;
        }
      }
    });

    return me.__goodsGrid;
  },

  // 控件 xtype:psi_goods_with_purchaseprice_field 会回调本方法
  // 见PSI.Goods.GoodsWithPurchaseFieldField的onOK方法
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
    if (me.__taxRateBySupplier) {
      if (data.taxRateType > 1) {
        // 该物料设置了自己的特定税率
        goods.set("taxRate", data.taxRate);
      } else {
        // 设置了供应商税率，优先使用供应商税率
        goods.set("taxRate", me.__taxRateBySupplier);
      }
    } else {
      // 没有设置供应商税率
      goods.set("taxRate", data.taxRate);
    }

    // 设置建议采购价
    goods.set("goodsPrice", data.purchasePrice);

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
    } else if (fieldName == "moneyWithTax") {
      if (goods.get(fieldName) != (new Number(oldValue)).toFixed(2)) {
        me.calcTax(goods);
      }
    } else if (fieldName == "tax") {
      if (goods.get(fieldName) != (new Number(oldValue)).toFixed(2)) {
        me.calcMoneyWithTax(goods);
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
      id: Ext.getCmp("hiddenId").getValue(),
      dealDate: Ext.Date.format(Ext.getCmp("editDealDate").getValue(),
        "Y-m-d"),
      supplierId: Ext.getCmp("editSupplier").getIdValue(),
      dealAddress: Ext.getCmp("editDealAddress").getValue(),
      contact: Ext.getCmp("editContact").getValue(),
      tel: Ext.getCmp("editTel").getValue(),
      fax: Ext.getCmp("editFax").getValue(),
      orgId: Ext.getCmp("editOrg").getIdValue(),
      bizUserId: Ext.getCmp("editBizUser").getIdValue(),
      paymentType: Ext.getCmp("editPaymentType").getValue(),
      billMemo: Ext.getCmp("editBillMemo").getValue(),
      sobillRef: me.getSobillRef(),
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
        goodsPriceWithTax: item.get("goodsPriceWithTax"),
        goodsMoney: item.get("goodsMoney"),
        tax: item.get("tax"),
        taxRate: item.get("taxRate"),
        moneyWithTax: item.get("moneyWithTax"),
        memo: item.get("memo")
      });
    }

    return Ext.JSON.encode(result);
  },

  setBillReadonly: function () {
    var me = this;
    me.__readonly = true;
    me.setTitle("<span style='font-size:160%;'>查看采购订单</span>");
    Ext.getCmp("buttonSave").setDisabled(true);
    Ext.getCmp("buttonCancel").setText("关闭");
    Ext.getCmp("editDealDate").setReadOnly(true);
    Ext.getCmp("editSupplier").setReadOnly(true);
    Ext.getCmp("editDealAddress").setReadOnly(true);
    Ext.getCmp("editContact").setReadOnly(true);
    Ext.getCmp("editTel").setReadOnly(true);
    Ext.getCmp("editFax").setReadOnly(true);
    Ext.getCmp("editOrg").setReadOnly(true);
    Ext.getCmp("editBizUser").setReadOnly(true);
    Ext.getCmp("editPaymentType").setReadOnly(true);
    Ext.getCmp("editBillMemo").setReadOnly(true);

    Ext.getCmp("columnActionDelete").hide();
    Ext.getCmp("columnActionAdd").hide();
    Ext.getCmp("columnActionAppend").hide();
  },

  // xtype:psi_supplierfield回调本方法
  // 参见PSI.Supplier.SupplierField的onOK方法
  __setSupplierExtData: function (data, scope) {
    var me = scope;

    Ext.getCmp("editDealAddress").setValue(data.address_shipping);
    Ext.getCmp("editTel").setValue(data.tel01);
    Ext.getCmp("editFax").setValue(data.fax);
    Ext.getCmp("editContact").setValue(data.contact01);

    me.__taxRateBySupplier = data.taxRate;
  },

  __supplierIdFunc: function () {
    return Ext.getCmp("editSupplier").getIdValue();
  }
});
