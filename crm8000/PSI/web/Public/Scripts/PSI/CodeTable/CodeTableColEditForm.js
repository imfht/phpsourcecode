//
// 码表列 - 新建或编辑界面
//
Ext.define("PSI.CodeTable.CodeTableColEditForm", {
  extend: "PSI.AFX.BaseDialogForm",

  config: {
    codeTable: null
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

    var t = entity == null ? "新增码表列" : "编辑码表列";
    var logoHtml = me.genLogoHtml(entity, t);

    var col2Width = 550;
    var col3Width = 820;
    Ext.apply(me, {
      header: {
        title: me.formatTitle(PSI.Const.PROD_NAME),
        height: 40
      },
      width: 870,
      height: 430,
      layout: "border",
      items: [{
        region: "north",
        border: 0,
        height: 90,
        html: logoHtml
      }, {
        region: "center",
        border: 0,
        id: "PSI_CodeTable_CodeTableColEditForm_editForm",
        xtype: "form",
        layout: {
          type: "table",
          columns: 3
        },
        height: "100%",
        bodyPadding: 5,
        defaultType: 'textfield',
        fieldDefaults: {
          labelWidth: 120,
          labelAlign: "right",
          labelSeparator: "",
          msgTarget: 'side'
        },
        items: [{
          xtype: "hidden",
          name: "id",
          value: entity == null ? null : entity.get("id")
        }, {
          xtype: "hidden",
          name: "codeTableId",
          value: me.getCodeTable().get("id")
        }, {
          id: "PSI_CodeTable_CodeTableColEditForm_editName",
          fieldLabel: "码表名称",
          readOnly: true,
          value: me.getCodeTable().get("name")
        }, {
          id: "PSI_CodeTable_CodeTableColEditForm_editTableName",
          fieldLabel: "数据库表名",
          readOnly: true,
          colspan: 2,
          width: col2Width,
          value: me.getCodeTable().get("tableName")
        }, {
          id: "PSI_CodeTable_CodeTableColEditForm_editCaption",
          fieldLabel: "列标题",
          allowBlank: false,
          blankText: "没有输入列标题",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          },
          name: "caption"
        }, {
          id: "PSI_CodeTable_CodeTableColEditForm_editFieldName",
          fieldLabel: "列数据库名",
          allowBlank: false,
          blankText: "没有输入列数据库名",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          },
          colspan: 2,
          width: col2Width,
          name: "fieldName"
        }, {
          id: "PSI_CodeTable_CodeTableColEditForm_editFieldType",
          xtype: "combo",
          queryMode: "local",
          editable: false,
          valueField: "id",
          labelAlign: "right",
          labelSeparator: "",
          fieldLabel: "列数据类型",
          allowBlank: false,
          blankText: "没有输入列数据类型",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          store: Ext.create("Ext.data.ArrayStore", {
            fields: ["id", "text"],
            data: [["varchar", "varchar"],
            ["int", "int"],
            ["decimal", "decimal"],
            ["datetime", "datetime"]]
          }),
          value: "varchar",
          name: "fieldType",
          listeners: {
            change: {
              fn: me.onFieldTypeChange,
              scope: me
            }
          }
        }, {
          id: "PSI_CodeTable_CodeTableColEditForm_editFieldLength",
          fieldLabel: "列数据长度",
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          },
          xtype: "numberfield",
          hideTrigger: true,
          allowDecimal: false,
          minValue: 1,
          value: 255,
          name: "fieldLength"
        }, {
          id: "PSI_CodeTable_CodeTableColEditForm_editFieldDec",
          fieldLabel: "列小数位数",
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          },
          xtype: "numberfield",
          hideTrigger: true,
          allowDecimal: false,
          minValue: 0,
          value: 0,
          name: "fieldDecimal",
          disabled: true
        }, {
          id: "PSI_CodeTable_CodeTableColEditForm_editValueFrom",
          xtype: "combo",
          queryMode: "local",
          editable: false,
          valueField: "id",
          labelAlign: "right",
          labelSeparator: "",
          fieldLabel: "值来源",
          allowBlank: false,
          blankText: "没有输入值来源",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          store: Ext.create("Ext.data.ArrayStore", {
            fields: ["id", "text"],
            data: [[1, "直接录入"],
            [2, "引用系统数据字典"],
            [3, "引用其他码表"],
            [4, "引用自身数据"]]
          }),
          value: 1,
          name: "valueFrom",
          listeners: {
            change: {
              fn: me.onValueFromChange,
              scope: me
            }
          },
          colspan: 3
        }, {
          id: "PSI_CodeTable_CodeTableColEditForm_editValueFromTableName",
          fieldLabel: "引用表名",
          disabled: true,
          name: "valueFromTableName"
        }, {
          id: "PSI_CodeTable_CodeTableColEditForm_editValueFromColName",
          fieldLabel: "引用列名(关联用)",
          disabled: true,
          name: "valueFromColName"
        }, {
          id: "PSI_CodeTable_CodeTableColEditForm_editValueFromColNameDisplay",
          fieldLabel: "引用列名(显示用)",
          disabled: true,
          name: "valueFromColNameDisplay"
        }, {
          id: "PSI_CodeTable_CodeTableColEditForm_editIsVisible",
          xtype: "combo",
          queryMode: "local",
          editable: false,
          valueField: "id",
          labelAlign: "right",
          labelSeparator: "",
          fieldLabel: "对用户可见",
          allowBlank: false,
          blankText: "没有输入对用户可见",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          store: Ext.create("Ext.data.ArrayStore", {
            fields: ["id", "text"],
            data: [[1, "对用户可见"],
            [2, "对用户不可见"]]
          }),
          value: 1,
          name: "isVisible"
        }, {
          id: "PSI_CodeTable_CodeTableColEditForm_editMustInput",
          xtype: "combo",
          queryMode: "local",
          editable: false,
          valueField: "id",
          labelAlign: "right",
          labelSeparator: "",
          fieldLabel: "必须录入",
          allowBlank: false,
          blankText: "没有输入必须录入",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          store: Ext.create("Ext.data.ArrayStore", {
            fields: ["id", "text"],
            data: [[1, "非必须录入项"],
            [2, "必须录入"]]
          }),
          value: 1,
          name: "mustInput"
        }, {
          id: "PSI_CodeTable_CodeTableColEditForm_editWidthInView",
          fieldLabel: "列视图宽度(px)",
          xtype: "numberfield",
          hideTrigger: true,
          allowDecimal: false,
          minValue: 10,
          value: 120,
          allowBlank: false,
          blankText: "没有输入列视图宽度",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          name: "widthInView",
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_CodeTable_CodeTableColEditForm_editShowOrder",
          fieldLabel: "编辑界面显示次序",
          allowBlank: false,
          blankText: "没有输入编辑界面显示次序",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          xtype: "numberfield",
          hideTrigger: true,
          allowDecimal: false,
          name: "showOrder",
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_CodeTable_CodeTableColEditForm_editColSpan",
          fieldLabel: "编辑器列占位",
          allowBlank: false,
          blankText: "没有输入编辑器列占位",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          xtype: "numberfield",
          hideTrigger: true,
          allowDecimal: false,
          minValue: 1,
          name: "colSpan",
          value: 1,
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_CodeTable_CodeTableColEditForm_editShowOrderInView",
          fieldLabel: "视图界面显示次序",
          allowBlank: false,
          blankText: "没有输入视图界面显示次序",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          xtype: "numberfield",
          hideTrigger: true,
          allowDecimal: false,
          name: "showOrderInView",
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          },
          colspan: 1
        }, {
          id: "PSI_CodeTable_CodeTableColEditForm_editEditorXtype",
          xtype: "combo",
          queryMode: "local",
          editable: false,
          valueField: "id",
          labelAlign: "right",
          labelSeparator: "",
          fieldLabel: "编辑器类型",
          allowBlank: false,
          blankText: "没有输入编辑器类型",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          store: Ext.create("Ext.data.ArrayStore", {
            fields: ["id", "text"],
            data: []
          }),
          value: "textfield",
          name: "editorXtype",
          colspan: 3,
          width: col2Width
        }, {
          id: "PSI_CodeTable_CodeTableColEditForm_editMemo",
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
          width: col3Width,
          colspan: 3
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

    me.editForm = Ext.getCmp("PSI_CodeTable_CodeTableColEditForm_editForm");

    me.editName = Ext.getCmp("PSI_CodeTable_CodeTableColEditForm_editName");
    me.editTableName = Ext.getCmp("PSI_CodeTable_CodeTableColEditForm_editTableName");
    me.editCaption = Ext.getCmp("PSI_CodeTable_CodeTableColEditForm_editCaption");
    me.editFieldName = Ext.getCmp("PSI_CodeTable_CodeTableColEditForm_editFieldName");
    me.editFieldType = Ext.getCmp("PSI_CodeTable_CodeTableColEditForm_editFieldType");
    me.editFieldLength = Ext.getCmp("PSI_CodeTable_CodeTableColEditForm_editFieldLength");
    me.editFieldDec = Ext.getCmp("PSI_CodeTable_CodeTableColEditForm_editFieldDec");
    me.editValueFrom = Ext.getCmp("PSI_CodeTable_CodeTableColEditForm_editValueFrom");
    me.editValueFromTableName = Ext.getCmp("PSI_CodeTable_CodeTableColEditForm_editValueFromTableName");
    me.editValueFromColName = Ext.getCmp("PSI_CodeTable_CodeTableColEditForm_editValueFromColName");
    me.editValueFromColNameDisplay = Ext.getCmp("PSI_CodeTable_CodeTableColEditForm_editValueFromColNameDisplay");
    me.editWidthInView = Ext.getCmp("PSI_CodeTable_CodeTableColEditForm_editWidthInView");
    me.editShowOrder = Ext.getCmp("PSI_CodeTable_CodeTableColEditForm_editShowOrder");
    me.editColSpan = Ext.getCmp("PSI_CodeTable_CodeTableColEditForm_editColSpan");
    me.editShowOrderInView = Ext.getCmp("PSI_CodeTable_CodeTableColEditForm_editShowOrderInView");
    me.editEditorXtype = Ext.getCmp("PSI_CodeTable_CodeTableColEditForm_editEditorXtype");
    me.editMemo = Ext.getCmp("PSI_CodeTable_CodeTableColEditForm_editMemo");
    me.editIsVisible = Ext.getCmp("PSI_CodeTable_CodeTableColEditForm_editIsVisible");
    me.editMustInput = Ext.getCmp("PSI_CodeTable_CodeTableColEditForm_editMustInput");

    me.__editorList = [
      me.editCaption, me.editFieldName, me.editWidthInView, me.editShowOrder,
      me.editColSpan, me.editShowOrderInView, me.editMemo
    ];
  },

  onWndShow: function () {
    var me = this;

    Ext.get(window).on('beforeunload', me.onWindowBeforeUnload);

    var el = me.getEl();
    el && el.mask(PSI.Const.LOADING);
    Ext.Ajax.request({
      url: me.URL("Home/CodeTable/codeTableColInfo"),
      params: {
        id: me.adding ? null : me.getEntity().get("id"),
        tableId: me.getCodeTable().get("id")
      },
      method: "POST",
      callback: function (options, success, response) {
        if (success) {
          el && el.unmask();

          var data = Ext.JSON.decode(response.responseText);
          if (data.editorXtype) {
            var store = me.editEditorXtype.getStore();
            store.removeAll();
            store.add(data.editorXtype);
          }

          if (me.adding) {
            // 新建
            var store = me.editEditorXtype.getStore();
            me.editEditorXtype.setValue(store.getAt(0));
          } else {
            // 编辑
            me.editTableName.setReadOnly(true);
            me.editCaption.setReadOnly(true);
            me.editFieldName.setReadOnly(true);
            me.editFieldType.setReadOnly(true);
            me.editFieldLength.setReadOnly(true);
            me.editFieldLength.setDisabled(false);
            me.editFieldDec.setReadOnly(true);
            me.editFieldDec.setDisabled(false);

            var col = data.col;
            if (col) {
              me.editCaption.setValue(col.caption);
              me.editFieldName.setValue(col.fieldName);
              me.editFieldType.setValue(col.fieldType);
              me.editFieldDec.setValue(col.fieldDecimal);
              var valueFrom = parseInt(col.valueFrom);
              me.editValueFrom.setValue(valueFrom);
              me.editValueFromTableName.setValue(col.valueFromTableName);
              me.editValueFromColName.setValue(col.valueFromColName);
              me.editValueFromColNameDisplay.setValue(col.valueFromColNameDisplay);

              if (valueFrom == 4) {
                // 引用自身数据
                me.editValueFrom.setReadOnly(true);
                me.editValueFromTableName.setReadOnly(true);
                me.editValueFromTableName.setDisabled(false);
                me.editValueFromColName.setDisabled(false);
                me.editValueFromColNameDisplay.setDisabled(false);
              }

              me.editIsVisible.setValue(parseInt(col.isVisible));
              me.editMustInput.setValue(parseInt(col.mustInput));
              me.editWidthInView.setValue(col.widthInView);
              me.editShowOrder.setValue(col.showOrder);
              me.editColSpan.setValue(col.colSpan);
              me.editShowOrderInView.setValue(col.showOrderInView);
              me.editEditorXtype.setValue(col.editorXtype);
              me.editMemo.setValue(col.memo);

              if (col.sysCol == 1) {
                // 系统列的时候，进一步限制字段的修改
                me.editValueFrom.setReadOnly(true);
                me.editIsVisible.setReadOnly(true);
                me.editMustInput.setReadOnly(true);
                me.editWidthInView.setReadOnly(true);
                me.editWidthInView.clearInvalid();
                me.editWidthInView.setMinValue(0);
                me.editEditorXtype.setReadOnly(true);
              } else {
                me.editCaption.setDisabled(false);
                me.editCaption.setReadOnly(false);
              }

              if (col.isVisible == 1) {
                // 可见的字段，也能设置在视图中的宽度
                me.editWidthInView.setReadOnly(false);
              }
            }
          }

          me.editCaption.focus();
        }
      }
    });
  },

  onOK: function () {
    var me = this;

    var f = me.editForm;
    var el = f.getEl();
    el && el.mask(PSI.Const.SAVING);
    f.submit({
      url: me.URL("Home/CodeTable/editCodeTableCol"),
      method: "POST",
      success: function (form, action) {
        el && el.unmask();
        PSI.MsgBox.tip("数据保存成功");
        me.focus();
        me.__lastId = action.result.id;
        me.close();
        var parentForm = me.getParentForm();
        if (parentForm) {
          parentForm.refreshColsGrid(me.__lastId);
        }
      },
      failure: function (form, action) {
        el && el.unmask();
        PSI.MsgBox.showInfo(action.result.msg, function () {
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
        me.getParentForm().refreshColsGrid(me.__lastId);
      }
    }
  },

  onFieldTypeChange: function () {
    var me = this;
    var v = me.editFieldType.getValue();
    if (v == "varchar") {
      me.editFieldLength.setValue(255);
      me.editFieldLength.setDisabled(false);
      me.editFieldDec.setDisabled(true);
      me.editFieldDec.setValue(null);
    } else if (v == "int") {
      me.editFieldLength.setValue(11);
      me.editFieldLength.setDisabled(true);
      me.editFieldDec.setDisabled(true);
      me.editFieldDec.setValue(null);
    } else if (v == "decimal") {
      me.editFieldLength.setValue(19);
      me.editFieldLength.setDisabled(true);
      me.editFieldDec.setDisabled(false);
      me.editFieldDec.setValue(2);
    } else if (v == "datetime") {
      me.editFieldLength.setValue(null);
      me.editFieldLength.setDisabled(true);
      me.editFieldDec.setDisabled(true);
      me.editFieldDec.setValue(null);
    }
  },

  onValueFromChange: function () {
    var me = this;
    var v = me.editValueFrom.getValue();
    if (v == 1) {
      me.editValueFromTableName.setDisabled(true);
      me.editValueFromColName.setDisabled(true);
      me.editValueFromColNameDisplay.setDisabled(true);
    } else if (v == 2) {
      me.editValueFromTableName.setDisabled(false);
      me.editValueFromColName.setDisabled(false);
      me.editValueFromColNameDisplay.setDisabled(false);
    } else if (v == 3) {
      me.editValueFromTableName.setDisabled(false);
      me.editValueFromColName.setDisabled(false);
      me.editValueFromColNameDisplay.setDisabled(false);
    } else if (v == 4) {
      me.editValueFromTableName.setDisabled(true);
      me.editValueFromColName.setDisabled(false);
      me.editValueFromColNameDisplay.setDisabled(false);
    }
  }
});
