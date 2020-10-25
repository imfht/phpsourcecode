$(document).ready(function() {
  if (g_mod == "advert" && g_ctrl == "adverts" && (g_act == "create" || g_act == "modify" || g_act == "view")) {
    if (g_act != "view") {
      Core.uploadPreviewImg("advert_src_file", "advert_src");
      Core.uploadPreviewImg("advert_src2_file", "advert_src2");
    }
    else {
      Core.uploadPreviewImg("advert_src_file", "advert_src", {uploadButtonClass: "ajax-file-upload-gray", url: "", returnType: ""});
      Core.uploadPreviewImg("advert_src2_file", "advert_src2", {uploadButtonClass: "ajax-file-upload-gray", url: "", returnType: ""});
    }

    Components.changeAdvertFields();
  }

  if (g_mod == "topic" && g_ctrl == "topic" && (g_act == "create" || g_act == "modify" || g_act == "view")) {
    if (g_act != "view") {
      Core.uploadPreviewImg("cover_file", "cover");
    }
    else {
      Core.uploadPreviewImg("cover_file", "cover", {uploadButtonClass: "ajax-file-upload-gray", url: "", returnType: ""});
    }
  }

  if (g_mod == "poll" && g_ctrl == "polls" && (g_act == "create" || g_act == "modify" || g_act == "view")) {
    Components.toggleMRankIds();
    Components.toggleMaxChoices();
    Components.toggleInterval();
  }
});

/**
 * Components
 * @author songhuan <trotri@yeah.net>
 * @version $Id: components.js 1 2014-10-22 19:37:00Z $
 */
Components = {
  /**
   * 通过“展现方式”，改变广告字段
   * @return void
   */
  changeAdvertFields: function() {
    var alls = ["show_code", "title", "advert_url", "advert_src", "advert_src_file", "advert_src2", "advert_src2_file", "attr_alt", "attr_width", "attr_height", "attr_fontsize", "attr_target"];

    var data = [];
    data["code"]  = ["show_code"];
    data["text"]  = ["title", "advert_url", "attr_fontsize", "attr_target"];
    data["image"] = ["advert_url", "advert_src", "advert_src_file", "advert_src2", "advert_src2_file", "attr_alt", "attr_width", "attr_height", "attr_target"];
    data["flash"] = ["advert_src", "advert_src_file", "attr_width", "attr_height"];

    var getElement = function(n) {
      if (n == "advert_src_file" || n == "advert_src2_file") {
        return $("#" + n);
      }

      var t = "input";
      if (n == "show_code" || n == "advert_url") {
        t = "textarea";
      }

      return $("#advanced " + t + "[name='" + n + "']");
    };

    var show = function(n) {
      getElement(n).parent().parent().show();
    };

    var hide = function(n) {
      getElement(n).parent().parent().hide();
    };

    var shows = function(a) {
      for (var i in a) {
        show(a[i]);
      }
    };

    var hides = function(a) {
      for (var i in a) {
        hide(a[i]);
      }
    };

    var exec = function() {
      var t = $("#advanced :radio[name='show_type']:checked").val();
      hides(alls);
      shows(data[t]);
    };

    exec();
    $("#advanced :radio[name='show_type']").on('ifChecked', function(event) {
      exec();
    });
  },

  /**
   * 显示和隐藏允许参与会员成长度
   * @return void
   */
  toggleMRankIds: function() {
    var exec = function(allowUnregistered) {
      var mRankIds = $("#main :checkbox[name='m_rank_ids[]']").parent().parent();
      if (mRankIds.attr("class") == "checkbox-inline") { mRankIds = mRankIds.parent(); }
      allowUnregistered == "n" ? mRankIds.show() : mRankIds.hide();
    };

    var o = $("#main :checkbox[name='allow_unregistered']");
    exec(o.val());
    o.change(function() {
      exec(($(this).val() == "y") ? "n" : "y");
    });
  },

  /**
   * 显示和隐藏最多可选数量
   * @return void
   */
  toggleMaxChoices: function() {
    var exec = function(isMultiple) {
      var maxChoices = $("#main :text[name='max_choices']").parent().parent();
      isMultiple == "y" ? maxChoices.show() : maxChoices.hide();
    };

    var o = $("#main :checkbox[name='is_multiple']");
    exec(o.val());
    o.change(function() {
      exec(($(this).val() == "y") ? "n" : "y");
    });
  },

  /**
   * 显示和隐藏间隔秒数
   * @return void
   */
  toggleInterval: function() {
    var exec = function(isInterval) {
      var interval = $("#main :text[name='interval']").parent().parent();
      isInterval == "y" ? interval.show() : interval.hide();
    };

    var o = $("#main :radio[name='join_type']");
    exec(($("#main :radio[name='join_type']:checked").val() == "interval") ? "y" : "n");
    o.on('ifChecked', function(event) {
      exec(($(this).val() == "interval") ? "y" : "n");
    });
  }

}
