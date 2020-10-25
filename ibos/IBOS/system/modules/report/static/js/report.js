var app = Ibos.app;
define(['director',
    app.assetUrl + '/js/util.js'
], function (Router, util) {
    window.appView = $("#container");
    var assetUrl = app.assetUrl;

    //先设置一个路由信息表，可以由html直出，纯字符串配置
    var routes = {
        'index': assetUrl +'/js/report_default_index.js',
        'add/:id': assetUrl +'/js/report_default_add.js',
        'edit/:id': assetUrl +'/js/report_default_edit.js',
        'send': assetUrl +'/js/report_default_send.js',
        'receive': assetUrl +'/js/report_default_receive.js',
        'send/detail/:id': assetUrl +'/js/report_default_detail.js',
        'receive/detail/:id': assetUrl +'/js/report_default_detail.js',
        'unread/detail/:id': assetUrl +'/js/report_default_detail.js',
        'error': assetUrl +'/js/error.js'
    };
    var initial = true,
        beforeHash = location.hash.slice(1);
    var pageInit = (function(){
        var $aside = $("#aside"),
            $asideLi = $aside.find('li');
        $aside.on('click', 'a',function(){
            var hash = $(this).data('hash');
            if( initial && (beforeHash !== hash) ){
                location.hash = hash;
            }
        });
        // 跳转页面时，如果还存在弹窗的话，需要把弹窗关闭
        var clearArtDialog = function(){
            var dialogList = $.artDialog && $.artDialog.list;
            for(var dialog in dialogList){
                dialogList[dialog].close();
            }
        };

        var switchMenu = function(){
            var hash = location.hash.slice(1);
            var index = -1;
            if( /send/.test(hash) ){
                index = 0;
            }
            if( /receive|unread/.test(hash) ){
                index = 1;
            }
            if( /manager|template/.test(hash) ){
                index = 2;
            }
            $asideLi.removeClass('active');
            if( index >= 0 ){
                $asideLi.eq(index).addClass('active');
            }
        };

        // 清除用户选择器多余框
        var clearUserSelect = function(){
            $("[id^='userselect_']").remove();
        };
        
        return function(done){
            initial = false;
            appView.html('');
            appView.waiting(null);

            clearArtDialog();
            switchMenu();
            clearUserSelect();
            done();
        };
    })();

    var currentController = null,
        rootScope;

    util.queue([
        function(done){
            // 获取权限
            util.fetch('report/api/getauthority').done(function(res){
                if( res.isSuccess ){
                    app.s('rootScope', res.data);
                    rootScope = res.data;
                    done();
                }else{
                    Ui.tip(res.msg, 'danger');
                    location.hash = "error";
                }
            });
        }, function(done){
            if( rootScope.manager ){
                $.extend(routes, {
                    'manager/index': assetUrl +'/js/report_manager_index.js',
                    'template/add': assetUrl +'/js/report_template_add.js',
                    'template/edit/:id': assetUrl +'/js/report_template_add.js',
                    'preview/:id': assetUrl +'/js/report_default_preview.js'
                });
            }
            if( rootScope.set ){
                $.extend(routes, {
                    'manager/index': assetUrl +'/js/report_manager_index.js',
                    'preview/:id': assetUrl +'/js/report_default_preview.js'
                });
            }
            done();
        }
    ], function(){
        //用于把字符串转化为一个函数，而这个也是路由的处理核心
        var routeHandler = function (config) {
            return function () {
                var url = config;
                var params = [].slice.call(arguments);
                var requireController = function(done){
                    require([url], function (controller) {
                        if(currentController && currentController !== controller){
                            currentController.onRouteChange && currentController.onRouteChange();
                        }
                        currentController = controller;
                        controller.apply(null, params.concat(done));
                    });
                };
                util.queue([ pageInit, requireController ], function(){
                    initial = true;
                    beforeHash = location.hash.slice(1);
                    appView.waiting(false);
                });
            };
        };

        for (var key in routes) {
            routes[key] = routeHandler(routes[key]);
        }
        Router(routes).init('send');
    });
});
