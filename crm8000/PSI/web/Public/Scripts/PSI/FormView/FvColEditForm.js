//
// 视图列 - 新建或编辑界面
//
Ext.define("PSI.FormView.FvColEditForm", {
  extend: "PSI.AFX.BaseDialogForm",

  config: {
    fv: null
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

    var t = entity == null ? "新增视图列" : "编辑视图列";
    var logoHtml = me.genLogoHtml(entity, t);

    // var col2Width = 550;
    var col3Width = 710;
    Ext.apply(me, {
      header: {
        title: me.formatTitle(PSI.Const.PROD_NAME),
        height: 40
      },
      width: 750,
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
        id: "PSI_FormView_FvColEditForm_editForm",
        xtype: "form",
        layout: {
          type: "table",
          columns: 3
        },
        height: "100%",
        bodyPadding: 5,
        defaultType: 'textfield',
        fieldDefaults: {
          labelWidth: 80,
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
          name: "fvId",
          value: me.getFv().get("id")
        }, {
          id: "PSI_FormView_FvColEditForm_editName",
          fieldLabel: "视图名称",
          readOnly: true,
          value: me.getFv().get("text"),
          colspan: 3,
          width: col3Width
        }, {
          id: "PSI_FormView_FvColEditForm_editCaption",
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
          id: "PSI_FormView_FvColEditForm_editValueFromTableName",
          fieldLabel: "取值表名",
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          },
          name: "valueFromTableName"
        }, {
          id: "PSI_FormView_FvColEditForm_editValueFromColName",
          fieldLabel: "取值列名",
          allowBlank: false,
          blankText: "没有输入取值列名",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          },
          name: "valueFromColName"
        }, {
          id: "PSI_FormView_FvColEditForm_editShowOrder",
          fieldLabel: "显示次序",
          allowBlank: false,
          blankText: "没有输入显示次序",
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
          id: "PSI_FormView_FvColEditForm_editWidth",
          fieldLabel: "宽度(px)",
          xtype: "numberfield",
          hideTrigger: true,
          allowDecimal: false,
          minValue: 10,
          value: 120,
          allowBlank: false,
          blankText: "没有输入宽度",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          name: "width",
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_FormView_FvColEditForm_editDisplayFormat",
          xtype: "combo",
          queryMode: "local",
          editable: false,
          valueField: "id",
          labelAlign: "right",
          labelSeparator: "",
          fieldLabel: "显示格式",
          allowBlank: false,
          blankText: "没有输入显示格式",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          store: Ext.create("Ext.data.ArrayStore", {
            fields: ["id", "text"],
            data: [[1, "原样"],
            [2, "带千分符的金额"],
            [3, "日期"],
            [4, "日期时间"]]
          }),
          value: 1,
          name: "displayFormat"
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

    me.editForm = Ext.getCmp("PSI_FormView_FvColEditForm_editForm");

    me.editName = Ext.getCmp("PSI_FormView_FvColEditForm_editName");
    me.editCaption = Ext.getCmp("PSI_FormView_FvColEditForm_editCaption");
    me.editValueFromTableName = Ext.getCmp("PSI_FormView_FvColEditForm_editValueFromTableName");
    me.editValueFromColName = Ext.getCmp("PSI_FormView_FvColEditForm_editValueFromColName");
    me.editShowOrder = Ext.getCmp("PSI_FormView_FvColEditForm_editShowOrder");
    me.editWidth = Ext.getCmp("PSI_FormView_FvColEditForm_editWidth");
    me.editDisplayFormat = Ext.getCmp("PSI_FormView_FvColEditForm_editDisplayFormat");

    me.__editorList = [
      me.editCaption, me.editValueFromTableName, me.editValueFromColName, me.editShowOrder, me.editWidth
    ];
  },

  onWndShow: function () {
    var me = this;

    Ext.get(window).on('beforeunload', me.onWindowBeforeUnload);

    var el = me.getEl();
    el && el.mask(PSI.Const.LOADING);
    Ext.Ajax.request({
      url: me.URL("Home/FormView/fvColInfo"),
      params: {
        id: me.adding ? null : me.getEntity().get("id"),
        fvId: me.getFv().get("id")
      },
      method: "POST",
      callback: function (options, success, response) {
        if (success) {
          el && el.unmask();

          me.editCaption.focus();

          var data = Ext.JSON.decode(response.responseText);

          if (me.adding) {
            // 新建
          } else {
            // 编辑
            me.editCaption.setValue(data.caption);
            me.editValueFromTableName.setValue(data.valueFromTableName);
            me.editValueFromColName.setValue(data.valueFromColName);
            me.editShowOrder.setValue(data.showOrder);
            me.editWidth.setValue(data.width);
            me.editDisplayFormat.setValue(parseInt(data.displayFormat));
          }
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
      url: me.URL("Home/FormView/editFvCol"),
      method: "POST",
      success: function (form, action) {
        el && el.unmask();
        PSI.MsgBox.tip("数据保存成功");
        me.focus();
        me.__lastId = action.result.id;
        me.close();
        var parentForm = me.getParentForm();
        if (parentForm) {
          parentForm.refreshColsGrid(me.getFv().get("id"), me.__lastId);
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
        // me.getParentForm().refreshColsGrid(me.__lastId);
      }
    }
  }
});
