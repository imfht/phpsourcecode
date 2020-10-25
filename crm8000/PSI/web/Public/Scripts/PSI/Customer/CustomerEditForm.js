/**
 * 客户资料 - 新增或编辑界面
 */
Ext.define("PSI.Customer.CustomerEditForm", {
  extend: "PSI.AFX.BaseDialogForm",

  initComponent: function () {
    var me = this;
    var entity = me.getEntity();
    me.adding = entity == null;
    var buttons = [];
    if (!entity) {
      buttons.push({
        text: "保存并继续新增",
        formBind: true,
        handler: function () {
          me.onOK(true);
        },
        scope: me
      });
    }

    buttons.push({
      text: "保存",
      formBind: true,
      iconCls: "PSI-button-ok",
      handler: function () {
        me.onOK(false);
      },
      scope: me
    }, {
        text: me.adding ? "关闭" : "取消",
        handler: function () {
          me.close();
        },
        scope: me
      });

    var categoryStore = null;
    if (me.getParentForm()) {
      categoryStore = me.getParentForm().categoryGrid.getStore();
    }

    var t = entity == null ? "新增客户" : "编辑客户";
    var f = entity == null
      ? "edit-form-create.png"
      : "edit-form-update.png";
    var logoHtml = "<img style='float:left;margin:10px 20px 0px 10px;width:48px;height:48px;' src='"
      + PSI.Const.BASE_URL
      + "Public/Images/"
      + f
      + "'></img>"
      + "<h2 style='color:#196d83'>"
      + t
      + "</h2>"
      + "<p style='color:#196d83'>标记 <span style='color:red;font-weight:bold'>*</span>的是必须录入数据的字段</p>";

    Ext.apply(me, {
      header: {
        title: me.formatTitle(PSI.Const.PROD_NAME),
        height: 40
      },
      width: 550,
      height: 570,
      layout: "border",
      items: [{
        region: "north",
        border: 0,
        height: 90,
        html: logoHtml
      }, {
        region: "center",
        border: 0,
        id: "PSI_Customer_CustomerEditForm_editForm",
        xtype: "form",
        layout: {
          type: "table",
          columns: 2
        },
        height: "100%",
        bodyPadding: 5,
        defaultType: 'textfield',
        fieldDefaults: {
          labelWidth: 100,
          labelAlign: "right",
          labelSeparator: "",
          msgTarget: 'side'
        },
        items: [{
          xtype: "hidden",
          name: "id",
          value: entity == null ? null : entity
            .get("id")
        }, {
          id: "PSI_Customer_CustomerEditForm_editCategory",
          xtype: "combo",
          fieldLabel: "分类",
          allowBlank: false,
          blankText: "没有输入客户分类",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          valueField: "id",
          displayField: "name",
          store: categoryStore,
          queryMode: "local",
          editable: false,
          value: categoryStore != null
            ? categoryStore.getAt(0).get("id")
            : null,
          name: "categoryId",
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_Customer_CustomerEditForm_editCode",
          fieldLabel: "编码",
          allowBlank: false,
          blankText: "没有输入客户编码",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          name: "code",
          value: entity == null ? null : entity
            .get("code"),
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_Customer_CustomerEditForm_editName",
          fieldLabel: "客户名称",
          allowBlank: false,
          blankText: "没有输入客户名称",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          name: "name",
          value: entity == null ? null : entity
            .get("name"),
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          },
          width: 510,
          colspan: 2
        }, {
          id: "PSI_Customer_CustomerEditForm_editAddress",
          fieldLabel: "地址",
          name: "address",
          value: entity == null ? null : entity
            .get("address"),
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          },
          width: 510,
          colspan: 2
        }, {
          id: "PSI_Customer_CustomerEditForm_editContact01",
          fieldLabel: "联系人",
          name: "contact01",
          value: entity == null ? null : entity
            .get("contact01"),
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_Customer_CustomerEditForm_editMobile01",
          fieldLabel: "手机",
          name: "mobile01",
          value: entity == null ? null : entity
            .get("mobile01"),
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_Customer_CustomerEditForm_editTel01",
          fieldLabel: "固话",
          name: "tel01",
          value: entity == null ? null : entity
            .get("tel01"),
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_Customer_CustomerEditForm_editQQ01",
          fieldLabel: "QQ",
          name: "qq01",
          value: entity == null ? null : entity
            .get("qq01"),
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_Customer_CustomerEditForm_editContact02",
          fieldLabel: "备用联系人",
          name: "contact02",
          value: entity == null ? null : entity
            .get("contact02"),
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_Customer_CustomerEditForm_editMobile02",
          fieldLabel: "备用联系人手机",
          name: "mobile02",
          value: entity == null ? null : entity
            .get("mobile02"),
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_Customer_CustomerEditForm_editTel02",
          fieldLabel: "备用联系人固话",
          name: "tel02",
          value: entity == null ? null : entity
            .get("tel02"),
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_Customer_CustomerEditForm_editQQ02",
          fieldLabel: "备用联系人QQ",
          name: "qq02",
          value: entity == null ? null : entity
            .get("qq02"),
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_Customer_CustomerEditForm_editAddressReceipt",
          fieldLabel: "收货地址",
          name: "addressReceipt",
          value: entity == null ? null : entity
            .get("addressReceipt"),
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          },
          width: 510,
          colspan: 2
        }, {
          id: "PSI_Customer_CustomerEditForm_editBankName",
          fieldLabel: "开户行",
          name: "bankName",
          value: entity == null ? null : entity
            .get("bankName"),
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_Customer_CustomerEditForm_editBankAccount",
          fieldLabel: "开户行账号",
          name: "bankAccount",
          value: entity == null ? null : entity
            .get("bankAccount"),
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_Customer_CustomerEditForm_editTax",
          fieldLabel: "税号",
          name: "tax",
          value: entity == null ? null : entity
            .get("tax"),
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_Customer_CustomerEditForm_editFax",
          fieldLabel: "传真",
          name: "fax",
          value: entity == null ? null : entity
            .get("fax"),
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_Customer_CustomerEditForm_editInitReceivables",
          fieldLabel: "应收期初余额",
          name: "initReceivables",
          xtype: "numberfield",
          hideTrigger: true,
          value: entity == null ? null : entity
            .get("initReceivables"),
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_Customer_CustomerEditForm_editInitReceivablesDT",
          fieldLabel: "余额日期",
          name: "initReceivablesDT",
          xtype: "datefield",
          format: "Y-m-d",
          value: entity == null ? null : entity
            .get("initReceivablesDT"),
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_Customer_CustomerEditForm_editWarehouse",
          xtype: "psi_warehousefield",
          fieldLabel: "销售出库仓库",
          value: null,
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          },
          width: 510,
          colspan: 2
        }, {
          id: "PSI_Customer_CustomerEditForm_editWarehouseId",
          xtype: "hiddenfield",
          name: "warehouseId"
        }, {
          id: "PSI_Customer_CustomerEditForm_editNote",
          fieldLabel: "备注",
          name: "note",
          value: entity == null ? null : entity
            .get("note"),
          listeners: {
            specialkey: {
              fn: me.onEditLastSpecialKey,
              scope: me
            }
          },
          width: 510,
          colspan: 2
        }, {
          id: "PSI_Customer_CustomerEditForm_editRecordStatus",
          xtype: "combo",
          queryMode: "local",
          editable: false,
          valueField: "id",
          fieldLabel: "状态",
          name: "recordStatus",
          store: Ext.create("Ext.data.ArrayStore", {
            fields: ["id", "text"],
            data: [[1000, "启用"], [0, "停用"]]
          }),
          value: 1000
        }],
        buttons: buttons
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

    me.editForm = Ext.getCmp("PSI_Customer_CustomerEditForm_editForm");
    me.editCategory = Ext
      .getCmp("PSI_Customer_CustomerEditForm_editCategory");
    me.editCode = Ext.getCmp("PSI_Customer_CustomerEditForm_editCode");
    me.editName = Ext.getCmp("PSI_Customer_CustomerEditForm_editName");
    me.editAddress = Ext
      .getCmp("PSI_Customer_CustomerEditForm_editAddress");
    me.editContact01 = Ext
      .getCmp("PSI_Customer_CustomerEditForm_editContact01");
    me.editMobile01 = Ext
      .getCmp("PSI_Customer_CustomerEditForm_editMobile01");
    me.editTel01 = Ext.getCmp("PSI_Customer_CustomerEditForm_editTel01");
    me.editQQ01 = Ext.getCmp("PSI_Customer_CustomerEditForm_editQQ01");
    me.editContact02 = Ext
      .getCmp("PSI_Customer_CustomerEditForm_editContact02");
    me.editMobile02 = Ext
      .getCmp("PSI_Customer_CustomerEditForm_editMobile02");
    me.editTel02 = Ext.getCmp("PSI_Customer_CustomerEditForm_editTel02");
    me.editQQ02 = Ext.getCmp("PSI_Customer_CustomerEditForm_editQQ02");
    me.editAddressReceipt = Ext
      .getCmp("PSI_Customer_CustomerEditForm_editAddressReceipt");
    me.editBankName = Ext
      .getCmp("PSI_Customer_CustomerEditForm_editBankName");
    me.editBankAccount = Ext
      .getCmp("PSI_Customer_CustomerEditForm_editBankAccount");
    me.editTax = Ext.getCmp("PSI_Customer_CustomerEditForm_editTax");
    me.editFax = Ext.getCmp("PSI_Customer_CustomerEditForm_editFax");
    me.editInitReceivables = Ext
      .getCmp("PSI_Customer_CustomerEditForm_editInitReceivables");
    me.editInitReceivablesDT = Ext
      .getCmp("PSI_Customer_CustomerEditForm_editInitReceivablesDT");
    me.editWarehouse = Ext
      .getCmp("PSI_Customer_CustomerEditForm_editWarehouse");
    me.editWarehouseId = Ext
      .getCmp("PSI_Customer_CustomerEditForm_editWarehouseId");
    me.editNote = Ext.getCmp("PSI_Customer_CustomerEditForm_editNote");
    me.editRecordStatus = Ext
      .getCmp("PSI_Customer_CustomerEditForm_editRecordStatus");

    me.__editorList = [me.editCategory, me.editCode, me.editName,
    me.editAddress, me.editContact01, me.editMobile01,
    me.editTel01, me.editQQ01, me.editContact02, me.editMobile02,
    me.editTel02, me.editQQ02, me.editAddressReceipt,
    me.editBankName, me.editBankAccount, me.editTax, me.editFax,
    me.editInitReceivables, me.editInitReceivablesDT,
    me.editWarehouse, me.editNote];
  },

  onWindowBeforeUnload: function (e) {
    return (window.event.returnValue = e.returnValue = '确认离开当前页面？');
  },

  onWndShow: function () {
    var me = this;

    Ext.get(window).on('beforeunload', me.onWindowBeforeUnload);

    if (!me.adding) {
      // 编辑客户资料
      var el = me.getEl();
      el.mask(PSI.Const.LOADING);
      Ext.Ajax.request({
        url: me.URL("Home/Customer/customerInfo"),
        params: {
          id: me.getEntity().get("id")
        },
        method: "POST",
        callback: function (options, success, response) {
          if (success) {
            var data = Ext.JSON
              .decode(response.responseText);
            me.editCategory.setValue(data.categoryId);
            me.editCode.setValue(data.code);
            me.editName.setValue(data.name);
            me.editAddress.setValue(data.address);
            me.editContact01.setValue(data.contact01);
            me.editMobile01.setValue(data.mobile01);
            me.editTel01.setValue(data.tel01);
            me.editQQ01.setValue(data.qq01);
            me.editContact02.setValue(data.contact02);
            me.editMobile02.setValue(data.mobile02);
            me.editTel02.setValue(data.tel02);
            me.editQQ02.setValue(data.qq02);
            me.editAddressReceipt
              .setValue(data.addressReceipt);
            me.editInitReceivables
              .setValue(data.initReceivables);
            me.editInitReceivablesDT
              .setValue(data.initReceivablesDT);
            me.editBankName.setValue(data.bankName);
            me.editBankAccount.setValue(data.bankAccount);
            me.editTax.setValue(data.tax);
            me.editFax.setValue(data.fax);
            me.editNote.setValue(data.note);

            if (data.warehouseId) {
              me.editWarehouse
                .setIdValue(data.warehouseId);
              me.editWarehouse
                .setValue(data.warehouseName);
            }

            me.editRecordStatus
              .setValue(parseInt(data.recordStatus));
          }

          el.unmask();
        }
      });
    } else {
      // 新建客户资料
      if (me.getParentForm()) {
        var grid = me.getParentForm().categoryGrid;
        var item = grid.getSelectionModel().getSelection();
        if (item == null || item.length != 1) {
          return;
        }

        me.editCategory.setValue(item[0].get("id"));
      } else {
        // 在其他界面中调用新增客户资料
        var modelName = "PSICustomerCategory_CustomerEditForm";
        Ext.define(modelName, {
          extend: "Ext.data.Model",
          fields: ["id", "code", "name", {
            name: "cnt",
            type: "int"
          }]
        });
        var store = Ext.create("Ext.data.Store", {
          model: modelName,
          autoLoad: false,
          data: []
        });
        me.editCategory.bindStore(store);
        var el = Ext.getBody();
        el.mask(PSI.Const.LOADING);
        Ext.Ajax.request({
          url: me.URL("Home/Customer/categoryList"),
          params: {
            recordStatus: -1
          },
          method: "POST",
          callback: function (options, success, response) {
            store.removeAll();

            if (success) {
              var data = Ext.JSON
                .decode(response.responseText);
              store.add(data);
              if (store.getCount() > 0) {
                var id = store.getAt(0).get("id");
                me.editCategory.setValue(id);
              }
            }

            el.unmask();
          }
        });
      }
    }

    var editCode = me.editCode;
    editCode.focus();
    editCode.setValue(editCode.getValue());
  },

  onWndClose: function () {
    var me = this;

    Ext.get(window).un('beforeunload', me.onWindowBeforeUnload);

    if (me.__lastId) {
      if (me.getParentForm()) {
        me.getParentForm().freshCustomerGrid(me.__lastId);
      }
    }
  },

  onOK: function (thenAdd) {
    var me = this;

    me.editWarehouseId.setValue(me.editWarehouse.getIdValue());

    var f = me.editForm;
    var el = f.getEl();
    el.mask(PSI.Const.SAVING);
    f.submit({
      url: me.URL("Home/Customer/editCustomer"),
      method: "POST",
      success: function (form, action) {
        el.unmask();
        me.__lastId = action.result.id;
        PSI.MsgBox.tip("数据保存成功");
        me.focus();
        if (thenAdd) {
          me.clearEdit();
        } else {
          me.close();
        }
      },
      failure: function (form, action) {
        el.unmask();
        PSI.MsgBox.showInfo(action.result.msg, function () {
          me.editCode.focus();
        });
      }
    });
  },

  onEditSpecialKey: function (field, e) {
    var me = this;

    if (e.getKey() === e.ENTER) {
      var id = field.getId();
      for (var i = 0; i < me.__editorList.length; i++) {
        var editor = me.__editorList[i];
        if (id === editor.getId()) {
          var edit = me.__editorList[i + 1];
          edit.focus();
          edit.setValue(edit.getValue());
        }
      }
    }
  },

  onEditLastSpecialKey: function (field, e) {
    var me = this;

    if (e.getKey() == e.ENTER) {
      var f = me.editForm;
      if (f.getForm().isValid()) {
        me.onOK(me.adding);
      }
    }
  },

  clearEdit: function () {
    var me = this;

    me.editCode.focus();

    var editors = [me.editCode, me.editName, me.editAddress,
    me.editContact01, me.editMobile01, me.editTel01, me.editQQ01,
    me.editContact02, me.editMobile02, me.editTel02, me.editQQ02,
    me.editAddressReceipt, me.editBankName, me.editBankAccount,
    me.editTax, me.editFax, me.editNote, me.editInitReceivables,
    me.editInitReceivablesDT];
    for (var i = 0; i < editors.length; i++) {
      var edit = editors[i];
      if (edit) {
        edit.setValue(null);
        edit.clearInvalid();
      }
    }
  }
});
