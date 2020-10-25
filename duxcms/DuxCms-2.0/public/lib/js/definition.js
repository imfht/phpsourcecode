//base载入
Do.add('baseJs', {
    path: duxConfig.baseDir + 'base/base.js',
});

Do.add('base', {
    path: duxConfig.libDir + 'base/lib.js',
    requires: ['baseJs']
});

//图表
Do.add('chartJs', {
    path: duxConfig.libDir + 'chart/Chart.min.js',
});
Do.add('chart', {
    path: duxConfig.libDir + 'chart/jquery.easypiechart.min.js'
});
//editor
Do.add('editorCss',{
    path : duxConfig.libDir + 'keditor/themes/default/default.css',
    type : 'css'
});
Do.add('editorSrc', {
    path: duxConfig.libDir + 'keditor/kindeditor-all-min.js',
    requires : ['editorCss']
});
Do.add('editor', {
    path: duxConfig.libDir + 'keditor/lang/zh-CN.js',
    requires: ['editorSrc']
});