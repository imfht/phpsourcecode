/**
 * 业务设置 - 主窗体
 */
Ext.define("PSI.BizConfig.MainForm", {
  extend: "PSI.AFX.BaseOneGridMainForm",

  /**
   * 重载父类方法
   */
  afxInitComponent: function () {
    var me = this;

    me.comboCompany = Ext.getCmp("comboCompany");

    me.queryCompany();
  },

  /**
   * 重载父类方法
   */
  afxGetToolbarCmp: function () {
    var me = this;
    var modelName = "PSI_BizConfig_MainForm_PSICompany";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "name"]
    });
    return [{
      xtype: "displayfield",
      value: "公司 "
    }, {
      cls: "PSI-toolbox",
      xtype: "combobox",
      id: "comboCompany",
      queryMode: "local",
      editable: false,
      valueField: "id",
      displayField: "name",
      store: Ext.create("Ext.data.Store", {
        model: modelName,
        autoLoad: false,
        data: []
      }),
      width: 400,
      listeners: {
        select: {
          fn: me.onComboCompanySelect,
          scope: me
        }
      }
    }, {
      text: "设置",
      iconCls: "PSI-button-edit",
      handler: me.onEdit,
      scope: me
    }, "-", {
      text: "帮助",
      handler: function () {
        window.open(me
          .URL("/Home/Help/index?t=bizconfig"));
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

    var modelName = "PSI_BizConfig_MainForm_PSIBizConfig";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "name", "value", "displayValue",
        "note"],
      idProperty: "id"
    });
    var store = Ext.create("Ext.data.Store", {
      model: modelName,
      data: [],
      autoLoad: false
    });

    me.__mainGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      loadMask: true,
      border: 0,
      columnLines: true,
      columns: [Ext.create("Ext.grid.RowNumberer", {
        text: "序号",
        width: 40
      }), {
        text: "设置项",
        dataIndex: "name",
        width: 250,
        menuDisabled: true
      }, {
        text: "值",
        dataIndex: "displayValue",
        width: 500,
        menuDisabled: true
      }, {
        text: "备注",
        dataIndex: "note",
        width: 500,
        menuDisabled: true
      }],
      store: store,
      listeners: {
        itemdblclick: {
          fn: me.onEdit,
          scope: me
        }
      }
    });

    return me.__mainGrid;
  },

  afxGetRefreshGridURL: function () {
    return "Home/BizConfig/allConfigs";

  },

  afxGetRefreshGridParams: function () {
    var me = this;
    return {
      companyId: me.comboCompany.getValue()
    };
  },

  /**
   * 设置按钮被单击
   */
  onEdit: function () {
    var me = this;

    var companyId = me.comboCompany.getValue();
    if (!companyId) {
      PSI.MsgBox.showInfo("没有选择要设置的公司");
      return;
    }

    var form = Ext.create("PSI.BizConfig.EditForm", {
      parentForm: me,
      companyId: companyId
    });
    form.show();
  },

  /**
   * 查询公司信息
   */
  queryCompany: function () {
    var me = this;
    var el = Ext.getBody();
    var comboCompany = me.comboCompany;
    var store = comboCompany.getStore();
    el.mask(PSI.Const.LOADING);
    var r = {
      url: me.URL("Home/BizConfig/getCompany"),
      method: "POST",
      callback: function (options, success, response) {
        store.removeAll();

        if (success) {
          var data = me.decodeJSON(response.responseText);
          store.add(data);
          if (data.length > 0) {
            comboCompany.setValue(data[0]["id"]);
            me.refreshGrid();
          }
        }

        el.unmask();
      }
    };
    Ext.Ajax.request(r);
  },

  onComboCompanySelect: function () {
    var me = this;

    me.refreshGrid();
  }
});
