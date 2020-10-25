/**
 * 物料单位 - 新增或编辑界面
 */
Ext.define("PSI.Material.UnitEditForm", {
  extend: "PSI.AFX.BaseDialogForm",

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

    var t = entity == null ? "新增物料单位" : "编辑物料单位";
    var logoHtml = me.genLogoHtml(entity, t);

    Ext.apply(me, {
      header: {
        title: me.formatTitle(PSI.Const.PROD_NAME),
        height: 40
      },
      width: 400,
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
        id: "PSI_Material_UnitEditForm_editForm",
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
          id: "PSI_Material_UnitEditForm_editName",
          fieldLabel: "物料单位",
          allowBlank: false,
          blankText: "没有输入物料单位",
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
          id: "PSI_Material_UnitEditForm_editCode",
          fieldLabel: "编码",
          name: "code",
          value: entity == null ? null : entity
            .get("code"),
          listeners: {
            specialkey: {
              fn: me.onEditCodeSpecialKey,
              scope: me
            }
          }
        }, {
          xtype: "combo",
          queryMode: "local",
          editable: false,
          valueField: "id",
          fieldLabel: "状态",
          allowBlank: false,
          blankText: "没有输入状态",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          name: "recordStatus",
          store: Ext.create("Ext.data.ArrayStore", {
            fields: ["id", "text"],
            data: [[1, "启用"], [2, "停用"]]
          }),
          value: entity == null
            ? 1
            : parseInt(entity.get("recordStatus"))
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

    me.editForm = Ext.getCmp("PSI_Material_UnitEditForm_editForm");
    me.editName = Ext.getCmp("PSI_Material_UnitEditForm_editName");
    me.editCode = Ext.getCmp("PSI_Material_UnitEditForm_editCode");
  },

  onOK: function (thenAdd) {
    var me = this;
    var f = me.editForm;
    var el = f.getEl();
    el.mask(PSI.Const.SAVING);
    f.submit({
      url: me.URL("Home/Material/editUnit"),
      method: "POST",
      success: function (form, action) {
        el.unmask();
        me.__lastId = action.result.id;
        PSI.MsgBox.tip("数据保存成功");
        me.focus();
        if (thenAdd) {
          me.clearEditor();
          me.refreshParentFormMainGrid();
        } else {
          me.close();
        }
      },
      failure: function (form, action) {
        el.unmask();
        PSI.MsgBox.showInfo(action.result.msg, function () {
          me.editName.focus();
        });
      }
    });
  },

  clearEditor: function () {
    var me = this;

    var editName = me.editName;
    editName.focus();
    editName.setValue(null);
    editName.clearInvalid();

    me.editCode.setValue(null);
  },

  onEditNameSpecialKey: function (field, e) {
    var me = this;

    if (e.getKey() == e.ENTER) {
      me.editCode.focus();
      me.editCode.setValue(me.editCode.getValue());
    }
  },

  onEditCodeSpecialKey: function (field, e) {
    var me = this;

    if (e.getKey() == e.ENTER) {
      var f = me.editForm;
      if (f.getForm().isValid()) {
        me.onOK(me.adding);
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
        me.getParentForm().freshGrid(me.__lastId);
      }
    }
  },

  onWndShow: function () {
    var me = this;

    Ext.get(window).on('beforeunload', me.onWindowBeforeUnload);

    var editName = me.editName;
    editName.focus();
    editName.setValue(editName.getValue());
  },

	/**
	 * 刷新父窗体上Grid的数据
	 * 
	 * 连续新增计量单位后，这样不用关闭当前窗体就能看到数据
	 */
  refreshParentFormMainGrid: function () {
    var me = this;
    var form = me.getParentForm();
    var grid = form.getMainGrid();
    var r = {
      url: me.URL(form.afxGetRefreshGridURL()),
      params: form.afxGetRefreshGridParams(),
      method: "POST",
      callback: function (options, success, response) {
        var store = grid.getStore();

        store.removeAll();

        if (success) {
          var data = me.decodeJSON(response.responseText);
          store.add(data);
        }
      }
    };
    Ext.Ajax.request(r);
  }
});
