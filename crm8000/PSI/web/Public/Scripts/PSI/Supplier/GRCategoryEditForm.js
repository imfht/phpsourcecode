/**
 * 关联物料 - 添加物料分类界面
 */
Ext.define("PSI.Supplier.GRCategoryEditForm", {
  extend: "PSI.AFX.BaseDialogForm",

	/**
	 * 初始化组件
	 */
  initComponent: function () {
    var me = this;

    var buttons = [];
    buttons.push({
      text: "保存并继续新增",
      formBind: true,
      handler: function () {
        me.onOK(true);
      },
      scope: me
    });

    buttons.push({
      text: "保存",
      formBind: true,
      iconCls: "PSI-button-ok",
      handler: function () {
        me.onOK(false);
      },
      scope: me
    }, {
        text: "取消",
        handler: function () {
          me.close();
        },
        scope: me
      });

    var t = "添加物料分类";
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
      width: 460,
      height: 220,
      layout: "border",
      items: [{
        region: "north",
        border: 0,
        height: 90,
        html: logoHtml
      }, {
        region: "center",
        border: 0,
        id: "PSI_Supplier_GRCategoryEditForm_editForm",
        xtype: "form",
        layout: {
          type: "table",
          columns: 1
        },
        height: "100%",
        bodyPadding: 5,
        defaultType: 'textfield',
        fieldDefaults: {
          labelWidth: 70,
          labelAlign: "right",
          labelSeparator: "",
          msgTarget: 'side'
        },
        items: [{
          id: "PSI_Supplier_GRCategoryEditForm_editCategory",
          xtype: "psi_goodscategoryfield",
          fieldLabel: "物料分类",
          allowBlank: false,
          blankText: "没有输入物料分类",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          width: 410,
          listeners: {
            specialkey: {
              fn: me.onLastEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_Supplier_GRCategoryEditForm_editCategoryId",
          name: "categoryId",
          xtype: "hidden"
        }, {
          name: "id",
          xtype: "hidden",
          value: me.getEntity().get("id")
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

    me.editForm = Ext.getCmp("PSI_Supplier_GRCategoryEditForm_editForm");
    me.editCategory = Ext
      .getCmp("PSI_Supplier_GRCategoryEditForm_editCategory");
    me.editCategoryId = Ext
      .getCmp("PSI_Supplier_GRCategoryEditForm_editCategoryId");
  },

  onWindowBeforeUnload: function (e) {
    return (window.event.returnValue = e.returnValue = '确认离开当前页面？');
  },

  onWndShow: function () {
    var me = this;

    Ext.get(window).on('beforeunload', me.onWindowBeforeUnload);

    me.editCategory.focus();
  },

  onOK: function (thenAdd) {
    var me = this;

    var categoryId = me.editCategory.getIdValue();
    me.editCategoryId.setValue(categoryId);

    var f = me.editForm;
    var el = f.getEl();
    el.mask(PSI.Const.SAVING);
    f.submit({
      url: me.URL("Home/Supplier/addGRCategory"),
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
        PSI.MsgBox.showInfo(action.result.msg);
      }
    });
  },

  onLastEditSpecialKey: function (field, e) {
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

    me.editCategory.setIdValue(null);
    me.editCategory.setValue(null);

    me.editCatgory.focus();
  },

  onWndClose: function () {
    var me = this;

    Ext.get(window).un('beforeunload', me.onWindowBeforeUnload);

    if (me.getParentForm()) {
      me.getParentForm().refreshGRCategoryGrid();
    }
  }
});
