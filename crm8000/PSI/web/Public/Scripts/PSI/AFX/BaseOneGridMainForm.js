/**
 * 只有一个Grid的主界面基类
 */
Ext.define("PSI.AFX.BaseOneGridMainForm", {
  extend: "PSI.AFX.BaseMainForm",

  initComponent: function () {
    var me = this;

    Ext.apply(me, {
      items: [{
        region: "center",
        xtype: "panel",
        layout: "fit",
        border: 0,
        items: [me.getMainGrid()]
      }]
    });

    me.callParent(arguments);

    if (me.afxGetRefreshGridURL() != null) {
      me.freshGrid();
    }
  },

  // public
  getMainGrid: function () {
    var me = this;
    return me.afxGetMainGrid();
  },

  // public
  gotoGridRecord: function (id) {
    var me = this;
    var grid = me.getMainGrid();
    var store = grid.getStore();
    if (id) {
      var r = store.findExact("id", id);
      if (r != -1) {
        grid.getSelectionModel().select(r);
      } else {
        grid.getSelectionModel().select(0);
      }
    }
  },

  // public
  freshGrid: function (id) {
    this.afxRefreshGrid(id);
  },

  // public
  refreshGrid: function (id) {
    this.afxRefreshGrid(id);
  },

  // public
  getPreIndexInMainGrid: function (id) {
    var me = this;

    var store = me.getMainGrid().getStore();
    var index = store.findExact("id", id) - 1;

    var result = null;
    var preEntity = store.getAt(index);
    if (preEntity) {
      result = preEntity.get("id");
    }

    return result;
  },

  // protected
  afxGetMainGrid: function () {
    return null;
  },

  // protected
  afxGetRefreshGridURL: function () {
    return null;
  },

  // protected
  afxGetRefreshGridParams: function () {
    return {};
  },

  // protected
  afxRefreshGrid: function (id) {
    var me = this;
    var grid = me.getMainGrid();
    var el = grid.getEl() || Ext.getBody();
    el.mask(PSI.Const.LOADING);
    var r = {
      url: me.URL(me.afxGetRefreshGridURL()),
      params: me.afxGetRefreshGridParams(),
      method: "POST",
      callback: function (options, success, response) {
        var store = grid.getStore();

        store.removeAll();

        if (success) {
          var data = me.decodeJSON(response.responseText);
          store.add(data);

          me.gotoGridRecord(id);
        }

        el.unmask();
      }
    };
    Ext.Ajax.request(r);
  },

  closeWindow: function () {
    if (PSI.Const.MOT == "0") {
      window.location.replace(PSI.Const.BASE_URL);
    } else {
      window.close();

      if (!window.closed) {
        window.location.replace(PSI.Const.BASE_URL);
      }
    }
  }
});
