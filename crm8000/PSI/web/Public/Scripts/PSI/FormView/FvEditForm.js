//
// 视图 - 新建或编辑界面
//
Ext.define("PSI.FormView.FvEditForm", {
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

    var t = entity == null ? "新增视图" : "编辑视图";
    var logoHtml = me.genLogoHtml(entity, t);

    Ext.apply(me, {
      header: {
        title: me.formatTitle(PSI.Const.PROD_NAME),
        height: 40
      },
      width: 550,
      height: 410,
      layout: "border",
      items: [{
        region: "north",
        border: 0,
        height: 90,
        html: logoHtml
      }, {
        region: "center",
        border: 0,
        id: "PSI_FormView_FvEditForm_editForm",
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
          value: entity == null ? null : entity.get("id")
        }, {
          id: "PSI_FormView_FvEditForm_editCategoryId",
          xtype: "hidden",
          name: "categoryId"
        }, {
          id: "PSI_FormView_FvEditForm_editCategory",
          xtype: "psi_fvcategoryfield",
          fieldLabel: "分类",
          allowBlank: false,
          blankText: "没有输入视图分类",
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
          id: "PSI_FormView_FvEditForm_editCode",
          fieldLabel: "编码",
          name: "code",
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_FormView_FvEditForm_editName",
          fieldLabel: "视图名称",
          allowBlank: false,
          blankText: "没有输入中文名称",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          name: "name",
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_FormView_FvEditForm_editModuleName",
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
          }
        }, {
          id: "PSI_FormView_FvEditForm_editXtype",
          xtype: "combo",
          queryMode: "local",
          editable: false,
          valueField: "id",
          fieldLabel: "xtype",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          store: Ext.create("Ext.data.ArrayStore", {
            fields: ["id", "text"],
            data: []
          }),
          name: "xtype",
          width: 510,
          colspan: 2
        }, {
          id: "PSI_FormView_FvEditForm_editRegion",
          xtype: "combo",
          queryMode: "local",
          editable: false,
          valueField: "id",
          fieldLabel: "位置",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          store: Ext.create("Ext.data.ArrayStore", {
            fields: ["id", "text"],
            data: [["center", "主体"], ["west", "左"], ["south", "下"]]
          }),
          name: "region",
          value: "center"
        }, {
          id: "PSI_FormView_FvEditForm_editWidthOrHeight",
          fieldLabel: "宽度/高度",
          allowBlank: false,
          blankText: "没有输入宽度/高度",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          name: "widthOrHeight",
          value: "100%",
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_FormView_FvEditForm_editLayout",
          xtype: "combo",
          queryMode: "local",
          editable: false,
          valueField: "id",
          fieldLabel: "布局",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          store: Ext.create("Ext.data.ArrayStore", {
            fields: ["id", "text"],
            data: [[1, "填满整个区域"], [2, "左右布局"], [3, "上下布局"]]
          }),
          name: "layout",
          value: 2
        }, {
          id: "PSI_FormView_FvEditForm_editDataSourceType",
          xtype: "combo",
          queryMode: "local",
          editable: false,
          valueField: "id",
          fieldLabel: "数据源",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          store: Ext.create("Ext.data.ArrayStore", {
            fields: ["id", "text"],
            data: [[1, "码表"], [2, "自定义表单"], [0, "[无]"]]
          }),
          name: "dataSourceType",
          value: 1
        }, {
          id: "PSI_FormView_FvEditForm_editDataSouceTableName",
          fieldLabel: "数据源表名",
          name: "dataSourceTableName",
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          },
          width: 510,
          colspan: 2
        }, {
          id: "PSI_FormView_FvEditForm_editHandlerClassName",
          fieldLabel: "业务逻辑类名",
          name: "handlerClassName",
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          },
          width: 510,
          colspan: 2
        }, {
          id: "PSI_FormView_FvEditForm_editMemo",
          fieldLabel: "备注",
          name: "memo",
          value: entity == null ? null : entity.get("note"),
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

    me.editForm = Ext.getCmp("PSI_FormView_FvEditForm_editForm");

    me.editCategoryId = Ext.getCmp("PSI_FormView_FvEditForm_editCategoryId");
    me.editCategory = Ext.getCmp("PSI_FormView_FvEditForm_editCategory");
    me.editCode = Ext.getCmp("PSI_FormView_FvEditForm_editCode");
    me.editName = Ext.getCmp("PSI_FormView_FvEditForm_editName");
    me.editModuleName = Ext.getCmp("PSI_FormView_FvEditForm_editModuleName");
    me.editXtype = Ext.getCmp("PSI_FormView_FvEditForm_editXtype");
    me.editRegion = Ext.getCmp("PSI_FormView_FvEditForm_editRegion");
    me.editWidthOrHeight = Ext.getCmp("PSI_FormView_FvEditForm_editWidthOrHeight");
    me.editLayout = Ext.getCmp("PSI_FormView_FvEditForm_editLayout");
    me.editDataSourceType = Ext.getCmp("PSI_FormView_FvEditForm_editDataSourceType");
    me.editDataSourceTableName = Ext.getCmp("PSI_FormView_FvEditForm_editDataSouceTableName");
    me.editHandlerClassName = Ext.getCmp("PSI_FormView_FvEditForm_editHandlerClassName");
    me.editMemo = Ext.getCmp("PSI_FormView_FvEditForm_editMemo");

    me.__editorList = [
      me.editCategory, me.editCode, me.editName, me.editModuleName,
      me.editWidthOrHeight, me.editDataSourceTableName, me.editHandlerClassName, me.editMemo];

    var c = me.getCategory();
    if (c) {
      me.editCategory.setIdValue(c.get("id"));
      me.editCategory.setValue(c.get("name"));
    }
  },

  onWndShow: function () {
    var me = this;

    Ext.get(window).on('beforeunload', me.onWindowBeforeUnload);

    var el = me.getEl();
    el && el.mask(PSI.Const.LOADING);
    me.ajax({
      url: me.URL("Home/FormView/fvInfo"),
      params: {
        id: me.adding ? null : me.getEntity().get("id")
      },
      callback: function (options, success, response) {
        if (success) {
          var data = Ext.JSON.decode(response.responseText);
          var store = me.editXtype.getStore();
          store.removeAll();
          store.add(data.allXtype);

          if (me.adding) {
            me.editXtype.setValue(store.getAt(0));
          } else {
            // 编辑
            me.editCategory.setIdValue(data.categoryId);
            me.editCategory.setValue(data.categoryName);
            me.editCode.setValue(data.code);
            me.editName.setValue(data.name);
            me.editModuleName.setValue(data.moduleName);
            me.editXtype.setValue(data.xtype);
            me.editRegion.setValue(data.region);
            me.editWidthOrHeight.setValue(data.widthOrHeight);
            me.editLayout.setValue(parseInt(data.layout));
            me.editDataSourceType.setValue(parseInt(data.dataSourceType));
            me.editDataSourceTableName.setValue(data.dataSourceTableName);
            me.editHandlerClassName.setValue(data.handlerClassName);
            me.editMemo.setValue(data.memo);

            me.editLayout.setReadOnly(true);
            me.editRegion.setReadOnly(true);

            if (data.parentId) {
              // 子视图
              me.editCategory.setReadOnly(true);
              me.editCode.setReadOnly(true);
              me.editModuleName.setReadOnly(true);

              me.editWidthOrHeight.focus();
            }
          }
        }

        el && el.unmask();
      }
    });

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
      url: me.URL("Home/FormView/editFv"),
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
