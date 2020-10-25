/**
 * 客户导入
 */
Ext.define("PSI.Customer.CustomerImportForm", {
  extend: "PSI.AFX.BaseDialogForm",

  initComponent: function () {
    var me = this;

    var buttons = [];

    buttons.push({
      text: "导入客户",
      formBind: true,
      iconCls: "PSI-button-ok",
      handler: function () {
        me.onOK();
      },
      scope: me
    }, {
        text: "关闭",
        handler: function () {
          me.close();
        },
        scope: me
      });

    Ext.apply(me, {
      title: "导入客户",
      header: {
        title: me.formatTitle("导入客户"),
        height: 40,
        iconCls: "PSI-button-import"
      },
      width: 512,
      height: 170,
      layout: "fit",
      items: [{
        id: "importForm",
        xtype: "form",
        layout: {
          type: "table",
          columns: 1
        },
        height: "100%",
        bodyPadding: 5,
        fieldDefaults: {
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: "",
          msgTarget: 'side'
        },
        items: [{
          xtype: 'filefield',
          name: 'data_file',
          afterLabelTextTpl: '<span style="color:red;font-weight:bold" data-qtip="必需填写">*</span>',
          fieldLabel: '文件',
          labelWidth: 50,
          width: 480,
          msgTarget: 'side',
          allowBlank: false,
          anchor: '100%',
          buttonText: '选择客户文件'
        }, {
          html: "<a href=../Uploads/Customer/customerModelFile.xls ><h4>下载客户导入模板</h4></a>",
          border: 0
        }],
        buttons: buttons
      }]
    });

    me.callParent(arguments);
  },

  onOK: function () {
    var me = this;
    var f = Ext.getCmp("importForm");
    var el = f.getEl();
    el.mask('正在导入...');
    f.submit({
      url: PSI.Const.BASE_URL + "Home/Customer/import",
      method: "POST",
      success: function (form, action) {
        el.unmask();

        PSI.MsgBox.showInfo("数据导入成功" + action.result.msg);
        me.close();
        me.getParentForm().freshCustomerGrid();
      },
      failure: function (form, action) {
        el.unmask();
        PSI.MsgBox.showInfo(action.result.msg);
      }
    });
  }
});
