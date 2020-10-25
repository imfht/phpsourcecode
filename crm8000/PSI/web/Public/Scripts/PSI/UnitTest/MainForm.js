/**
 * Unit Test - 主界面
 * 
 * @author 李静波
 */
Ext.define("PSI.UnitTest.MainForm", {
  extend: "PSI.AFX.BaseMainExForm",
  border: 0,

	/**
	 * 初始化组件
	 */
  initComponent: function () {
    var me = this;

    Ext.apply(me, {
      tbar: me.getToolbarCmp(),
      layout: "border",
      items: [{
        region: "center",
        layout: "fit",
        border: 0,
        items: [me.getMainGrid()]
      }]
    });

    me.callParent(arguments);
  },

  getToolbarCmp: function () {
    var me = this;

    return [{
      text: "开始测试",
      iconCls: "PSI-button-commit",
      handler: me.onStartUnitTest,
      scope: me
    }, {
      text: "关闭",
      iconCls: "PSI-button-exit",
      handler: function () {
        window.close();
      }
    }];
  },

  getMainGrid: function () {
    var me = this;
    if (me.__mainGrid) {
      return me.__mainGrid;
    }

    var modelName = "PSIUnitTestResult";

    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "name", "result", "msg"]
    });

    var store = Ext.create("Ext.data.Store", {
      autoLoad: false,
      model: modelName,
      data: []
    });

    me.__mainGrid = Ext.create("Ext.grid.Panel", {
      viewConfig: {
        enableTextSelection: true
      },
      cls: "PSI",
      title: "单元测试结果",
      columnLines: true,
      border: 0,
      columns: [{
        header: "编号",
        dataIndex: "id",
        menuDisabled: true,
        sortable: false,
        width: 80
      }, {
        header: "名称",
        dataIndex: "name",
        menuDisabled: true,
        sortable: false,
        width: 600
      }, {
        header: "结果",
        dataIndex: "result",
        menuDisabled: true,
        sortable: false,
        width: 60,
        renderer: function (value) {
          if (value == 0) {
            return "<span style='color:red'>失败</span>";
          } else {
            return "成功";
          }
        }
      }, {
        header: "信息",
        dataIndex: "msg",
        menuDisabled: true,
        sortable: false,
        width: 600
      }],
      store: store
    });

    return me.__mainGrid;
  },

	/**
	 * 开始单元测试
	 */
  onStartUnitTest: function () {
    var me = this;

    var confirmFunc = function () {
      var r = {
        url: me.URL("UnitTest/Index/runAllTests"),
        callback: function (options, success, response) {
          el.unmask();

          if (success) {
            var data = me.decodeJSON(response.responseText);
            var store = me.getMainGrid().getStore();
            store.removeAll();
            store.add(data);
          } else {
            me.showInfo("网络错误");
          }
        }
      };
      var el = Ext.getBody();
      el.mask("正在单元测试中...");
      me.ajax(r);
    };

    var info = "请确认是否开始单元测试?";
    me.confirm(info, confirmFunc);
  }
});
