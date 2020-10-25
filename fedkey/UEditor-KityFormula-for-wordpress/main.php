<?php
/**
 * Plugin Name: UEditor-KityFormula for wordpress
 * Plugin URI: https://gitee.com/fedkey/UEditor-KityFormula-for-wordpress/
 * Description: 百度开源富文本编辑器,多功能的富文本编辑器,添加了百度数学公式插件kityformula,学生、老师、数学爱好者写博必备。
 * Version: 2.0.3
 * Author: 大山, SamLiu, taoqili, bmqy, fedkey
 * Author URI: http://www.yangshengliang.com
 */
@include_once( dirname( __FILE__ ) . "/ueditor.class.php" );
if ( class_exists( "UEditor" ) ) {
    $ueditor_lang = 'en';
    if( stripos($_SERVER['HTTP_ACCEPT_LANGUAGE'], 'zh-cn') !== false){
        $ueditor_lang = 'zh-cn';
    }
    $ue = new UEditor("postdivrich",array(
        //此处可以配置编辑器的所有配置项，配置方法同editor_config.js        
        "toolbars"=>array(
        	array(
			'source', //源代码
			'anchor', //锚点
			'undo', //撤销
			'redo', //重做
			'bold', //加粗
			'indent', //首行缩进
			'snapscreen', //截图
			'italic', //斜体
			'underline', //下划线
			'strikethrough', //删除线
			'subscript', //下标
			'fontborder', //字符边框
			'superscript', //上标
			'formatmatch', //格式刷
			'blockquote', //引用
			'pasteplain', //纯文本粘贴模式
			'selectall', //全选
			'print', //打印
			'preview', //预览
			'horizontal', //分隔线
			'removeformat', //清除格式
			'time', //时间
			'date', //日期
			'unlink', //取消链接
			'insertrow', //前插入行
			'insertcol', //前插入列
			'mergeright', //右合并单元格
			'mergedown', //下合并单元格
			'deleterow', //删除行
			'deletecol', //删除列
			'splittorows', //拆分成行
			'splittocols', //拆分成列
			'splittocells', //完全拆分单元格
			'deletecaption', //删除表格标题
			'inserttitle', //插入标题
			'mergecells', //合并多个单元格
			'deletetable', //删除表格
			'cleardoc', //清空文档
			'insertparagraphbeforetable', //"表格前插入行"
			'insertcode', //代码语言
			'fontfamily', //字体
			'fontsize', //字号
			'paragraph', //段落格式
			'simpleupload', //单图上传
			'insertimage', //多图上传
			'edittable', //表格属性
			'edittd', //单元格属性
			'link', //超链接
			'emotion', //表情
			'spechars', //特殊字符
			'searchreplace', //查询替换
			'map', //Baidu地图
			'gmap', //Google地图
			'insertvideo', //视频
			'justifyleft', //居左对齐
			'justifyright', //居右对齐
			'justifycenter', //居中对齐
			'justifyjustify', //两端对齐
			'forecolor', //字体颜色
			'backcolor', //背景色
			'insertorderedlist', //有序列表
			'insertunorderedlist', //无序列表
			'fullscreen', //全屏
			'directionalityltr', //从左向右输入
			'directionalityrtl', //从右向左输入
			'rowspacingtop', //段前距
			'rowspacingbottom', //段后距
			'pagebreak', //分页
			'insertframe', //插入Iframe
			'imagenone', //默认
			'imageleft', //左浮动
			'imageright', //右浮动
			'attachment', //附件
			'imagecenter', //居中
			'wordimage', //图片转存
			'lineheight', //行间距
			'edittip ', //编辑提示
			'customstyle', //自定义标题
			'autotypeset', //自动排版
			'webapp', //百度应用
			'touppercase', //字母大写
			'tolowercase', //字母小写
			'background', //背景
			'template', //模板
			'scrawl', //涂鸦
			//'music', //音乐
			'inserttable', //插入表格
			'drafts', // 从草稿箱加载
			'charts', // 图表
			'help', //帮助
			'kityformula')
        )
        ,'lang'=>$ueditor_lang
        ,"focus"=>true
        ,"textarea"=>"content"
        ,"zIndex"=>1
        ,"initialFrameHeight"=>320  //初始化编辑器高度,默认320
        ,"wordCount"=>false          //是否开启字数统计
        ,"autoHeightEnabled"=>false  // 是否自动长高,默认true
        //是否可以拉伸长高,默认true(当开启时，自动长高失效)
        ,"scaleEnabled"=>true
        //浮动时工具栏距离浏览器顶部的高度，用于某些具有固定头部的页面
        ,"topOffset"=>32
        ,"minFrameHeight"=>320  //编辑器拖动时最小高度,默认220
        ,"initialStyle"=>'p{font-size:14px;line-height:1.8;}'//编辑器层级的基数,可以用来改变字体等
        ,"catchRemoteImageEnable"=>false //设置是否抓取远程图片
    ));
    register_activation_hook( __FILE__, array(  &$ue, 'ue_closeDefaultEditor' ) );
    register_deactivation_hook( __FILE__, array(  &$ue, 'ue_openDefaultEditor' ) );
    add_action("wp_head",array(&$ue,'ue_importSyntaxHighlighter'));
    add_action("wp_footer",array(&$ue,'ue_syntaxHighlighter'));
    add_action("admin_head",array(&$ue,'ue_importUEditorResource'));
    add_action('edit_form_advanced', array(&$ue, 'ue_renderUEditor'));
    add_action('edit_page_form', array(&$ue, 'ue_renderUEditor'));
    add_action( 'plugins_unload', array(&$ue, 'ue_openDefaultEditor'));

    add_filter('the_editor', 'enable_ueditor');
}
function enable_ueditor($editor_box){
    if( strpos($editor_box, 'wp-content-editor-container') > 0 ){
        $js=<<<js_enable_ueditor
        <script type="text/javascript">
                var ueditor_container = document.getElementById('postdivrich');
                var editor_content = document.getElementById('content');
                var ueditor_content_container = document.createElement('script');
                var wp_ueditor_content = editor_content.defaultValue;
                ueditor_container.appendChild(ueditor_content_container);
                ueditor_content_container.setAttribute('id', 'postdivrich');
                ueditor_content_container.setAttribute('class', 'postarea');
                ueditor_content_container.setAttribute('type', 'text/plain');
                ueditor_container.removeAttribute('id');
                ueditor_container.removeAttribute('class');
                var mce_container = document.getElementById("wp-content-wrap");
                mce_container.parentNode.removeChild(mce_container);
        </script>
js_enable_ueditor;
        return $editor_box.$js;
    }
    return $editor_box;
}

function UEditorAjaxGetHandler(){
    include_once( dirname( __FILE__ ) . "/ueditor/php/imageManager.php" );
    exit;
}
add_action( 'wp_ajax_ueditor_get', 'UEditorAjaxGetHandler' );

// Should return an array in the style of array( 'ext' => $ext, 'type' => $type, 'proper_filename' => $proper_filename )
function ueditor_mime_types($mime_types ){
    $types = array(
        'apk' => 'application/android binary'
    );
    return array_merge($types, $mime_types);
}
add_filter( 'mime_types', 'ueditor_mime_types' );

function UEditorAjaxPostHandler(){
    switch($_REQUEST['method']){
        case 'imageUp':
            include_once( dirname( __FILE__ ) . "/ueditor/php/imageUp.php" );
            break;
        case 'scrawlUp':
            include_once( dirname( __FILE__ ) . "/ueditor/php/scrawlUp.php" );
            break;
        case 'fileUp':
            include_once( dirname( __FILE__ ) . "/ueditor/php/fileUp.php" );
            break;
        case 'getRemoteImage':
            include_once( dirname( __FILE__ ) . "/ueditor/php/getRemoteImage.php" );
            break;
        case 'wordImage':
            include_once( dirname( __FILE__ ) . "/ueditor/php/wordImage.php" );
            break;
        case 'onekey':
            include_once( dirname( __FILE__ ) . "/ueditor/php/onekeyUp.php" );
            break;
        default:
            break;
    }
    exit;
}
add_action( 'wp_ajax_ueditor_post', 'UEditorAjaxPostHandler' );

?>
