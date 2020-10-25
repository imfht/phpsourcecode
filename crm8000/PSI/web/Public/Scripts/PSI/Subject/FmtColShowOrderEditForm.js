//
// 账样字段显示次序
//
Ext.define("PSI.Subject.FmtColShowOrderEditForm", {
  extend: "PSI.AFX.BaseDialogForm",

	/**
	 * 初始化组件
	 */
  initComponent: function () {
    var me = this;

    var entity = me.getEntity();

    var buttons = [];

    var btn = {
      text: "保存",
      formBind: true,
      iconCls: "PSI-button-ok",
      handler: function () {
        me.onOK(false);
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

    var t = "设置字段显示次序";
    var f = "edit-form-update.png";
    var logoHtml = "<img style='float:left;margin:10px 20px 0px 10px;width:48px;height:48px;' src='"
      + PSI.Const.BASE_URL
      + "Public/Images/"
      + f
      + "'></img>"
      + "<h2 style='color:#196d83'>"
      + t
      + "</h2>"
      + "<p style='color:#196d83'>通过拖动列来调整显示次序</p>";
    Ext.apply(me, {
      header: {
        title: me.formatTitle(PSI.Const.PROD_NAME),
        height: 40
      },
      width: 1000,
      height: 340,
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
        layout: "fit",
        items: [me.getMainGrid()],
        buttons: buttons
      }]
    });

    me.callParent(arguments);
  },

	/**
	 * 保存
	 */
  onOK: function (thenAdd) {
    var me = this;

    var columns = me.getMainGrid().columnManager.columns;

    var data = [];
    for (var i = 0; i < columns.length; i++) {
      var col = columns[i];

      data.push(col.dataIndex);
    }

    var showOrder = data.join(",");

    var el = Ext.getBody();
    el.mask("正在操作中...");
    var r = {
      url: me.URL("Home/Subject/editFmtColShowOrder"),
      params: {
        id: me.getEntity().get("id"), // 科目id
        idList: showOrder
      },
      callback: function (options, success, response) {
        el.unmask();

        if (success) {
          var data = me.decodeJSON(response.responseText);
          if (data.success) {
            me.showInfo("成功修改字段显示次序", function () {
              me.close();
              if (me.getParentForm()) {
                me.getParentForm().refreshFmtColsGrid();
              }
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
  },

  onWindowBeforeUnload: function (e) {
    return (window.event.returnValue = e.returnValue = '确认离开当前页面？');
  },

  onWndClose: function () {
    var me = this;

    Ext.get(window).un('beforeunload', me.onWindowBeforeUnload);

    if (me.__lastId) {
      if (me.getParentForm()) {
        me.getParentForm().refreshFmtColsGrid()
      }
    }
  },

  getMainGrid: function () {
    var me = this;
    if (me.__mainGrid) {
      return me.__mainGrid;
    }

    var modelName = "PSIEditFMTColsShowOrder";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id"]
    });

    me.__mainGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      header: {
        height: 30,
        title: me.formatGridHeaderTitle("账样字段")
      },
      columnLines: true,
      columns: {
        defaults: {
          menuDisabled: true,
          sortable: false
        },
        items: []
      },
      store: Ext.create("Ext.data.Store", {
        model: modelName,
        autoLoad: false,
        data: []
      })
    });

    return me.__mainGrid;
  },

  onWndShow: function () {
    var me = this;

    Ext.get(window).on('beforeunload', me.onWindowBeforeUnload);

    var id = me.getEntity().get("id");

    var el = me.getEl() || Ext.getBody();
    el.mask(PSI.Const.LOADING);
    var r = {
      url: me.URL("Home/Subject/fmtGridColsList"),
      params: {
        id: id
      },
      callback: function (options, success, response) {
        el.unmask();

        if (success) {
          var data = me.decodeJSON(response.responseText);
          me.reconfigMainGrid(data);
        } else {
          me.showInfo("网络错误")
        }
      }
    };

    me.ajax(r);
  },

  reconfigMainGrid: function (data) {
    var me = this;
    var cols = [];
    for (var i = 0; i < data.length; i++) {
      var item = data[i];
      cols.push({
        text: item.caption,
        dataIndex: item.id
      });
    }
    me.getMainGrid().reconfigure(me.getMainGrid().getStore(), cols);
  }
});
