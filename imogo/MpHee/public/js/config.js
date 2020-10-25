var pathName = window.document.location.pathname;
var baseDir = pathName.substring(0,pathName.substr(1).indexOf('/')+1) + '/public/js/';
var publicDir = pathName.substring(0,pathName.substr(1).indexOf('/')+1) + '/public/';
var rootUrl = pathName.substring(0,pathName.substr(1).indexOf('/')+1) + '/';

//核心配置
Do.setConfig('coreLib', [baseDir + 'core/jquery.js'/*,baseDir + 'core/zepto.js'*/]);

//angularjs
Do.add('angular',
{
    path : baseDir + 'core/angular.js'
}
);

//artdialog
Do.add('artdialogCss',
{
    path : baseDir + 'artdialog/css/ui-dialog.css',
    type : 'css'
}
);
Do.add('artdialogJs',
{
    path : baseDir + 'artdialog/dialog-min.js',
    requires : ['artdialogCss']
}
);
Do.add('artdialog',
{
    path : baseDir + 'artdialog/dialog-plus-min.js',
    requires : ['artdialogJs']
}
);

//base_jq & base_js
Do.add('base_jq',
{
    path : baseDir + 'base/base-jq.js'
}
);
Do.add('base',
{
    path : baseDir + 'base/base-js.js',
    requires : ['base_jq']
}
);

//code
Do.add('codeCss',
{
    path : baseDir + 'code/prettify.css',
    type : 'css'
}
);
Do.add('code',
{
    path : baseDir + 'code/prettify.js',
    requires : ['codeCss']
}
);

//chart
Do.add('chart',
{
    path : baseDir + 'js/chart.min.js',
}
);

//color
Do.add('colorCss',
{
    path : baseDir + 'color/style.css',
    type : 'css'
}
);
Do.add('color',
{
    path : baseDir + 'color/soColorPacker.js',
    requires : ['colorCss']
}
);

//laydate
Do.add('laydate',
{
    path : baseDir + 'laydate/laydate.js',
}
);

//layer
Do.add('layer',
{
    path : baseDir + 'layer/layer.min.js',
}
);

//form
Do.add('formCss',
{
    path : baseDir + 'form/style.css',
    type : 'css'
}
);
Do.add('formJs',
{
    path : baseDir + 'form/validform.js'
}
);
Do.add('form',
{
    path : baseDir + 'form/Validform_Datatype.js',
    requires : ['formCss', 'formJs']
}
);

//scrollbar
Do.add('scrollbarJs',
{
    path : baseDir + 'scrollbar/jquery.mousewheel.js'
}
);
Do.add('scrollbar',
{
    path : baseDir + 'scrollbar/jquery.jscrollpane.min.js',
    requires : ['scrollbarJs']
}
);

//tabs
Do.add('tabs',
{
    path : baseDir + 'jquery/jquery.powerSwitch.min.js'
}
);

//tip
Do.add('tipCss',
{
    path : baseDir + 'tip/style.css',
    type : 'css'
}
);
Do.add('tip',
{
    path : baseDir + 'tip/powerFloat.js',
    requires : ['tipCss']
}
);

//touch
Do.add('touch',
{
    path : baseDir + 'js/touch.js'
}
);

//sort
Do.add('sort',
{
    path : baseDir + 'jquery/jquery.sortable.js'
}
);

//prettify
Do.add('prettifyCss',
{
    path : baseDir + 'code/prettify.css',
    type : 'css'
}
);
Do.add('prettify',
{
    path : baseDir + 'code/prettify.js',
    requires : ['prettifyCss']
}
);

//Pupload
Do.add('upload',
{
    path : baseDir + 'upload/upload.full.min.js'
}
);

//KindEditor
Do.add('kindeditor',
{
    path : baseDir + 'kindeditor/kindeditor-min.js',
}
);

/*CSS框架---Start*/
//BootStrap
Do.add('bootstrapCss',
{
    path : publicDir + 'css/bootstrap/css/bootstrap.css',
    type : 'css'
}
);
Do.add('bootstrap',
{
    path : publicDir + 'css/bootstrap/js/bootstrap.js',
    requires : ['bootstrapCss']
}
);

//AmazeUI
Do.add('AmazeUICss',
{
    path : publicDir + 'css/amazeui/css/amazeui.min.css',
    type : 'css'
}
);
Do.add('amazeui',
{
    path : publicDir + 'css/amazeui/js/amazeui.min.js',
    requires : ['AmazeUICss']
}
);
Do.add('amazeadminui',
{
    path : publicDir + 'css/amazeui/css/admin.css',
    requires : ['amazeui']
}
);

//underscore
Do.add('underscore',
{
    path : baseDir + 'js/underscore.min.js'
}
);

//调试函数
function debug(obj)
{
    if (typeof console != 'undefined')
    {
        console.log(obj);
    }
}
