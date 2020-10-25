//
// 销售合同 - 新增或编辑界面
//
Ext.define("PSI.SaleContract.SCEditForm", {
  extend: "PSI.AFX.BaseDialogForm",

  config: {
    showAddGoodsButton: false
  },

  mixins: ["PSI.Mix.GoodsPrice"],

  initComponent: function () {
    var me = this;
    me.__readOnly = false;
    var entity = me.getEntity();
    this.adding = entity == null;

    var title = entity == null ? "新建销售合同" : "编辑销售合同";
    title = me.formatTitle(title);
    var iconCls = entity == null ? "PSI-button-add" : "PSI-button-edit";

    Ext.apply(me, {
      header: {
        title: title,
        height: 40,
        iconCls: iconCls
      },
      defaultFocus: "PSI_SaleContract_SCEditForm_editCustomer",
      maximized: true,
      width: 1000,
      height: 600,
      layout: "border",
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

          PSI.MsgBox.confirm("请确认是否取消当前操作？",
            function () {
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
        xtype: "tabpanel",
        border: 0,
        bodyPadding: 10,
        items: [me.getGoodsGrid(), me.getClausePanel()]
      }, {
        region: "north",
        id: "editForm",
        layout: {
          type: "table",
          columns: 4
        },
        height: 150,
        bodyPadding: 10,
        border: 0,
        items: me.getEditorList()
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

    me.hiddenId = Ext.getCmp("PSI_SaleContract_SCEditForm_hiddenId");
    me.editRef = Ext.getCmp("PSI_SaleContract_SCEditForm_editRef");
    me.editCustomer = Ext
      .getCmp("PSI_SaleContract_SCEditForm_editCustomer");
    me.editBeginDT = Ext.getCmp("PSI_SaleContract_SCEditForm_editBeginDT");
    me.editEndDT = Ext.getCmp("PSI_SaleContract_SCEditForm_editEndDT");
    me.editOrg = Ext.getCmp("PSI_SaleContract_SCEditForm_editOrg");
    me.editBizDT = Ext.getCmp("PSI_SaleContract_SCEditForm_editBizDT");
    me.editDealDate = Ext
      .getCmp("PSI_SaleContract_SCEditForm_editDealDate");
    me.editDealAddress = Ext
      .getCmp("PSI_SaleContract_SCEditForm_editDealAddress");
    me.editBizUser = Ext.getCmp("PSI_SaleContract_SCEditForm_editBizUser");
    me.editDiscount = Ext
      .getCmp("PSI_SaleContract_SCEditForm_editDiscount");
    me.editBillMemo = Ext
      .getCmp("PSI_SaleContract_SCEditForm_editBillMemo");
    me.editQualityClause = Ext
      .getCmp("PSI_SaleContract_SCEditForm_editQualityClause");
    me.editInsuranceClause = Ext
      .getCmp("PSI_SaleContract_SCEditForm_editInsuranceClause");
    me.editTransportClause = Ext
      .getCmp("PSI_SaleContract_SCEditForm_editTrasportClause");
    me.editOtherClause = Ext
      .getCmp("PSI_SaleContract_SCEditForm_editOtherClause");

    me.__editorList = ["PSI_SaleContract_SCEditForm_editCustomer",
      "PSI_SaleContract_SCEditForm_editBeginDT",
      "PSI_SaleContract_SCEditForm_editEndDT",
      "PSI_SaleContract_SCEditForm_editOrg",
      "PSI_SaleContract_SCEditForm_editBizDT",
      "PSI_SaleContract_SCEditForm_editDealDate",
      "PSI_SaleContract_SCEditForm_editDealAddress",
      "PSI_SaleContract_SCEditForm_editBizUser",
      "PSI_SaleContract_SCEditForm_editDiscount",
      "PSI_SaleContract_SCEditForm_editBillMemo"];
  },

  getEditorList: function () {
    var me = this;
    var entity = me.getEntity();

    return [{
      xtype: "hidden",
      id: "PSI_SaleContract_SCEditForm_hiddenId",
      value: entity == null ? null : entity.get("id")
    }, {
      id: "PSI_SaleContract_SCEditForm_editRef",
      labelWidth: 90,
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "销售合同号",
      xtype: "displayfield",
      colspan: 4,
      value: "<span style='color:red'>保存后自动生成</span>"
    }, {
      id: "PSI_SaleContract_SCEditForm_editCustomer",
      colspan: 2,
      width: 430,
      labelWidth: 90,
      labelAlign: "right",
      labelSeparator: "",
      xtype: "psi_customerfield",
      fieldLabel: "甲方客户",
      allowBlank: false,
      blankText: "没有输入客户",
      beforeLabelTextTpl: PSI.Const.REQUIRED,
      listeners: {
        specialkey: {
          fn: me.onEditSpecialKey,
          scope: me
        }
      },
      showAddButton: true,
      callbackFunc: me.__setCustomerExtData
    }, {
      id: "PSI_SaleContract_SCEditForm_editBeginDT",
      fieldLabel: "合同开始日期",
      labelWidth: 90,
      labelAlign: "right",
      labelSeparator: "",
      allowBlank: false,
      blankText: "没有输入合同开始日期",
      beforeLabelTextTpl: PSI.Const.REQUIRED,
      xtype: "datefield",
      format: "Y-m-d",
      listeners: {
        specialkey: {
          fn: me.onEditSpecialKey,
          scope: me
        }
      }
    }, {
      id: "PSI_SaleContract_SCEditForm_editEndDT",
      fieldLabel: "合同结束日期",
      labelWidth: 90,
      labelAlign: "right",
      labelSeparator: "",
      allowBlank: false,
      blankText: "没有输入合同结束日期",
      beforeLabelTextTpl: PSI.Const.REQUIRED,
      xtype: "datefield",
      format: "Y-m-d",
      listeners: {
        specialkey: {
          fn: me.onEditSpecialKey,
          scope: me
        }
      }
    }, {
      id: "PSI_SaleContract_SCEditForm_editOrg",
      labelWidth: 90,
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "乙方组织机构",
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
      id: "PSI_SaleContract_SCEditForm_editBizDT",
      fieldLabel: "合同签订日期",
      labelWidth: 90,
      labelAlign: "right",
      labelSeparator: "",
      allowBlank: false,
      blankText: "没有输入合同签订日期",
      beforeLabelTextTpl: PSI.Const.REQUIRED,
      xtype: "datefield",
      format: "Y-m-d",
      listeners: {
        specialkey: {
          fn: me.onEditSpecialKey,
          scope: me
        }
      }
    }, {
      id: "PSI_SaleContract_SCEditForm_editDealDate",
      fieldLabel: "交货日期",
      labelWidth: 90,
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
      id: "PSI_SaleContract_SCEditForm_editDealAddress",
      labelWidth: 90,
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
      id: "PSI_SaleContract_SCEditForm_editBizUser",
      labelWidth: 90,
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
      id: "PSI_SaleContract_SCEditForm_editDiscount",
      labelWidth: 90,
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "折扣率(%)",
      xtype: "numberfield",
      hideTrigger: true,
      allowDecimals: false,
      value: 100,
      listeners: {
        specialkey: {
          fn: me.onEditSpecialKey,
          scope: me
        }
      }
    }, {
      id: "PSI_SaleContract_SCEditForm_editBillMemo",
      labelWidth: 90,
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "备注",
      xtype: "textfield",
      colspan: 3,
      width: 670,
      listeners: {
        specialkey: {
          fn: me.onLastEditSpecialKey,
          scope: me
        }
      }
    }];
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
      url: PSI.Const.BASE_URL + "Home/SaleContract/scBillInfo",
      params: {
        id: me.hiddenId.getValue()
      },
      method: "POST",
      callback: function (options, success, response) {
        el.unmask();

        if (success) {
          var data = Ext.JSON.decode(response.responseText);

          if (data.ref) {
            me.editRef.setValue(data.ref);
            me.editCustomer.setIdValue(data.customerId);
            me.editCustomer.setValue(data.customerName);
            me.editBeginDT.setValue(data.beginDT);
            me.editEndDT.setValue(data.endDT);
            me.editBizDT.setValue(data.bizDT);
            me.editDealDate.setValue(data.dealDate);
            me.editDealAddress.setValue(data.dealAddress);
            me.editDiscount.setValue(data.discount);
            me.editBillMemo.setValue(data.billMemo);
            me.editOrg.setIdValue(data.orgId);
            me.editOrg.setValue(data.orgFullName);
            me.editQualityClause
              .setValue(data.qualityClause);
            me.editInsuranceClause
              .setValue(data.insuranceClause);
            me.editTransportClause
              .setValue(data.transportClause);
            me.editOtherClause.setValue(data.otherClause);
          }

          me.editBizUser.setIdValue(data.bizUserId);
          me.editBizUser.setValue(data.bizUserName);

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

          me.editCustomer.focus();
        }
      }
    });
  },

  onOK: function () {
    var me = this;
    var customerId = me.editCustomer.getIdValue();
    if (!customerId) {
      me.showInfo("没有输入甲方客户", function () {
        me.editCustomer.focus();
      });
      return;
    }
    var beginDT = me.editBeginDT.getValue();
    if (!beginDT) {
      me.showInfo("没有输入合同开始日期", function () {
        me.editBeginDT.focus();
      });
      return;
    }
    var endDT = me.editEndDT.getValue();
    if (!endDT) {
      me.showInfo("没有输入合同结束日期", function () {
        me.editEndDT.focus();
      });
      return;
    }
    var orgId = me.editOrg.getIdValue();
    if (!orgId) {
      me.showInfo("没有输入乙方组织机构", function () {
        me.editOrg.focus();
      });
      return;
    }
    var bizDT = me.editBizDT.getValue();
    if (!bizDT) {
      me.showInfo("没有输入合同签订日期", function () {
        me.editBizDT.focus();
      });
      return;
    }
    var dealDate = me.editDealDate.getValue();
    if (!dealDate) {
      me.showInfo("没有输入交货日期", function () {
        me.editDealDate.focus();
      });
      return;
    }

    Ext.getBody().mask("正在保存中...");
    Ext.Ajax.request({
      url: PSI.Const.BASE_URL + "Home/SaleContract/editSCBill",
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
    var modelName = "PSISCBillDetail_EditForm";
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
      title: "商品明细",
      features: [{
        ftype: "summary"
      }],
      plugins: [me.__cellEditing],
      columnLines: true,
      columns: [{
        xtype: "rownumberer"
      }, {
        header: "商品编码",
        dataIndex: "goodsCode",
        menuDisabled: true,
        sortable: false,
        draggable: false,
        editor: {
          xtype: "psi_goods_with_saleprice_field",
          showAddButton: me.getShowAddGoodsButton() == "1",
          parentCmp: me,
          editCustomerName: "editCustomer"
        }
      }, {
        header: "商品名称",
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
        header: "销售数量",
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
        header: "销售单价",
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
          return "销售金额合计";
        }
      }, {
        header: "销售金额",
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

  getClausePanel: function () {
    var me = this;
    if (me.__clausePanel) {
      return me.__clausePanel;
    }

    me.__clausePanel = Ext.create("Ext.panel.Panel", {
      title: "合同条款",
      autoScroll: true,
      border: 0,
      layout: "form",
      bodyPadding: 5,
      defaults: {
        labelSeparator: "",
        hideLabel: true,
        rows: 3
      },
      cls: "PSI-SCBill",
      items: [{
        xtype: "displayfield",
        value: "品质条款"
      }, {
        xtype: "textareafield",
        id: "PSI_SaleContract_SCEditForm_editQualityClause"
      }, {
        xtype: "displayfield",
        value: "保险条款"
      }, {
        xtype: "textareafield",
        id: "PSI_SaleContract_SCEditForm_editInsuranceClause"
      }, {
        xtype: "displayfield",
        value: "运输条款"
      }, {
        xtype: "textareafield",
        id: "PSI_SaleContract_SCEditForm_editTrasportClause"
      }, {
        xtype: "displayfield",
        value: "其他条款"
      }, {
        xtype: "textareafield",
        id: "PSI_SaleContract_SCEditForm_editOtherClause"
      }]
    });

    return me.__clausePanel;
  },

  // xtype:psi_goods_with_saleprice_field回调本方法
  // 参见PSI.Goods.GoodsWithSalePriceField的onOK方法
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
    goods.set("goodsPrice", data.salePrice);
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
      id: me.hiddenId.getValue(),
      customerId: me.editCustomer.getIdValue(),
      beginDT: Ext.Date.format(me.editBeginDT.getValue(), "Y-m-d"),
      endDT: Ext.Date.format(me.editEndDT.getValue(), "Y-m-d"),
      orgId: me.editOrg.getIdValue(),
      bizDT: Ext.Date.format(me.editBizDT.getValue(), "Y-m-d"),
      dealDate: Ext.Date.format(me.editDealDate.getValue(), "Y-m-d"),
      dealAddress: me.editDealAddress.getValue(),
      bizUserId: me.editBizUser.getIdValue(),
      discount: me.editDiscount.getValue(),
      billMemo: me.editBillMemo.getValue(),
      qualityClause: me.editQualityClause.getValue(),
      insuranceClause: me.editInsuranceClause.getValue(),
      transportClause: me.editTransportClause.getValue(),
      otherClause: me.editOtherClause.getValue(),
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
        tax: item.get("tax"),
        taxRate: item.get("taxRate"),
        moneyWithTax: item.get("moneyWithTax"),
        memo: item.get("memo"),
        goodsPriceWithTax: item.get("goodsPriceWithTax")
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
    me.editDealDate.setReadOnly(true);
    me.editCustomer.setReadOnly(true);
    me.editDealAddress.setReadOnly(true);
    me.editOrg.setReadOnly(true);
    me.editBizUser.setReadOnly(true);
    me.editBillMemo.setReadOnly(true);

    Ext.getCmp("columnActionDelete").hide();
    Ext.getCmp("columnActionAdd").hide();
    Ext.getCmp("columnActionAppend").hide();
  },

  // xtype:psi_customerfield回调本方法
  // 参见PSI.Customer.CustomerField的onOK方法
  __setCustomerExtData: function (data) {
    var me = this;

    Ext.getCmp("PSI_SaleContract_SCEditForm_editDealDate")
      .setValue(data.address_receipt);
  }
});
