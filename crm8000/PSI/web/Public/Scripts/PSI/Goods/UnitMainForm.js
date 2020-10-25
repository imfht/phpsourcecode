/**
 * 物料计量单位 - 主界面
 */
Ext.define("PSI.Goods.UnitMainForm", {
  extend: "PSI.AFX.BaseOneGridMainForm",

	/**
	 * 重载父类方法
	 */
  afxGetToolbarCmp: function () {
    var me = this;
    return [{
      text: "新增计量单位",
      handler: me.onAddUnit,
      scope: me
    }, "-", {
      text: "编辑计量单位",
      handler: me.onEditUnit,
      scope: me
    }, "-", {
      text: "删除计量单位",
      handler: me.onDeleteUnit,
      scope: me
    }, "-", {
      text: "帮助",
      handler: function () {
        window.open(me.URL("Home/Help/index?t=goodsUnit"));
      }
    }, "-", {
      text: "关闭",
      handler: function () {
        me.closeWindow();
      }
    }];
  },

	/**
	 * 重载父类方法
	 */
  afxGetMainGrid: function () {
    var me = this;
    if (me.__mainGrid) {
      return me.__mainGrid;
    }

    var modelName = "PSI_Goods_UnitMainForm_PSIGoodsUnit";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "name", "goodsCount", "goodsEnabledCount",
        "goodsDisabledCount", "code", "recordStatus"]
    });

    me.__mainGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      columnLines: true,
      columns: {
        defaults: {
          menuDisabled: true,
          sortable: false
        },
        items: [{
          xtype: "rownumberer",
          width: 40
        }, {
          header: "编码",
          dataIndex: "code"
        }, {
          header: "物料计量单位",
          dataIndex: "name",
          width: 200,
          renderer: function (value, metaData, record) {
            if (parseInt(record.get("recordStatus")) == 1) {
              return value;
            } else {
              return "<span style='color:gray;text-decoration:line-through;'>"
                + value + "</span>";
            }
          }
        }, {
          header: "状态",
          dataIndex: "recordStatus",
          renderer: function (value, metaData, record) {
            if (parseInt(record.get("recordStatus")) == 1) {
              return "启用";
            } else {
              return "<span style='color:red;'>停用</span>";
            }
          }
        }, {
          header: "使用该计量单位的物料数",
          align: "right",
          width: 180,
          columns: [{
            header: "启用状态物料数",
            dataIndex: "goodsEnabledCount",
            align: "right",
            menuDisabled: true,
            sortable: false,
            width: 120

          }, {
            header: "停用状态物料数",
            dataIndex: "goodsDisabledCount",
            align: "right",
            menuDisabled: true,
            sortable: false,
            width: 120

          }, {
            header: "总物料数",
            dataIndex: "goodsCount",
            align: "right",
            menuDisabled: true,
            sortable: false
          }]
        }]
      },
      store: Ext.create("Ext.data.Store", {
        model: modelName,
        autoLoad: false,
        data: []
      }),
      listeners: {
        itemdblclick: {
          fn: me.onEditUnit,
          scope: me
        }
      }
    });

    return me.__mainGrid;
  },

	/**
	 * 重载父类方法
	 */
  afxGetRefreshGridURL: function () {
    return "Home/Goods/allUnits";
  },

	/**
	 * 新增商品计量单位
	 */
  onAddUnit: function () {
    var me = this;
    var form = Ext.create("PSI.Goods.UnitEditForm", {
      parentForm: me
    });

    form.show();
  },

	/**
	 * 编辑商品计量单位
	 */
  onEditUnit: function () {
    var me = this;

    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      PSI.MsgBox.showInfo("请选择要编辑的商品计量单位");
      return;
    }

    var unit = item[0];

    var form = Ext.create("PSI.Goods.UnitEditForm", {
      parentForm: me,
      entity: unit
    });

    form.show();
  },

	/**
	 * 删除商品计量单位
	 */
  onDeleteUnit: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      PSI.MsgBox.showInfo("请选择要删除的商品计量单位");
      return;
    }

    var unit = item[0];
    var info = "请确认是否删除商品计量单位 <span style='color:red'>" + unit.get("name")
      + "</span> ?";

    var preIndex = me.getPreIndexInMainGrid(unit.get("id"));

    var funcConfirm = function () {
      var el = Ext.getBody();
      el.mask(PSI.Const.LOADING);
      var r = {
        url: PSI.Const.BASE_URL + "Home/Goods/deleteUnit",
        params: {
          id: unit.get("id")
        },
        method: "POST",
        callback: function (options, success, response) {
          el.unmask();
          if (success) {
            var data = Ext.JSON.decode(response.responseText);
            if (data.success) {
              PSI.MsgBox.tip("成功完成删除操作");
              me.freshGrid(preIndex);
            } else {
              PSI.MsgBox.showInfo(data.msg);
            }
          } else {
            PSI.MsgBox.showInfo("网络错误");
          }
        }
      };
      Ext.Ajax.request(r);
    };

    PSI.MsgBox.confirm(info, funcConfirm);
  }
});
