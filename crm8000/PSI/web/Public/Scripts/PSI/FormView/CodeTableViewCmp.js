/**
 * 码表视图自定义控件
 */
Ext.define("PSI.FormView.CodeTableViewCmp", {
  extend: "Ext.panel.Panel",
  alias: "widget.psi_codetable_view_cmp",

  config: {
    fid: null
  },

  initComponent: function () {
    var me = this;

    Ext.apply(me, {
      layout: "fit",
      border: 0,
      items: [me.getMainGrid()]
    });

    me.callParent(arguments);
  },

  getMainGrid: function () {
    var me = this;

    if (me.__mainGrid) {
      return me.__mainGrid;
    }

    var modelName = "PSICodeTableViewCmp" + Ext.id();

    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id"]
    });

    me.__mainGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      columnLines: true,
      columns: [{
        header: "编码",
        dataIndex: "code",
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
  }
});
