$(document).ready(function() {
  Member.bindFocus();
  if (g_ctrl == "show") {
    if (g_act == "social") {
      Member.loadBirth();
      $("#head_portrait_file").attr("url", Member.getAjaxHeadPortraitUrl());
      Core.uploadPreviewImg("head_portrait_file", "head_portrait");

      var regionUrl = Core.getAjaxRegionsUrl({"def": 1});
      Core.regions(regionUrl, {
        // country  : "live_country_id",
        province : "live_province_id",
        city     : "live_city_id",
        district : "live_district_id"
      }, g_data, false);
      Core.regions(regionUrl, {
        // country  : "address_country_id",
        province : "address_province_id",
        city     : "address_city_id",
        district : "address_district_id"
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
   * 获取会员注册链接
   * @param object params
   * @return string
   */
  getRegUrl: function(params) {
    return Core.getUrl("member", "show", "reg", params);
  },

  /**
   * 获取会员登录链接
   * @param object params
   * @return string
   */
  getLoginUrl: function(params) {
    return Core.getUrl("member", "show", "login", params);
  },

  /**
   * 获取会员退出登录链接
   * @param object params
   * @return string
   */
  getLogoutUrl: function(params) {
    return Core.getUrl("member", "show", "logout", params);
  },

  /**
   * 获取Ajax会员注册链接
   * @param object params
   * @return string
   */
  getAjaxRegUrl: function(params) {
    return Core.getUrl("member", "data", "reg", params) + "&" + new Date().getTime();
  },

  /**
   * 获取Ajax会员登录链接
   * @param object params
   * @return string
   */
  getAjaxLoginUrl: function(params) {
    return Core.getUrl("member", "data", "login", params) + "&" + new Date().getTime();
  },

  /**
   * 获取Ajax通过原始密码重设新密码链接
   * @param object params
   * @return string
   */
  getAjaxRepwdoldpwdUrl: function(params) {
    return Core.getUrl("member", "data", "repwdoldpwd", params) + "&" + new Date().getTime();
  },

  /**
   * 获取Ajax发送找回密码邮件链接
   * @param object params
   * @return string
   */
  getAjaxRepwdsendmailUrl: function(params) {
    return Core.getUrl("member", "data", "repwdsendmail", params) + "&" + new Date().getTime();
  },

  /**
   * 获取Ajax通过邮箱找回密码链接
   * @param object params
   * @return string
   */
  getAjaxRepwdmailUrl: function(params) {
    return Core.getUrl("member", "data", "repwdmail", params) + "&" + new Date().getTime();
  },

  /**
   * 获取Ajax上传会员头像
   * @param object params
   * @return string
   */
  getAjaxHeadPortraitUrl: function(params) {
    return Core.getUrl("member", "data", "upload", params) + "&" + new Date().getTime();
  },

  /**
   * 获取Ajax修改个人中心的链接
   * @param object params
   * @return string
   */
  getAjaxSocialUrl: function(params) {
    return Core.getUrl("member", "data", "social", params) + "&" + new Date().getTime();
  },

  /**
   * @param json 寄存字段名和字段类型
   */
  fields: {
    "login_name"          : ":text",
    "password"            : ":password",
    "repassword"          : ":password",
    "old_pwd"             : ":password",
    "member_mail"         : ":text",
    "remember_me"         : ":checkbox",
    "cipher"              : ":hidden",
    "http_referer"        : ":hidden",
    "realname"            : ":text",
    "sex"                 : ":radio",
    "birth_y"             : "select",
    "birth_m"             : "select",
    "birth_d"             : "select",
    "is_pub_birth"        : ":checkbox",
    "head_portrait"       : ":text",
    "interests"           : ":checkbox",
    "is_pub_interests"    : ":checkbox",
    "qq"                  : ":text",
    "live_province_id"    : "select",
    "live_city_id"        : "select",
    "live_district_id"    : "select",
    "address_province_id" : "select",
    "address_city_id"     : "select",
    "address_district_id" : "select",
    "introduce"           : "textarea",
  },

  /**
   * 在所有的字段名上绑定得到焦点事件
   * @return void
   */
  bindFocus: function() {
    for (var fieldName in Member.fields) {
      var o = $(Member.fields[fieldName] + "[name='" + fieldName + "']");
      o.focus(function() {
        $(this).parent().parent().removeClass("has-error");
        $(this).parent().next().html($(this).parent().next().attr("title"));
      });
    }
  },

  /**
   * 通过字段名获取字段对象
   * @param string n
   * @param string ID
   * @return object
   */
  getObj: function(n, ID) {
    eval("var type = Member.fields." + n + ";");
    if (typeof(ID) == "undefined") { ID = ""; }

    if (ID != "") {
      if (!Trotri.startWith("#", ID)) { ID = "#" + ID; }
      ID += " ";
    }

    return $(ID + type + "[name='" + n + "']");
  },

  /**
   * 会员注册
   * @return void
   */
  ajaxReg: function() {
    var formId = "register";

    var loginName  = Member.getObj("login_name", formId).val();
    var password   = Member.getObj("password",   formId).val();
    var repassword = Member.getObj("repassword", formId).val();

    $.getJSON(Member.getAjaxRegUrl(), {"login_name": loginName, "password": password, "repassword": repassword}, function(ret) {
      if (ret.err_no > 0) {
        for (var fieldName in ret.data.errors) {
          var o = Member.getObj(fieldName, formId);
          o.parent().parent().addClass("has-error");
          o.parent().next().html(ret.data.errors[fieldName]);
        }
      }
      else {
        $("#" + formId).find(".alert").html(ret.err_msg);
        setTimeout(function() {
          location.href = Member.getLoginUrl({"http_referer" : $(":hidden[name='http_referer']").val()});
        }, 1000);
      }
    });
  },

  /**
   * 会员登录
   * @return void
   */
  ajaxLogin: function() {
    var formId = "login";

    var loginName  = Member.getObj("login_name", formId).val();
    var password   = Member.getObj("password",   formId).val();
    var rememberMe = ($("#" + formId + " :checkbox[name='remember_me']:checked").val() == "1") ? 1 : 0;

    $.getJSON(Member.getAjaxLoginUrl(), {"login_name": loginName, "password": password, "remember_me": rememberMe}, function(ret) {
      $("#" + formId).find(".alert").html(ret.err_msg);
      if (ret.err_no > 0) {
        $("#" + formId).find(".alert").css("color", "#a94442");
      }
      else {
        $("#" + formId).find(".alert").css("color", "");
        setTimeout(function() {
          location.href = $(":hidden[name='http_referer']").val();
        }, 1000);
      }
    });
  },

  /**
   * 通过原始密码重设新密码
   * @return void
   */
  ajaxRepwdoldpwd: function() {
    var formId = "repwdoldpwd";

    var oldPwd     = Member.getObj("old_pwd", formId).val();
    var password   = Member.getObj("password", formId).val();
    var repassword = Member.getObj("repassword", formId).val();

    $.getJSON(Member.getAjaxRepwdoldpwdUrl(), {"old_pwd": oldPwd, "password": password, "repassword": repassword}, function(ret) {
      $("#" + formId).find(".alert").html(ret.err_msg);
      if (ret.err_no > 0) {
        $("#" + formId).find(".alert").css("color", "#a94442");
      }
      else {
        $("#" + formId).find(".alert").css("color", "");
        setTimeout(function() {
          location.href = Member.getLogoutUrl();
        }, 1000);
      }
    });
  },

  /**
   * 发送找回密码邮件
   * @return void
   */
  ajaxRepwdsendmail: function() {
    var formId = "repwdsendmail";
    var o = Member.getObj("member_mail", formId);

    o.focus(function() {
      $("#" + formId).find(".alert").html("");
      $("#" + formId).find(".alert").css("color", "");
    });

    var memberMail  = o.val();

    $.getJSON(Member.getAjaxRepwdsendmailUrl(), {"member_mail": memberMail}, function(ret) {
      $("#" + formId).find(".alert").html(ret.err_msg);
      if (ret.err_no > 0) {
        $("#" + formId).find(".alert").css("color", "#a94442");
      }
      else {
        $("#" + formId).find(".alert").css("color", "");
      }
    });
  },

  /**
   * 通过邮箱找回密码
   * @return void
   */
  ajaxRepwdmail: function() {
    var formId = "repwdmail";

    var cipher     = Member.getObj("cipher", formId).val();
    var password   = Member.getObj("password", formId).val();
    var repassword = Member.getObj("repassword", formId).val();

    $.getJSON(Member.getAjaxRepwdmailUrl(), {"cipher": cipher, "password": password, "repassword": repassword}, function(ret) {
      $("#" + formId).find(".alert").html(ret.err_msg);
      if (ret.err_no > 0) {
        $("#" + formId).find(".alert").css("color", "#a94442");
      }
      else {
        $("#" + formId).find(".alert").css("color", "");
        setTimeout(function() {
          location.href = Member.getLogoutUrl();
        }, 1000);
      }
    });
  },

  /**
   * 修改会员详情
   * @return void
   */
  ajaxSocial: function() {
    var formId = "social";

    var realname = Member.getObj("realname", formId).val();
    var sex = $("#" + formId + " :radio[name='sex']:checked").val();
    var birthY = Member.getObj("birth_y", formId).val();
    var birthM = Member.getObj("birth_m", formId).val();
    var birthD = Member.getObj("birth_d", formId).val();
    var isPubBirth = $("#" + formId + " :checkbox[name='is_pub_birth']:checked").val();
    if (isPubBirth != "y") { isPubBirth = "n"; }
    var headPortrait = Member.getObj("head_portrait", formId).val();
    var interests = Trotri.getCheckedValues(interests);
    var isPubInterests = $("#" + formId + " :checkbox[name='is_pub_interests']:checked").val();
    if (isPubInterests != "y") { isPubInterests = "n"; }
    var qq = Member.getObj("qq", formId).val();
    var liveProvinceId = Member.getObj("live_province_id", formId).val();
    var liveCityId = Member.getObj("live_city_id", formId).val();
    var liveDistrictId = Member.getObj("live_district_id", formId).val();
    var addressProvinceId = Member.getObj("address_province_id", formId).val();
    var addressCityId = Member.getObj("address_city_id", formId).val();
    var addressDistrictId = Member.getObj("address_district_id", formId).val();
    var introduce = Member.getObj("introduce", formId).val();

    var birthYmd = birthY + birthM + birthD;

    var data = {
      "realname": realname,
      "sex": sex,
      "birth_ymd": birthYmd,
      "is_pub_birth": isPubBirth,
      "head_portrait": headPortrait,
      "interests": interests,
      "is_pub_interests": isPubInterests,
      "qq": qq,
      "live_province_id": liveProvinceId,
      "live_city_id": liveCityId,
      "live_district_id": liveDistrictId,
      "address_province_id": addressProvinceId,
      "address_city_id": addressCityId,
      "address_district_id": addressDistrictId,
      "introduce": introduce
    };

    $.ajax({
      url: Member.getAjaxSocialUrl(),
      data: data,
      type: "POST",
      dataType: "JSON",
      success: function(ret) {
        $("#" + formId).find(".alert").html(ret.err_msg);
        $("#" + formId + " :button[name='social_button']").parent().next().html(ret.err_msg);
        if (ret.err_no > 0) {
          for (var fieldName in ret.data.errors) {
            var o = Member.getObj(fieldName, formId);
            o.parent().parent().addClass("has-error");
            o.parent().next().html(ret.data.errors[fieldName]);
          }
          $("#" + formId).find(".alert").css("color", "#a94442");
        }
        else {
          $("#" + formId).find(".alert").css("color", "");
        }
      }
    });
  },

  /**
   * 当改变年和月时，改变日期
   * @return void
   */
  loadBirth: function() {
    $("select[name='birth_y']").change(function() {
      $("select[name='birth_m'] option").eq(0).attr("selected", "true");
      $("select[name='birth_d'] option").eq(0).attr("selected", "true");
    });

    $("select[name='birth_m']").change(function() {
      var year = $("select[name='birth_y']").val();
      var month = $(this).val();
      if (year != "" && month != "") {
        year = parseInt(year);
        month = parseInt(month);

        var maxDay = 31;
        if (Trotri.inArray(month, [4, 6, 9, 11]) > -1) {
          maxDay = 30;
        }
        else if (month == 2) {
          maxDay = 28;
          if (Trotri.isLeapYear(year)) maxDay = 29;
        }

        var html = "<option value=''>" + $("select[name='birth_d'] option").eq(0).text() + "</option>";
        for (var day = 1; day <= maxDay; day++) {
          if (day < 10) { day = "0" + day; }
          html += "<option value='" + day + "'>" + day + "</option>";
        }

        $("select[name='birth_d']").html(html);
      }
      else {
        $("select[name='birth_d'] option").eq(0).attr("selected", "true");
      }
    });
  }

}
