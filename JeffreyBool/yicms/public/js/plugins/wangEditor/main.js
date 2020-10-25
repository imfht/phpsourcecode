define(function(require, exports, module){
    var $ = require('jquery');
    require('wangEditor')($);

    $(function(){
        // 获取元素
		    var textarea = document.getElementById('content');
		    // 生成编辑器
		    var editor = new wangEditor(textarea);
		    editor.config.menus = [];
		    editor.create();
    });
});