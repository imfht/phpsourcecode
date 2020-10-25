/**
 * 商品导入
 * 
 * @author 张健
 */
Ext.define("PSI.Goods.GoodsImportForm", {
  extend: "PSI.AFX.BaseDialogForm",

  config: {
    parentForm: null
  },

	/**
	 * 初始化组件
	 */
  initComponent: function () {
    var me = this;

    var buttons = [];

    buttons.push({
      text: "导入商品",
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
      header: {
        title: me.formatTitle("导入商品"),
        height: 40,
        iconCls: "PSI-button-import"
      },
      modal: true,
      resizable: false,
      onEsc: Ext.emptyFn,
      width: 512,
      height: 150,
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
          buttonText: '选择商品文件'
        }, {
          html: "<a href=../Uploads/Goods/goodsModelFile.xls ><h4>下载商品导入模板</h4></a>",
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
    el && el.mask('正在导入...');
    f.submit({
      url: PSI.Const.BASE_URL + "Home/Goods/import",
      method: "POST",
      success: function (form, action) {
        el && el.unmask();

        PSI.MsgBox.showInfo("数据导入成功");

        me.close();
        me.getParentForm().freshGoodsGrid();
      },
      failure: function (form, action) {
        el && el.unmask();
        PSI.MsgBox.showInfo(action.result.msg);
      }
    });
  }
});
