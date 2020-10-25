/**
 * 价格体系 - 新增或编辑界面
 */
Ext.define("PSI.Goods.PriceSystemEditForm", {
  extend: "PSI.AFX.BaseDialogForm",

  config: {
    parentForm: null,
    entity: null
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

    var t = entity == null ? "新增价格" : "编辑价格";
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
      resizable: false,
      onEsc: Ext.emptyFn,
      width: 400,
      height: 250,
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
          columns: 1
        },
        height: "100%",
        bodyPadding: 5,
        defaultType: 'textfield',
        fieldDefaults: {
          labelWidth: 100,
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
          id: "editName",
          fieldLabel: "价格名称",
          allowBlank: false,
          blankText: "没有输入价格名称",
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
          id: "editFactor",
          xtype: "numberfield",
          hideTrigger: true,
          fieldLabel: "销售基准价倍数",
          allowBlank: false,
          blankText: "没有输入销售基准价倍数",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          name: "factor",
          value: entity == null ? 1 : entity
            .get("factor"),
          listeners: {
            specialkey: {
              fn: me.onEditFactorSpecialKey,
              scope: me
            }
          }
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
  },

  onOK: function (thenAdd) {
    var me = this;
    var f = Ext.getCmp("editForm");
    var el = f.getEl();
    el.mask(PSI.Const.SAVING);
    f.submit({
      url: PSI.Const.BASE_URL + "Home/Goods/editPriceSystem",
      method: "POST",
      success: function (form, action) {
        el.unmask();
        me.__lastId = action.result.id;
        PSI.MsgBox.tip("数据保存成功");
        me.focus();
        if (thenAdd) {
          var editName = Ext.getCmp("editName");
          editName.focus();
          editName.setValue(null);
          editName.clearInvalid();
        } else {
          me.close();
        }
      },
      failure: function (form, action) {
        el.unmask();
        PSI.MsgBox.showInfo(action.result.msg, function () {
          Ext.getCmp("editName").focus();
        });
      }
    });
  },

  onEditNameSpecialKey: function (field, e) {
    if (e.getKey() == e.ENTER) {
      var editFactor = Ext.getCmp("editFactor");
      editFactor.focus();
      editFactor.setValue(editFactor.getValue());
    }
  },

  onEditFactorSpecialKey: function (field, e) {
    if (e.getKey() == e.ENTER) {
      var f = Ext.getCmp("editForm");
      if (f.getForm().isValid()) {
        this.onOK(this.adding);
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
      me.getParentForm().freshGrid(me.__lastId);
    }
  },

  onWndShow: function () {
    var me = this;

    Ext.get(window).on('beforeunload', me.onWindowBeforeUnload);

    var editName = Ext.getCmp("editName");
    editName.focus();
    editName.setValue(editName.getValue());
  }
});
