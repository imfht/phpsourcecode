/**
 * PSI的应用容器：承载主菜单、其他模块的UI
 */
Ext.define("PSI.App", {
  config: {
    userName: "",
    productionName: "PSI",
    showCopyright: false
  },

  constructor: function (config) {
    var me = this;

    me.initConfig(config);

    me.createMainUI();

    if (config.appHeaderInfo) {
      me.setAppHeader(config.appHeaderInfo);
    }
  },

  createMainUI: function () {
    var me = this;

    me.mainPanel = Ext.create("Ext.panel.Panel", {
      border: 0,
      layout: "fit"
    });

    Ext.define("PSIFId", {
      extend: "Ext.data.Model",
      fields: ["fid", "name"]
    });

    var storeRecentFid = Ext.create("Ext.data.Store", {
      autoLoad: false,
      model: "PSIFId",
      data: []
    });

    me.gridRecentFid = Ext.create("Ext.grid.Panel", {
      header: {
        title: "常用功能 - 根据使用频率自动生成",
        height: 28
      },
      border: 0,
      titleAlign: "center",
      cls: "PSI-recent-fid",
      forceFit: true,
      hideHeaders: true,
      tools: [{
        type: "close",
        handler: function () {
          Ext.getCmp("PSI_Main_RecentPanel").collapse();
        },
        scope: me
      }],
      columns: [{
        dataIndex: "name",
        menuDisabled: true,
        menuDisabled: true,
        sortable: false,
        width: 16,
        renderer: function (value, metaData, record) {
          var fid = record.get("fid");
          var fileName = PSI.Const.BASE_URL + "Public/Images/fid/fid" + fid + ".png";
          if (fid.substring(0, 2) == "ct") {
            // 码表
            fileName = PSI.Const.BASE_URL + "Public/Images/fid/default.png";
          } else if (fid.substring(0, 2) == "fm") {
            // 自定义表单
            fileName = PSI.Const.BASE_URL + "Public/Images/fid/default.png";
          }

          return "<a href='#' style='text-decoration:none'><img src='"
            + fileName
            + "' style='vertical-align: middle;margin:0px 5px 0px 5px'></img></a>";
        }
      }, {
        dataIndex: "name",
        menuDisabled: true,
        menuDisabled: true,
        sortable: false,
        renderer: function (value, metaData, record) {
          return "<a href='#' style='text-decoration:none'><span style='vertical-align: middle'>"
            + value + "</span></a>";
        }
      }, {
        dataIndex: "name",
        menuDisabled: true,
        menuDisabled: true,
        sortable: false,
        width: 30,
        hidden: PSI.Const.MOT != "0",
        renderer: function (v, m, r) {
          var fileName = PSI.Const.BASE_URL + "Public/Images/icons/open_in_new_window.png";
          var name = r.get("name");
          return "<a href='#'><img src='" + fileName + "' style='vertical-align: middle' title='新窗口打开【" + name + "】'></img></a>";
        }
      }],
      store: storeRecentFid
    });

    me.gridRecentFid.on("cellclick", function (me, td, cellIndex, r, tr, rowIndex, e, eOpts) {
      var fid = r.get("fid");

      var url = PSI.Const.BASE_URL + "Home/MainMenu/navigateTo/fid/" + fid + "/t/1";

      if (fid === "-9999") {
        PSI.MsgBox.confirm("请确认是否重新登录", function () {
          location.replace(url);
        });
      } else {
        if (PSI.Const.MOT == "0") {
          if (cellIndex == 2) {
            window.open(url);
          }
          else {
            location.replace(url);
          }
        } else {
          window.open(url);
        }
      }
    }, me);

    var year = new Date().getFullYear();

    me.vp = Ext.create("Ext.container.Viewport", {
      layout: "fit",
      items: [{
        id: "__PSITopPanel",
        xtype: "panel",
        border: 0,
        layout: "border",
        header: {
          height: 40,
          tools: []
        },
        items: [{
          region: "center",
          border: 0,
          layout: "fit",
          xtype: "panel",
          items: [me.mainPanel]
        }, {
          id: "PSI_Main_RecentPanel",
          xtype: "panel",
          region: "east",
          width: 250,
          maxWidth: 250,
          split: true,
          collapsible: true,
          collapseMode: "mini",
          collapsed: me.getRecentFidPanelCollapsed(),
          header: false,
          border: 1,
          layout: "border",
          items: [{
            region: "center",
            layout: "fit",
            border: 0,
            items: me.gridRecentFid
          }, {
            region: "south",
            height: 30,
            border: 0,
            layout: "form",
            items: [{
              fieldLabel: "快捷访问",
              labelSeparator: "",
              margin: 5,
              labelAlign: "right",
              labelWidth: 60,
              emptyText: "双击此处弹出选择框",
              xtype: "psi_mainmenushortcutfield"
            }]
          }],
          listeners: {
            collapse: {
              fn: me.onRecentFidPanelCollapse,
              scope: me
            },
            expand: {
              fn: me.onRecentFidPanelExpand,
              scope: me
            }
          }
        }, {
          xtype: "panel",
          region: "south",
          hidden: !me.getShowCopyright(),
          height: 25,
          border: 0,
          header: {
            titleAlign: "center",
            title: "Copyright &copy; 2015-"
              + year
              + " PSI Team, All Rights Reserved"
          }
        }]
      }]
    });

    var el = Ext.getBody();
    el.mask("系统正在加载中...");

    Ext.Ajax.request({
      url: PSI.Const.BASE_URL + "Home/MainMenu/mainMenuItems",
      method: "POST",
      callback: function (opt, success, response) {
        if (success) {
          var data = Ext.JSON.decode(response.responseText);
          me.createMainMenu(data);
          me.refreshRectFidGrid();
        }

        el.unmask();
      },
      scope: me
    });
  },

  refreshRectFidGrid: function () {
    var me = this;

    var el = me.gridRecentFid.getEl() || Ext.getBody();
    el.mask("系统正在加载中...");
    var store = me.gridRecentFid.getStore();
    store.removeAll();

    Ext.Ajax.request({
      url: PSI.Const.BASE_URL + "Home/MainMenu/recentFid",
      method: "POST",
      callback: function (opt, success, response) {
        if (success) {
          var data = Ext.JSON.decode(response.responseText);
          store.add(data);
        }
        el.unmask();
      },
      scope: me
    });
  },

  createMainMenu: function (root) {
    var me = this;

    var menuItemClick = function () {
      var fid = this.fid;

      if (fid == "-9995") {
        window.open(PSI.Const.BASE_URL + "Home/Help/index");
      } else if (fid === "-9999") {
        // 重新登录
        PSI.MsgBox.confirm("请确认是否重新登录", function () {
          location.replace(PSI.Const.BASE_URL + "Home/MainMenu/navigateTo/fid/-9999");
        });
      } else {
        me.vp.focus();

        var url = PSI.Const.BASE_URL + "Home/MainMenu/navigateTo/fid/" + fid;
        if (PSI.Const.MOT == "0") {
          location.replace(url);
        } else {
          window.open(url);
        }
      }
    };

    var mainMenu = [];
    for (var i = 0; i < root.length; i++) {
      var m1 = root[i];

      var menuItem = Ext.create("Ext.menu.Menu");
      for (var j = 0; j < m1.children.length; j++) {
        var m2 = m1.children[j];

        if (m2.children.length === 0) {
          // 只有二级菜单
          if (m2.fid) {
            menuItem.add({
              text: m2.caption,
              fid: m2.fid,
              handler: menuItemClick,
              iconCls: "PSI-fid" + m2.fid
            });
          }
        } else {
          var menuItem2 = Ext.create("Ext.menu.Menu");

          menuItem.add({
            text: m2.caption,
            menu: menuItem2
          });

          // 三级菜单
          for (var k = 0; k < m2.children.length; k++) {
            var m3 = m2.children[k];
            menuItem2.add({
              text: m3.caption,
              fid: m3.fid,
              handler: menuItemClick,
              iconCls: "PSI-fid" + m3.fid
            });
          }
        }
      }

      if (m1.children.length > 0) {
        mainMenu.push({
          text: m1.caption,
          menu: menuItem
        });
      }
    }

    var mainToolbar = Ext.create("Ext.toolbar.Toolbar", {
      border: 0,
      dock: "top"
    });
    mainToolbar.add(mainMenu);

    var theCmp = me.vp.getComponent(0);
    theCmp.addTool(mainToolbar);
    var spacers = [];
    for (var i = 0; i < 10; i++) {
      spacers.push({
        xtype: "tbspacer"
      });
    }
    theCmp.addTool(spacers);
    theCmp.addTool({
      xtype: "tbtext",
      text: "<span style='color:#196d6d;font-weight:bold;font-size:13px'>当前用户："
        + me.getUserName() + "&nbsp;</span>"
    });
  },

  // 设置模块的标题
  setAppHeader: function (header) {
    if (!header) {
      return;
    }
    var panel = Ext.getCmp("__PSITopPanel");
    var title = "<span style='font-size:140%;color:#196d6d;font-weight:bold;'>"
      + header.title + " - " + this.getProductionName() + "</span>";
    panel.setTitle(title);
  },

  add: function (comp) {
    this.mainPanel.add(comp);
  },

  onRecentFidPanelCollapse: function () {
    Ext.util.Cookies.set("PSI_RECENT_FID", "1", Ext.Date.add(new Date(),
      Ext.Date.YEAR, 1));
  },

  onRecentFidPanelExpand: function () {
    Ext.util.Cookies.clear("PSI_RECENT_FID");
  },

  getRecentFidPanelCollapsed: function () {
    var v = Ext.util.Cookies.get("PSI_RECENT_FID");
    return v === "1";
  }
});
