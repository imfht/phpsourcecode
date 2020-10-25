/**
 * 导入销售出库单
 * 
 * @author 李静波
 */
Ext.define("PSI.Sale.WSImportForm", {
  extend: "Ext.window.Window",
  config: {
    parentForm: null
  },

  initComponent: function () {
    var me = this;

    Ext.apply(me, {
      title: "导入销售出库单临时数据",
      modal: true,
      onEsc: Ext.emptyFn,
      width: 400,
      height: 420,
      layout: "fit",
      defaultFocus: "editData",
      items: [{
        id: "editForm",
        xtype: "form",
        layout: "form",
        height: "100%",
        bodyPadding: 5,
        defaultType: 'textfield',
        fieldDefaults: {
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: "",
          msgTarget: 'side'
        },
        items: [{
          id: "editData",
          fieldLabel: "临时数据",
          xtype: "textareafield",
          height: 300
        }],
        buttons: [{
          text: "导入",
          handler: me.onOK,
          scope: me
        }, {
          text: "取消",
          handler: function () {
            me.close();
          },
          scope: me
        }]
      }],
      listeners: {
        show: {
          fn: me.onWndShow,
          scope: me
        }
      }
    });

    me.callParent(arguments);
  },

  onWndShow: function () {
    var editName = Ext.getCmp("editData");
    editName.focus();
  },

  onOK: function () {
    var me = this;

    var data = Ext.getCmp("editData").getValue();
    if (!data) {
      PSI.MsgBox.showInfo("没有输入导入数据");
      return;
    }

    var bill = Ext.JSON.decode(data, true);

    if (!bill) {
      PSI.MsgBox.showInfo("输入的数据不正确，无法导入");
      return;
    }

    var result = me.getParentForm().importBill(bill);

    if (result) {
      me.close();
    }
  }
});
