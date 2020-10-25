/**
 * 客户分类 - 新增或编辑界面
 */
Ext.define("PSI.Customer.CategoryEditForm", {
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
        text: entity == null ? "关闭" : "取消",
        handler: function () {
          me.close();
        },
        scope: me
      });

    var modelName = "PSIPriceSystem";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "name"]
    });

    var t = entity == null ? "新增客户分类" : "编辑客户分类";
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
      width: 400,
      height: 280,
      layout: "border",
      items: [{
        region: "north",
        border: 0,
        height: 90,
        html: logoHtml
      }, {
        region: "center",
        border: 0,
        id: "PSI_Customer_CategoryEditForm_editForm",
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
          name: "id",
          value: entity == null ? null : entity
            .get("id")
        }, {
          id: "PSI_Customer_CategoryEditForm_editCode",
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
          id: "PSI_Customer_CategoryEditForm_editName",
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
          xtype: "combobox",
          fieldLabel: "价格体系",
          id: "PSI_Customer_CategoryEditForm_comboPrice",
          queryMode: "local",
          editable: false,
          valueField: "id",
          displayField: "name",
          store: Ext.create("Ext.data.Store", {
            model: modelName,
            autoLoad: false,
            data: []
          }),
          listeners: {
            specialkey: {
              fn: me.onComboPriceSpecialKey,
              scope: me
            }
          }
        }, {
          xtype: "hidden",
          name: "psId",
          id: "PSI_Customer_CategoryEditForm_editPsId"
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

    me.editForm = Ext.getCmp("PSI_Customer_CategoryEditForm_editForm");

    me.editCode = Ext.getCmp("PSI_Customer_CategoryEditForm_editCode");
    me.editName = Ext.getCmp("PSI_Customer_CategoryEditForm_editName");

    me.comboPrice = Ext.getCmp("PSI_Customer_CategoryEditForm_comboPrice");
    me.editPsId = Ext.getCmp("PSI_Customer_CategoryEditForm_editPsId");
  },

  onOK: function (thenAdd) {
    var me = this;

    me.editPsId.setValue(me.comboPrice.getValue());

    var f = me.editForm;
    var el = f.getEl();
    el.mask(PSI.Const.SAVING);
    f.submit({
      url: me.URL("Home/Customer/editCategory"),
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
      var editName = me.editName;
      editName.focus();
      editName.setValue(editName.getValue());
    }
  },

  onEditNameSpecialKey: function (field, e) {
    var me = this;

    if (e.getKey() == e.ENTER) {
      me.comboPrice.focus();
    }
  },

  onComboPriceSpecialKey: function (field, e) {
    var me = this;

    if (e.getKey() == e.ENTER) {
      if (me.editForm.getForm().isValid()) {
        me.onOK(me.adding);
      }
    }
  },

  onWindowBeforeUnload: function (e) {
    return (window.event.returnValue = e.returnValue = '确认离开当前页面？');
  },

  onWndClose: function () {
    var me = this;

    Ext.get(window).un('beforeunload', me.onWindowBeforeUnload);

    if (me.__lastId) {
      if (me.getParentForm()) {
        me.getParentForm().freshCategoryGrid(me.__lastId);
      }
    }
  },

  onWndShow: function () {
    var me = this;

    Ext.get(window).on('beforeunload', me.onWindowBeforeUnload);

    var editCode = me.editCode;
    editCode.focus();
    editCode.setValue(editCode.getValue());

    var id = me.adding ? null : me.getEntity().get("id");

    var store = me.comboPrice.getStore();

    var el = me.getEl();
    el.mask(PSI.Const.LOADING);
    var r = {
      url: me.URL("Home/Customer/priceSystemList"),
      params: {
        id: id
      },
      callback: function (options, success, response) {
        store.removeAll();

        if (success) {
          var data = me.decodeJSON(response.responseText);
          store.add(data.priceList);
          if (id && data.psId) {
            me.comboPrice.setValue(data.psId);
          }
        }

        el.unmask();
      }
    };

    me.ajax(r);
  }
});
