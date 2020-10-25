/**
 * 初始化系统库
 */
(function (win, doc) {

    var path = packagePath + '/member/js/';

    window.mobile = true;

    /**
     * 核心模块
     */
    Do.add('base', {
        path: path + 'base.js',
        type: 'js',
        requires: ['common']
    });


    Do.add('base_mobile', {
        path: path + 'base_mobile.js',
        type: 'js'
    });





})(window, document);