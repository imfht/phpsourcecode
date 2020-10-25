/**
 * 主界面基类
 */
Ext.define("PSI.AFX.BaseMainExForm", {
  extend: "Ext.panel.Panel",

  border: 0,

  layout: "border",

  URL: function (url) {
    return PSI.Const.BASE_URL + url;
  },

  decodeJSON: function (str) {
    return Ext.JSON.decode(str);
  },

  tip: function (info) {
    PSI.MsgBox.tip(info);
  },

  showInfo: function (info, func) {
    PSI.MsgBox.showInfo(info, func);
  },

  confirm: function (confirmInfo, funcOnYes) {
    PSI.MsgBox.confirm(confirmInfo, funcOnYes);
  },

  ajax: function (r) {
    if (!r.method) {
      r.method = "POST";
    }
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
  },

  formatGridHeaderTitle: function (title) {
    return "<span style='font-size:13px'>" + title + "</sapn>";
  }
});
