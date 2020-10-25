/**
 * 仓库 - 新增或编辑界面
 */
Ext.define("PSI.Warehouse.EditForm", {
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

    var t = entity == null ? "新增仓库" : "编辑仓库";
    var logoHtml = me.genLogoHtml(entity, t);

    Ext.apply(me, {
      header: {
        title: me.formatTitle(PSI.Const.PROD_NAME),
        height: 40
      },
      width: 400,
      height: 410,
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
        id: "PSI_Warehouse_EditForm_editForm",
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
          name: "id",
          value: entity == null ? null : entity
            .get("id")
        }, {
          id: "PSI_Warehouse_EditForm_editCode",
          fieldLabel: "仓库编码",
          allowBlank: false,
          blankText: "没有输入仓库编码",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
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
          id: "PSI_Warehouse_EditForm_editName",
          fieldLabel: "仓库名称",
          allowBlank: false,
          blankText: "没有输入仓库名称",
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
          id: "PSI_Warehouse_EditForm_editOrg",
          fieldLabel: "核算组织机构",
          xtype: "psi_orgfield",
          value: entity == null ? null : entity
            .get("orgName"),
          listeners: {
            specialkey: {
              fn: me.onEditOrgSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_Warehouse_EditForm_hiddenOrgId",
          xtype: "hidden",
          name: "orgId"
        }, {
          id: "PSI_Warehouse_EditForm_editSaleArea",
          fieldLabel: "销售核算面积",
          value: entity == null ? null : entity
            .get("saleArea"),
          xtype: "numberfield",
          hideTrigger: true,
          allowDecimal: true,
          minValue: 0,
          name: "saleArea",
          listeners: {
            specialkey: {
              fn: me.onEditSaleAreaSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_Warehouse_EditForm_editEnabled",
          xtype: "combo",
          queryMode: "local",
          editable: false,
          valueField: "id",
          labelAlign: "right",
          labelSeparator: "",
          fieldLabel: "状态",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          store: Ext.create("Ext.data.ArrayStore", {
            fields: ["id", "text"],
            data: [[1, "启用"], [2, "停用"]]
          }),
          value: entity == null
            ? 1
            : parseInt(entity.get("enabled"))
        }, {
          id: "PSI_Warehouse_EditForm_hiddenEnabled",
          xtype: "hidden",
          name: "enabled"
        }, {
          id: "PSI_Warehouse_EditForm_editUsageType",
          xtype: "combo",
          queryMode: "local",
          editable: false,
          valueField: "id",
          labelAlign: "right",
          labelSeparator: "",
          fieldLabel: "用途",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          store: Ext.create("Ext.data.ArrayStore", {
            fields: ["id", "text"],
            data: [[10, "原材料库"], [20, "半成品库"], [30, "产成品库"], [40, "商品库"]]
          }),
          value: entity == null
            ? 40
            : parseInt(entity.get("usageType"))
        }, {
          id: "PSI_Warehouse_EditForm_hiddenUsageType",
          xtype: "hidden",
          name: "usageType"
        }, {
          id: "PSI_Warehouse_EditForm_editLimitGoods",
          xtype: "combo",
          queryMode: "local",
          editable: false,
          valueField: "id",
          labelAlign: "right",
          labelSeparator: "",
          fieldLabel: "物料限制",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          store: Ext.create("Ext.data.ArrayStore", {
            fields: ["id", "text"],
            data: [[0, "不启用物料限制"], [1, "启用物料限制"]]
          }),
          value: entity == null
            ? 1
            : parseInt(entity.get("limitGoods")),
          name: "limitGoods"
        }],
        buttons: buttons
      }]
    });

    me.callParent(arguments);

    me.editForm = Ext.getCmp("PSI_Warehouse_EditForm_editForm");

    me.editCode = Ext.getCmp("PSI_Warehouse_EditForm_editCode");
    me.editName = Ext.getCmp("PSI_Warehouse_EditForm_editName");
    me.editEnabled = Ext.getCmp("PSI_Warehouse_EditForm_editEnabled");
    me.editOrg = Ext.getCmp("PSI_Warehouse_EditForm_editOrg");
    me.editSaleArea = Ext.getCmp("PSI_Warehouse_EditForm_editSaleArea");
    me.hiddenEnabled = Ext.getCmp("PSI_Warehouse_EditForm_hiddenEnabled");
    me.hiddenOrgId = Ext.getCmp("PSI_Warehouse_EditForm_hiddenOrgId");
    me.editUsageType = Ext.getCmp("PSI_Warehouse_EditForm_editUsageType");
    me.hiddenUsageType = Ext.getCmp("PSI_Warehouse_EditForm_hiddenUsageType");

    if (!me.adding) {
      me.editOrg.setIdValue(entity.get("orgId"));
    }
  },

  /**
   * 保存
   */
  onOK: function (thenAdd) {
    var me = this;

    me.hiddenOrgId.setValue(me.editOrg.getIdValue());
    me.hiddenEnabled.setValue(me.editEnabled.getValue());
    me.hiddenUsageType.setValue(me.editUsageType.getValue());

    var f = me.editForm;
    var el = f.getEl();
    el.mask(PSI.Const.SAVING);
    var sf = {
      url: me.URL("Home/Warehouse/editWarehouse"),
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

  onEditCodeSpecialKey: function (field, e) {
    var me = this;

    if (e.getKey() == e.ENTER) {
      var edit = me.editName;
      edit.focus();
      edit.setValue(edit.getValue());
    }
  },

  onEditNameSpecialKey: function (field, e) {
    var me = this;

    if (e.getKey() == e.ENTER) {
      var edit = me.editOrg;
      edit.focus();
      edit.setValue(edit.getValue());
    }
  },

  onEditOrgSpecialKey: function (field, e) {
    var me = this;

    if (e.getKey() == e.ENTER) {
      var edit = me.editSaleArea;
      edit.focus();
      edit.setValue(edit.getValue());
    }
  },

  onEditSaleAreaSpecialKey: function (field, e) {
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
    me.editCode.focus();

    var editors = [me.editCode, me.editName];
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
        me.getParentForm().freshGrid(me.__lastId);
      }
    }
  },

  onWndShow: function () {
    var me = this;

    Ext.get(window).on('beforeunload', me.onWindowBeforeUnload);

    var editCode = me.editCode;
    editCode.focus();
    editCode.setValue(editCode.getValue());
  }
});
