// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

// 当前资源URL目录
var baseRoot = (function () {
    var scripts = document.scripts, src = scripts[0].src;
	return src.substring(0, src.lastIndexOf("/") - 9);
})();

// 配置参数
require.config({
    waitSeconds: 60,
    baseUrl: baseRoot,
    map: {'*': {css: baseRoot + 'plugins/require/require.css.js'}},
    paths: {
        'template': ['plugins/template/template'],
        'pcasunzips': ['plugins/jquery/pcasunzips'],
        // openSource
        'layui': ['plugins/layui/layui'],
        'base64': ['plugins/jquery/base64.min'],
        'angular': ['plugins/angular/angular.min'],
        'ckeditor': ['plugins/ckeditor/ckeditor'],
        'websocket': ['plugins/socket/websocket'],
        // jQuery
        'jquery': ['plugins/jquery/jquery.min'],
        'json': ['plugins/jquery/json2.min'],
        'jquery.ztree': ['plugins/ztree/jquery.ztree.all.min'],
        'jquery.masonry': ['plugins/jquery/masonry.min'],
        'jquery.cookies': ['plugins/jquery/jquery.cookie'],
        // bootstrap
        'bootstrap': ['plugins/bootstrap/js/bootstrap.min'],
        'bootstrap.typeahead': ['plugins/bootstrap/js/bootstrap3-typeahead.min'],
        'bootstrap.multiselect': ['plugins/bootstrap-multiselect/bootstrap-multiselect'],
        // distpicker
        'distpicker': ['plugins/distpicker/distpicker'],

        // nanoscroller
		'nanoscroller': ['plugins/nanoscroller/jquery.nanoscroller.min'],
		'sheetjs': ['plugins/sheetjs/xlsx.full.min']
    },
    shim: {
        // open-source
        'websocket': {deps: [baseRoot + 'plugins/socket/swfobject.min.js']},
        // jquery
        'jquery.ztree': {deps: ['css!' + baseRoot + 'plugins/ztree/zTreeStyle/zTreeStyle.css']},
		// bootstrap
		'bootstrap':{deps: ['jquery', 'css!' + baseRoot + 'plugins/bootstrap/css/bootsrap.min.css']},
        'bootstrap.typeahead': {deps: ['bootstrap']},
        'bootstrap.multiselect': {deps: ['bootstrap', 'css!' + baseRoot + 'plugins/bootstrap-multiselect/bootstrap-multiselect.css']},
        'distpicker': {deps: [baseRoot + 'plugins/distpicker/distpicker.data.js']},
        'nanoscroller': {deps: ['css!' + baseRoot + 'plugins/nanoscroller/nanoscroller.css']}
    },
    deps: ['json'],
    // 开启debug模式，不缓存资源
    urlArgs: "ver=" + (new Date()).getTime()
});

// 注册jquery到require模块
define(['jquery'], function ($) {
	
});