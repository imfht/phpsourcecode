<?php if ($extView = $this->getExtViewFile(__FILE__)) {
    include $extView;
    return helper::cd();
} ?>
<?php
/* Get current module and method. */
$module = $this->moduleName;
$method = $this->methodName;

if (!isset($config->$module->editor->$method)) return;

/* Export $jsRoot var. */
js::set('jsRoot', $jsRoot);
js::set('webRoot', $webRoot);

/* Get editor settings for current page. */
$editors = $config->$module->editor->$method;

$editors['id'] = explode(',', $editors['id']);
js::set('editors', $editors);

$this->app->loadLang('file');
js::set('errorUnwritable', $lang->file->errorUnwritable);

/* Get current lang. */
$editorLangs = array('en' => 'en', 'zh-cn' => 'zh_CN', 'zh-tw' => 'zh_TW');
$editorLang = isset($editorLangs[$app->getClientLang()]) ? $editorLangs[$app->getClientLang()] : 'en';
js::set('editorLang', $editorLang);

/* set uid for upload. */
$uid = uniqid('');
js::set('uid', $uid);
/* import editormd css && js */

css::import($jsRoot . "editormd/css/editormd.preview.min.css");
css::import($jsRoot . "editormd/css/editormd.min.css");
css::import($jsRoot . "editormd/css/editormd.logo.min.css");
js::import($jsRoot . "editormd/editormd.js");
?>
<script>
    var Editor={};
    $(document).ready(initKindeditor);
    function initKindeditor() {
        $(':input[type=submit]').after("<input type='hidden' id='uid' name='uid' value=" + v.uid + ">");
        var nextFormControl = 'input:not([type="hidden"]), textarea:not(.ke-edit-textarea), button[type="submit"], select';
        $.each(v.editors.id, function (key, editorID) {
            var editor = $('#' + editorID);
            var value = editor.val();
//        setType('text');
//        $("input[name=type]").val('text');
            var dom = $('#typetext');
            if (dom != undefined) {
                dom.click();
            }
//        editor.parent().parent().show();
//        editor.parent().parent().hide();
            editor.parent().append('<div id="' + editorID + '"></div>');
            editor.remove();

            Editor[key] = editormd(editorID, {
                width: "90%",
                height: 240,
                path: v.jsRoot + 'editormd/lib/',
                theme: "dark",
                previewTheme: "dark",
                editorTheme: "pastel-on-dark",
                markdown: value,
                placeholder:'海诺博客欢迎你,尽情享用markdown盛宴吧!',
                codeFold: false,
                //syncScrolling : false,
                saveHTMLToTextarea: false,    // 保存 HTML 到 Textarea
                searchReplace: true,
                watch: false,                // 关闭实时预览
                htmlDecode: "style,script,iframe|on*",            // 开启 HTML 标签解析，为了安全性，默认不开启
                toolbar: true,             //关闭工具栏
                previewCodeHighlight: true, // 关闭预览 HTML 的代码块高亮，默认开启
                emoji: true,
                taskList: true,
                tocm: true,         // Using [TOCM]
                tex: true,                   // 开启科学公式TeX语言支持，默认关闭
                flowChart: true,             // 开启流程图支持，默认关闭
                sequenceDiagram: true,       // 开启时序/序列图支持，默认关闭,
                //dialogLockScreen : false,   // 设置弹出层对话框不锁屏，全局通用，默认为true
                //dialogShowMask : false,     // 设置弹出层对话框显示透明遮罩层，全局通用，默认为true
                //dialogDraggable : false,    // 设置弹出层对话框不可拖动，全局通用，默认为true
                //dialogMaskOpacity : 0.4,    // 设置透明遮罩层的透明度，全局通用，默认值为0.1
                //dialogMaskBgColor : "#000", // 设置透明遮罩层的背景颜色，全局通用，默认为#fff
                imageUpload: true,
                imageFormats: ["jpg", "jpeg", "gif", "png", "bmp", "webp"],
                imageUploadURL: "<?php echo $this->createLink('file', 'ajaxUploadImage', "objectType=".$module."&objectID=2&isImage=1")?>",
                onload: function () {
//                    console.log('onload', this);
//                console.log('dddd',this.markdownTextarea);
                    this.markdownTextarea.attr('name', this.id);
                    //this.fullscreen();
                    //this.unwatch();
                    //this.watch().fullscreen();

                    //this.setMarkdown("#PHP");
                    //this.width("100%");
                    //this.height(480);
//                this.resize("100%", 240);
//                console.log(editor);

                },
                onresize: function () {
//                this.resize("100%", 240);
//                var editor = this.editor;
//                console.log(editor);
                }
            });

        });
    }

    $("#ajaxForm").submit(function (data) {

    });

</script>
<style>
    td div.form-control.edui-default {
        width: 100%;
        height: auto;
    }
</style>
