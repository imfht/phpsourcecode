/**
 * 预付款管理 - 供应商退回采购预付款
 */
Ext.define("PSI.Funds.ReturnPrePaymentForm", {
  extend: "PSI.AFX.BaseDialogForm",

  initComponent: function () {
    var me = this;

    var t = "供应商退回采购预付款";
    var f = "edit-form-money.png";
    var logoHtml = "<img style='float:left;margin:20px 20px 0px 10px;width:48px;height:48px;' src='"
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
      width: 400,
      height: 340,
      layout: "border",
      defaultFocus: "editSupplier",
      listeners: {
        show: {
          fn: me.onWndShow,
          scope: me
        },
        close: {
          fn: me.onWndClose,
          scope: me
        }
      },
      items: [{
        region: "north",
        border: 0,
        height: 90,
        html: logoHtml
      }, {
        region: "center",
        border: 0,
        id: "editForm",
        xtype: "form",
        layout: {
          type: "table",
          columns: 1
        },
        height: "100%",
        bodyPadding: 5,
        defaultType: 'textfield',
        fieldDefaults: {
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: "",
          msgTarget: 'side',
          width: 370,
          margin: "5"
        },
        items: [{
          id: "editSupplierId",
          xtype: "hidden",
          name: "supplierId"
        }, {
          id: "editSupplier",
          fieldLabel: "供应商",
          xtype: "psi_supplierfield",
          allowBlank: false,
          blankText: "没有输入供应商",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          listeners: {
            specialkey: {
              fn: me.onEditSupplierSpecialKey,
              scope: me
            }
          }
        }, {
          id: "editBizDT",
          fieldLabel: "退款日期",
          allowBlank: false,
          blankText: "没有输入退款日期",
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
          fieldLabel: "退款金额",
          allowBlank: false,
          blankText: "没有输入退款金额",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          xtype: "numberfield",
          hideTrigger: true,
          name: "inMoney",
          id: "editInMoney",
          listeners: {
            specialkey: {
              fn: me.onEditInMoneySpecialKey,
              scope: me
            }
          }
        }, {
          id: "editBizUserId",
          xtype: "hidden",
          name: "bizUserId"
        }, {
          id: "editBizUser",
          fieldLabel: "收款人",
          xtype: "psi_userfield",
          allowBlank: false,
          blankText: "没有输入收款人",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          listeners: {
            specialkey: {
              fn: me.onEditBizUserSpecialKey,
              scope: me
            }
          }
        }, {
          fieldLabel: "备注",
          name: "memo",
          id: "editMemo",
          listeners: {
            specialkey: {
              fn: me.onEditMemoSpecialKey,
              scope: me
            }
          }
        }],
        buttons: [{
          text: "保存",
          iconCls: "PSI-button-ok",
          formBind: true,
          handler: me.onOK,
          scope: me
        }, {
          text: "取消",
          handler: function () {
            me.close();
          },
          scope: me
        }]
      }]
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

    var f = Ext.getCmp("editForm");
    var el = f.getEl();
    el.mask(PSI.Const.LOADING);
    Ext.Ajax.request({
      url: PSI.Const.BASE_URL
        + "Home/Funds/returnPrePaymentInfo",
      params: {},
      method: "POST",
      callback: function (options, success, response) {
        el.unmask();

        if (success) {
          var data = Ext.JSON.decode(response.responseText);

          Ext.getCmp("editBizUserId")
            .setValue(data.bizUserId);
          Ext.getCmp("editBizUser")
            .setValue(data.bizUserName);
          Ext.getCmp("editBizUser")
            .setIdValue(data.bizUserId);
        } else {
          PSI.MsgBox.showInfo("网络错误")
        }
      }
    });
  },

  // private
  onOK: function () {
    var me = this;
    Ext.getCmp("editBizUserId").setValue(Ext.getCmp("editBizUser")
      .getIdValue());
    Ext.getCmp("editSupplierId").setValue(Ext.getCmp("editSupplier")
      .getIdValue());

    var f = Ext.getCmp("editForm");
    var el = f.getEl();
    el.mask(PSI.Const.SAVING);
    f.submit({
      url: PSI.Const.BASE_URL + "Home/Funds/returnPrePayment",
      method: "POST",
      success: function (form, action) {
        el.unmask();

        me.close();

        me.getParentForm().onQuery();
      },
      failure: function (form, action) {
        el.unmask();
        PSI.MsgBox.showInfo(action.result.msg, function () {
          Ext.getCmp("editBizDT").focus();
        });
      }
    });
  },

  onEditSupplierSpecialKey: function (field, e) {
    if (e.getKey() == e.ENTER) {
      Ext.getCmp("editBizDT").focus();
    }
  },

  onEditBizDTSpecialKey: function (field, e) {
    if (e.getKey() == e.ENTER) {
      Ext.getCmp("editInMoney").focus();
    }
  },

  onEditInMoneySpecialKey: function (field, e) {
    if (e.getKey() == e.ENTER) {
      Ext.getCmp("editBizUser").focus();
    }
  },

  onEditBizUserSpecialKey: function (field, e) {
    if (e.getKey() == e.ENTER) {
      Ext.getCmp("editMemo").focus();
    }
  },

  onEditMemoSpecialKey: function (field, e) {
    if (e.getKey() == e.ENTER) {
      var f = Ext.getCmp("editForm");
      if (f.getForm().isValid()) {
        var me = this;
        PSI.MsgBox.confirm("请确认是否录入退款记录?", function () {
          me.onOK();
        });
      }
    }
  }
});
