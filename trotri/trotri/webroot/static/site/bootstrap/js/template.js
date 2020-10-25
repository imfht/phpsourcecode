/*!
 * IE10 viewport hack for Surface/desktop Windows 8 bug
 * Copyright 2014 Twitter, Inc.
 * Licensed under the Creative Commons Attribution 3.0 Unported License. For
 * details, see http://creativecommons.org/licenses/by/3.0/.
 */

// See the Getting Started docs for more information:
// http://getbootstrap.com/getting-started/#support-ie10-width

(function () {
  'use strict';
  if (navigator.userAgent.match(/IEMobile\/10\.0/)) {
    var msViewportStyle = document.createElement('style')
    msViewportStyle.appendChild(
      document.createTextNode(
        '@-ms-viewport{width:auto!important}'
      )
    )
    document.querySelector('head').appendChild(msViewportStyle)
  }
})();

$(document).ready(function() {
  $('[data-toggle=offcanvas]').click(function() {
    $('.row-offcanvas').toggleClass('active');
  });
});

/**
 * Core
 * @author songhuan <trotri@yeah.net>
 * @version $Id: template.js 1 2013-10-16 18:38:00Z $
 */
Core = {
  /**
   * 获取分页HTML
   * @param string funcName
   * @param integer totalRows
   * @param integer listRows
   * @param integer firstRow
   * @param string prevStr
   * @param string nextStr
   */
  getPaginator: function(funcName, totalRows, listRows, firstRow, prevStr, nextStr) {
    if (typeof(funcName) == "undefined" || typeof(totalRows) == "undefined" || typeof(listRows) == "undefined" || typeof(firstRow) == "undefined") {
      return "";
    }

    if (typeof(prevStr) == "undefined") {
      prevStr = "&lt;&lt;";
    }

    if (typeof(nextStr) == "undefined") {
      nextStr = "&gt;&gt;";
    }

    totalRows = parseInt(totalRows);
    listRows = parseInt(listRows);
    if (totalRows <= 0 || listRows <= 0) {
      return "";
    }

    if ((firstRow = parseInt(firstRow)) < 0) {
      firstRow = 0;
    }

    var totalPages = Math.ceil(totalRows / listRows);
    var currPage = Math.floor(firstRow / listRows) + 1;

    var string  = "<ul class=\"pagination\">";
    if (currPage > 1) {
      string += "<li><a href=\"javascript: " + funcName + "('" + (currPage - 1) + "');\">" + prevStr + "</a></li>";
    }

    if (currPage < totalPages) {
      string += "<li><a href=\"javascript: " + funcName + "('" + (currPage + 1) + "');\">" + nextStr + "</a></li>";
    }

    string += "</ul>";
    return string;
  },

  /**
   * 获取URL
   * @param string act
   * @param string ctrl
   * @param string mod
   * @param object params
   * @return string
   */
  getUrl: function(mod, ctrl, act, params) {
    var url = g_url + "?r=" + mod + "/" + ctrl + "/" + act;
    if (typeof(params) == "object") {
      for (var key in params) {
        url += "&" + key + "=" + params[key];
      }
    }
    return url;
  },

  /**
   * 页面重定向到登录页面
   * @return void
   */
  toLogin: function() {
    var url = Core.getUrl("member", "show", "login");
    Trotri.href(url);
  },

  /**
   * 获取Ajax会员注册链接
   * @param object params
   * @return string
   */
  getAjaxRegionsUrl: function(params) {
    return Core.getUrl("system", "data", "regions", params) + "&pid=";
  },

  /**
   * 投票
   * @param string name
   * @param string type
   * @return string
   */
  vote: function(name, type) {
    var url = Core.getUrl("poll", "data", "vote", {"t" : new Date().getTime()});
    var value = (type == "checkbox") ? Trotri.getCheckedValues(name + "[]") : $(":radio[name='" + name + "']:checked").val();
    $.getJSON(url, {"key": name, "value": value}, function(ret) {
      if (ret.err_no === 3001) {
        Core.toLogin();
      }

      alert(ret.err_msg);
    });
  },

  /**
   * Ajax上传并预览单张图片，基于jquery.uploadfile.js和jquery.form.js开发框架
   * @param string btnId
   * @param string name
   * @param json options
   * @return void
   */
  uploadPreviewImg: function(btnId, name, options) {
    var button = $("#" + btnId);
    var field = button.parent().parent();
    var oIpt = $("input[name='" + name + "']");

    var removeImg = function() {
      $(".ajax-file-upload-statusbar").next(".ajax-file-upload-statusbar").remove();
    };

    var getFlash = function(url) {
      return '<embed width="' + defaults.previewHeight + '" height="' + defaults.previewWidth + '" src=' + url + ' type="application/x-shockwave-flash" wmode="transparent"></embed>';
    };

    var loadImg = function() {
      var url = oIpt.val();
      if (url != "") {
        var string = '<div class="ajax-file-upload-statusbar" style="width: ' + defaults.statusBarWidth + ';">';
        if (Trotri.endWith(".swf", url)) {
          string += getFlash(url);
        }
        else {
          string += '<img class="ajax-file-upload-preview" src="' + url + '" style="display: inline-block; height: ' + defaults.previewHeight + '; width: ' + defaults.previewWidth + ';">';
        }
        string += '</div>';
        button.next().after(string);
      }

      removeImg();
    };

    var setError = function(errMsg) {
      if (errMsg == undefined) { errMsg = ""; }
      var obj = button.parent().parent();
      errMsg ? obj.addClass("has-error") : obj.removeClass("has-error");
      button.parent().next().text(errMsg);
    };

    oIpt.blur(function() {
      if ($(this).val() != "") {
        loadImg();
      }
    });

    var defaults = {
      url: button.attr("url"),
      fileName: button.attr("name"),
      returnType: "JSON",
      allowedTypes: "jpg,gif,png,bmp,swf",
      multiple: false,
      dragDrop: true,
      showDone: false,
      showAbort: false,
      showFileCounter: false,
      showProgress: true,
      showQueueDiv: false,
      showPreview: false,
      uploadButtonClass: "ajax-file-upload-green",
      dragDropStr: "<span><b>&nbsp; Drag &amp; Drop Files</b></span>",
      statusBarWidth: "70%",
      dragdropWidth: "99.8%",
      previewHeight: "200px",
      previewWidth: "200px",
      onLoad: function (obj) {
        loadImg();
      },
      onSubmit: function(files, xhr) {
        removeImg();
        setError();
      },
      onSuccess: function(files, response, xhr, pd) {
        if (response.err_no == 0) {
          var url = response.data.url;
          oIpt.val(url);
          if (Trotri.endWith(".swf", url)) {
            field.find(".ajax-file-upload-preview").after(getFlash(url));
          }
          else {
            field.find(".ajax-file-upload-preview").show("fast", function() { $(this).attr("src", url); }); 
          }
        }
        else {
          $(".ajax-file-upload-bar").text("Upload Failed");
          setError(response.err_msg);
        }
      },
      onError: function(files, status, message, pd) {},
      onCancel: function(files, pd) {}
    };

    $.extend(defaults, options);
    button.uploadFile(defaults);
  },

  /**
   * Ajax加载地区
   * @param string url
   * @param json columns {country : "addr_country_id", province : "addr_province_id", city : "addr_city_id", district : "addr_district_id"}
   * @param json data {"addr_country_id" : "0", "addr_province_id" : "0", "addr_city_id" : "0", "addr_district_id" : "0"}
   * @return void
   */
  regions: function(url, columns, data) {
    var defaults = {
      country  : "country",
      province : "province",
      city     : "city",
      district : "district",
    };

    columns = $.extend(defaults, columns);

    var getObj = function(sName) {
      return $("select[name='" + sName + "']");
    };

    var oCountry  = getObj(columns.country);
    var oProvince = getObj(columns.province);
    var oCity     = getObj(columns.city);
    var oDistrict = getObj(columns.district);

    var hasCountry  = (typeof(oCountry)  == "object" && oCountry.length  > 0) ? true : false;
    var hasProvince = (typeof(oProvince) == "object" && oProvince.length > 0) ? true : false;
    var hasCity     = (typeof(oCity)     == "object" && oCity.length     > 0) ? true : false;
    var hasDistrict = (typeof(oDistrict) == "object" && oCity.length     > 0) ? true : false;

    if (!hasProvince || !hasCity || !hasDistrict) {
      return ;
    }

    var loadRegions = function(E, regionPid, selectedValue, recursive) {
      E.empty();

      if ((regionPid = parseInt(regionPid)) < 0) {
        return ;
      }

      if (!$.isNumeric(regionPid)) {
        return ;
      }

      var sName = E.attr("name");
      if (sName != columns.country && regionPid == 0) {
        if (sName == columns.city) {
          oDistrict.empty();
        }

        if (sName == columns.province) {
          oCity.empty();
          oDistrict.empty();
        }

        return ;
      }

      if (typeof(recursive) == "undefined") {
        recursive = true;
      }

      selectedValue = parseInt(selectedValue);
      $.isNumeric(selectedValue) ? "" : selectedValue = 0;

      $.getJSON(url + regionPid, function(ret) {
        if (ret.err_no == 0) {
          var firstValue = 0;
          for (var regionId in ret.data) { firstValue = regionId; break; }

          var hasSelected = false;
          for (var regionId in ret.data) {
            if (regionId == selectedValue) { hasSelected = true; break; }
          }

          hasSelected ? "" : selectedValue = firstValue;

          var text = "";
          for (var regionId in ret.data) {
            var selected = (regionId == selectedValue) ? " selected" : "";
            text += "<option value='" + regionId + "'" + selected + ">" + ret.data[regionId] + "</option>";
          }

          E.html(text);

          if (recursive) {
            switch (true) {
              case sName == columns.country:
                loadRegions(oProvince, selectedValue, 0, recursive);
                break;
              case sName == columns.province:
                loadRegions(oCity, selectedValue, 0, recursive);
                break;
              case sName == columns.city:
                loadRegions(oDistrict, selectedValue, 0, recursive);
                break;
              default:
                break;
            }
          }
        }
      });
    };

    var objs = hasCountry ? [oCountry, oProvince, oCity, oDistrict] : [oProvince, oCity, oDistrict];
    for (var i in objs) {
      eval("var selectedValue = parseInt(data." + objs[i].attr("name") + ");");
      $.isNumeric(selectedValue) ? "" : selectedValue = 0;
      objs[i].attr("selected_value", selectedValue);
    }

    var oPrev = null;
    for (var i in objs) {
      loadRegions(objs[i], (oPrev ? oPrev.attr("selected_value") : (hasCountry ? 0 : 1)), objs[i].attr("selected_value"), ((objs[i].attr("selected_value") > 0) ? false : true));
      oPrev = objs[i];
    }

    if (hasCountry) {
      oCountry.change(function() {
        loadRegions(oProvince, $(this).val());
      });
    }

    oProvince.change(function() {
      loadRegions(oCity, $(this).val());
    });

    oCity.change(function() {
      loadRegions(oDistrict, $(this).val());
    });
  }

}
