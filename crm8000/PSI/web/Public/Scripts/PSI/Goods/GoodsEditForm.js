/**
 * 商品 - 新建或编辑界面
 */
Ext.define("PSI.Goods.GoodsEditForm", {
  extend: "PSI.AFX.BaseDialogForm",

	/**
	 * 初始化组件
	 */
  initComponent: function () {
    var me = this;
    var entity = me.getEntity();

    var modelName = "PSIGoodsUnit";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "name"]
    });

    var unitStore = Ext.create("Ext.data.Store", {
      model: modelName,
      autoLoad: false,
      data: []
    });
    me.unitStore = unitStore;

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

    var selectedCategory = null;
    var defaultCategoryId = null;

    if (me.getParentForm()) {
      var selectedCategory = me.getParentForm().getCategoryGrid()
        .getSelectionModel().getSelection();
      var defaultCategoryId = null;
      if (selectedCategory != null && selectedCategory.length > 0) {
        defaultCategoryId = selectedCategory[0].get("id");
      }
    } else {
      // 当 me.getParentForm() == null的时候，本窗体是在其他地方被调用
      // 例如：业务单据中选择商品的界面中，也可以新增商品
    }

    var t = entity == null ? "新增物料" : "编辑物料";
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
      + "<p style='color:#196d83'>标记 <span style='color:red;font-weight:bold'>*</span>的是必须录入数据的字段</p>";;

    Ext.apply(me, {
      header: {
        title: me.formatTitle(PSI.Const.PROD_NAME),
        height: 40
      },
      width: 460,
      height: 440,
      layout: "border",
      items: [{
        region: "north",
        border: 0,
        height: 90,
        html: logoHtml
      }, {
        region: "center",
        border: 0,
        id: "PSI_Goods_GoodsEditForm_editForm",
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
          msgTarget: 'side'
        },
        items: [{
          xtype: "hidden",
          name: "id",
          value: entity == null ? null : entity
            .get("id")
        }, {
          id: "PSI_Goods_GoodsEditForm_editCategory",
          xtype: "psi_goodscategoryfield",
          fieldLabel: "分类",
          allowBlank: false,
          blankText: "没有输入物料分类",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_Goods_GoodsEditForm_editCategoryId",
          name: "categoryId",
          xtype: "hidden",
          value: defaultCategoryId
        }, {
          id: "PSI_Goods_GoodsEditForm_editCode",
          fieldLabel: "编码",
          width: 205,
          allowBlank: false,
          blankText: "没有输入物料编码",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          name: "code",
          value: entity == null ? null : entity
            .get("code"),
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_Goods_GoodsEditForm_editName",
          fieldLabel: "品名",
          colspan: 2,
          width: 430,
          allowBlank: false,
          blankText: "没有输入品名",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          name: "name",
          value: entity == null ? null : entity
            .get("name"),
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_Goods_GoodsEditForm_editSpec",
          fieldLabel: "规格型号",
          colspan: 2,
          width: 430,
          name: "spec",
          value: entity == null ? null : entity
            .get("spec"),
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_Goods_GoodsEditForm_editUnit",
          xtype: "combo",
          fieldLabel: "计量单位",
          allowBlank: false,
          blankText: "没有输入计量单位",
          beforeLabelTextTpl: PSI.Const.REQUIRED,
          valueField: "id",
          displayField: "name",
          store: unitStore,
          queryMode: "local",
          editable: false,
          name: "unitId",
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_Goods_GoodsEditForm_editBarCode",
          fieldLabel: "条形码",
          width: 205,
          name: "barCode",
          value: entity == null ? null : entity
            .get("barCode"),
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          id: "PSI_Goods_GoodsEditForm_editBrandId",
          xtype: "hidden",
          name: "brandId"
        }, {
          id: "PSI_Goods_GoodsEditForm_editBrand",
          fieldLabel: "品牌",
          name: "brandName",
          xtype: "PSI_goods_brand_field",
          colspan: 2,
          width: 430,
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          fieldLabel: "销售基准价",
          xtype: "numberfield",
          hideTrigger: true,
          name: "salePrice",
          id: "PSI_Goods_GoodsEditForm_editSalePrice",
          value: entity == null ? null : entity
            .get("salePrice"),
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          fieldLabel: "建议采购价",
          xtype: "numberfield",
          width: 205,
          hideTrigger: true,
          name: "purchasePrice",
          id: "PSI_Goods_GoodsEditForm_editPurchasePrice",
          value: entity == null ? null : entity
            .get("purchasePrice"),
          listeners: {
            specialkey: {
              fn: me.onEditSpecialKey,
              scope: me
            }
          }
        }, {
          fieldLabel: "备注",
          name: "memo",
          id: "PSI_Goods_GoodsEditForm_editMemo",
          value: entity == null ? null : entity
            .get("memo"),
          listeners: {
            specialkey: {
              fn: me.onLastEditSpecialKey,
              scope: me
            }
          },
          colspan: 2,
          width: 430
        }, {
          id: "PSI_Goods_GoodsEditForm_editRecordStatus",
          xtype: "combo",
          queryMode: "local",
          editable: false,
          valueField: "id",
          fieldLabel: "状态",
          name: "recordStatus",
          store: Ext.create("Ext.data.ArrayStore", {
            fields: ["id", "text"],
            data: [[1000, "启用"], [0, "停用"]]
          }),
          value: 1000
        }, {
          id: "PSI_Goods_GoodsEditForm_editTaxRate",
          xtype: "combo",
          queryMode: "local",
          editable: false,
          valueField: "id",
          fieldLabel: "税率",
          store: Ext.create("Ext.data.ArrayStore", {
            fields: ["id", "text"],
            data: [[-1, "[不设定]"],
            [0, "0%"], [1, "1%"],
            [2, "2%"], [3, "3%"],
            [4, "4%"], [5, "5%"],
            [6, "6%"], [7, "7%"],
            [8, "8%"], [9, "9%"],
            [10, "10%"],
            [11, "11%"],
            [12, "12%"],
            [13, "13%"],
            [14, "14%"],
            [15, "15%"],
            [16, "16%"],
            [17, "17%"]]
          }),
          value: -1,
          name: "taxRate",
          width: 200
        }, {
          id: "PSI_Goods_GoodsEditForm_editMType",
          xtype: "combo",
          queryMode: "local",
          editable: false,
          valueField: "id",
          fieldLabel: "物料类型",
          name: "mType",
          store: Ext.create("Ext.data.ArrayStore", {
            fields: ["id", "text"],
            data: [[1000, "原材料"], [2000, "半成品"],
            [3000, "产成品"], [4000, "商品"]]
          }),
          value: 4000
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

    me.editForm = Ext.getCmp("PSI_Goods_GoodsEditForm_editForm");
    me.editCategory = Ext.getCmp("PSI_Goods_GoodsEditForm_editCategory");
    me.editCategoryId = Ext
      .getCmp("PSI_Goods_GoodsEditForm_editCategoryId");
    me.editCode = Ext.getCmp("PSI_Goods_GoodsEditForm_editCode");
    me.editName = Ext.getCmp("PSI_Goods_GoodsEditForm_editName");
    me.editSpec = Ext.getCmp("PSI_Goods_GoodsEditForm_editSpec");
    me.editUnit = Ext.getCmp("PSI_Goods_GoodsEditForm_editUnit");
    me.editBarCode = Ext.getCmp("PSI_Goods_GoodsEditForm_editBarCode");
    me.editBrand = Ext.getCmp("PSI_Goods_GoodsEditForm_editBrand");
    me.editBrandId = Ext.getCmp("PSI_Goods_GoodsEditForm_editBrandId");
    me.editSalePrice = Ext.getCmp("PSI_Goods_GoodsEditForm_editSalePrice");
    me.editPurchasePrice = Ext.getCmp("PSI_Goods_GoodsEditForm_editPurchasePrice");
    me.editMemo = Ext.getCmp("PSI_Goods_GoodsEditForm_editMemo");
    me.editRecordStatus = Ext.getCmp("PSI_Goods_GoodsEditForm_editRecordStatus");
    me.editTaxRate = Ext.getCmp("PSI_Goods_GoodsEditForm_editTaxRate");
    me.editMType = Ext.getCmp("PSI_Goods_GoodsEditForm_editMType");

    me.__editorList = [me.editCategory, me.editCode, me.editName,
    me.editSpec, me.editUnit, me.editBarCode, me.editBrand,
    me.editSalePrice, me.editPurchasePrice, me.editMemo];
  },

  onWindowBeforeUnload: function (e) {
    return (window.event.returnValue = e.returnValue = '确认离开当前页面？');
  },

  onWndShow: function () {
    var me = this;

    Ext.get(window).on('beforeunload', me.onWindowBeforeUnload);

    var editCode = me.editCode;
    editCode.focus();
    editCode.setValue(editCode.getValue());

    var categoryId = me.editCategoryId.getValue();
    var el = me.getEl();
    var unitStore = me.unitStore;
    el.mask(PSI.Const.LOADING);
    Ext.Ajax.request({
      url: me.URL("Home/Goods/goodsInfo"),
      params: {
        id: me.adding ? null : me.getEntity().get("id"),
        categoryId: categoryId
      },
      method: "POST",
      callback: function (options, success, response) {
        unitStore.removeAll();

        if (success) {
          var data = Ext.JSON.decode(response.responseText);
          if (data.units) {
            unitStore.add(data.units);
          }

          if (!me.adding) {
            // 编辑商品信息
            me.editCategory.setIdValue(data.categoryId);
            me.editCategory.setValue(data.categoryName);
            me.editCode.setValue(data.code);
            me.editName.setValue(data.name);
            me.editSpec.setValue(data.spec);
            me.editUnit.setValue(data.unitId);
            me.editSalePrice.setValue(data.salePrice);
            me.editPurchasePrice
              .setValue(data.purchasePrice);
            me.editBarCode.setValue(data.barCode);
            me.editMemo.setValue(data.memo);
            var brandId = data.brandId;
            if (brandId) {
              var editBrand = me.editBrand;
              editBrand.setIdValue(brandId);
              editBrand.setValue(me.htmlDecode(data.brandFullName));
            }
            me.editRecordStatus.setValue(parseInt(data.recordStatus));
            if (data.taxRate) {
              me.editTaxRate.setValue(parseInt(data.taxRate));
            } else {
              me.editTaxRate.setValue(-1);
            }

            if (data.mType) {
              me.editMType.setValue(parseInt(data.mType));
            }
          } else {
            // 新增商品
            if (unitStore.getCount() > 0) {
              var unitId = unitStore.getAt(0).get("id");
              me.editUnit.setValue(unitId);
            }
            if (data.categoryId) {
              me.editCategory.setIdValue(data.categoryId);
              me.editCategory.setValue(data.categoryName);
              var cmt = parseInt(data.categoryMType);
              if (cmt != -1) {
                // cmt == -1表示分类不限物料类型
                me.editMType.setValue(cmt);
              } else {
                me.editMType.setValue(4000);
              }
            } else {
              me.editMType.setValue(4000);
            }
          }
        }

        el.unmask();
      }
    });
  },

  onOK: function (thenAdd) {
    var me = this;

    var categoryId = me.editCategory.getIdValue();
    me.editCategoryId.setValue(categoryId);

    var brandId = me.editBrand.getIdValue();
    me.editBrandId.setValue(brandId);

    var f = me.editForm;
    var el = f.getEl();
    el.mask(PSI.Const.SAVING);
    f.submit({
      url: me.URL("Home/Goods/editGoods"),
      method: "POST",
      success: function (form, action) {
        el.unmask();
        me.__lastId = action.result.id;
        if (me.getParentForm()) {
          me.getParentForm().__lastId = me.__lastId;
        }

        PSI.MsgBox.tip("数据保存成功");
        me.focus();

        if (thenAdd) {
          me.clearEdit();
        } else {
          me.close();
          if (me.getParentForm()) {
            me.getParentForm().freshGoodsGrid();
          }
        }
      },
      failure: function (form, action) {
        el.unmask();
        PSI.MsgBox.showInfo(action.result.msg, function () {
          me.editCode.focus();
        });
      }
    });
  },

  onEditSpecialKey: function (field, e) {
    var me = this;

    if (e.getKey() === e.ENTER) {
      var id = field.getId();
      for (var i = 0; i < me.__editorList.length; i++) {
        var editor = me.__editorList[i];
        if (id === editor.getId()) {
          var edit = me.__editorList[i + 1];
          edit.focus();
          edit.setValue(edit.getValue());
        }
      }
    }
  },

  onLastEditSpecialKey: function (field, e) {
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

    var editors = [me.editCode, me.editName, me.editSpec, me.editSalePrice,
    me.editPurchasePrice, me.editBarCode, me.editMemo];
    for (var i = 0; i < editors.length; i++) {
      var edit = editors[i];
      edit.setValue(null);
      edit.clearInvalid();
    }
  },

  onWndClose: function () {
    var me = this;

    Ext.get(window).un('beforeunload', me.onWindowBeforeUnload);

    if (me.getParentForm()) {
      me.getParentForm().__lastId = me.__lastId;
      me.getParentForm().freshGoodsGrid();
    }
  }
});
