/**
 * 商品分类 - 编辑界面
 */
Ext.define("PSI.Goods.CategoryEditForm", {
  extend: "PSI.AFX.BaseDialogForm",

	/**
	 * 初始化组件
	 */
  initComponent: function () {
    var me = this;
    var entity = me.getEntity();

    me.__lastId = entity == null ? null : entity.get("id");

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
      text: entity == null ? "关闭" : "取消",
      handler: function () {
        me.close();
      },
      scope: me
    });

    var t = entity == null ? "新增物料分类" : "编辑物料分类";
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
      + "<p style='color:#196d83'>标记 <span style='color:red;font-weight:bold'>*</span>的是必须录入数据的字段</p>";;

    Ext.apply(me, {
      header: {
        title: me.formatTitle(PSI.Const.PROD_NAME),
        height: 40
      },
      width: 400,
      height: 320,
      layout: "border",
      items: [{
        region: "north",
        border: 0,
        height: 90,
        html: logoHtml
      }, {
        region: "center",
        border: 0,
        id: "PSI_Goods_CategoryEditForm_editForm",
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
          width: 370
        },
        items: [{
          xtype: "hidden",
          name: "id",
          value: entity == null ? null : entity
            .get("id")
        }, {
          id: "PSI_Goods_CategoryEditForm_editCode",
          fieldLabel: "分类编码",
          allowBlank: false,
          blankText: "没有输入分类编码",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          name: "code",
          value: entity == null ? null : entity
            .get("code"),
          listeners: {
            specialkey: {
              fn: me.onEditCodeSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_Goods_CategoryEditForm_editName",
          fieldLabel: "分类名称",
          allowBlank: false,
          blankText: "没有输入分类名称",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          name: "name",
          value: entity == null ? null : entity
            .get("name"),
          listeners: {
            specialkey: {
              fn: me.onEditNameSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_Goods_CategoryEditForm_editParentCategory",
          fieldLabel: "上级分类",
          xtype: "psi_goodsparentcategoryfield",
          listeners: {
            specialkey: {
              fn: me.onEditCategorySpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_Goods_CategoryEditForm_editParentCategoryId",
          xtype: "hidden",
          name: "parentId"
        }, {
          id: "PSI_Goods_CategoryEditForm_editTaxRate",
          xtype: "combo",
          queryMode: "local",
          editable: false,
          valueField: "id",
          labelAlign: "right",
          labelSeparator: "",
          fieldLabel: "默认税率",
          store: Ext.create("Ext.data.ArrayStore", {
            fields: ["id", "text"],
            data: [[-1, "[不设定]"],
            [0, "0%"], [1, "1%"],
            [2, "2%"], [3, "3%"],
            [4, "4%"], [5, "5%"],
            [6, "6%"], [7, "7%"],
            [8, "8%"], [9, "9%"],
            [10, "10%"],
            [11, "11%"],
            [12, "12%"],
            [13, "13%"],
            [14, "14%"],
            [15, "15%"],
            [16, "16%"],
            [17, "17%"]]
          }),
          value: -1,
          name: "taxRate"
        }, {
          id: "PSI_Goods_CategoryEditForm_editMType",
          xtype: "combo",
          queryMode: "local",
          editable: false,
          valueField: "id",
          labelAlign: "right",
          labelSeparator: "",
          fieldLabel: "物料类型",
          store: Ext.create("Ext.data.ArrayStore", {
            fields: ["id", "text"],
            data: [[-1, "[不限]"],
            [1000, "原材料"],
            [2000, "半成品"],
            [3000, "产成品"],
            [4000, "商品"]]
          }),
          value: -1,
          name: "mType"
        }],
        buttons: buttons
      }],
      listeners: {
        close: {
          fn: me.onWndClose,
          scope: me
        },
        show: {
          fn: me.onWndShow,
          scope: me
        }
      }
    });

    me.callParent(arguments);

    me.editForm = Ext.getCmp("PSI_Goods_CategoryEditForm_editForm");

    me.editCode = Ext.getCmp("PSI_Goods_CategoryEditForm_editCode");
    me.editName = Ext.getCmp("PSI_Goods_CategoryEditForm_editName");
    me.editParentCategory = Ext
      .getCmp("PSI_Goods_CategoryEditForm_editParentCategory");
    me.editParentCategoryId = Ext
      .getCmp("PSI_Goods_CategoryEditForm_editParentCategoryId");
    me.editTaxRate = Ext.getCmp("PSI_Goods_CategoryEditForm_editTaxRate");
    me.editMType = Ext.getCmp("PSI_Goods_CategoryEditForm_editMType");
  },

  onOK: function (thenAdd) {
    var me = this;

    me.editParentCategoryId.setValue(me.editParentCategory.getIdValue());

    var f = me.editForm;
    var el = f.getEl();
    el.mask(PSI.Const.SAVING);
    f.submit({
      url: me.URL("Home/Goods/editCategory"),
      method: "POST",
      success: function (form, action) {
        el.unmask();
        PSI.MsgBox.tip("数据保存成功");
        me.focus();
        me.__lastId = action.result.id;
        if (thenAdd) {
          var editCode = me.editCode;
          editCode.setValue(null);
          editCode.clearInvalid();
          editCode.focus();

          var editName = me.editName;
          editName.setValue(null);
          editName.clearInvalid();
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

  onEditCodeSpecialKey: function (field, e) {
    var me = this;

    if (e.getKey() == e.ENTER) {
      me.editName.focus();
    }
  },

  onEditNameSpecialKey: function (field, e) {
    var me = this;

    if (e.getKey() == e.ENTER) {
      me.editParentCategory.focus();
    }
  },

  onEditCategorySpecialKey: function (field, e) {
    var me = this;

    if (e.getKey() == e.ENTER) {
      var f = me.editForm;
      if (f.getForm().isValid()) {
        me.editCode.focus();
        me.onOK(me.adding);
      }
    }
  },

  onWndClose: function () {
    var me = this;

    Ext.get(window).un('beforeunload', me.onWindowBeforeUnload);

    if (me.__lastId) {
      if (me.getParentForm()) {
        me.getParentForm().freshCategoryGrid();
      }
    }
  },

  onWindowBeforeUnload: function (e) {
    return (window.event.returnValue = e.returnValue = '确认离开当前页面？');
  },

	/**
	 * 窗体显示的时候查询数据
	 */
  onWndShow: function () {
    var me = this;

    Ext.get(window).on('beforeunload', me.onWindowBeforeUnload);

    var editCode = me.editCode;
    editCode.focus();
    editCode.setValue(editCode.getValue());

    if (!me.getEntity()) {
      return;
    }

    var el = me.getEl();
    el.mask(PSI.Const.LOADING);
    Ext.Ajax.request({
      url: me.URL("Home/Goods/getCategoryInfo"),
      params: {
        id: me.getEntity().get("id")
      },
      method: "POST",
      callback: function (options, success, response) {
        if (success) {
          var data = Ext.JSON.decode(response.responseText);

          if (data.code) {
            me.editCode.setValue(data.code);
            me.editName.setValue(data.name);
            me.editParentCategory.setIdValue(data.parentId);
            me.editParentCategory.setValue(data.parentName);
            if (data.taxRate) {
              me.editTaxRate.setValue(parseInt(data.taxRate));
            } else {
              me.editTaxRate.setValue(-1);
            }

            if (data.mType) {
              me.editMType.setValue(parseInt(data.mType));
            } else {
              me.editMType.setValue(-1);
            }
          }
        }

        el.unmask();
      }
    });
  }
});
