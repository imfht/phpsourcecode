/**
 * 业务设置 - 编辑设置项目
 */
Ext.define("PSI.BizConfig.EditForm", {
  extend: "PSI.AFX.BaseDialogForm",

  config: {
    companyId: null
  },

  initComponent: function () {
    var me = this;

    var buttons = [];

    buttons.push({
      text: "保存",
      formBind: true,
      iconCls: "PSI-button-ok",
      handler: function () {
        me.onOK();
      },
      scope: me
    }, {
        text: "取消",
        handler: function () {
          me.close();
        },
        scope: me
      });

    var modelName = "PSIWarehouse";
    Ext.define(modelName, {
      extend: "Ext.data.Model",
      fields: ["id", "name"]
    });

    var storePW = Ext.create("Ext.data.Store", {
      model: modelName,
      autoLoad: false,
      fields: ["id", "name"],
      data: []
    });
    me.__storePW = storePW;
    var storeWS = Ext.create("Ext.data.Store", {
      model: modelName,
      autoLoad: false,
      fields: ["id", "name"],
      data: []
    });
    me.__storeWS = storeWS;

    Ext.apply(me, {
      header: {
        title: me.formatTitle("业务设置"),
        height: 40,
        iconCls: "PSI-button-edit"
      },
      width: 500,
      height: 520,
      layout: "fit",
      items: [{
        xtype: "tabpanel",
        bodyPadding: 5,
        border: 0,
        items: [{
          title: "公司",
          border: 0,
          layout: "form",
          iconCls: "PSI-fid2008",
          items: [{
            id: "editName9000-01",
            xtype: "displayfield"
          }, {
            id: "editValue9000-01",
            xtype: "textfield"
          }, {
            id: "editName9000-02",
            xtype: "displayfield"
          }, {
            id: "editValue9000-02",
            xtype: "textfield"
          }, {
            id: "editName9000-03",
            xtype: "displayfield"
          }, {
            id: "editValue9000-03",
            xtype: "textfield"
          }, {
            id: "editName9000-04",
            xtype: "displayfield"
          }, {
            id: "editValue9000-04",
            xtype: "textfield"
          }, {
            id: "editName9000-05",
            xtype: "displayfield"
          }, {
            id: "editValue9000-05",
            xtype: "textfield"
          }]
        }, {
          title: "采购",
          border: 0,
          layout: "form",
          iconCls: "PSI-fid2001",
          items: [{
            id: "editName2001-01",
            xtype: "displayfield"
          }, {
            id: "editValue2001-01",
            xtype: "combo",
            queryMode: "local",
            editable: false,
            valueField: "id",
            displayField: "name",
            store: storePW,
            name: "value2001-01"
          }, {
            id: "editName2001-02",
            xtype: "displayfield"
          }, {
            id: "editValue2001-02",
            xtype: "combo",
            queryMode: "local",
            editable: false,
            valueField: "id",
            store: Ext.create(
              "Ext.data.ArrayStore", {
                fields: ["id", "text"],
                data: [["0", "记应付账款"],
                ["1", "现金付款"],
                ["2", "预付款"]]
              }),
            value: "0"
          }, {
            id: "editName2001-03",
            xtype: "displayfield"
          }, {
            id: "editValue2001-03",
            xtype: "combo",
            queryMode: "local",
            editable: false,
            valueField: "id",
            store: Ext.create(
              "Ext.data.ArrayStore", {
                fields: ["id", "text"],
                data: [["0", "记应付账款"],
                ["1", "现金付款"],
                ["2", "预付款"]]
              }),
            value: "0"
          }, {
            id: "editName2001-04",
            xtype: "displayfield"
          }, {
            id: "editValue2001-04",
            xtype: "combo",
            queryMode: "local",
            editable: false,
            valueField: "id",
            store: Ext.create(
              "Ext.data.ArrayStore", {
                fields: ["id", "text"],
                data: [
                  ["0", "不做限制"],
                  ["1",
                    "不能超过采购订单未入库量"]]
              }),
            value: "0"
          }]
        }, {
          title: "销售",
          border: 0,
          layout: "form",
          iconCls: "PSI-fid2002",
          items: [{
            id: "editName2002-02",
            xtype: "displayfield"
          }, {
            id: "editValue2002-02",
            xtype: "combo",
            queryMode: "local",
            editable: false,
            valueField: "id",
            displayField: "name",
            store: storeWS,
            name: "value2002-02"
          }, {
            id: "editName2002-01",
            xtype: "displayfield"
          }, {
            id: "editValue2002-01",
            xtype: "combo",
            queryMode: "local",
            editable: false,
            valueField: "id",
            store: Ext.create(
              "Ext.data.ArrayStore", {
                fields: ["id", "text"],
                data: [["0", "不允许编辑销售单价"],
                ["1", "允许编辑销售单价"]]
              }),
            name: "value2002-01"
          }, {
            id: "editName2002-03",
            xtype: "displayfield"
          }, {
            id: "editValue2002-03",
            xtype: "combo",
            queryMode: "local",
            editable: false,
            valueField: "id",
            store: Ext.create(
              "Ext.data.ArrayStore", {
                fields: ["id", "text"],
                data: [["0", "记应收账款"],
                ["1", "现金收款"],
                ["2", "用预收款支付"]]
              }),
            value: "0"
          }, {
            id: "editName2002-04",
            xtype: "displayfield"
          }, {
            id: "editValue2002-04",
            xtype: "combo",
            queryMode: "local",
            editable: false,
            valueField: "id",
            store: Ext.create(
              "Ext.data.ArrayStore", {
                fields: ["id", "text"],
                data: [["0", "记应收账款"],
                ["1", "现金收款"]]
              }),
            value: "0"
          }, {
            id: "editName2002-05",
            xtype: "displayfield"
          }, {
            id: "editValue2002-05",
            xtype: "combo",
            queryMode: "local",
            editable: false,
            valueField: "id",
            store: Ext.create(
              "Ext.data.ArrayStore", {
                fields: ["id", "text"],
                data: [
                  ["0", "不做限制"],
                  ["1",
                    "不能超过销售订单未出库量"]]
              }),
            value: "0"
          }]
        }, {
          title: "存货",
          border: 0,
          layout: "form",
          iconCls: "PSI-fid1003",
          items: [{
            id: "editName1003-02",
            xtype: "displayfield"
          }, {
            id: "editValue1003-02",
            xtype: "combo",
            queryMode: "local",
            editable: false,
            valueField: "id",
            store: Ext.create(
              "Ext.data.ArrayStore", {
                fields: ["id", "text"],
                data: [["0", "移动平均法"],
                ["1", "先进先出法"]]
              }),
            name: "value1003-01"
          }]
        }, {
          title: "财务",
          border: 0,
          iconCls: "PSI-fid2024",
          layout: "form",
          items: [{
            id: "editName9001-01",
            xtype: "displayfield"
          }, {
            id: "editValue9001-01",
            xtype: "numberfield",
            hideTrigger: true,
            allowDecimals: false
          }]
        }, {
          title: "单号前缀",
          border: 0,
          layout: {
            type: "table",
            columns: 2
          },
          items: [{
            id: "editName9003-01",
            xtype: "displayfield"
          }, {
            id: "editValue9003-01",
            xtype: "textfield"
          }, {
            id: "editName9003-02",
            xtype: "displayfield"
          }, {
            id: "editValue9003-02",
            xtype: "textfield"
          }, {
            id: "editName9003-03",
            xtype: "displayfield"
          }, {
            id: "editValue9003-03",
            xtype: "textfield"
          }, {
            id: "editName9003-04",
            xtype: "displayfield"
          }, {
            id: "editValue9003-04",
            xtype: "textfield"
          }, {
            id: "editName9003-05",
            xtype: "displayfield"
          }, {
            id: "editValue9003-05",
            xtype: "textfield"
          }, {
            id: "editName9003-06",
            xtype: "displayfield"
          }, {
            id: "editValue9003-06",
            xtype: "textfield"
          }, {
            id: "editName9003-07",
            xtype: "displayfield"
          }, {
            id: "editValue9003-07",
            xtype: "textfield"
          }, {
            id: "editName9003-08",
            xtype: "displayfield"
          }, {
            id: "editValue9003-08",
            xtype: "textfield"
          }, {
            id: "editName9003-09",
            xtype: "displayfield"
          }, {
            id: "editValue9003-09",
            xtype: "textfield"
          }, {
            id: "editName9003-10",
            xtype: "displayfield"
          }, {
            id: "editValue9003-10",
            xtype: "textfield"
          }, {
            id: "editName9003-11",
            xtype: "displayfield"
          }, {
            id: "editValue9003-11",
            xtype: "textfield"
          }, {
            id: "editName9003-12",
            xtype: "displayfield"
          }, {
            id: "editValue9003-12",
            xtype: "textfield"
          }]
        }, {
          title: "系统",
          border: 0,
          iconCls: "PSI-fid-9994",
          layout: "form",
          items: [{
            id: "editName9002-01",
            xtype: "displayfield"
          }, {
            id: "editValue9002-01",
            xtype: "textfield"
          }, {
            id: "editName9002-02",
            xtype: "displayfield"
          }, {
            id: "editValue9002-02",
            xtype: "combo",
            queryMode: "local",
            editable: false,
            valueField: "id",
            store: Ext.create(
              "Ext.data.ArrayStore", {
                fields: ["id", "text"],
                data: [["0", "原窗口打开"],
                ["1", "新窗口打开"]]
              })
          }, {
            id: "editName9002-03",
            xtype: "displayfield"
          }, {
            id: "editValue9002-03",
            xtype: "combo",
            queryMode: "local",
            editable: false,
            valueField: "id",
            store: Ext.create(
              "Ext.data.ArrayStore", {
                fields: ["id", "text"],
                data: [["0", "整数"],
                ["1", "1位小数"],
                ["2", "2位小数"],
                ["3", "3位小数"],
                ["4", "4位小数"],
                ["5", "5位小数"],
                ["6", "6位小数"],
                ["7", "7位小数"],
                ["8", "8位小数"]]
              })
          }]
        }],
        buttons: buttons
      }],
      listeners: {
        close: {
          fn: me.onWndClose,
          scope: me
        },
        show: {
          fn: me.onWndShow,
          scope: me
        }
      }
    });

    me.callParent(arguments);
  },

  getSaveData: function () {
    var me = this;

    var result = {
      companyId: me.getCompanyId(),
      'value9000-01': Ext.getCmp("editValue9000-01").getValue(),
      'value9000-02': Ext.getCmp("editValue9000-02").getValue(),
      'value9000-03': Ext.getCmp("editValue9000-03").getValue(),
      'value9000-04': Ext.getCmp("editValue9000-04").getValue(),
      'value9000-05': Ext.getCmp("editValue9000-05").getValue(),
      'value1003-02': Ext.getCmp("editValue1003-02").getValue(),
      'value2001-01': Ext.getCmp("editValue2001-01").getValue(),
      'value2001-02': Ext.getCmp("editValue2001-02").getValue(),
      'value2001-03': Ext.getCmp("editValue2001-03").getValue(),
      'value2001-04': Ext.getCmp("editValue2001-04").getValue(),
      'value2002-01': Ext.getCmp("editValue2002-01").getValue(),
      'value2002-02': Ext.getCmp("editValue2002-02").getValue(),
      'value2002-03': Ext.getCmp("editValue2002-03").getValue(),
      'value2002-04': Ext.getCmp("editValue2002-04").getValue(),
      'value2002-05': Ext.getCmp("editValue2002-05").getValue(),
      'value9001-01': Ext.getCmp("editValue9001-01").getValue(),
      'value9002-01': Ext.getCmp("editValue9002-01").getValue(),
      'value9002-02': Ext.getCmp("editValue9002-02").getValue(),
      'value9002-03': Ext.getCmp("editValue9002-03").getValue(),
      'value9003-01': Ext.getCmp("editValue9003-01").getValue(),
      'value9003-02': Ext.getCmp("editValue9003-02").getValue(),
      'value9003-03': Ext.getCmp("editValue9003-03").getValue(),
      'value9003-04': Ext.getCmp("editValue9003-04").getValue(),
      'value9003-05': Ext.getCmp("editValue9003-05").getValue(),
      'value9003-06': Ext.getCmp("editValue9003-06").getValue(),
      'value9003-07': Ext.getCmp("editValue9003-07").getValue(),
      'value9003-08': Ext.getCmp("editValue9003-08").getValue(),
      'value9003-09': Ext.getCmp("editValue9003-09").getValue(),
      'value9003-10': Ext.getCmp("editValue9003-10").getValue(),
      'value9003-11': Ext.getCmp("editValue9003-11").getValue(),
      'value9003-12': Ext.getCmp("editValue9003-12").getValue()
    };

    return result;
  },

  onOK: function (thenAdd) {
    var me = this;
    Ext.getBody().mask("正在保存中...");
    Ext.Ajax.request({
      url: PSI.Const.BASE_URL + "Home/BizConfig/edit",
      method: "POST",
      params: me.getSaveData(),
      callback: function (options, success, response) {
        Ext.getBody().unmask();

        if (success) {
          var data = Ext.JSON.decode(response.responseText);
          if (data.success) {
            me.__saved = true;
            PSI.MsgBox.showInfo("成功保存数据", function () {
              me.close();
            });
          } else {
            PSI.MsgBox.showInfo(data.msg);
          }
        }
      }
    });
  },

  onWindowBeforeUnload: function (e) {
    return (window.event.returnValue = e.returnValue = '确认离开当前页面？');
  },

  onWndClose: function () {
    var me = this;

    Ext.get(window).un('beforeunload', me.onWindowBeforeUnload);

    if (me.__saved) {
      me.getParentForm().refreshGrid();
    }
  },

  onWndShow: function () {
    var me = this;
    me.__saved = false;

    Ext.get(window).on('beforeunload', me.onWindowBeforeUnload);

    var el = me.getEl() || Ext.getBody();
    el.mask(PSI.Const.LOADING);
    Ext.Ajax.request({
      url: PSI.Const.BASE_URL + "Home/BizConfig/allConfigsWithExtData",
      params: {
        companyId: me.getCompanyId()
      },
      method: "POST",
      callback: function (options, success, response) {
        if (success) {
          var data = Ext.JSON.decode(response.responseText);
          me.__storePW.add(data.extData.warehouse);
          me.__storeWS.add(data.extData.warehouse);

          for (var i = 0; i < data.dataList.length; i++) {
            var item = data.dataList[i];
            var editName = Ext.getCmp("editName" + item.id);
            if (editName) {
              editName.setValue(item.name);
            }
            var editValue = Ext.getCmp("editValue"
              + item.id);
            if (editValue) {
              editValue.setValue(item.value);
            }
          }
        } else {
          PSI.MsgBox.showInfo("网络错误");
        }

        el.unmask();
      }
    });
  }
});
