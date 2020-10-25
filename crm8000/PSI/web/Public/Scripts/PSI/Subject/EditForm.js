/**
 * 科目 - 新增或编辑界面
 */
Ext.define("PSI.Subject.EditForm", {
  extend: "PSI.AFX.BaseDialogForm",

  config: {
    company: null
  },

	/**
	 * 初始化组件
	 */
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

    var t = entity == null ? "新增科目" : "编辑科目";
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
      height: 340,
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
        id: "PSI_Subject_EditForm_editForm",
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
          id: "PSI_Subject_EditForm_hiddenId",
          name: "id",
          value: entity == null ? null : entity
            .get("id")
        }, {
          xtype: "hidden",
          name: "companyId",
          value: me.getCompany().get("id")
        }, {
          xtype: "displayfield",
          fieldLabel: "组织机构",
          value: me.getCompany().get("name")
        }, {
          xtype: "hidden",
          id: "PSI_Subject_EditForm_hiddenParentCode",
          name: "parentCode",
          value: null
        }, {
          id: "PSI_Subject_EditForm_editParentCode",
          xtype: "PSI_parent_subject_field",
          fieldLabel: "上级科目",
          allowBlank: false,
          blankText: "没有输入上级科目",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          listeners: {
            specialkey: {
              fn: me.onEditParentCodeSpecialKey,
              scope: me
            }
          },
          callbackFunc: me.onParentCodeCallback,
          callbackScope: me,
          companyId: me.getCompany().get("id")
        }, {
          id: "PSI_Subject_EditForm_editCode",
          fieldLabel: "科目码",
          allowBlank: false,
          blankText: "没有输入科目码",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          name: "code",
          listeners: {
            specialkey: {
              fn: me.onEditCodeSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_Subject_EditForm_editName",
          fieldLabel: "科目名称",
          allowBlank: false,
          blankText: "没有输入科目名称",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          name: "name",
          listeners: {
            specialkey: {
              fn: me.onEditNameSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_Subject_EditForm_editIsLeaf",
          xtype: "combo",
          name: "isLeaf",
          queryMode: "local",
          editable: false,
          valueField: "id",
          fieldLabel: "末级科目",
          store: Ext.create("Ext.data.ArrayStore", {
            fields: ["id", "text"],
            data: [[1, "是"], [0, "否"]]
          }),
          value: 1
        }],
        buttons: buttons
      }]
    });

    me.callParent(arguments);

    me.editForm = Ext.getCmp("PSI_Subject_EditForm_editForm");

    me.hiddenId = Ext.getCmp("PSI_Subject_EditForm_hiddenId");

    me.hiddenParentCode = Ext
      .getCmp("PSI_Subject_EditForm_hiddenParentCode");
    me.editParentCode = Ext.getCmp("PSI_Subject_EditForm_editParentCode");
    me.editCode = Ext.getCmp("PSI_Subject_EditForm_editCode");
    me.editName = Ext.getCmp("PSI_Subject_EditForm_editName");
    me.editIsLeaf = Ext.getCmp("PSI_Subject_EditForm_editIsLeaf");
  },

	/**
	 * 保存
	 */
  onOK: function (thenAdd) {
    var me = this;

    me.hiddenParentCode.setValue(me.editParentCode.getIdValue());

    var f = me.editForm;
    var el = f.getEl();
    el.mask(PSI.Const.SAVING);
    var sf = {
      url: me.URL("Home/Subject/editSubject"),
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
        el.unmask();
        PSI.MsgBox.showInfo(action.result.msg, function () {
          me.editCode.focus();
        });
      }
    };
    f.submit(sf);
  },

  onEditParentCodeSpecialKey: function (field, e) {
    var me = this;

    if (e.getKey() == e.ENTER) {
      var editName = me.editCode;
      editName.focus();
      editName.setValue(editName.getValue());
    }
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
      var f = me.editForm;
      if (f.getForm().isValid()) {
        me.onOK(me.adding);
      }
    }
  },

  clearEdit: function () {
    var me = this;
    me.editParentCode.focus();

    var editors = [me.editParentCode, me.editCode, me.editName];
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
        me.getParentForm().onCompanyGridSelect();
      }
    }
  },

  onWndShow: function () {
    var me = this;

    Ext.get(window).on('beforeunload', me.onWindowBeforeUnload);

    if (me.adding) {
      me.editParentCode.focus();
      return;
    }

    var el = me.getEl() || Ext.getBody();
    el.mask(PSI.Const.LOADING);
    var r = {
      url: me.URL("Home/Subject/subjectInfo"),
      params: {
        id: me.hiddenId.getValue()
      },
      callback: function (options, success, response) {
        el.unmask();

        if (success) {
          var data = me.decodeJSON(response.responseText);

          me.editParentCode.setValue(data.parentCode);
          me.editParentCode.setReadOnly(true);

          me.editCode.setValue(data.code);

          me.editName.setValue(data.name);
          me.editIsLeaf.setValue(parseInt(data.isLeaf));

          // 编辑的时候不允许编辑科目码
          me.editCode.setReadOnly(true);

          if (data.code.length == 4) {
            // 一级科目
            me.editName.setReadOnly(true);

            me.editIsLeaf.focus();
          } else {
            me.editName.focus();
            me.editName.setValue(me.editName.getValue());
          }
        } else {
          me.showInfo("网络错误")
        }
      }
    };

    me.ajax(r);
  },

  onParentCodeCallback: function (data) {
    var me = this;
    me.editCode.setValue(data.code);
    me.editName.setValue(data.name + " - ");
  }
});
