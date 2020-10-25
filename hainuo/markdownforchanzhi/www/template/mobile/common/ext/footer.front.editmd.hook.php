<?php
/**
 * Created by PhpStorm.
 * User: fengliu
 * Date: 16/4/3
 * Time: 02:28
 */

if(!in_array($this->moduleName ,array('rss',''))){
    css::import($jsRoot . "editormd/css/editormd.preview.min.css");
    css::import($jsRoot . "editormd/css/editormd.min.css");
    css::import($jsRoot . "editormd/css/editormd.logo.min.css");
    js::import($jsRoot . "editormd/lib/marked.min.js");
    js::import($jsRoot . "editormd/lib/prettify.min.js");
    js::import($jsRoot . "editormd/lib/raphael.min.js");
    js::import($jsRoot . "editormd/lib/underscore.min.js");
    js::import($jsRoot . "editormd/lib/flowchart.min.js");
    js::import($jsRoot . "editormd/lib/jquery.flowchart.min.js");
    js::import($jsRoot . "editormd/editormd.js");
}
?>
<script type="text/javascript">

    $(function() {
        $.each($('.article-content'),function (i,e) {
            $(e).attr('id','editormd-'+i);
            var text=$(e).html();
            text=text.replace(/\&nbsp;/g,' ');
            text=text.replace(/\&gt;/g,'>');
//            console.log(text);
            $(e).html(' ');
            editormd.markdownToHTML("editormd-"+i, {
                markdown: $.trim(text),//+ "\r\n" + $("#append-test").text(),
                htmlDecode: true,       // 开启 HTML 标签解析，为了安全性，默认不开启
                htmlDecode: "style,script,iframe",  // you can filter tags decode
                emoji: true,
                taskList: true,
                tex: true,  // 默认不解析
                flowChart: true,  // 默认不解析
            });
        });
        //处理友情链接相关的pannel
        var links=$('div[data-ve=links]').html();
        $('div[data-ve=links]').html(' ');
        $('div[data-ve=links]').attr('id','editormd-links');
        editormd.markdownToHTML("editormd-links", {
                markdown: $.trim(links),//+ "\r\n" + $("#append-test").text(),
                htmlDecode: true,       // 开启 HTML 标签解析，为了安全性，默认不开启
                htmlDecode: "style,script,iframe",  // you can filter tags decode
                emoji: true,
                taskList: true,
                tex: true,  // 默认不解析
                flowChart: true,  // 默认不解析
            });
        var companyDesc=$('div[data-ve=companyDesc]').html();
        $('div[data-ve=companyDesc]').html(' ');
        $('div[data-ve=companyDesc]').attr('id','editormd-companyDesc');
        editormd.markdownToHTML("editormd-companyDesc", {
                markdown: $.trim(companyDesc),//+ "\r\n" + $("#append-test").text(),
                htmlDecode: true,       // 开启 HTML 标签解析，为了安全性，默认不开启
                htmlDecode: "style,script,iframe",  // you can filter tags decode
                emoji: true,
                taskList: true,
                tex: true,  // 默认不解析
                flowChart: true,  // 默认不解析
            });
 <?php
        if(in_array($this->moduleName,['links','companyDesc'])):
?>
        $.each($('.panel-body'),function (i,e) {
            $(e).attr('id','editormd-panel'+i);
            var text=$(e).html();
            text=text.replace(/\&nbsp;/g,' ');
            text=text.replace(/\&gt;/g,'>');
            console.log(text);
            $(e).html(' ');
            editormd.markdownToHTML("editormd-panel"+i, {
                markdown: $.trim(text),//+ "\r\n" + $("#append-test").text(),
                htmlDecode: true,       // 开启 HTML 标签解析，为了安全性，默认不开启
                htmlDecode: "style,script,iframe",  // you can filter tags decode
                emoji: true,
                taskList: true,
                tex: true,  // 默认不解析
                flowChart: true,  // 默认不解析
            });
        });
 <?php 
        endif;
 ?>
    });
</script>
