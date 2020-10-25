//
// 自定义表单运行- 主界面
//
Ext.define("PSI.Form.RuntimeMainForm", {
  extend: "PSI.AFX.BaseMainExForm",
  border: 0,

  config: {
    fid: null
  },

  initComponent: function () {
    var me = this;

    Ext.apply(me, {
      tbar: {
        id: "PSI_Form_RuntimeMainForm_toolBar",
        xtype: "toolbar"
      },
      layout: "border",
      items: [{
        region: "north",
        id: "PSI_Form_RuntimeMainForm_panelMain",
        layout: "fit",
        split: true,
        height: "40%",
        border: 0,
        items: []
      }, {
        region: "center",
        id: "PSI_Form_RuntimeMainForm_panelDetail",
        layout: "fit",
        border: 0,
        items: []
      }]
    });

    me.callParent(arguments);

    me.__toolBar = Ext.getCmp("PSI_Form_RuntimeMainForm_toolBar");
    me.__panelMain = Ext.getCmp("PSI_Form_RuntimeMainForm_panelMain");
    me.__panelDetail = Ext.getCmp("PSI_Form_RuntimeMainForm_panelDetail");

    me.fetchMeatData();
  },

  getMetaData: function () {
    return this.__md;
  },

  fetchMeatData: function () {
    var me = this;
    var el = me.getEl();
    el && el.mask(PSI.Const.LOADING);
    me.ajax({
      url: me.URL("Home/Form/getMetaDataForRuntime"),
      params: {
        fid: me.getFid()
      },
      callback: function (options, success, response) {
        if (success) {
          var data = me.decodeJSON(response.responseText);

          me.__md = data;

          me.initUI();
        }

        el && el.unmask();
      }
    });
  },

  initUI: function () {
    var me = this;

    var md = me.getMetaData();
    if (!md) {
      return;
    }

    var name = md.name;
    if (!name) {
      return;
    }

    // 按钮
    var toolBar = me.__toolBar;
    toolBar.add([{
      text: "新增" + name,
      id: "buttonAddFormRecord",
      handler: me.onAddFormRecord,
      scope: me
    }, {
      text: "编辑" + name,
      id: "buttonEditFormRecord",
      handler: me.onEditFormRecord,
      scope: me
    }, {
      text: "删除" + name,
      id: "buttonDeleteFormRecord",
      handler: me.onDeleteFormRecord,
      scope: me
    }, "-", , {
      text: "关闭",
      handler: function () {
        me.closeWindow();
      }
    }]);

    me.__mainGrid = me.createMainGrid(md);
    me.__panelMain.add(me.__mainGrid);

    // 明细表
    if (md.details.length > 1) {
      // 多个明细表
    } else {
      // 一个明细表
      me.__detailGrid = me.createDetailGrid(md.details[0]);
      me.__panelDetail.add(me.__detailGrid);
    }
  },

  createMainGrid: function (md) {
    var modelName = "PSIFormRuntime_" + Ext.id();

    var fields = [];
    var cols = [];
    var colsLength = md.cols.length;
    for (var i = 0; i < colsLength; i++) {
      var mdCol = md.cols[i];

      fields.push(mdCol.dataIndex);

      cols.push({
        header: mdCol.caption,
        dataIndex: mdCol.dataIndex,
        width: parseInt(mdCol.widthInView),
        menuDisabled: true,
        sortable: false
      });
    }

    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: fields
    });

    return Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      columnLines: true,
      border: 0,
      columns: cols,
      store: Ext.create("Ext.data.Store", {
        model: modelName,
        autoLoad: false,
        data: []
      })
    });
  },

  createDetailGrid: function (md) {
    var modelName = "PSIFormRuntime_Detail_" + Ext.id();

    var fields = [];
    var cols = [];
    var colsLength = md.cols.length;
    for (var i = 0; i < colsLength; i++) {
      var mdCol = md.cols[i];

      fields.push(mdCol.dataIndex);

      cols.push({
        header: mdCol.caption,
        dataIndex: mdCol.dataIndex,
        width: parseInt(mdCol.widthInView),
        menuDisabled: true,
        sortable: false
      });
    }

    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: fields
    });

    return Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      columnLines: true,
      border: 0,
      columns: cols,
      store: Ext.create("Ext.data.Store", {
        model: modelName,
        autoLoad: false,
        data: []
      })
    });
  },


  onAddFormRecord: function () {
    var me = this;
    me.showInfo("TODO");
  },

  onEditFormRecord: function () {
    var me = this;
    me.showInfo("TODO");
  },

  onDeleteFormRecord: function () {
    var me = this;
    me.showInfo("TODO");
  }
});
