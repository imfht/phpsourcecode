<?php if(!defined("RUN_MODE")) die();?>
<?php
/**
 * The aboutus view file of company for mobile template of chanzhiEPS.
 * The view should be used as ajax content
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPLV12 (http://zpl.pub/page/zplv12.html)
 * @author      Hao Sun <sunhao@cnezsoft.com>
 * @package     company
 * @version     $Id$
 * @link        http://www.chanzhi.org
 */
?>
<?php include TPL_ROOT . 'common/header.html.php';?>
<div class='block-region region-top no-padding blocks' data-region='company_index-top'><?php $this->block->printRegion($layouts, 'company_index', 'top');?></div>
<div class='article-content' id='company'>
  <?php echo $company->content;?>
</div>
<div class='block-region region-bottom no-padding blocks' data-region='company_index-bottom'><?php $this->block->printRegion($layouts, 'company_index', 'bottom');?></div>
<?php
    css::import($jsRoot . "editormd/css/editormd.preview.min.css");
    css::import($jsRoot . "editormd/css/editormd.min.css");
    css::import($jsRoot . "editormd/css/editormd.logo.min.css");
    js::import($jsRoot . "editormd/lib/marked.min.js");
    js::import($jsRoot . "editormd/lib/prettify.min.js");
    js::import($jsRoot . "editormd/lib/raphael.min.js");
    js::import($jsRoot . "editormd/lib/underscore.min.js");
    js::import($jsRoot . "editormd/lib/sequence-diagram.min.js");
    js::import($jsRoot . "editormd/lib/flowchart.min.js");
    js::import($jsRoot . "editormd/lib/jquery.flowchart.min.js");
    js::import($jsRoot . "editormd/editormd.js");

?>
<script type="text/javascript">
$(function(){
  $.each($('.article-content'),function (i,e) {
    $(e).attr('id','editormd-'+i);
    var text=$(e).html();
    text=text.replace(/\&nbsp;/g,' ');
    text=text.replace(/\&gt;/g,'>');
     // console.log(text);
    $(e).html(' ');
    editormd.markdownToHTML("editormd-"+i, {
        markdown: $.trim(text),//+ "\r\n" + $("#append-test").text(),
        htmlDecode: true,       // 开启 HTML 标签解析，为了安全性，默认不开启
        htmlDecode: "style,script,iframe",  // you can filter tags decode
        emoji: true,
        taskList: true,
    });
  });
});
</script>
