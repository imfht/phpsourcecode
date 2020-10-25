/**
 * 新增或编辑商品品牌
 */
Ext.define("PSI.Goods.BrandEditForm", {
  extend: "PSI.AFX.BaseDialogForm",

	/**
	 * 初始化组件
	 */
  initComponent: function () {
    var me = this;
    var entity = me.getEntity();

    var t = entity == null ? "新增商品品牌" : "编辑商品品牌";
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
      height: 270,
      layout: "border",
      items: [{
        region: "north",
        border: 0,
        height: 90,
        html: logoHtml
      }, {
        region: "center",
        border: 0,
        id: "PSI_Goods_BrandEditForm_editForm",
        xtype: "form",
        layout: {
          type: "table",
          columns: 1
        },
        height: "100%",
        bodyPadding: 5,
        defaultType: 'textfield',
        fieldDefaults: {
          labelWidth: 50,
          labelAlign: "right",
          labelSeparator: "",
          msgTarget: 'side'
        },
        items: [{
          xtype: "hidden",
          name: "id",
          value: entity === null ? null : entity
            .get("id")
        }, {
          id: "PSI_Goods_BrandEditForm_editName",
          fieldLabel: "品牌",
          labelWidth: 60,
          allowBlank: false,
          blankText: "没有输入品牌",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          name: "name",
          value: entity === null ? null : entity
            .get("text"),
          listeners: {
            specialkey: {
              fn: me.onEditNameSpecialKey,
              scope: me
            }
          },
          width: 370
        }, {
          id: "PSI_Goods_BrandEditForm_editParentBrand",
          xtype: "PSI_parent_brand_editor",
          parentItem: me,
          fieldLabel: "上级品牌",
          labelWidth: 60,
          listeners: {
            specialkey: {
              fn: me.onEditParentBrandSpecialKey,
              scope: me
            }
          },
          width: 370
        }, {
          id: "PSI_Goods_BrandEditForm_editParentBrandId",
          xtype: "hidden",
          name: "parentId",
          value: entity === null ? null : entity
            .get("parentId")
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
            : parseInt(entity
              .get("recordStatus")),
          width: 370
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
          fn: me.onEditFormShow,
          scope: me
        },
        close: {
          fn: me.onWndClose,
          scope: me
        }
      }
    });

    me.callParent(arguments);

    me.editForm = Ext.getCmp("PSI_Goods_BrandEditForm_editForm");

    me.editName = Ext.getCmp("PSI_Goods_BrandEditForm_editName");
    me.editParentBrand = Ext
      .getCmp("PSI_Goods_BrandEditForm_editParentBrand");
    me.editParentBrandId = Ext
      .getCmp("PSI_Goods_BrandEditForm_editParentBrandId");
  },

  onWindowBeforeUnload: function (e) {
    return (window.event.returnValue = e.returnValue = '确认离开当前页面？');
  },

  onWndClose: function () {
    var me = this;

    Ext.get(window).un('beforeunload', me.onWindowBeforeUnload);
  },

  onEditFormShow: function () {
    var me = this;

    Ext.get(window).on('beforeunload', me.onWindowBeforeUnload);

    me.editName.focus();

    var entity = me.getEntity();
    if (entity === null) {
      return;
    }

    me.getEl().mask("数据加载中...");
    Ext.Ajax.request({
      url: me.URL("Home/Goods/brandParentName"),
      method: "POST",
      params: {
        id: entity.get("id")
      },
      callback: function (options, success, response) {
        me.getEl().unmask();
        if (success) {
          var data = Ext.JSON.decode(response.responseText);
          me.editParentBrand.setValue(me
            .htmlDecode(data.parentBrandName));
          me.editParentBrandId.setValue(data.parentBrandId);
          me.editName.setValue(me.htmlDecode(data.name));
        }
      }
    });
  },

  setParentBrand: function (data) {
    var me = this;

    me.editParentBrand.setValue(Ext.String.htmlDecode(data.fullName));
    me.editParentBrandId.setValue(data.id);
  },

  onOK: function () {
    var me = this;
    var f = me.editForm;
    var el = f.getEl();
    el.mask("数据保存中...");
    f.submit({
      url: me.URL("Home/Goods/editBrand"),
      method: "POST",
      success: function (form, action) {
        el.unmask();
        me.close();
        if (me.getParentForm()) {
          me.getParentForm().refreshGrid();
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

  onEditNameSpecialKey: function (field, e) {
    var me = this;

    if (e.getKey() == e.ENTER) {
      me.editParentBrand.focus();
    }
  },

  onEditParentBrandSpecialKey: function (field, e) {
    var me = this;
    if (e.getKey() == e.ENTER) {
      if (me.editForm.getForm().isValid()) {
        me.onOK();
      }
    }
  }
});
