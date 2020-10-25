/**
 * 业务日志 - 主界面
 * 
 * @author 李静波
 */
Ext.define("PSI.Bizlog.MainForm", {
  extend: "PSI.AFX.BaseMainExForm",

  config: {
    unitTest: "0"
  },

  initComponent: function () {
    var me = this;

    Ext.apply(me, {
      tbar: me.getToolbarCmp(),
      items: [{
        id: "panelQueryCmp",
        region: "north",
        height: 65,
        layout: "fit",
        border: 0,
        header: false,
        collapsible: true,
        collapseMode: "mini",
        layout: {
          type: "table",
          columns: 4
        },
        items: me.getQueryCmp()
      }, {
        region: "center",
        layout: "fit",
        border: 0,
        items: [me.getMainGrid()]
      }]
    });

    me.callParent();

    me.fetchLogCategory();

    me.onRefresh();
  },

  fetchLogCategory: function () {
    var me = this;

    var r = {
      url: me.URL("Home/Bizlog/getLogCategoryList"),
      callback: function (options, success, response) {
        var combo = Ext.getCmp("comboCategory");
        var store = combo.getStore();

        store.removeAll();

        if (success) {
          var data = Ext.JSON.decode(response.responseText);
          store.add(data);

          if (store.getCount() > 0) {
            combo.setValue(store.getAt(0).get("id"))
          }
        }
      }
    };
    me.ajax(r);
  },

  getToolbarCmp: function () {
    var me = this;

    var store = me.getMainGrid().getStore();

    var buttons = [{
      cls: "PSI-toolbox",
      id: "pagingToobar",
      xtype: "pagingtoolbar",
      border: 0,
      store: store
    }, "-", {
      xtype: "displayfield",
      value: "每页显示"
    }, {
      cls: "PSI-toolbox",
      id: "comboCountPerPage",
      xtype: "combobox",
      editable: false,
      width: 60,
      store: Ext.create("Ext.data.ArrayStore", {
        fields: ["text"],
        data: [["20"], ["50"], ["100"], ["300"],
        ["1000"]]
      }),
      value: 20,
      listeners: {
        change: {
          fn: function () {
            store.pageSize = Ext.getCmp("comboCountPerPage").getValue();
            store.currentPage = 1;
            Ext.getCmp("pagingToobar").doRefresh();
          },
          scope: me
        }
      }
    }, {
      xtype: "displayfield",
      value: "条记录"
    }, "-", {
      text: "帮助",
      iconCls: "PSI-help",
      handler: function () {
        window.open(me.URL("Home/Help/index?t=bizlog"));
      }
    }, "-", {
      text: "关闭",
      handler: function () {
        me.closeWindow();
      }
    }, "->", {
      text: "一键升级数据库",
      iconCls: "PSI-button-database",
      scope: me,
      handler: me.onUpdateDatabase
    }];

    if (me.getUnitTest() == "1") {
      buttons.push("-", {
        text: "单元测试",
        handler: me.onUnitTest,
        scope: me
      });
    }

    return buttons;
  },

  getQueryCmp: function () {
    var me = this;

    Ext.define("PSILogCategory", {
      extend: "Ext.data.Model",
      fields: ["id", "name"]
    });

    return [{
      id: "editQueryLoginName",
      labelWidth: 60,
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "登录名",
      margin: "5, 0, 0, 0",
      xtype: "textfield"
    }, {
      id: "editQueryUser",
      labelWidth: 60,
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "姓名",
      margin: "5, 0, 0, 0",
      xtype: "psi_userfield",
      showModal: true
    }, {
      id: "editQueryFromDT",
      xtype: "datefield",
      margin: "5, 0, 0, 0",
      format: "Y-m-d",
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "日志日期（起）"
    }, {
      id: "editQueryToDT",
      xtype: "datefield",
      margin: "5, 0, 0, 0",
      format: "Y-m-d",
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "日志日期（止）"
    }, {
      id: "editQueryIP",
      labelWidth: 60,
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "IP",
      margin: "5, 0, 0, 0",
      xtype: "textfield"
    }, {
      xtype: "combobox",
      id: "comboCategory",
      queryMode: "local",
      editable: false,
      matchFieldWidth: false,
      labelWidth: 60,
      labelAlign: "right",
      labelSeparator: "",
      fieldLabel: "日志分类",
      margin: "5, 0, 0, 0",
      valueField: "id",
      displayField: "name",
      store: Ext.create("Ext.data.Store", {
        model: "PSILogCategory",
        autoLoad: false,
        data: []
      })
    }, {
      xtype: "container",
      items: [{
        xtype: "button",
        text: "查询",
        width: 100,
        height: 26,
        margin: "5 0 0 10",
        handler: me.onRefresh,
        scope: me
      }, {
        xtype: "button",
        text: "清空查询条件",
        width: 100,
        height: 26,
        margin: "5, 0, 0, 10",
        handler: me.onClearQuery,
        scope: me
      }]
    }, {
      xtype: "container",
      items: [{
        xtype: "button",
        iconCls: "PSI-button-hide",
        text: "隐藏查询条件栏",
        width: 130,
        height: 26,
        margin: "5 0 0 10",
        handler: function () {
          Ext.getCmp("panelQueryCmp").collapse();
        },
        scope: me
      }]
    }];
  },

  getMainGrid: function () {
    var me = this;
    if (me.__mainGrid) {
      return me.__mainGrid;
    }

    var modelName = "PSI_Bizlog_MainForm_PSILog";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "loginName", "userName", "ip", "ipFrom",
        "content", "dt", "logCategory"],
      idProperty: "id"
    });
    var store = Ext.create("Ext.data.Store", {
      model: modelName,
      pageSize: 20,
      proxy: {
        type: "ajax",
        actionMethods: {
          read: "POST"
        },
        url: me.URL("Home/Bizlog/logList"),
        reader: {
          root: 'logs',
          totalProperty: 'totalCount'
        }
      },
      autoLoad: true
    });
    store.on("beforeload", function () {
      store.proxy.extraParams = me.getQueryParam();
    });

    me.__mainGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      loadMask: true,
      columnLines: true,
      columns: {
        defaults: {
          menuDisabled: true,
          sortable: false
        },
        items: [Ext.create("Ext.grid.RowNumberer", {
          text: "序号",
          width: 60
        }), {
          text: "登录名",
          dataIndex: "loginName",
          width: 60
        }, {
          text: "姓名",
          dataIndex: "userName",
          width: 80
        }, {
          text: "IP",
          dataIndex: "ip",
          width: 120,
          renderer: function (value, md, record) {
            return "<a href='http://www.baidu.com/s?wd="
              + encodeURIComponent(value)
              + "' target='_blank'>" + value + "</a>";
          }
        }, {
          text: "IP所属地",
          dataIndex: "ipFrom",
          width: 200
        }, {
          text: "日志分类",
          dataIndex: "logCategory",
          width: 150
        }, {
          text: "日志内容",
          dataIndex: "content",
          flex: 1
        }, {
          text: "日志记录时间",
          dataIndex: "dt",
          width: 140
        }]
      },
      store: store,
      listeners: {
        celldblclick: {
          fn: me.onCellDbclick,
          scope: me
        }
      }
    });

    return me.__mainGrid;
  },

  onCellDbclick: function (ths, td, cellIndex, record, tr, rowIndex, e, eOpts) {
    var me = this;
    if (cellIndex == 1) {
      Ext.getCmp("editQueryLoginName").setValue(record.get("loginName"));
      me.onRefresh();
    } else if (cellIndex == 3) {
      Ext.getCmp("editQueryIP").setValue(record.get("ip"));
      me.onRefresh();
    }
  },

	/**
	 * 刷新
	 */
  onRefresh: function () {
    var me = this;

    me.getMainGrid().getStore().currentPage = 1;
    Ext.getCmp("pagingToobar").doRefresh();
    me.focus();
  },

	/**
	 * 升级数据库
	 */
  onUpdateDatabase: function () {
    var me = this;

    PSI.MsgBox.confirm("请确认是否升级数据库？", function () {
      var el = Ext.getBody();
      el.mask("正在升级数据库，请稍等......");

      // 把超时设置为5分钟，主要是为了在低端配置上能正确升级
      Ext.Ajax.timeout = 5 * 60 * 1000;
      Ext.Ajax.request({
        url: PSI.Const.BASE_URL + "Home/Bizlog/updateDatabase",
        method: "POST",
        callback: function (options, success, response) {
          el.unmask();

          if (success) {
            var data = Ext.JSON.decode(response.responseText);
            if (data.success) {
              PSI.MsgBox.showInfo("成功升级数据库", function () {
                me.onRefresh();
              });
            } else {
              PSI.MsgBox.showInfo(data.msg);
            }
          } else {
            PSI.MsgBox.showInfo("网络错误", function () {
              window.location.reload();
            });
          }
        }
      });
    });
  },

  onUnitTest: function () {
    var url = PSI.Const.BASE_URL + "UnitTest";
    window.open(url);
  },

  getQueryParam: function () {
    var result = {
      loginName: Ext.getCmp("editQueryLoginName").getValue(),
      userId: Ext.getCmp("editQueryUser").getIdValue(),
      ip: Ext.getCmp("editQueryIP").getValue(),
      logCategory: Ext.getCmp("comboCategory").getValue()
    };

    var fromDT = Ext.getCmp("editQueryFromDT").getValue();
    if (fromDT) {
      result.fromDT = Ext.Date.format(fromDT, "Y-m-d");
    }

    var toDT = Ext.getCmp("editQueryToDT").getValue();
    if (toDT) {
      result.toDT = Ext.Date.format(toDT, "Y-m-d");
    }

    return result;
  },

  onClearQuery: function () {
    var me = this;

    Ext.getCmp("editQueryLoginName").setValue(null);
    Ext.getCmp("editQueryUser").clearIdValue();
    Ext.getCmp("editQueryIP").setValue(null);
    Ext.getCmp("editQueryFromDT").setValue(null);
    Ext.getCmp("editQueryToDT").setValue(null);
    var combo = Ext.getCmp("comboCategory");
    var store = combo.getStore();
    if (store.getCount() > 0) {
      combo.setValue(store.getAt(0).get("id"))
    }

    me.getMainGrid().getStore().currentPage = 1;

    me.onRefresh();
  }
});
