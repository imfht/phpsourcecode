$(document).ready(function() {
  if (g_ctrl == "pictures") {
    if (g_act == "upload") {
      Core.batchUploadPreviewImg("batch_upload_picture_file"); 
    }

    if (g_act == "index") {
      System.folderLink();
    }
  }

});

/**
 * System
 * @author songhuan <trotri@yeah.net>
 * @version $Id: system.js 1 2013-10-16 18:38:00Z $
 */
System = {
  textCopy: function(data) {
    if (window.clipboardData) {
      window.clipboardData.clearData();
      window.clipboardData.setData("Text", data);
      alert("已复制到剪贴版！");
    }
    else {
      alert("被浏览器拒绝！请使用IE浏览器！");
    }
  },

  folderLink: function() {
    $(".glyphicon-folder-close").parent().click(function() {
      var link = $(this).find("a").attr("href");
      Trotri.href(link);
    });
  }
}
