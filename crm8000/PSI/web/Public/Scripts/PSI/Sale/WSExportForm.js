/**
 * 导出销售出库单
 */
Ext.define("PSI.Sale.WSExportForm", {
  extend: "Ext.window.Window",
  config: {
    billData: null
  },

  initComponent: function () {
    var me = this;

    Ext.apply(me, {
      title: "导出销售出库单临时数据",
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
          height: 300,
          readOnly: true,
          value: me.getBillData()
        }, {
          fieldLabel: "使用说明",
          xtype: "displayfield",
          value: "按 Ctrl-A, Ctrl-C 复制数据，然后手工把复制的数据保存到本地文件"
        }],
        buttons: [{
          text: "关闭",
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
  }
});
