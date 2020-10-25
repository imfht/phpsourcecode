//
// 会计期间 - 主界面
//
Ext.define("PSI.GLPeriod.MainForm", {
  extend: "PSI.AFX.BaseMainExForm",

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
        layout: "border",
        border: 0,
        items: [{
          region: "center",
          layout: "fit",
          split: true,
          items: me.getMainGrid()
        }]
      }]
    });

    me.callParent(arguments);

    me.refreshCompanyGrid();
  },

  getToolbarCmp: function () {
    var me = this;
    return [{
      text: "初始化本年度会计期间",
      handler: me.onInitPeriod,
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
      url: me.URL("Home/GLPeriod/companyList"),
      callback: function (options, success, response) {
        store.removeAll();

        if (success) {
          var data = me.decodeJSON(response.responseText);
          store.add(data);
          if (store.getCount() > 0) {
            me.getCompanyGrid().getSelectionModel().select(0);
          }
        }

        el.unmask();
      }
    };
    me.ajax(r);
  },

  getCompanyGrid: function () {
    var me = this;
    if (me.__companyGrid) {
      return me.__companyGrid;
    }

    var modelName = "PSI_GLPeriod_Company";

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

  onCompanyGridSelect: function () {
    var me = this;

    me.getMainGrid().setTitle(me.formatGridHeaderTitle("会计期间"));
    var item = me.getCompanyGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      return;
    }

    var company = item[0];

    var title = Ext.String.format("{0} - 会计期间", company.get("name"));
    me.getMainGrid().setTitle(me.formatGridHeaderTitle(title));

    var grid = me.getMainGrid();
    var el = grid.getEl();
    el && el.mask(PSI.Const.LOADING);

    var r = {
      url: me.URL("Home/GLPeriod/periodList"),
      params: {
        companyId: company.get("id")
      },
      callback: function (options, success, response) {
        var store = grid.getStore();

        store.removeAll();

        if (success) {
          var data = me.decodeJSON(response.responseText);
          store.add(data);
        }

        el && el.unmask();
      }
    };
    me.ajax(r);
  },

  getMainGrid: function () {
    var me = this;
    if (me.__mainGrid) {
      return me.__mainGrid;
    }

    var modelName = "PSIFMTProp";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "year", "month", "glKept", "glClosed",
        "detailKept", "detailClosed", "periodClosed", "yearForward"]
    });

    me.__mainGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      header: {
        height: 30,
        title: me.formatGridHeaderTitle("会计期间")
      },
      columnLines: true,
      columns: {
        defaults: {
          menuDisabled: true,
          sortable: false,
          draggable: false
        },
        items: [{
          header: "年",
          dataIndex: "year",
          width: 60,
          align: "center"
        }, {
          header: "月",
          dataIndex: "month",
          width: 60,
          align: "center"
        }, {
          header: "总账",
          columns: [{
            header: "已记账",
            dataIndex: "glKept",
            width: 90,
            align: "center",
            menuDisabled: true,
            sortable: false,
            draggable: false
          }, {
            header: "已结账",
            dataIndex: "glClosed",
            width: 90,
            align: "center",
            menuDisabled: true,
            sortable: false,
            draggable: false
          }]
        }, {
          header: "明细账",
          columns: [{
            header: "已记账",
            dataIndex: "detailKept",
            width: 100,
            align: "center",
            menuDisabled: true,
            sortable: false,
            draggable: false
          }, {
            header: "已结账",
            dataIndex: "detailClosed",
            width: 100,
            align: "center",
            menuDisabled: true,
            sortable: false,
            draggable: false
          }]
        }, {
          header: "本期间已结账",
          dataIndex: "periodClosed",
          width: 100,
          align: "center"
        }, {
          header: "年终结转",
          dataIndex: "yearForward",
          width: 90,
          align: "center"
        }]
      },
      store: Ext.create("Ext.data.Store", {
        model: modelName,
        autoLoad: false,
        data: []
      })
    });

    return me.__mainGrid;
  },

  onInitPeriod: function () {
    var me = this;
    var item = me.getCompanyGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      return;
    }

    var company = item[0];

    var info = "请确认是否初始化[" + company.get("name") + "]的本年度会计期间?";
    var funcConfirm = function () {
      var el = Ext.getBody();
      el.mask("正在操作中...");
      var r = {
        url: me.URL("Home/GLPeriod/initPeriod"),
        params: {
          companyId: company.get("id")
        },
        callback: function (options, success, response) {
          el.unmask();

          if (success) {
            var data = me.decodeJSON(response.responseText);
            if (data.success) {
              me.showInfo("成功完成初始化操作", function () {
                me.onCompanyGridSelect();
              });
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
