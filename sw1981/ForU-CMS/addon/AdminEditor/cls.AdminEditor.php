<?php
include_once LIB_PATH . 'cls.addon.php';

class AdminEditor extends Addon{

  public function kindeditor() {
    echo '<link rel="stylesheet" href="../editor/kindeditor/themes/default/default.css">';
    echo '<script charset="utf-8" type="text/javascript" src="../editor/kindeditor/kindeditor.js"></script><script src="../editor/kindeditor/lang/zh-CN.js"></script>';
    echo '<script type="text/javascript">KindEditor.ready(function(K) { K.create(".editor",{allowFileManager:true, width:"100%", height:"300px"}); });</script>';

    $arr = cms('kindeditor');
    if (!empty($arr)) {
      echo '<script type="text/javascript">KindEditor.ready(function(K) { var editor = K.editor({allowFileManager : true});';
      foreach ($arr as $value) {
        list($type, $id) = $value;
        $ipt = str_replace("_upload", "", $id);
        switch ($type) {
          case 'image':
            echo 'K("' . $id . '").click(function() {editor.loadPlugin("image", function() {editor.plugin.imageDialog({imageUrl : K("' . $ipt . '").val(), clickFn : function(url, title, width, height, border, align) {K("' . $ipt . '").val(url); editor.hideDialog(); } }); }); });';
            break;

          case 'multiimage':
            echo 'K("' . $id . '").click(function() {editor.loadPlugin("multiimage", function() {editor.plugin.multiImageDialog({clickFn : function(urlList) {var tem_val = ""; var tem_s = ""; K.each(urlList, function(i, data) {tem_val = tem_val + tem_s + data.url; tem_s = "|"; }); K("' . $ipt . '").val(tem_val); editor.hideDialog(); } }); }); });';
            break;

          case 'insertfile':
            echo 'K("' . $id . '").click(function() {editor.loadPlugin("insertfile", function() {editor.plugin.fileDialog({fileUrl : K("' . $ipt . '").val(), clickFn : function(url, title) {K("' . $ipt . '").val(url); editor.hideDialog(); } }); }); });';
            break;

          case 'media':
            echo 'K("' . $id . '").click(function() {editor.loadPlugin("media", function() {editor.plugin.media.edit({ }); }); });';
            break;

          case 'flv':
            echo 'K("' . $id . '").click(function() {editor.loadPlugin("flv", function() {editor.plugin.flv.edit({  }); }); });';
            break;

          default:
            break;
        }
      }
      echo ' });</script>';
    }
  }

}
