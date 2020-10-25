/**
 * 仓库 - 编辑数据域
 */
Ext.define("PSI.Warehouse.EditDataOrgForm", {
  extend: "PSI.AFX.BaseDialogForm",

	/**
	 * 初始化组件
	 */
  initComponent: function () {
    var me = this;

    var entity = me.getEntity();

    var buttons = [{
      text: "数据域的使用帮助",
      iconCls: "PSI-help",
      handler: function () {
        var url = me.URL("Home/Help/index?t=dataOrg")
        window.open(url);
      }
    }, "->"];

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
      text: entity == null ? "关闭" : "取消",
      handler: function () {
        me.close();
      },
      scope: me
    };
    buttons.push(btn);

    var logoHtml = "<img style='float:left;margin:10px 10px 0px 20px;width:48px;height:48px;' src='"
      + PSI.Const.BASE_URL
      + "Public/Images/edit-form-data.png'></img>"
      + "<h2 style='color:#196d83'>修改数据域</h2>"
      + "<p style='color:#196d83'>点击帮助按钮可以了解更多数据域的应用场景</p>";

    Ext.apply(me, {
      header: {
        title: me.formatTitle(PSI.Const.PROD_NAME),
        height: 40
      },
      modal: true,
      resizable: false,
      onEsc: Ext.emptyFn,
      width: 400,
      height: 310,
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
        id: "PSI_Warehouse_EditDataOrgForm_editForm",
        xtype: "form",
        layout: {
          type: "table",
          columns: 1
        },
        height: "100%",
        bodyPadding: 5,
        defaultType: 'textfield',
        fieldDefaults: {
          labelWidth: 60,
          labelAlign: "right",
          labelSeparator: "",
          msgTarget: 'side',
          width: 370,
          margin: "5"
        },
        items: [{
          id: "PSI_Warehouse_EditDataOrgForm_editId",
          xtype: "hidden",
          value: entity.get("id")
        }, {
          readOnly: true,
          fieldLabel: "仓库编码",
          value: entity.get("code")
        }, {
          readOnly: true,
          fieldLabel: "仓库名称",
          value: entity.get("name")
        }, {
          readOnly: true,
          fieldLabel: "原数据域",
          value: entity.get("dataOrg"),
          id: "PSI_Warehouse_EditDataOrgForm_editOldDataOrg"
        }, {
          id: "PSI_Warehouse_EditDataOrgForm_editDataOrg",
          fieldLabel: "新数据域",
          name: "dataOrg",
          xtype: "psi_selectuserdataorgfield"
        }],
        buttons: buttons
      }]
    });

    me.callParent(arguments);

    me.editForm = Ext.getCmp("PSI_Warehouse_EditDataOrgForm_editForm");

    me.editId = Ext.getCmp("PSI_Warehouse_EditDataOrgForm_editId");
    me.editOldDataOrg = Ext
      .getCmp("PSI_Warehouse_EditDataOrgForm_editOldDataOrg");
    me.editDataOrg = Ext
      .getCmp("PSI_Warehouse_EditDataOrgForm_editDataOrg");
  },

	/**
	 * 保存
	 */
  onOK: function () {
    var me = this;

    var oldDataOrg = me.editOldDataOrg.getValue();
    var newDataOrg = me.editDataOrg.getValue();
    if (!newDataOrg) {
      PSI.MsgBox.showInfo("没有输入新数据域", function () {
        me.editDataOrg.focus();
      });

      return;
    }
    if (oldDataOrg == newDataOrg) {
      PSI.MsgBox.showInfo("新数据域没有变动，不用保存");

      return;
    }

    var f = me.editForm;
    var el = f.getEl();
    el.mask(PSI.Const.SAVING);

    var r = {
      url: me.URL("Home/Warehouse/editDataOrg"),
      params: {
        id: me.editId.getValue(),
        dataOrg: newDataOrg
      },
      method: "POST",
      callback: function (options, success, response) {
        el.unmask();
        if (success) {
          var data = Ext.JSON.decode(response.responseText);
          if (data.success) {
            me.__lastId = data.id;
            PSI.MsgBox.tip("成功修改数据域");
            me.close();
          } else {
            PSI.MsgBox.showInfo(data.msg);
          }
        } else {
          PSI.MsgBox.showInfo("网络错误");
        }
      }
    };

    Ext.Ajax.request(r);
  },

  onWindowBeforeUnload: function (e) {
    return (window.event.returnValue = e.returnValue = '确认离开当前页面？');
  },

  onWndClose: function () {
    var me = this;

    Ext.get(window).un('beforeunload', me.onWindowBeforeUnload);

    if (me.__lastId) {
      if (me.getParentForm()) {
        me.getParentForm().freshGrid(me.__lastId);
      }
    }
  },

  onWndShow: function () {
    var me = this;

    Ext.get(window).on('beforeunload', me.onWindowBeforeUnload);

    me.editDataOrg.focus();
  }
});
