//
// 码表 - 调整编辑界面字段显示次序
//
Ext.define("PSI.CodeTable.CodeTableEditColShowOrderForm", {
  extend: "PSI.AFX.BaseDialogForm",

  config: {
    codeTable: null
  },

  initComponent: function () {
    var me = this;
    var entity = me.getEntity();
    this.adding = entity == null;

    var buttons = [];

    buttons.push({
      text: "保存",
      formBind: true,
      iconCls: "PSI-button-ok",
      handler: function () {
        me.onOK(false);
      },
      scope: me
    }, {
      text: "取消",
      handler: function () {
        me.close();
      },
      scope: me
    });


    Ext.apply(me, {
      resizable: true,
      header: {
        title: me.formatTitle("调整编辑界面字段显示次序"),
        height: 40
      },
      width: 900,
      height: 200,
      layout: "border",
      items: [{
        region: "north",
        border: 0,
        html: "<h1>拖动列来调整显示次序</h1>"
      }, {
        region: "center",
        layout: "fit",
        border: 0,
        id: "CodeTableEditColShowOrderForm_panelMain",
        items: []
      }],
      buttons: buttons,
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

    me.__mainPanel = Ext.getCmp("CodeTableEditColShowOrderForm_panelMain");
  },

  onWndShow: function () {
    var me = this;

    Ext.get(window).on('beforeunload', me.onWindowBeforeUnload);

    var el = me.getEl();
    el && el.mask(PSI.Const.LOADING);
    Ext.Ajax.request({
      url: me.URL("Home/CodeTable/queryCodeTableEditColShowOrder"),
      params: {
        tableId: me.getCodeTable().get("id")
      },
      method: "POST",
      callback: function (options, success, response) {
        if (success) {
          el && el.unmask();

          var data = Ext.JSON.decode(response.responseText);
          me.__mainPanel.add(me.createMainGrid(data));
        }
      }
    });

  },

  onOK: function () {
    var me = this;

    var grid = me.getMainGrid();
    var cols = grid.columnManager.columns;
    var layout = [];
    for (var i = 0; i < cols.length; i++) {
      var c = cols[i];
      layout.push({ dataIndex: c.dataIndex });
    }
    var json = Ext.JSON.encode(layout);

    var info = "请确认是否保存编辑字段显示次序?";

    var funcConfirm = function () {
      var el = Ext.getBody();
      el && el.mask(PSI.Const.LOADING);
      var r = {
        url: me.URL("Home/CodeTable/saveColEditShowOrder"),
        params: {
          id: me.getCodeTable().get("id"),
          json: json
        },
        method: "POST",
        callback: function (options, success, response) {
          el && el.unmask();
          if (success) {
            var data = me.decodeJSON(response.responseText);
            if (data.success) {
              me.tip("成功完成操作");
              me.getParentForm().refreshColsGrid();
              me.close();
            } else {
              me.showInfo(data.msg);
            }
          } else {
            me.showInfo("网络错误");
          }
        }
      };

      me.ajax(r);
    }

    me.confirm(info, funcConfirm);
  },

  onWindowBeforeUnload: function (e) {
    return (window.event.returnValue = e.returnValue = '确认离开当前页面？');
  },

  onWndClose: function () {
    var me = this;

    Ext.get(window).un('beforeunload', me.onWindowBeforeUnload);
  },

  getMainGrid: function () {
    var me = this;
    return me.__mainGrid;
  },

  createMainGrid: function (cols) {
    var me = this;


    var fields = [];
    var columns = [];
    if (!cols) {
      columns.push({});
    } else {
      for (var i = 0; i < cols.length; i++) {
        var col = cols[i];
        columns.push({
          header: col.caption,
          dataIndex: col.dataIndex
        });
        fields.push(col.dataIndex);
      }
    }

    var modelName = "PSICodeTableEditColShowOrder";

    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: fields
    });

    me.__mainGrid = Ext.create("Ext.grid.Panel", {
      columnLines: true,
      columns: {
        defaults: {
          menuDisabled: true,
          sortable: false

        }, items: columns
      }
    });

    return me.__mainGrid;
  }
});
