/**
 * 应收账款 - 收款记录
 */
Ext.define("PSI.Funds.RvRecordEditForm", {
  extend: "PSI.AFX.BaseDialogForm",

  config: {
    rvDetail: null
  },

  initComponent: function () {
    var me = this;

    var t = "录入收款记录";
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
      defaultFocus: "editActMoney",
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
          xtype: "hidden",
          name: "refNumber",
          value: me.getRvDetail()
            .get("refNumber")
        }, {
          xtype: "hidden",
          name: "refType",
          value: me.getRvDetail()
            .get("refType")
        }, {
          fieldLabel: "单号",
          xtype: "displayfield",
          value: me.getRvDetail()
            .get("refNumber")
        }, {
          id: "editBizDT",
          fieldLabel: "收款日期",
          allowBlank: false,
          blankText: "没有输入收款日期",
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
          fieldLabel: "收款金额",
          allowBlank: false,
          blankText: "没有输入收款金额",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          xtype: "numberfield",
          hideTrigger: true,
          name: "actMoney",
          id: "editActMoney",
          listeners: {
            specialkey: {
              fn: me.onEditActMoneySpecialKey,
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
          name: "remark",
          id: "editRemark",
          listeners: {
            specialkey: {
              fn: me.onEditRemarkSpecialKey,
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
      url: PSI.Const.BASE_URL + "Home/Funds/rvRecInfo",
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

    var f = Ext.getCmp("editForm");
    var el = f.getEl();
    el.mask(PSI.Const.SAVING);
    f.submit({
      url: PSI.Const.BASE_URL + "Home/Funds/addRvRecord",
      method: "POST",
      success: function (form, action) {
        el.unmask();

        me.close();
        var pf = me.getParentForm();
        pf.refreshRvInfo();
        pf.refreshRvDetailInfo();
        pf.getRvRecordGrid().getStore().loadPage(1);
      },
      failure: function (form, action) {
        el.unmask();
        PSI.MsgBox.showInfo(action.result.msg, function () {
          Ext.getCmp("editBizDT").focus();
        });
      }
    });
  },

  onEditBizDTSpecialKey: function (field, e) {
    if (e.getKey() == e.ENTER) {
      Ext.getCmp("editActMoney").focus();
    }
  },

  onEditActMoneySpecialKey: function (field, e) {
    if (e.getKey() == e.ENTER) {
      Ext.getCmp("editBizUser").focus();
    }
  },

  onEditBizUserSpecialKey: function (field, e) {
    if (e.getKey() == e.ENTER) {
      Ext.getCmp("editRemark").focus();
    }
  },

  onEditRemarkSpecialKey: function (field, e) {
    if (e.getKey() == e.ENTER) {
      var f = Ext.getCmp("editForm");
      if (f.getForm().isValid()) {
        var me = this;
        PSI.MsgBox.confirm("请确认是否录入收款记录?", function () {
          me.onOK();
        });
      }
    }
  }
});
