$(document).ready(function() {
  if (g_act == "permissionmodify") {
    Users.checkedToggle();
  }

  if (g_ctrl == "account" && g_act == "login") {
    Users.forwardLoginHistory();
  }
});

/**
 * Users
 * @author songhuan <trotri@yeah.net>
 * @version $Id: users.js 1 2013-10-16 18:38:00Z $
 */
Users = {
  /**
   * 登录成功后，跳转到来源页或首页
   * @return void
   */
  forwardLoginHistory: function() {
    if ($("#alert_bar_1").hasClass("alert-success")) {
      var httpReferer = $(":hidden[name='http_referer']").val();
      if (httpReferer != "") {
        $("#alert_bar_1").html($("#alert_bar_1").text() + "&nbsp;&nbsp;正在跳转...");
        $(".form-signin").hide();
        setTimeout(function() {
          location.href = httpReferer;
        }, 1000);
      }
    }
  },

  /**
   * 批量禁用
   * @param string url
   * @return void
   */
  batchForbidden: function(url) {
    var n = $(":checkbox[name='checked_toggle']").val();
    var ids = Trotri.getCheckedValues(n);
    if (ids == "") {
      $("#dialog_alert_view_body").html("请选中禁用项！");
      $("#dialog_alert").modal("show");
      return ;
    }

    url += "&ids=" + ids + "&column_name=forbidden&value=y";
    Trotri.href(url);
  },

  /**
   * 批量解除禁用
   * @param string url
   * @return void
   */
  batchUnforbidden: function(url) {
    var n = $(":checkbox[name='checked_toggle']").val();
    var ids = Trotri.getCheckedValues(n);
    if (ids == "") {
      $("#dialog_alert_view_body").html("请选中解除禁用项！");
      $("#dialog_alert").modal("show");
      return ;
    }

    url += "&ids=" + ids + "&column_name=forbidden&value=n";
    Trotri.href(url);
  },

  /**
   * CheckBox全选|全不选
   * @return void
   */
  checkedToggle: function() {
    // 收集所有的CheckBox内容
    var iChecks = [];
    $(".icheck").each(function() {
      var n = $(this).attr("name");
      var v = $(this).val();

      if (n == "__mod__[]") {
        iChecks[v] = [];
      }
      else {
        for (var k in iChecks) {
          if (Trotri.startWith(k, n)) {
            iChecks[k][n] = "";
          }
        }
      }
    });

    var check = function(E, b) {
      if (typeof b == "undefined") { b = false; }
      if (typeof E != "object") {
        E = $(":checkbox[name='" + E + "']");
      }

      E.iCheck(b ? "check" : "uncheck");
    }

    var checkAll = function(k, b) {
      for (var n in iChecks[k]) {
        check(n, b);
      }
    }

    // 初始化全选|全不选按钮状态
    $(":checkbox[name='__mod__[]']").each(function() {
      var v = $(this).val();
      var b = true;
      for (var k in iChecks[v]) {
        if ($(":checkbox[name='" + k + "']").length != $(":checkbox[name='" + k + "']:checked").length) {
          b = false;
        }
      }

      check($(this), b);
    });

    $(':checkbox').on('ifChecked', function(event) {
      exec($(this), true);
    });

    $(':checkbox').on('ifUnchecked', function(event) {
      exec($(this), false);
    });

    $(':checkbox').on('ifClicked', function(event) {
      if ($(this).attr("name") == "__mod__[]") {
        checkAll($(this).val(), !event.delegateTarget.checked);
      }
    });

    var exec = function(o, b) {
      var n = $(o).attr("name");
      if (n == "__mod__[]") {
        return ;
      }

      $(":checkbox[name='__mod__[]']").each(function() {
        var v = $(this).val();
        if (Trotri.startWith(v, n)) {
          var b = true;
          for (var k in iChecks[v]) {
            if ($(":checkbox[name='" + k + "']").length != $(":checkbox[name='" + k + "']:checked").length) {
              b = false;
            }
          }

          check($(this), b);
        }
      });
    }
  }
}
