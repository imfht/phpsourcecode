/**
* 表单 - 新建或编辑界面
* @author 李静波
*/
Ext.define("PSI.Form.FormEditForm", {
  extend: "PSI.AFX.BaseDialogForm",

  config: {
    category: null
  },

  initComponent: function () {
    var me = this;
    var entity = me.getEntity();
    this.adding = entity == null;

    var buttons = [];

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

    var t = entity == null ? "新增表单" : "编辑表单";
    var logoHtml = me.genLogoHtml(entity, t);

    Ext.apply(me, {
      header: {
        title: me.formatTitle(PSI.Const.PROD_NAME),
        height: 40
      },
      width: 550,
      height: 310,
      layout: "border",
      items: [{
        region: "north",
        border: 0,
        height: 90,
        html: logoHtml
      }, {
        region: "center",
        border: 0,
        id: "PSI_Form_FormEditForm_editForm",
        xtype: "form",
        layout: {
          type: "table",
          columns: 2
        },
        height: "100%",
        bodyPadding: 5,
        defaultType: 'textfield',
        fieldDefaults: {
          labelWidth: 100,
          labelAlign: "right",
          labelSeparator: "",
          msgTarget: 'side'
        },
        items: [{
          xtype: "hidden",
          name: "id",
          value: entity == null ? null : entity
            .get("id")
        }, {
          id: "PSI_Form_FormEditForm_editCategoryId",
          xtype: "hidden",
          name: "categoryId"
        }, {
          id: "PSI_Form_FormEditForm_editCategory",
          xtype: "psi_formcategoryfield",
          fieldLabel: "分类",
          allowBlank: false,
          blankText: "没有输入表单分类",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          valueField: "id",
          displayField: "name",
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_Form_FormEditForm_editCode",
          fieldLabel: "编码",
          name: "code",
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_Form_FormEditForm_editName",
          fieldLabel: "表单名称",
          allowBlank: false,
          blankText: "没有输入中文名称",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          name: "name",
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          },
          colspan: 2,
          width: 510
        }, {
          id: "PSI_Form_FormEditForm_editTableName",
          fieldLabel: "数据库表名",
          allowBlank: false,
          blankText: "没有输入数据库表名",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          name: "tableName",
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          },
          colspan: 2,
          width: 510
        }, {
          id: "PSI_Form_FormEditForm_editModuleName",
          fieldLabel: "模块名称",
          allowBlank: false,
          blankText: "没有输入模块名称",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          name: "moduleName",
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          },
          colspan: 2,
          width: 510
        }, {
          id: "PSI_Form_FormEditForm_editMemo",
          fieldLabel: "备注",
          name: "memo",
          value: entity == null ? null : entity
            .get("note"),
          listeners: {
            specialkey: {
              fn: me.onEditLastSpecialKey,
              scope: me
            }
          },
          width: 510,
          colspan: 2
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

    me.editForm = Ext.getCmp("PSI_Form_FormEditForm_editForm");

    me.editCategoryId = Ext.getCmp("PSI_Form_FormEditForm_editCategoryId");
    me.editCategory = Ext.getCmp("PSI_Form_FormEditForm_editCategory");
    me.editCode = Ext.getCmp("PSI_Form_FormEditForm_editCode");
    me.editName = Ext.getCmp("PSI_Form_FormEditForm_editName");
    me.editTableName = Ext.getCmp("PSI_Form_FormEditForm_editTableName");
    me.editModuleName = Ext.getCmp("PSI_Form_FormEditForm_editModuleName");
    me.editMemo = Ext.getCmp("PSI_Form_FormEditForm_editMemo");

    me.__editorList = [
      me.editCategory, me.editCode, me.editName,
      me.editTableName, me.editModuleName, me.editMemo];

    var c = me.getCategory();
    if (c) {
      me.editCategory.setIdValue(c.get("id"));
      me.editCategory.setValue(c.get("name"));
    }
  },

  onWndShow: function () {
    var me = this;

    Ext.get(window).on('beforeunload', me.onWindowBeforeUnload);

    if (me.adding) {
      // 新建
      me.editTableName.setValue("t_");
    } else {
      // 编辑
      var el = me.getEl();
      el && el.mask(PSI.Const.LOADING);
      Ext.Ajax.request({
        url: me.URL("Home/Form/formInfo"),
        params: {
          id: me.getEntity().get("id")
        },
        method: "POST",
        callback: function (options, success, response) {
          if (success) {
            var data = me.decodeJSON(response.responseText);
            me.editCategory.setIdValue(data.categoryId);
            me.editCategory.setValue(data.categoryName);
            me.editCode.setValue(data.code);
            me.editName.setValue(data.name);
            me.editTableName.setValue(data.tableName);
            me.editTableName.setReadOnly(true);
            me.editModuleName.setValue(data.moduleName);
            me.editMemo.setValue(data.memo);
          }

          el && el.unmask();
        }
      });
    }

    me.editCode.focus();
    me.editCode.setValue(me.editCode.getValue());
  },

  onOK: function () {
    var me = this;

    me.editCategoryId.setValue(me.editCategory.getIdValue());

    var f = me.editForm;
    var el = f.getEl();
    el && el.mask(PSI.Const.SAVING);
    f.submit({
      url: me.URL("Home/Form/editForm"),
      method: "POST",
      success: function (form, action) {
        el && el.unmask();
        PSI.MsgBox.tip("数据保存成功");
        me.focus();
        me.__lastId = action.result.id;
        me.close();
      },
      failure: function (form, action) {
        el && el.unmask();
        PSI.MsgBox.showInfo(action.result.msg, function () {
          me.editCode.focus();
        });
      }
    });
  },

  onEditSpecialKey: function (field, e) {
    var me = this;

    if (e.getKey() === e.ENTER) {
      var id = field.getId();
      for (var i = 0; i < me.__editorList.length; i++) {
        var edit = me.__editorList[i];
        if (id == edit.getId()) {
          var edit = me.__editorList[i + 1];
          edit.focus();
          edit.setValue(edit.getValue());
        }
      }
    }
  },

  onEditLastSpecialKey: function (field, e) {
    var me = this;

    if (e.getKey() === e.ENTER) {
      var f = me.editForm;
      if (f.getForm().isValid()) {
        me.onOK();
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
        me.getParentForm().refreshMainGrid(me.__lastId);
      }
    }
  }
});
