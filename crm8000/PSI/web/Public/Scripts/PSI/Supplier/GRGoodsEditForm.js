/**
 * 关联物料 - 添加个别物料界面
 */
Ext.define("PSI.Supplier.GRGoodsEditForm", {
  extend: "PSI.AFX.BaseDialogForm",

  initComponent: function () {
    var me = this;

    var buttons = [];
    var btn = {
      text: "保存并继续新增",
      formBind: true,
      handler: function () {
        me.onOK(true);
      },
      scope: me
    };

    buttons.push(btn);

    var btn = {
      text: "保存",
      formBind: true,
      iconCls: "PSI-button-ok",
      handler: function () {
        me.onOK(false);
      },
      scope: me
    };
    buttons.push(btn);

    var btn = {
      text: "取消",
      handler: function () {
        me.close();
      },
      scope: me
    };
    buttons.push(btn);

    var t = "添加个别物料";
    var f = "edit-form-create.png";
    var logoHtml = "<img style='float:left;margin:10px 20px 0px 10px;width:48px;height:48px;' src='"
      + PSI.Const.BASE_URL
      + "Public/Images/"
      + f
      + "'></img>"
      + "<h2 style='color:#196d83'>"
      + t
      + "</h2>"
      + "<p style='color:#196d83'>标记 <span style='color:red;font-weight:bold'>*</span>的是必须录入数据的字段</p>";;

    Ext.apply(me, {
      header: {
        title: me.formatTitle(PSI.Const.PROD_NAME),
        height: 40
      },
      width: 520,
      height: 300,
      layout: "border",
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
        id: "PSI_Supplier_GRGoodsEditForm_editForm",
        xtype: "form",
        layout: {
          type: "table",
          columns: 2
        },
        height: "100%",
        bodyPadding: 5,
        defaultType: 'textfield',
        fieldDefaults: {
          labelAlign: "right",
          labelSeparator: "",
          msgTarget: 'side',
          margin: "5"
        },
        items: [{
          xtype: "hidden",
          name: "id",
          value: me.getEntity().get("id")
        }, {
          xtype: "hidden",
          id: "PSI_Supplier_GRGoodsEditForm_editGoodsId",
          name: "goodsId"
        }, {
          id: "PSI_Supplier_GRGoodsEditForm_editGoodsCode",
          fieldLabel: "物料编码",
          width: 470,
          colspan: 2,
          allowBlank: false,
          blankText: "没有输入物料",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          xtype: "psi_goodsfield",
          parentCmp: me,
          listeners: {
            specialkey: {
              fn: me.onEditCodeSpecialKey,
              scope: me
            }
          }
        }, {
          fieldLabel: "品名",
          width: 470,
          readOnly: true,
          colspan: 2,
          id: "PSI_Supplier_GRGoodsEditForm_editGoodsName"
        }, {
          fieldLabel: "规格型号",
          readOnly: true,
          width: 470,
          colspan: 2,
          id: "PSI_Supplier_GRGoodsEditForm_editGoodsSpec"
        }],
        buttons: buttons
      }]
    });

    me.callParent(arguments);

    me.editForm = Ext.getCmp("PSI_Supplier_GRGoodsEditForm_editForm");

    me.editGoodsId = Ext.getCmp("PSI_Supplier_GRGoodsEditForm_editGoodsId");
    me.editGoodsCode = Ext
      .getCmp("PSI_Supplier_GRGoodsEditForm_editGoodsCode");
    me.editGoodsName = Ext
      .getCmp("PSI_Supplier_GRGoodsEditForm_editGoodsName");
    me.editGoodsSpec = Ext
      .getCmp("PSI_Supplier_GRGoodsEditForm_editGoodsSpec");
  },

	/**
	 * 保存
	 */
  onOK: function (thenAdd) {
    var me = this;
    var f = me.editForm;
    var el = f.getEl();
    el.mask(PSI.Const.SAVING);
    var sf = {
      url: me.URL("Home/Supplier/addGRGoods"),
      method: "POST",
      success: function (form, action) {
        el.unmask();

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
          me.editGoodsCode.focus();
        });
      }
    };
    f.submit(sf);
  },

  onEditCodeSpecialKey: function (field, e) {
    var me = this;

    if (e.getKey() == e.ENTER) {
      var f = me.editForm;
      if (f.getForm().isValid()) {
        me.onOK();
      }
    }
  },

  clearEdit: function () {
    var me = this;
    me.editGoodsCode.focus();

    var editors = [me.editGoodsCode, me.editGoodsName, me.editGoodsSpec];
    for (var i = 0; i < editors.length; i++) {
      var edit = editors[i];
      edit.setValue(null);
      edit.clearInvalid();
    }
  },

  onWindowBeforeUnload: function (e) {
    return (window.event.returnValue = e.returnValue = '确认离开当前页面？');
  },

  onWndClose: function () {
    var me = this;

    Ext.get(window).un('beforeunload', me.onWindowBeforeUnload);

    if (me.getParentForm()) {
      me.getParentForm().refreshGRGoodsGrid();
    }
  },

  onWndShow: function () {
    var me = this;

    Ext.get(window).on('beforeunload', me.onWindowBeforeUnload);

    var editCode = me.editGoodsCode;
    editCode.focus();
  },

  __setGoodsInfo: function (goods) {
    var me = this;
    if (goods) {
      me.editGoodsId.setValue(goods.id);
      me.editGoodsName.setValue(goods.name);
      me.editGoodsSpec.setValue(goods.spec);
    } else {
      me.editGoodsId.setValue(null);
      me.editGoodsName.setValue(null);
      me.editGoodsSpec.setValue(null);
    }
  }
});
