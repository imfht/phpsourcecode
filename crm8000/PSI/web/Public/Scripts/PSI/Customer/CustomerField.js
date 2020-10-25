/**
 * 自定义字段 - 客户
 */
Ext.define("PSI.Customer.CustomerField", {
  extend: "Ext.form.field.Trigger",
  alias: "widget.psi_customerfield",

  config: {
    showAddButton: false,
    callbackFunc: null,
    showModal: false
  },

  initComponent: function () {
    var me = this;
    me.__idValue = null;

    me.enableKeyEvents = true;

    me.callParent(arguments);

    me.on("keydown", function (field, e) {
      if (me.readOnly) {
        return;
      }

      if (e.getKey() == e.BACKSPACE) {
        field.setValue(null);
        me.setIdValue(null);
        e.preventDefault();
        return false;
      }

      if (e.getKey() != e.ENTER && !e.isSpecialKey(e.getKey())) {
        me.onTriggerClick(e);
      }
    });

    me.on({
      render: function (p) {
        p.getEl().on("dblclick", function () {
          me.onTriggerClick();
        });
      },
      single: true
    });
  },

  onTriggerClick: function (e) {
    var me = this;
    var modelName = "PSICustomerField";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "code", "name", "mobile01", "tel01", "fax",
        "address_receipt", "contact01", "warehouseName",
        "warehouseId"]
    });

    var store = Ext.create("Ext.data.Store", {
      model: modelName,
      autoLoad: false,
      data: []
    });
    var lookupGrid = Ext.create("Ext.grid.Panel", {
      cls: "PSI",
      columnLines: true,
      border: 0,
      store: store,
      columns: [{
        header: "编码",
        dataIndex: "code",
        menuDisabled: true
      }, {
        header: "客户",
        dataIndex: "name",
        menuDisabled: true,
        flex: 1
      }, {
        header: "手机",
        dataIndex: "mobile01",
        menuDisabled: true,
        width: 120
      }, {
        header: "销售出库仓库",
        dataIndex: "warehouseName",
        menuDisabled: true,
        width: 200
      }]
    });
    me.lookupGrid = lookupGrid;
    me.lookupGrid.on("itemdblclick", me.onOK, me);

    var wnd = Ext.create("Ext.window.Window", {
      title: "选择 - 客户",
      modal: me.getShowModal(),
      header: false,
      border: 0,
      width: 700,
      height: 390,
      layout: "border",
      defaultFocus: "__editCustomer",
      items: [{
        region: "center",
        xtype: "panel",
        layout: "fit",
        border: 0,
        items: [lookupGrid]
      }, {
        xtype: "panel",
        region: "south",
        height: 90,
        layout: "fit",
        border: 0,
        items: [{
          xtype: "form",
          layout: "form",
          bodyPadding: 5,
          items: [{
            id: "__editCustomer",
            xtype: "textfield",
            fieldLabel: "客户",
            labelWidth: 50,
            labelAlign: "right",
            labelSeparator: ""
          }, {
            xtype: "displayfield",
            fieldLabel: " ",
            value: "输入编码、客户拼音字头、手机号可以过滤查询",
            labelWidth: 50,
            labelAlign: "right",
            labelSeparator: ""
          }, {
            xtype: "displayfield",
            fieldLabel: " ",
            value: "↑ ↓ 键改变当前选择项 ；回车键返回",
            labelWidth: 50,
            labelAlign: "right",
            labelSeparator: ""
          }]
        }]
      }],
      buttons: [{
        text: "新增客户资料",
        iconCls: "PSI-button-add",
        hidden: !me.getShowAddButton(),
        handler: me.onAdd,
        scope: me
      }, {
        text: "确定",
        handler: me.onOK,
        scope: me
      }, {
        text: "取消",
        handler: function () {
          wnd.close();
        }
      }]
    });

    wnd.on("close", function () {
      me.focus();
    });
    if (!me.getShowModal()) {
      wnd.on("deactivate", function () {
        wnd.close();
      });
    }
    me.wnd = wnd;

    var editName = Ext.getCmp("__editCustomer");
    editName.on("change", function () {
      var store = me.lookupGrid.getStore();
      Ext.Ajax.request({
        url: PSI.Const.BASE_URL + "Home/Customer/queryData",
        params: {
          queryKey: editName.getValue()
        },
        method: "POST",
        callback: function (opt, success, response) {
          store.removeAll();
          if (success) {
            var data = Ext.JSON
              .decode(response.responseText);
            store.add(data);
            if (data.length > 0) {
              me.lookupGrid.getSelectionModel().select(0);
              editName.focus();
            }
          } else {
            PSI.MsgBox.showInfo("网络错误");
          }
        },
        scope: this
      });

    }, me);

    editName.on("specialkey", function (field, e) {
      if (e.getKey() == e.ENTER) {
        me.onOK();
      } else if (e.getKey() == e.UP) {
        var m = me.lookupGrid.getSelectionModel();
        var store = me.lookupGrid.getStore();
        var index = 0;
        for (var i = 0; i < store.getCount(); i++) {
          if (m.isSelected(i)) {
            index = i;
          }
        }
        index--;
        if (index < 0) {
          index = 0;
        }
        m.select(index);
        e.preventDefault();
        editName.focus();
      } else if (e.getKey() == e.DOWN) {
        var m = me.lookupGrid.getSelectionModel();
        var store = me.lookupGrid.getStore();
        var index = 0;
        for (var i = 0; i < store.getCount(); i++) {
          if (m.isSelected(i)) {
            index = i;
          }
        }
        index++;
        if (index > store.getCount() - 1) {
          index = store.getCount() - 1;
        }
        m.select(index);
        e.preventDefault();
        editName.focus();
      }
    }, me);

    me.wnd.on("show", function () {
      editName.focus();
      editName.fireEvent("change");
    }, me);
    wnd.showBy(me);
  },

  onOK: function () {
    var me = this;
    var grid = me.lookupGrid;
    var item = grid.getSelectionModel().getSelection();
    if (item == null || item.length != 1) {
      return;
    }

    var data = item[0].getData();

    me.wnd.close();
    me.focus();
    me.setValue(data.name);
    me.focus();

    me.setIdValue(data.id);

    var func = me.getCallbackFunc();
    if (func) {
      func(data);
    }
  },

  setIdValue: function (id) {
    this.__idValue = id;
  },

  getIdValue: function () {
    return this.__idValue;
  },

  clearIdValue: function () {
    this.setValue(null);
    this.__idValue = null;
  },

	/**
	 * 新增客户资料
	 */
  onAdd: function () {
    var form = Ext.create("PSI.Customer.CustomerEditForm");
    form.show();
  }
});
