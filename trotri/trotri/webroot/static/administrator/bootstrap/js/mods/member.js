$(document).ready(function() {
  if (g_ctrl == "members") {
    if (g_act == "index") {
      Member.ajaxAccount();
    }
  }

  if (g_ctrl == "social") {
    if (g_act == "modify") {
      var regionUrl = g_url + "?r=system/regions/index&def=1&pid=";
      Core.regions(regionUrl, {
        country  : "live_country_id",
        province : "live_province_id",
        city     : "live_city_id",
        district : "live_district_id"
      }, g_data, false);
      Core.regions(regionUrl, {
        country  : "address_country_id",
        province : "address_province_id",
        city     : "address_city_id",
        district : "address_district_id"
      }, g_data, false);

      Core.uploadPreviewImg("head_portrait_file", "head_portrait");
    }

    if (g_act == "view") {
      Core.uploadPreviewImg("head_portrait_file", "head_portrait", {uploadButtonClass: "ajax-file-upload-gray", url: "", returnType: ""});
    }
  }

  if (g_ctrl == "addresses") {
    if (g_act == "create" || g_act == "modify") {
      var regionUrl = g_url + "?r=system/regions/index&def=1&pid=";
      Core.regions(regionUrl, {
        country  : "addr_country_id",
        province : "addr_province_id",
        city     : "addr_city_id",
        district : "addr_district_id"
      }, g_data, false);
    }
  }
});

/**
 * Member
 * @author songhuan <trotri@yeah.net>
 * @version $Id: member.js 1 2013-10-16 18:38:00Z $
 */
Member = {
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
   * 提交会员账户
   * @return void
   */
  ajaxAccount: function() {
    var setError = function(errMsg) {
      $("#dialog_ajax_view_body .form-group").addClass("has-error");
      $("#dialog_ajax_view_body :text[name='value']").focus().select();
    };

    var btn = $("#dialog_ajax_view .btn");
    btn.removeAttr("data-dismiss");
    btn.click(function() {
      var url = $("#dialog_ajax_view_body :hidden[name='url']").val();
      var value = $("#dialog_ajax_view_body :text[name='value']").val();
      if (value != "") {
        Trotri.href(url + value);
      }
      else {
        setError();
      }
    });
  }
}
