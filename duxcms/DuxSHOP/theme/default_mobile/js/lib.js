/**
 * 初始化系统库
 */
(function (win, doc) {


    window.mobile = true;

    var path = rootUrl + '/theme/default_mobile/js/';

    /**
     * 核心模块
     */
    Do.add('base', {
        path: path + 'base.js',
        type: 'js',
        requires: ['common']
    });


})(window, document);