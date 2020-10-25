//
// 账样字段 - 新增或编辑界面
//
Ext.define("PSI.Subject.FmtColEditForm", {
  extend: "PSI.AFX.BaseDialogForm",

  config: {
    subject: null,
    company: null
  },

  initComponent: function () {
    var me = this;

    var entity = me.getEntity();

    me.adding = entity == null;

    var buttons = [];
    if (!entity) {
      var btn = {
        text: "保存并继续新增",
        formBind: true,
        handler: function () {
          me.onOK(true);
        },
        scope: me
      };

      buttons.push(btn);
    }

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
      text: entity == null ? "关闭" : "取消",
      handler: function () {
        me.close();
      },
      scope: me
    };
    buttons.push(btn);

    var t = entity == null ? "新增账样字段" : "编辑账样字段";
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
      height: 310,
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
        height: 90,
        border: 0,
        html: logoHtml
      }, {
        region: "center",
        border: 0,
        id: "PSI_Subject_FmtColEditForm_editForm",
        xtype: "form",
        layout: {
          type: "table",
          columns: 1
        },
        height: "100%",
        bodyPadding: 5,
        defaultType: 'textfield',
        fieldDefaults: {
          labelWidth: 80,
          labelAlign: "right",
          labelSeparator: "",
          msgTarget: 'side',
          width: 370,
          margin: "5"
        },
        items: [{
          xtype: "hidden",
          id: "PSI_Subject_FmtColEditForm_hiddenId",
          name: "id",
          value: entity == null ? null : entity
            .get("id")
        }, {
          xtype: "hidden",
          name: "companyId",
          value: me.getCompany().get("id")
        }, {
          xtype: "hidden",
          name: "subjectCode",
          value: me.getSubject().get("code")
        }, {
          xtype: "displayfield",
          fieldLabel: "科目",
          value: me.getSubject().get("code") + " - "
            + me.getSubject().get("name")
        }, {
          id: "PSI_Subject_FmtColEditForm_editCaption",
          fieldLabel: "列标题",
          allowBlank: false,
          blankText: "没有输入列标题",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          name: "fieldCaption",
          listeners: {
            specialkey: {
              fn: me.onEditCaptionSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_Subject_FmtColEditForm_editName",
          fieldLabel: "数据库字段",
          allowBlank: false,
          blankText: "没有输入数据库字段名称",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          name: "fieldName",
          listeners: {
            specialkey: {
              fn: me.onEditNameSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_Subject_FmtColEditForm_editFieldType",
          xtype: "combo",
          name: "fieldType",
          queryMode: "local",
          editable: false,
          valueField: "id",
          fieldLabel: "类型",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          store: Ext.create("Ext.data.ArrayStore", {
            fields: ["id", "text"],
            data: [[1, "字符串"], [2, "日期"],
            [3, "金额(两位小数)"]]
          }),
          value: 1,
          listeners: {
            specialkey: {
              fn: me.onEditTypeSpecialKey,
              scope: me
            }
          }
        }],
        buttons: buttons
      }]
    });

    me.callParent(arguments);

    me.editForm = Ext.getCmp("PSI_Subject_FmtColEditForm_editForm");

    me.hiddenId = Ext.getCmp("PSI_Subject_FmtColEditForm_hiddenId");
    me.editCaption = Ext.getCmp("PSI_Subject_FmtColEditForm_editCaption");
    me.editName = Ext.getCmp("PSI_Subject_FmtColEditForm_editName");
    me.editType = Ext.getCmp("PSI_Subject_FmtColEditForm_editFieldType");
  },

  // 保存
  onOK: function (thenAdd) {
    var me = this;

    var f = me.editForm;
    var el = f.getEl();
    el && el.mask(PSI.Const.SAVING);
    var sf = {
      url: me.URL("Home/Subject/editFmtCol"),
      method: "POST",
      success: function (form, action) {
        me.__lastId = action.result.id;

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
        el && el.unmask();
        PSI.MsgBox.showInfo(action.result.msg, function () {
          me.editCaption.focus();
        });
      }
    };
    f.submit(sf);
  },

  onEditCaptionSpecialKey: function (field, e) {
    var me = this;

    if (e.getKey() == e.ENTER) {
      me.editName.focus();
      me.editName.setValue(me.editName.getValue());
    }
  },

  onEditNameSpecialKey: function (field, e) {
    var me = this;

    if (e.getKey() == e.ENTER) {
      me.editType.focus();
    }
  },

  onEditTypeSpecialKey: function (field, e) {
    var me = this;

    if (e.getKey() == e.ENTER) {
      var f = me.editForm;
      if (f.getForm().isValid()) {
        me.onOK(me.adding);
      }
    }
  },

  clearEdit: function () {
    var me = this;
    me.editCaption.focus();

    var editors = [me.editCaption, me.editName];
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

    if (me.__lastId) {
      if (me.getParentForm()) {
        me.getParentForm().refreshFmtColsGrid();
      }
    }
  },

  onWndShow: function () {
    var me = this;

    Ext.get(window).on('beforeunload', me.onWindowBeforeUnload);

    if (me.adding) {
      me.editCaption.focus();
      return;
    }

    var el = me.getEl() || Ext.getBody();
    el.mask(PSI.Const.LOADING);
    var r = {
      url: me.URL("Home/Subject/fmtColInfo"),
      params: {
        id: me.hiddenId.getValue()
      },
      callback: function (options, success, response) {
        el.unmask();

        if (success) {
          var data = me.decodeJSON(response.responseText);
          me.editCaption.setValue(data.caption);
          me.editName.setValue(data.fieldName);
          me.editType.setValue(data.fieldType);
          if (parseInt(data.sysCol) != 0) {
            // 标准账样字段，不能字段名和类型
            me.editName.setReadOnly(true);
            me.editType.setReadOnly(true);
          }

          if (parseInt(data.dbTableCreated) == 1) {
            // 已经创建了数据库表，字段名和类型也不能修改了
            me.editName.setReadOnly(true);
            me.editType.setReadOnly(true);
          }

          me.editCaption.focus();
        } else {
          me.showInfo("网络错误")
        }
      }
    };

    me.ajax(r);
  }
});
