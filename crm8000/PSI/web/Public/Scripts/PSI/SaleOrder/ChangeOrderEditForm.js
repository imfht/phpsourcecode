//
// 销售订单 - 订单变更界面
//
Ext.define("PSI.SaleOrder.ChangeOrderEditForm", {
  extend: "PSI.AFX.BaseDialogForm",

  initComponent: function () {
    var me = this;

    var entity = me.getEntity();

    var buttons = [];

    var btn = {
      text: "保存",
      formBind: true,
      iconCls: "PSI-button-ok",
      handler: function () {
        me.onOK();
      },
      scope: me
    };
    buttons.push(btn);

    var btn = {
      text: "取消",
      handler: function () {
        me.close();
      },
      scope: me
    };
    buttons.push(btn);

    var t = "订单变更";
    var f = "edit-form-update.png";
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
      height: 380,
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
        id: "PSI_SaleOrder_ChangeOrderEditForm_editForm",
        xtype: "form",
        layout: {
          type: "table",
          columns: 2
        },
        height: "100%",
        bodyPadding: 5,
        defaultType: 'textfield',
        fieldDefaults: {
          labelWidth: 70,
          labelAlign: "right",
          labelSeparator: "",
          msgTarget: 'side',
          width: 370,
          margin: "5"
        },
        items: [{
          xtype: "hidden",
          name: "id",
          value: entity.get("id")
        }, {
          id: "PSI_SaleOrder_ChangeOrderEditForm_editGoodsCode",
          fieldLabel: "商品编码",
          readOnly: true,
          value: entity.get("goodsCode"),
          colspan: 2
        }, {
          id: "PSI_SaleOrder_ChangeOrderEditForm_editGoodsName",
          fieldLabel: "商品名称",
          readOnly: true,
          value: entity.get("goodsName"),
          colspan: 2
        }, {
          id: "PSI_SaleOrder_ChangeOrderEditForm_editGoodsSpec",
          fieldLabel: "规格型号",
          readOnly: true,
          value: entity.get("goodsSpec"),
          colspan: 2
        }, {
          id: "PSI_SaleOrder_ChangeOrderEditForm_editGoodsCount",
          fieldLabel: "销售数量",
          xtype: "numberfield",
          hideTrigger: true,
          name: "goodsCount",
          value: entity.get("goodsCount"),
          allowDecimals: PSI.Const.GC_DEC_NUMBER > 0,
          decimalPrecision: PSI.Const.GC_DEC_NUMBER,
          minValue: 0,
          allowBlank: false,
          blankText: "没有输入销售数量",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          width: 180,
          listeners: {
            specialkey: {
              fn: me.onEditGoodsCountSpecialKey,
              scope: me
            },
            change: {
              fn: me.onEditGoodsCountChange,
              scope: me
            }
          }
        }, {
          id: "PSI_SaleOrder_ChangeOrderEditForm_editUnitName",
          fieldLabel: "单位",
          readOnly: true,
          value: entity.get("unitName"),
          width: 180
        }, {
          id: "PSI_SaleOrder_ChangeOrderEditForm_editGoodsPrice",
          fieldLabel: "销售单价",
          hideTrigger: true,
          xtype: "numberfield",
          name: "goodsPrice",
          value: entity.get("goodsPrice"),
          allowDecimals: true,
          decimalPrecision: 2,
          minValue: 0,
          allowBlank: false,
          blankText: "没有输入销售单价",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          width: 180,
          listeners: {
            specialkey: {
              fn: me.onEditGoodsPriceSpecialKey,
              scope: me
            },
            change: {
              fn: me.onEditGoodsPriceChange,
              scope: me
            }
          }
        }, {
          id: "PSI_SaleOrder_ChangeOrderEditForm_editGoodsMoney",
          fieldLabel: "销售金额",
          readOnly: true,
          value: entity.get("goodsMoney"),
          width: 180
        }, {
          id: "PSI_SaleOrder_ChangeOrderEditForm_editGoodsWSCount",
          fieldLabel: "已出库数量",
          readOnly: true,
          value: entity.get("wsCount"),
          width: 180
        }, {
          id: "PSI_SaleOrder_ChangeOrderEditForm_editGoodsLeftCount",
          fieldLabel: "未出库数量",
          readOnly: true,
          value: entity.get("leftCount"),
          width: 180
        }],
        buttons: buttons
      }]
    });

    me.callParent(arguments);

    me.editForm = Ext.getCmp("PSI_SaleOrder_ChangeOrderEditForm_editForm");
    me.editGoodsCount = Ext
      .getCmp("PSI_SaleOrder_ChangeOrderEditForm_editGoodsCount");
    me.editGoodsPrice = Ext
      .getCmp("PSI_SaleOrder_ChangeOrderEditForm_editGoodsPrice");
    me.editGoodsMoney = Ext
      .getCmp("PSI_SaleOrder_ChangeOrderEditForm_editGoodsMoney");
    me.editWSCount = Ext
      .getCmp("PSI_SaleOrder_ChangeOrderEditForm_editGoodsWSCount");
    me.editLeftCount = Ext
      .getCmp("PSI_SaleOrder_ChangeOrderEditForm_editGoodsLeftCount");
  },

  onOK: function (thenAdd) {
    var me = this;

    var confirmFunc = function () {
      var f = me.editForm;
      var el = f.getEl();
      el.mask(PSI.Const.SAVING);
      var sf = {
        url: me.URL("Home/Sale/changeSaleOrder"),
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
            me.editGoodsCount.focus();
            me.editGoodsCount.setValue(me.editGoodsCount
              .getValue());
          });
        }
      };
      f.submit(sf);
    };

    me.confirm("请确认是否变更销售订单?", confirmFunc);
  },

  onWindowBeforeUnload: function (e) {
    return (window.event.returnValue = e.returnValue = '确认离开当前页面？');
  },

  onWndClose: function () {
    var me = this;

    Ext.get(window).un('beforeunload', me.onWindowBeforeUnload);

    if (me.__lastId) {
      if (me.getParentForm()) {
        me.getParentForm().refreshAterChangeOrder(me.__lastId);
      }
    }
  },

  onWndShow: function () {
    var me = this;

    Ext.get(window).on('beforeunload', me.onWindowBeforeUnload);

    me.editGoodsCount.focus();
    me.editGoodsCount.setValue(me.editGoodsCount.getValue());
  },

  onEditGoodsCountSpecialKey: function (field, e) {
    var me = this;

    if (e.getKey() == e.ENTER) {
      var edit = me.editGoodsPrice;
      edit.focus();
      edit.setValue(edit.getValue());
    }
  },

  onEditGoodsPriceSpecialKey: function (field, e) {
    var me = this;

    if (e.getKey() == e.ENTER) {
      me.onOK();
    }
  },

  onEditGoodsCountChange: function () {
    var me = this;

    var cnt = me.editGoodsCount.getValue();
    var price = me.editGoodsPrice.getValue();
    me.editGoodsMoney.setValue(cnt * price);

    var wsCount = me.editWSCount.getValue();
    me.editLeftCount.setValue(cnt - wsCount);
  },

  onEditGoodsPriceChange: function () {
    var me = this;

    var cnt = me.editGoodsCount.getValue();
    var price = me.editGoodsPrice.getValue();
    me.editGoodsMoney.setValue(cnt * price);
  }
});
