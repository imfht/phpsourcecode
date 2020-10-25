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
    js::import($jsRoot . "editormd/lib/sequence-diagram.min.js");
    js::import($jsRoot . "editormd/lib/flowchart.min.js");
    js::import($jsRoot . "editormd/lib/jquery.flowchart.min.js");
    js::import($jsRoot . "editormd/editormd.js");
}


//if($this->moduleName=='article'):
?>
    <script type="text/javascript">


        $(function() {
            $.each($('section.article-content'),function (i,e) {
                $(e).attr('id','editormd-'+i);
                var text=$(e).html();
                text=text.replace(/\&nbsp;/g,' ');
                text=text.replace(/\&gt;/g,'>');

//                console.log(text);
                $(e).html(' ');
                editormd.markdownToHTML("editormd-"+i, {
                    markdown: $.trim(text),//+ "\r\n" + $("#append-test").text(),
                    htmlDecode: true,       // 开启 HTML 标签解析，为了安全性，默认不开启
                    htmlDecode: "style,script,iframe",  // you can filter tags decode
                    emoji: true,
                    taskList: true,
                    tex: true,  // 默认不解析
                    flowChart: true,  // 默认不解析
                    sequenceDiagram: true,  // 默认不解析
                });
            })

        });
    </script>
<?php
//    endif;
?>