$(document).ready(function() {
  if (g_ctrl == "posts" && (g_act == "create" || g_act == "modify" || g_act == "view")) {
    if (g_act != "view") {
      Core.uploadPreviewImg("picture_file", "picture");
    }
    else {
      Core.uploadPreviewImg("picture_file", "picture", {uploadButtonClass: "ajax-file-upload-gray", url: "", returnType: ""});
    }

    Posts.toggleJumpUrl();

    if (g_act == "create") {
      Posts.changeFields();
    }
  }
});

/**
 * Posts
 * @author songhuan <trotri@yeah.net>
 * @version $Id: posts.js 1 2013-10-16 18:38:00Z $
 */
Posts = {
  /**
   * 批量开放浏览
   * @param string url
   * @return void
   */
  batchPublish: function(url) {
    var n = $(":checkbox[name='checked_toggle']").val();
    var ids = Trotri.getCheckedValues(n);
    if (ids == "") {
      $("#dialog_alert_view_body").html("请选中开放浏览项！");
      $("#dialog_alert").modal("show");
      return ;
    }

    url += "&ids=" + ids + "&column_name=is_published&value=y";
    Trotri.href(url);
  },

  /**
   * 批量改为草稿
   * @param string url
   * @return void
   */
  batchUnpublish: function(url) {
    var n = $(":checkbox[name='checked_toggle']").val();
    var ids = Trotri.getCheckedValues(n);
    if (ids == "") {
      $("#dialog_alert_view_body").html("请选中改为草稿项！");
      $("#dialog_alert").modal("show");
      return ;
    }

    url += "&ids=" + ids + "&column_name=is_published&value=n";
    Trotri.href(url);
  },

  /**
   * 显示和隐藏跳转链接
   * @return void
   */
  toggleJumpUrl: function() {
    var exec = function(isJump) {
      var jumpUrl = $("#advanced :text[name='jump_url']").parent().parent();
      isJump == "y" ? jumpUrl.show() : jumpUrl.hide();
    };

    var o = $("#advanced :checkbox[name='is_jump']");
    exec(o.val());
    o.change(function() {
      exec($(this).val() == "y" ? "n" : "y");
    });
  },

  /**
   * 通过“所属模型”，改变扩展字段
   * @return void
   */
  changeFields: function() {
    var append = function(a) {
      $("#profile").find(".fields").remove();
      for (var n in a) {
        var s = "<div class=\"form-group fields\">";
        s += "<label class=\"col-lg-2 control-label\">" + a[n].label + "</label>";
        s += "<div class=\"col-lg-4\">";
        s += "<textarea class=\"form-control input-sm\" rows=\"5\" name=\"" + n + "\"></textarea>";
        s += "</div>";
        s += "<span class=\"control-label\">" + a[n].hint + "</span>";
        s += "</div>";
        $("#profile").append(s);
      }
    };

    var exec = function(mId) {
      var a = {};
      for (var id in g_fields) {
        if (mId == id) {
          a = g_fields[id];
        }
      }

      append(a);
    };

    var o = $("#profile select[name='module_id']");
    exec(o.val());
    o.change(function() {
      exec($(this).val());
    });
  }
}
