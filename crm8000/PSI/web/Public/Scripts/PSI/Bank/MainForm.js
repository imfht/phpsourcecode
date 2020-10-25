/**
 * 银行账户 - 主界面
 */
Ext.define("PSI.Bank.MainForm", {
  extend: "PSI.AFX.BaseMainExForm",

  /**
   * 初始化组件
   */
  initComponent: function () {
    var me = this;

    Ext.apply(me, {
      tbar: me.getToolbarCmp(),
      items: [{
        region: "west",
        width: 300,
        layout: "fit",
        border: 0,
        split: true,
        items: [me.getCompanyGrid()]
      }, {
        region: "center",
        xtype: "panel",
        layout: "fit",
        border: 0,
        items: [me.getMainGrid()]
      }]
    });

    me.callParent(arguments);

    me.refreshCompanyGrid();
  },

  getToolbarCmp: function () {
    var me = this;
    return [{
      text: "新增银行账户",
      handler: me.onAddBank,
      scope: me
    }, "-", {
      text: "编辑银行账户",
      handler: me.onEditBank,
      scope: me
    }, "-", {
      text: "删除银行账户",
      handler: me.onDeleteBank,
      scope: me
    }, "-", {
      text: "关闭",
      handler: function () {
        me.closeWindow();
      }
    }];
  },

  refreshCompanyGrid: function () {
    var me = this;
    var el = Ext.getBody();
    var store = me.getCompanyGrid().getStore();
    el.mask(PSI.Const.LOADING);
    var r = {
      url: me.URL("Home/Bank/companyList"),
      callback: function (options, success, response) {
        store.removeAll();

        if (success) {
          var data = me.decodeJSON(response.responseText);
          store.add(data);
          if (store.getCount() > 0) {
            me.getCompanyGrid().getSelectionModel()
              .select(0);
          }
        }

        el.unmask();
      }
    };
    me.ajax(r);
  },

  refreshMainGrid: function () {
    var me = this;

    me.getMainGrid().setTitle(me.formatGridHeaderTitle("银行账户"));
    var item = me.getCompanyGrid().getSelectionModel()
      .getSelection();
    if (item == null || item.length != 1) {
      return;
    }

    var company = item[0];
    var title = Ext.String
      .format("{0} - 银行账户", company.get("name"));
    me.getMainGrid().setTitle(me.formatGridHeaderTitle(title));

    var el = me.getMainGrid().getEl();
    var store = me.getMainGrid().getStore();
    el && el.mask(PSI.Const.LOADING);
    var r = {
      params: {
        companyId: company.get("id")
      },
      url: me.URL("Home/Bank/bankList"),
      callback: function (options, success, response) {
        store.removeAll();

        if (success) {
          var data = me.decodeJSON(response.responseText);
          store.add(data);
          if (store.getCount() > 0) {
            me.getMainGrid().getSelectionModel().select(0);
          }
        }

        el && el.unmask();
      }
    };
    me.ajax(r);
  },

  getCompanyGrid: function () {
    var me = this;
    if (me.__companyGrid) {
      return me.__companyGrid;
    }

    var modelName = "PSI_Bank_CompanyModel";

    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "code", "name", "orgType"]
    });

    me.__companyGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      header: {
        height: 30,
        title: me.formatGridHeaderTitle("核算组织机构")
      },
      forceFit: true,
      columnLines: true,
      columns: [{
        header: "编码",
        dataIndex: "code",
        menuDisabled: true,
        sortable: false,
        width: 70
      }, {
        header: "组织机构名称",
        dataIndex: "name",
        flex: 1,
        menuDisabled: true,
        sortable: false
      }, {
        header: "组织机构性质",
        dataIndex: "orgType",
        width: 100,
        menuDisabled: true,
        sortable: false
      }],
      store: Ext.create("Ext.data.Store", {
        model: modelName,
        autoLoad: false,
        data: []
      }),
      listeners: {
        select: {
          fn: me.onCompanyGridSelect,
          scope: me
        }
      }
    });
    return me.__companyGrid;
  },

  getMainGrid: function () {
    var me = this;
    if (me.__mainGrid) {
      return me.__mainGrid;
    }

    var modelName = "PSI_Bank_BankAccountModel";

    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "bankName", "bankNumber", "memo"]
    });

    me.__mainGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      header: {
        height: 30,
        title: me.formatGridHeaderTitle("银行账户")
      },
      columnLines: true,
      columns: [{
        header: "银行",
        dataIndex: "bankName",
        menuDisabled: true,
        sortable: false,
        width: 300
      }, {
        header: "账号",
        dataIndex: "bankNumber",
        width: 300,
        menuDisabled: true,
        sortable: false
      }, {
        header: "备注",
        dataIndex: "memo",
        width: 200,
        menuDisabled: true,
        sortable: false
      }],
      store: Ext.create("Ext.data.Store", {
        model: modelName,
        autoLoad: false,
        data: []
      })
    });
    return me.__mainGrid;
  },

  onCompanyGridSelect: function () {
    var me = this;

    me.refreshMainGrid();
  },

  onAddBank: function () {
    var me = this;

    var item = me.getCompanyGrid().getSelectionModel()
      .getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("没有选择公司");
      return;
    }

    var company = item[0];

    var form = Ext.create("PSI.Bank.EditForm", {
      parentForm: me,
      company: company
    });
    form.show();
  },

  onEditBank: function () {
    var me = this;
    var item = me.getCompanyGrid().getSelectionModel()
      .getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("没有选择公司");
      return;
    }

    var company = item[0];

    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("没有选择要编辑的银行账户");
      return;
    }

    var bank = item[0];
    var form = Ext.create("PSI.Bank.EditForm", {
      parentForm: me,
      company: company,
      entity: bank
    });
    form.show();
  },

  onDeleteBank: function () {
    var me = this;
    var item = me.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      me.showInfo("没有选择要删除的银行账户");
      return;
    }

    var bank = item[0];

    var info = Ext.String.format(
      "请确认是否删除银行账户 <span style='color:red'>{0}-{1}</span> ?",
      bank.get("bankName"), bank.get("bankNumber"));

    var funcConfirm = function () {
      var el = Ext.getBody();
      el.mask(PSI.Const.LOADING);
      var r = {
        url: me.URL("Home/Bank/deleteBank"),
        params: {
          id: bank.get("id")
        },
        method: "POST",
        callback: function (options, success, response) {
          el.unmask();
          if (success) {
            var data = me.decodeJSON(response.responseText);
            if (data.success) {
              me.tip("成功完成删除操作");
              me.refreshMainGrid();
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

  }
});
