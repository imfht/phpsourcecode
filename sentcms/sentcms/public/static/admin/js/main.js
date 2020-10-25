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
	return src.substring(0, src.lastIndexOf("/") - 9) + '/';
})();

// 配置参数
require.config({
	waitSeconds: 60,
	packages: [{
		name: 'moment',
		location: 'plugins/moment',
		main: 'moment'
	}
	],
	baseUrl: baseRoot,
	map: {'*': {css: baseRoot + 'plugins/require/require.css.js'}},
	paths: {
		'sent': ['common/js/sent'],
		'form': ['common/js/require-form'],
		'xlsxs': ['common/js/require-xlsx'],
		'upload': 'common/js/require-upload',
		'validator': 'common/js/require-validator',
		'message': ['plugins/messager/messager'],
		'template': ['plugins/art-template/template-native'],
		'webupload': ['plugins/webuploader/webuploader.min'],

		//表单组件
		'board': ['plugins/board/board.min'],
		'droppable': ['plugins/droppable/droppable'],
		'tagsinput': ['plugins/tagsinput/bootstrap-tagsinput'],
		'icheck': ['plugins/iCheck/icheck.min'],
		'select2': ['plugins/select2/select2.full'],
		'iconpicker': ['plugins/bootstrap-iconpicker/dist/js/bootstrap-iconpicker.bundle.min'],
		'dragsort': 'plugins/dragsort/jquery.dragsort',

		// openSource
		'layer': ['plugins/layer/layer'],
		'base64': ['plugins/jquery/base64.min'],
		'cxselect': ['plugins/cxselect/jquery.cxselect.min'],
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
		'bootstrap-editable': 'plugins/bootstrap-editable/js/bootstrap-editable.min',
		'bootstrap-datetimepicker': 'plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min',
		'bootstrap-daterangepicker': 'plugins/bootstrap-daterangepicker/daterangepicker',

		'validator-core': 'plugins/nice-validator/jquery.validator',
		'validator-lang': 'plugins/nice-validator/local/zh-CN',

		'NKeditor': 'plugins/NKeditor/NKeditor-all-min',
		
		//adminlte
		'adminlte': ['plugins/adminlte/js/adminlte.min'], 

		// nanoscroller
		'slimscroll': 'plugins/jquery-slimscroll/jquery.slimscroll',
		'xlsx': ['plugins/sheetjs/xlsx.full.min']
	},
	shim: {
		'message': {deps: ['jquery', 'css!'+'plugins/messager/css/style.css']},
		'board': {deps: ['css!'+'plugins/board/board.min.css']},
		// open-source
		'websocket': {deps: [baseRoot + 'plugins/socket/swfobject.min.js']},
		// jquery
		'jquery.ztree': {deps: ['css!' + baseRoot + 'plugins/ztree/zTreeStyle/zTreeStyle.css']},
		// bootstrap
		'bootstrap':{deps: ['jquery']},
		'bootstrap.typeahead': {deps: ['bootstrap']},
		'bootstrap.multiselect': {deps: ['bootstrap', 'css!' + baseRoot + 'plugins/bootstrap-multiselect/bootstrap-multiselect.css']},
		'bootstrap-editable': {deps:['bootstrap', 'css!'+baseRoot+'plugins/bootstrap-editable/css/bootstrap-editable.css'], exports:'$.fn.editable'},
		'distpicker': {deps: [baseRoot + 'plugins/distpicker/distpicker.data.js']},
		'bootstrap-daterangepicker': ['moment/locale/zh-cn'],
		'bootstrap-datetimepicker': ['moment/locale/zh-cn','css!'+baseRoot+'plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css'],
		'select2': {deps: ['jquery', 'bootstrap', 'css!'+baseRoot+'plugins/select2/select2.css', 'css!'+baseRoot+'plugins/select2/sent_diy.css']},

		'board': {deps:['jquery', 'droppable', 'css!'+baseRoot+'plugins/board/board.min.css'], exports: '$.fn.board'},
		'droppable': {deps:['jquery'], exports: '$.fn.droppable'},
		'tagsinput':{deps: ['jquery', 'bootstrap', 'css!'+baseRoot+'plugins/tagsinput/bootstrap-tagsinput.css'], exports: '$.fn.tagsinput'},
		'icheck': {deps: ['jquery', 'css!'+baseRoot+'plugins/iCheck/all.css']},
		'iconpicker':{deps: ['jquery', 'bootstrap', 'css!'+baseRoot+'plugins/bootstrap-iconpicker/dist/css/bootstrap-iconpicker.min.css'], exports: '$.fn.iconpicker'},

		'validator-lang': ['validator-core'],

		'slimscroll': {deps: ['jquery'],exports: '$.fn.extend'},
		'adminlte': {deps: ['bootstrap', 'slimscroll'],exports: '$.AdminLTE'},

		'form': {deps: ['css!'+baseRoot+'common/css/form.css']},
		'webupload': {deps: ['jquery', 'css!'+baseRoot+'plugins/webuploader/theme/webuploader.css', 'css!'+baseRoot+'/plugins/webuploader/theme/app.css']},

		'layer': {deps: ['jquery', 'css!'+baseRoot+'plugins/layer/theme/default/layer.css']},
		'layui':{exports: "layui"},
		// 'sheetjs':{exports: 'XLSX'}
	},
	deps: ['json'],
	// 开启debug模式，不缓存资源
	urlArgs: "ver=" + requirejs.s.contexts._.config.config.version
});

// 注册jquery到require模块
require(['jquery', 'bootstrap', 'message', 'adminlte'], function ($) {
	//初始配置
	var Config = requirejs.s.contexts._.config.config;
	//将Config渲染到全局
	window.Config = Config;
	// 配置语言包的路径
	var paths = {};    // 避免目录冲突
	paths['backend'] = 'backend/';
	require.config({paths: paths});
	$(function(){
		require(['sent'], function(sent){
			require(['admin/js/backend'], function(backend){
				var controller = (Config.jsname && Config.jsname !== 'upload') ? 'admin/js/module/'+Config.jsname : Config.jsname;
				//加载相应模块
				if (Config.jsname) {
					require([controller], function (Controller) {
						if (Controller.hasOwnProperty(Config.actionname)) {
							Controller[Config.actionname]();
						} else {
							if (Controller.hasOwnProperty("_empty")) {
								Controller._empty();
							}
						}
					}, function (e) {
						console.error(e);
						// 这里可捕获模块加载的错误
					});
				}
			})
		})
	})
});