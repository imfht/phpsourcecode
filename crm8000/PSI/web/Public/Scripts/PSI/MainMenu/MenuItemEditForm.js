//
// 主菜单维护 - 新增或编辑界面
//
Ext.define("PSI.MainMenu.MenuItemEditForm", {
  extend: "PSI.AFX.BaseDialogForm",

	/**
	 * 初始化组件
	 */
  initComponent: function () {
    var me = this;

    var entity = me.getEntity();

    me.adding = entity == null;

    var buttons = [];

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

    var t = entity == null ? "新增菜单项" : "编辑菜单项";
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
        id: "PSI_MainMenu_MenuItemEditForm_editForm",
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
          id: "PSI_MainMenu_MenuItemEditForm_editFid",
          fieldLabel: "fid",
          xtype: "psi_fidfield",
          allowBlank: false,
          showModal: true,
          blankText: "没有输入fid",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          listeners: {
            specialkey: {
              fn: me.onEditFidSpecialKey,
              scope: me
            }
          },
          callbackFunc: me.__fidCallbackFunc,
          callbackScope: me
        }, {
          id: "PSI_MainMenu_MenuItemEditForm_hiddenFid",
          name: "fid",
          xtype: "hidden"
        }, {
          id: "PSI_MainMenu_MenuItemEditForm_editCaption",
          fieldLabel: "菜单标题",
          allowBlank: false,
          blankText: "没有输入菜单标题",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          name: "caption",
          listeners: {
            specialkey: {
              fn: me.onEditCaptionSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_MainMenu_MenuItemEditForm_editParentMenu",
          xtype: "psi_menuitemfield",
          fieldLabel: "上级菜单",
          allowBlank: false,
          blankText: "没有输入上级菜单",
          showModal: true,
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          listeners: {
            specialkey: {
              fn: me.onEditParentMenuSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_MainMenu_MenuItemEditForm_hiddenParentMenuId",
          name: "parentMenuId",
          xtype: "hidden"
        }, {
          id: "PSI_MainMenu_MenuItemEditForm_editShowOrder",
          xtype: "numberfield",
          fieldLabel: "显示排序",
          hideTrigger: true,
          allowDecimals: false,
          allowBlank: false,
          blankText: "没有输入显示排序",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          name: "showOrder",
          listeners: {
            specialkey: {
              fn: me.onEditShowOrderSpecialKey,
              scope: me
            }
          }
        }],
        buttons: buttons
      }]
    });

    me.callParent(arguments);

    me.editForm = Ext.getCmp("PSI_MainMenu_MenuItemEditForm_editForm");

    me.editFid = Ext.getCmp("PSI_MainMenu_MenuItemEditForm_editFid");
    me.editCaption = Ext
      .getCmp("PSI_MainMenu_MenuItemEditForm_editCaption");
    me.editParentMenu = Ext
      .getCmp("PSI_MainMenu_MenuItemEditForm_editParentMenu");
    me.editShowOrder = Ext
      .getCmp("PSI_MainMenu_MenuItemEditForm_editShowOrder");

    me.hiddenFid = Ext.getCmp("PSI_MainMenu_MenuItemEditForm_hiddenFid");
    me.hiddenParentMenuId = Ext
      .getCmp("PSI_MainMenu_MenuItemEditForm_hiddenParentMenuId");
  },

	/**
	 * 保存
	 */
  onOK: function () {
    var me = this;

    me.hiddenFid.setValue(me.editFid.getIdValue());
    me.hiddenParentMenuId.setValue(me.editParentMenu.getIdValue());

    var f = me.editForm;
    var el = f.getEl();
    el.mask(PSI.Const.SAVING);
    var sf = {
      url: me.URL("Home/MainMenu/editMenuItem"),
      method: "POST",
      success: function (form, action) {
        me.__lastId = action.result.id;

        el.unmask();

        PSI.MsgBox.tip("数据保存成功");
        me.focus();
        me.close();
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

  onEditFidSpecialKey: function (field, e) {
    var me = this;

    if (e.getKey() == e.ENTER) {
      me.editCaption.focus();
      me.editCaption.setValue(me.editCaption.getValue());
    }
  },

  onEditCaptionSpecialKey: function (field, e) {
    var me = this;

    if (e.getKey() == e.ENTER) {
      me.editParentMenu.focus();
      me.editParentMenu.setValue(me.editParentMenu.getValue());
    }
  },

  onEditParentMenuSpecialKey: function (field, e) {
    var me = this;

    if (e.getKey() == e.ENTER) {
      me.editShowOrder.focus();
      me.editShowOrder.setValue(me.editShowOrder.getValue());
    }
  },

  onEditShowOrderSpecialKey: function (field, e) {
    var me = this;

    if (e.getKey() == e.ENTER) {
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
  },

  onWndShow: function () {
    var me = this;

    Ext.get(window).on('beforeunload', me.onWindowBeforeUnload);

    me.editFid.focus();

    var entity = me.getEntity();
    if (entity === null) {
      return;
    }

    var el = me.getEl();
    el && el.mask("数据加载中...");
    var r = {
      url: me.URL("Home/MainMenu/menuItemInfo"),
      method: "POST",
      params: {
        id: entity.get("id")
      },
      callback: function (options, success, response) {
        el && el.unmask();
        if (success) {
          var data = Ext.JSON.decode(response.responseText);
          me.editFid.setIdValue(data.fid);
          me.editFid.setValue(data.fidName);
          me.editCaption.setValue(data.caption);
          me.editParentMenu.setIdValue(data.parentMenuId);
          me.editParentMenu.setValue(data.parentMenuCaption);
          me.editShowOrder.setValue(data.showOrder);
        }
      }
    };

    me.ajax(r);
  },

  // 自定义字段psi_fidfield回调本方法
  __fidCallbackFunc: function (data, scope) {
    var me = scope;

    if (!me.editCaption.getValue()) {
      me.editCaption.setValue(data.get("name"));
    }
  }
});
