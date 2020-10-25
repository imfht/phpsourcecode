/**
 * 预收款管理 - 主界面
 */
Ext.define("PSI.Funds.PreReceivingMainForm", {
  extend: "PSI.AFX.BaseMainExForm",

  initComponent: function () {
    var me = this;

    var modelName = "PSICustomerCategroy";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "name"]
    });

    Ext.apply(me, {
      tbar: [{
        text: "收取预收款",
        handler: me.onReceivingMoney,
        scope: me
      }, "-", {
        text: "退还预收款",
        handler: me.onReturnMoney,
        scope: me
      }, "-", {
        xtype: "displayfield",
        margin: "5 0 0 0",
        value: "客户分类"
      }, {
        cls: "PSI-toolbox",
        xtype: "combobox",
        id: "comboCategory",
        queryMode: "local",
        editable: false,
        valueField: "id",
        displayField: "name",
        store: Ext.create("Ext.data.Store", {
          model: modelName,
          autoLoad: false,
          data: []
        })
      }, " ", "-", " ", {
        xtype: "displayfield",
        margin: "5 0 0 0",
        value: "客户 "
      }, {
        cls: "PSI-toolbox",
        id: "editCustomerQuery",
        xtype: "psi_customerfield",
        width: 200,
        showModal: true
      }, {
        text: "查询",
        iconCls: "PSI-button-refresh",
        handler: me.onQuery,
        scope: me
      }, {
        text: "清空查询条件查询",
        handler: me.onClearQuery,
        scope: me
      }, "-", {
        text: "关闭",
        handler: function () {
          me.closeWindow();
        }
      }],
      layout: "border",
      border: 0,
      items: [{
        region: "center",
        layout: "fit",
        border: 0,
        items: [me.getMainGrid()]
      }, {
        region: "south",
        layout: "fit",
        border: 0,
        split: true,
        height: "50%",
        items: [me.getDetailGrid()]
      }]
    });

    me.callParent(arguments);

    me.queryCustomerCategory();
  },

  getMainGrid: function () {
    var me = this;
    if (me.__mainGrid) {
      return me.__mainGrid;
    }

    var modelName = "PSIPreReceiving";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "customerId", "code", "name", "inMoney",
        "outMoney", "balanceMoney"]
    });

    var store = Ext.create("Ext.data.Store", {
      model: modelName,
      pageSize: 20,
      proxy: {
        type: "ajax",
        actionMethods: {
          read: "POST"
        },
        url: PSI.Const.BASE_URL
          + "Home/Funds/prereceivingList",
        reader: {
          root: 'dataList',
          totalProperty: 'totalCount'
        }
      },
      autoLoad: false,
      data: []
    });

    store.on("beforeload", function () {
      Ext.apply(store.proxy.extraParams, {
        categoryId: Ext.getCmp("comboCategory")
          .getValue(),
        customerId: Ext.getCmp("editCustomerQuery")
          .getIdValue()
      });
    });

    me.__mainGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      border: 0,
      bbar: ["->", {
        xtype: "pagingtoolbar",
        border: 0,
        store: store
      }],
      columnLines: true,
      columns: [{
        header: "客户编码",
        dataIndex: "code",
        menuDisabled: true,
        sortable: false,
        width: 120
      }, {
        header: "客户名称",
        dataIndex: "name",
        menuDisabled: true,
        sortable: false,
        width: 300
      }, {
        header: "收",
        dataIndex: "inMoney",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        width: 160
      }, {
        header: "支",
        dataIndex: "outMoney",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        width: 160
      }, {
        header: "预付款余额",
        dataIndex: "balanceMoney",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        width: 160
      }],
      store: store,
      listeners: {
        select: {
          fn: me.onMainGridSelect,
          scope: me
        }
      }
    });

    return me.__mainGrid;
  },

  getDetailParam: function () {
    var item = this.getMainGrid().getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      return null;
    }

    var rv = item[0];
    var result = {
      dtFrom: Ext.Date.format(Ext.getCmp("dtFrom").getValue(), "Y-m-d"),
      dtTo: Ext.Date.format(Ext.getCmp("dtTo").getValue(), "Y-m-d"),
      customerId: rv.get("customerId")
    };

    return result;
  },

  onMainGridSelect: function () {
    this.getDetailGrid().getStore().loadPage(1);
  },

  getDetailGrid: function () {
    var me = this;
    if (me.__detailGrid) {
      return me.__detailGrid;
    }

    var modelName = "PSIPreReceivingDetail";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "inMoney", "outMoney", "balanceMoney",
        "refType", "refNumber", "bizDT", "dateCreated",
        "bizUserName", "inputUserName", "memo"]
    });

    var store = Ext.create("Ext.data.Store", {
      model: modelName,
      pageSize: 20,
      proxy: {
        type: "ajax",
        actionMethods: {
          read: "POST"
        },
        url: PSI.Const.BASE_URL
          + "Home/Funds/prereceivingDetailList",
        reader: {
          root: 'dataList',
          totalProperty: 'totalCount'
        }
      },
      autoLoad: false,
      data: []
    });

    store.on("beforeload", function () {
      Ext.apply(store.proxy.extraParams, me.getDetailParam());
    });

    me.__detailGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      viewConfig: {
        enableTextSelection: true
      },
      header: {
        height: 30,
        title: me.formatGridHeaderTitle("预收款明细")
      },
      border: 0,
      tbar: [{
        xtype: "displayfield",
        value: "业务日期 从"
      }, {
        id: "dtFrom",
        xtype: "datefield",
        format: "Y-m-d",
        width: 90
      }, {
        xtype: "displayfield",
        value: " 到 "
      }, {
        id: "dtTo",
        xtype: "datefield",
        format: "Y-m-d",
        width: 90,
        value: new Date()
      }, {
        text: "查询",
        iconCls: "PSI-button-refresh",
        handler: me.onQueryDetail,
        scope: me
      }, "-", {
        xtype: "pagingtoolbar",
        border: 0,
        store: store
      }],
      columnLines: true,
      columns: [{
        header: "业务类型",
        dataIndex: "refType",
        menuDisabled: true,
        sortable: false,
        width: 120
      }, {
        header: "单号",
        dataIndex: "refNumber",
        menuDisabled: true,
        sortable: false,
        width: 120,
        renderer: function (value, md, record) {
          return "<a href='"
            + PSI.Const.BASE_URL
            + "Home/Bill/viewIndex?fid=2025&refType="
            + encodeURIComponent(record
              .get("refType"))
            + "&ref="
            + encodeURIComponent(record
              .get("refNumber"))
            + "' target='_blank'>" + value
            + "</a>";
        }
      }, {
        header: "业务日期",
        dataIndex: "bizDT",
        menuDisabled: true,
        sortable: false
      }, {
        header: "收",
        dataIndex: "inMoney",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        width: 160
      }, {
        header: "支",
        dataIndex: "outMoney",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        width: 160
      }, {
        header: "预收款余额",
        dataIndex: "balanceMoney",
        menuDisabled: true,
        sortable: false,
        align: "right",
        xtype: "numbercolumn",
        width: 160
      }, {
        header: "创建时间",
        dataIndex: "dateCreated",
        menuDisabled: true,
        sortable: false,
        width: 140
      }, {
        header: "业务员",
        dataIndex: "bizUserName",
        menuDisabled: true,
        sortable: false,
        width: 120
      }, {
        header: "制单人",
        dataIndex: "inputUserName",
        menuDisabled: true,
        sortable: false,
        width: 120
      }, {
        header: "备注",
        dataIndex: "memo",
        menuDisabled: true,
        sortable: false,
        width: 300
      }],
      store: store
    });

    var dt = new Date();
    dt.setDate(dt.getDate() - 7);
    Ext.getCmp("dtFrom").setValue(dt);

    return me.__detailGrid;
  },

  onQuery: function () {
    var me = this;

    me.getMainGrid().getStore().removeAll();
    me.getDetailGrid().getStore().removeAll();

    me.getMainGrid().getStore().loadPage(1);
  },

  queryCustomerCategory: function () {
    var combo = Ext.getCmp("comboCategory");
    var el = Ext.getBody();
    el.mask(PSI.Const.LOADING);
    Ext.Ajax.request({
      url: PSI.Const.BASE_URL + "Home/Customer/categoryList",
      method: "POST",
      callback: function (options, success, response) {
        var store = combo.getStore();

        store.removeAll();

        if (success) {
          var data = Ext.JSON.decode(response.responseText);
          store.add({
            id: "",
            name: "[全部]"
          });
          store.add(data);

          if (store.getCount() > 0) {
            combo.setValue(store.getAt(0).get("id"));
          }
        }

        el.unmask();
      }
    });
  },

  onReceivingMoney: function () {
    var form = Ext.create("PSI.Funds.AddPreReceivingForm", {
      parentForm: this
    });
    form.show();
  },

  onReturnMoney: function () {
    var form = Ext.create("PSI.Funds.ReturnPreReceivingForm", {
      parentForm: this
    });
    form.show();
  },

  onQueryDetail: function () {
    var dtTo = Ext.getCmp("dtTo").getValue();
    if (dtTo == null) {
      Ext.getCmp("dtTo").setValue(new Date());
    }

    var dtFrom = Ext.getCmp("dtFrom").getValue();
    if (dtFrom == null) {
      var dt = new Date();
      dt.setDate(dt.getDate() - 7);
      Ext.getCmp("dtFrom").setValue(dt);
    }

    this.getDetailGrid().getStore().loadPage(1);
  },

  onClearQuery: function () {
    var me = this;

    Ext.getCmp("editCustomerQuery").clearIdValue();
    me.onQuery();
  }
});
