/**
 * 新增或编辑用户界面
 */
Ext.define("PSI.User.UserEditForm", {
  extend: "PSI.AFX.BaseDialogForm",

  config: {
    defaultOrg: null
  },

	/**
	 * 初始化组件
	 */
  initComponent: function () {
    var me = this;

    var entity = me.getEntity();
    me.adding = entity == null;

    var t = entity == null ? "新增用户" : "编辑用户";
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
      modal: true,
      onEsc: Ext.emptyFn,
      width: 470,
      height: me.adding ? 400 : 370,
      layout: "border",
      items: [{
        region: "north",
        border: 0,
        height: 90,
        html: logoHtml
      }, {
        region: "center",
        border: 0,
        id: "editForm",
        xtype: "form",
        layout: {
          type: "table",
          columns: 2
        },
        height: "100%",
        bodyPadding: 5,
        defaultType: 'textfield',
        fieldDefaults: {
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: "",
          msgTarget: 'side'
        },
        items: [{
          xtype: "hidden",
          name: "id",
          value: entity === null ? null : entity.id
        }, {
          id: "editLoginName",
          fieldLabel: "登录名",
          allowBlank: false,
          blankText: "没有输入登录名",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          name: "loginName",
          value: entity === null
            ? null
            : entity.loginName,
          colspan: 2,
          width: 430,
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "editName",
          fieldLabel: "姓名",
          allowBlank: false,
          blankText: "没有输入姓名",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          name: "name",
          value: entity === null
            ? null
            : entity.name,
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "editOrgCode",
          fieldLabel: "编码",
          allowBlank: false,
          blankText: "没有输入编码",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          name: "orgCode",
          value: entity === null
            ? null
            : entity.orgCode,
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          },
          colspan: 1
        }, {
          id: "editOrgName",
          xtype: "PSI_org_editor",
          fieldLabel: "所属组织",
          allowBlank: false,
          blankText: "没有选择组织机构",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          parentItem: this,
          value: entity === null
            ? null
            : entity.orgName,
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          },
          colspan: 2,
          width: 430
        }, {
          id: "editOrgId",
          xtype: "hidden",
          name: "orgId",
          value: entity === null
            ? null
            : entity.orgId
        }, {
          id: "editBirthday",
          fieldLabel: "生日",
          xtype: "datefield",
          format: "Y-m-d",
          name: "birthday",
          value: entity === null
            ? null
            : entity.birthday,
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "editIdCardNumber",
          fieldLabel: "身份证号",
          name: "idCardNumber",
          value: entity === null
            ? null
            : entity.idCardNumber,
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "editTel",
          fieldLabel: "联系电话",
          name: "tel",
          value: entity === null ? null : entity.tel,
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "editTel02",
          fieldLabel: "备用电话",
          name: "tel02",
          value: entity === null
            ? null
            : entity.tel02,
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "editAddress",
          fieldLabel: "家庭住址",
          name: "address",
          value: entity === null
            ? null
            : entity.address,
          listeners: {
            specialkey: {
              fn: me.onLastEditSpecialKey,
              scope: me
            }
          },
          colspan: 2,
          width: 430
        }, {
          xtype: "radiogroup",
          fieldLabel: "性别",
          id: "editGender",
          columns: 2,
          items: [{
            boxLabel: "男 ",
            name: "gender",
            inputValue: "男",
            checked: entity === null
              ? true
              : entity.gender == "男"
          }, {
            boxLabel: "女 ",
            name: "gender",
            inputValue: "女",
            checked: entity === null
              ? false
              : entity.gender == "女"
          }],
          width: 200
        }, {
          xtype: "radiogroup",
          fieldLabel: "能否登录",
          id: "editEnabled",
          columns: 2,
          items: [{
            boxLabel: "允许登录",
            name: "enabled",
            inputValue: true,
            checked: entity === null
              ? true
              : entity.enabled == 1
          }, {
            boxLabel: "<span style='color:red'>禁止登录</span>",
            name: "enabled",
            inputValue: false,
            checked: entity === null
              ? false
              : entity.enabled != 1
          }],
          width: 240
        }, {
          xtype: "displayfield",
          fieldLabel: "说明",
          colspan: 2,
          value: "新用户的默认登录密码是 123456",
          hidden: !me.adding
        }],
        buttons: [{
          text: "确定",
          formBind: true,
          iconCls: "PSI-button-ok",
          handler: me.onOK,
          scope: me
        }, {
          text: "取消",
          handler: function () {
            PSI.MsgBox.confirm("请确认是否取消操作?",
              function () {
                me.close();
              });
          },
          scope: me
        }]
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

    me.__editorList = ["editLoginName", "editName", "editOrgCode",
      "editOrgName", "editBirthday", "editIdCardNumber", "editTel",
      "editTel02", "editAddress"];

    if (me.getDefaultOrg()) {
      var org = me.getDefaultOrg();
      me.setOrg({
        id: org.get("id"),
        fullName: org.get("fullName")
      });
    }

    me.editLoginName = Ext.getCmp("editLoginName");
    me.editName = Ext.getCmp("editName");
    me.editOrgCode = Ext.getCmp("editOrgCode");
    me.editOrgId = Ext.getCmp("editOrgId");
    me.editOrgName = Ext.getCmp("editOrgName");
    me.editBirthday = Ext.getCmp("editBirthday");
    me.editIdCardNumber = Ext.getCmp("editIdCardNumber");
    me.editTel = Ext.getCmp("editTel");
    me.editTel02 = Ext.getCmp("editTel02");
    me.editAddress = Ext.getCmp("editAddress");
    me.editGender = Ext.getCmp("editGender");
    me.editEnabled = Ext.getCmp("editEnabled");
  },

  onWindowBeforeUnload: function (e) {
    return (window.event.returnValue = e.returnValue = '确认离开当前页面？');
  },

  onWndClose: function () {
    var me = this;

    Ext.get(window).un('beforeunload', me.onWindowBeforeUnload);
  },

  onWndShow: function () {
    var me = this;

    Ext.get(window).on('beforeunload', me.onWindowBeforeUnload);

    if (me.adding) {
      me.editLoginName.focus();
      return;
    }

    // 下面的是编辑

    var el = me.getEl();
    el.mask(PSI.Const.LOADING);
    Ext.Ajax.request({
      url: me.URL("Home/User/userInfo"),
      params: {
        id: me.getEntity().id
      },
      method: "POST",
      callback: function (options, success, response) {
        if (success) {
          var data = Ext.JSON.decode(response.responseText);

          me.editLoginName.setValue(data.loginName);
          me.editName.setValue(data.name);
          me.editOrgCode.setValue(data.orgCode);
          me.editBirthday.setValue(data.birthday);
          me.editIdCardNumber.setValue(data.idCardNumber);
          me.editTel.setValue(data.tel);
          me.editTel02.setValue(data.tel02);
          me.editAddress.setValue(data.address);
          me.editGender.setValue({
            gender: data.gender
          });
          me.editEnabled.setValue({
            enabled: data.enabled == 1
          });
          me.editOrgId.setValue(data.orgId);
          me.editOrgName.setValue(data.orgFullName);
        }

        el.unmask();
      }
    });

    me.editLoginName.focus();
    me.editLoginName.setValue(me.editLoginName.getValue());
  },

  setOrg: function (data) {
    var editOrgName = Ext.getCmp("editOrgName");
    editOrgName.setValue(data.fullName);

    var editOrgId = Ext.getCmp("editOrgId");
    editOrgId.setValue(data.id);
  },

  onOK: function () {
    var me = this;
    var f = Ext.getCmp("editForm");
    var el = f.getEl();
    el.mask("数据保存中...");
    f.submit({
      url: PSI.Const.BASE_URL + "Home/User/editUser",
      method: "POST",
      success: function (form, action) {
        el.unmask();
        PSI.MsgBox.showInfo("数据保存成功", function () {
          me.close();
          me.getParentForm().freshUserGrid();
        });
      },
      failure: function (form, action) {
        el.unmask();
        PSI.MsgBox.showInfo(action.result.msg, function () {
          Ext.getCmp("editName").focus();
        });
      }
    });
  },

  onEditSpecialKey: function (field, e) {
    if (e.getKey() === e.ENTER) {
      var me = this;
      var id = field.getId();
      for (var i = 0; i < me.__editorList.length; i++) {
        var editorId = me.__editorList[i];
        if (id === editorId) {
          var edit = Ext.getCmp(me.__editorList[i + 1]);
          edit.focus();
          edit.setValue(edit.getValue());
        }
      }
    }
  },

  onLastEditSpecialKey: function (field, e) {
    if (e.getKey() === e.ENTER) {
      var f = Ext.getCmp("editForm");
      if (f.getForm().isValid()) {
        this.onOK();
      }
    }
  }
});
