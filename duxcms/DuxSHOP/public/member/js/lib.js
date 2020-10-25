/**
 * 初始化系统库
 */
(function (win, doc) {

    var path = packagePath + '/member/js/';

    window.mobile = false;

    /**
     * 核心模块
     */
    Do.add('base', {
        path: path + 'base.js',
        type: 'js',
        requires: ['common']
    });


})(window, document);