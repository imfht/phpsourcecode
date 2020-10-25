/**
 * 修改我的密码
 */
Ext.define("PSI.User.ChangeMyPasswordForm", {
  extend: "Ext.panel.Panel",

  config: {
    loginUserId: null,
    loginUserName: null,
    loginUserFullName: null
  },

  border: 0,
  layout: "border",

	/**
	 * 初始化组件
	 */
  initComponent: function () {
    var me = this;
    var user = {
      id: me.getLoginUserId(),
      loginName: me.getLoginUserName(),
      name: me.getLoginUserFullName()
    };

    Ext.apply(me, {
      items: [{
        region: "center",
        xtype: "panel",
        layout: "absolute",
        border: 0,
        items: [{
          id: "PSI_User_ChangeMyPasswordForm_editForm",
          x: 200,
          y: 50,
          xtype: "form",
          layout: {
            type: "table",
            columns: 1
          },
          height: 170,
          width: 300,
          defaultType: 'textfield',
          border: 0,
          fieldDefaults: {
            labelWidth: 60,
            labelAlign: "right",
            labelSeparator: "",
            msgTarget: 'side',
            width: 300
          },
          items: [{
            xtype: "hidden",
            name: "userId",
            value: user.id
          }, {
            fieldLabel: "登录名",
            xtype: "displayfield",
            value: user.loginName
          }, {
            fieldLabel: "用户名",
            xtype: "displayfield",
            value: user.name
          }, {
            id: "PSI_User_ChangeMyPasswordForm_editOldPassword",
            fieldLabel: "旧密码",
            allowBlank: false,
            blankText: "没有输入旧密码",
            beforeLabelTextTpl: PSI.Const.REQUIRED,
            inputType: "password",
            name: "oldPassword",
            listeners: {
              specialkey: {
                fn: me.onEditOldPasswordSpecialKey,
                scope: me
              }
            }
          }, {
            id: "PSI_User_ChangeMyPasswordForm_editNewPassword",
            fieldLabel: "新密码",
            allowBlank: false,
            blankText: "没有输入新密码",
            beforeLabelTextTpl: PSI.Const.REQUIRED,
            inputType: "password",
            name: "newPassword",
            listeners: {
              specialkey: {
                fn: me.onEditNewPasswordSpecialKey,
                scope: me
              }
            }
          }, {
            id: "PSI_User_ChangeMyPasswordForm_editConfirmPassword",
            fieldLabel: "确认密码",
            allowBlank: false,
            blankText: "没有输入确认密码",
            beforeLabelTextTpl: PSI.Const.REQUIRED,
            inputType: "password",
            listeners: {
              specialkey: {
                fn: me.onEditConfirmPasswordSpecialKey,
                scope: me
              }
            }
          }],
          buttons: [{
            id: "PSI_User_ChangeMyPasswordForm_buttonOK",
            text: "修改密码",
            formBind: true,
            handler: me.onOK,
            scope: me,
            iconCls: "PSI-button-ok"
          }, {
            text: "取消",
            handler: function () {
              me.closeWindow();
            }
          }]
        }]
      }]
    });

    me.callParent(arguments);

    me.editNewPassword = Ext
      .getCmp("PSI_User_ChangeMyPasswordForm_editNewPassword");
    me.editConfirmPassword = Ext
      .getCmp("PSI_User_ChangeMyPasswordForm_editConfirmPassword");
    me.editForm = Ext.getCmp("PSI_User_ChangeMyPasswordForm_editForm");
    me.buttonOK = Ext.getCmp("PSI_User_ChangeMyPasswordForm_buttonOK");
  },

	/**
	 * 修改密码
	 */
  onOK: function () {
    var me = this;

    var editNewPassword = me.editNewPassword;
    var editConfirmPassword = me.editConfirmPassword;

    var np = editNewPassword.getValue();
    var cp = editConfirmPassword.getValue();

    if (np != cp) {
      PSI.MsgBox.showInfo("确认密码与新密码不一致", function () {
        editNewPassword.focus();
      });
      return;
    }

    var form = me.editForm;
    var el = Ext.getBody();
    form.submit({
      url: PSI.Const.BASE_URL + "Home/User/changeMyPasswordPOST",
      method: "POST",
      success: function (form, action) {
        el.unmask();
        PSI.MsgBox.showInfo("成功修改登录密码", function () {
          me.closeWindow();
        });
      },
      failure: function (form, action) {
        el.unmask();
        PSI.MsgBox.showInfo(action.result.msg);
      }
    });
  },

  onEditOldPasswordSpecialKey: function (field, e) {
    var me = this;

    if (e.getKey() == e.ENTER) {
      me.editNewPassword.focus();
    }
  },

  onEditNewPasswordSpecialKey: function (field, e) {
    var me = this;

    if (e.getKey() == e.ENTER) {
      me.editConfirmPassword.focus();
    }
  },

  onEditConfirmPasswordSpecialKey: function (field, e) {
    var me = this;

    if (e.getKey() == e.ENTER) {
      me.buttonOK.focus();
    }
  },

  closeWindow: function () {
    if (PSI.Const.MOT == "0") {
      window.location.replace(PSI.Const.BASE_URL);
    } else {
      window.close();

      if (!window.closed) {
        window.location.replace(PSI.Const.BASE_URL);
      }
    }
  }
});
