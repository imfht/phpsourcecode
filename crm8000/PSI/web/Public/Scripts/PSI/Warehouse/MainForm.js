/**
 * 仓库 - 主界面
 */
Ext.define("PSI.Warehouse.MainForm", {
  extend: "PSI.AFX.BaseOneGridMainForm",

  config: {
    pAdd: null,
    pEdit: null,
    pDelete: null,
    pEditDataOrg: null,
    pInitInv: null
  },

  /**
   * 重载父类方法
   */
  afxGetToolbarCmp: function () {
    var me = this;

    var result = [{
      text: "新增仓库",
      disabled: me.getPAdd() == "0",
      handler: me.onAddWarehouse,
      scope: me
    }, {
      text: "编辑仓库",
      disabled: me.getPEdit() == "0",
      handler: me.onEditWarehouse,
      scope: me
    }, {
      text: "删除仓库",
      disabled: me.getPDelete() == "0",
      handler: me.onDeleteWarehouse,
      scope: me
    }, "-", {
      text: "修改数据域",
      disabled: me.getPEditDataOrg() == "0",
      handler: me.onEditDataOrg,
      scope: me
    }];

    if (me.getPInitInv() == "1") {
      result.push("-", {
        text: "打开库存建账模块",
        handler: function () {
          window.open(me
            .URL("Home/MainMenu/navigateTo/fid/2000"));
        }
      });
    }

    result.push("-", {
      text: "帮助",
      handler: function () {
        window.open(me.URL("Home/Help/index?t=warehouse"));
      }
    }, "-", {
      text: "关闭",
      handler: function () {
        me.closeWindow();
      }
    });

    return result;
  },

  /**
   * 重载父类方法
   */
  afxGetRefreshGridURL: function () {
    return "Home/Warehouse/warehouseList";
  },

  /**
   * 重载父类方法
   */
  afxGetMainGrid: function () {
    var me = this;
    if (me.__mainGrid) {
      return me.__mainGrid;
    }

    var modelName = "PSI_Warehouse_MainForm_PSIWarehouse";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "code", "name", "inited", "dataOrg",
        "enabled", "orgId", "orgName", "saleArea", "usageType", "usageTypeName",
        "limitGoods"]
    });

    me.__mainGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      border: 0,
      viewConfig: {
        enableTextSelection: true
      },
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
          header: "仓库编码",
          dataIndex: "code",
          width: 100,
          renderer: function (value, metaData, record) {
            if (parseInt(record.get("enabled")) == 1) {
              return value;
            } else {
              return "<span style='color:gray;text-decoration:line-through;'>"
                + value + "</span>";
            }
          }
        }, {
          header: "仓库名称",
          dataIndex: "name",
          width: 200
        }, {
          header: "核算组织机构",
          dataIndex: "orgName",
          width: 250
        }, {
          header: "销售核算面积(平方米)",
          dataIndex: "saleArea",
          width: 150,
          align: "right"
        }, {
          header: "库存建账",
          dataIndex: "inited",
          width: 90,
          renderer: function (value) {
            return value == 1
              ? "建账完毕"
              : "<span style='color:red'>待建账</span>";
          }
        }, {
          header: "用途",
          dataIndex: "usageTypeName",
          width: 200
        }, {
          header: "仓库状态",
          dataIndex: "enabled",
          width: 90,
          renderer: function (value) {
            return value == 1
              ? "启用"
              : "<span style='color:red'>停用</span>";
          }
        }, {
          header: "创建人的数据域",
          dataIndex: "dataOrg",
          width: 150
        }]
      },
      store: Ext.create("Ext.data.Store", {
        model: modelName,
        autoLoad: false,
        data: []
      }),
      listeners: {
        itemdblclick: {
          fn: me.onEditWarehouse,
          scope: me
        }
      }
    });

    return me.__mainGrid;
  },

  /**
   * 新增仓库
   */
  onAddWarehouse: function () {
    var me = this;

    var form = Ext.create("PSI.Warehouse.EditForm", {
      parentForm: me
    });

    form.show();
  },

  /**
   * 编辑仓库
   */
  onEditWarehouse: function () {
    var me = this;

    if (me.getPEdit() == "0") {
      return;
    }

    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要编辑的仓库");
      return;
    }

    var warehouse = item[0];

    var form = Ext.create("PSI.Warehouse.EditForm", {
      parentForm: me,
      entity: warehouse
    });

    form.show();
  },

  /**
   * 删除仓库
   */
  onDeleteWarehouse: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要删除的仓库");
      return;
    }

    var warehouse = item[0];
    var info = "请确认是否删除仓库 <span style='color:red'>" + warehouse.get("name")
      + "</span> ?";

    var preIndex = me.getPreIndexInMainGrid(warehouse.get("id"));

    var funcConfirm = function () {
      var el = Ext.getBody();
      el.mask(PSI.Const.LOADING);
      var r = {
        url: me.URL("Home/Warehouse/deleteWarehouse"),
        params: {
          id: warehouse.get("id")
        },
        method: "POST",
        callback: function (options, success, response) {
          el.unmask();
          if (success) {
            var data = me.decodeJSON(response.responseText);
            if (data.success) {
              me.tip("成功完成删除操作");
              me.freshGrid(preIndex);
            } else {
              me.showInfo(data.msg);
            }
          } else {
            me.showInfo("网络错误");
          }
        }
      };

      me.ajax(r);
    };

    me.confirm(info, funcConfirm);
  },

  /**
   * 编辑数据域
   */
  onEditDataOrg: function () {
    var me = this;

    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("请选择要编辑数据域的仓库");
      return;
    }

    var warehouse = item[0];

    var form = Ext.create("PSI.Warehouse.EditDataOrgForm", {
      parentForm: me,
      entity: warehouse
    });

    form.show();
  }
});
