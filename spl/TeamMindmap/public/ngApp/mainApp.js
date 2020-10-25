/**
 * Created by spatra on 14-12-2.
 */


/**
 * 前端使用AngularJS构建的Web App应用的统一入口
 */
define(['angular', 'projectJS/ng-require', 'personalJS/ng-require', 'libraryJS/globalNotification/ng-require', 'angularUIRouter','angularAnimate','angularToasty'],
  function(angular, projectModule, personalModule, globalNotificationModule){

    var app = angular.module('TeamMindmap', [projectModule, personalModule, globalNotificationModule, 'ui.router','ngAnimate','toasty']);

    app.constant('baseUrl', 'ngApp/');

    app.run(['$rootScope', 'LoginStatusService', 'GlobalNotification', 'UserUnreadService',
      function($rootScope, LoginStatusService, GlobalNotification, UserUnreadService){
        //初始化用户的登陆状态
        LoginStatusService.init();
        //初始化一个更新未读信息的服务
        UserUnreadService.init();

        $rootScope.functionNavItems =  [
          {name: 'project', uiSerf: 'project.list', label: '我的项目'},
          {name: 'personal', uiSerf: 'personal.information.setting', label: '个人主页'}
        ];

        //让标题随着页面改变
        $rootScope.$on('$stateChangeSuccess', function (event, toState) {
          $rootScope.title = toState.title || 'TeamMindmap';
        });

        //在根作用域下初始化全局通知
        GlobalNotification.init($rootScope);

        //去掉ngApp启动前的加载动画
        angular.element('.ngApp-init-loading').remove();
     }]);

    app.config(['$httpProvider', '$stateProvider', '$urlRouterProvider',
      function($httpProvider, $stateProvider, $urlRouterProvider){
        //设置CSRF防护TOKEN
        $httpProvider.defaults.headers.common['X-CSRF-TOKEN'] = $('meta[name=csrf_token]').attr('content');

        //设置超时拦截器
        $httpProvider.interceptors.push('TimeoutInterceptors');

        $urlRouterProvider.otherwise('project/list');

      }]);

    /**
     * HTTP拦截器，用于处理请求超时
     */
    app.factory('TimeoutInterceptors', ['$q', function($q){
      return {
        request: function(config){
          config.timeout = 5000;  //5000毫秒没有响应则认为请求超时
          return config;
        },
        responseError: function(rejection){
          if( rejection.status === 0 && rejection.statusText === '' ){
            rejection['data'] = {
              error: '请求超时，请重试'
            };
          }
          return $q.reject(rejection);
        }
      };
    }]);

    return app;

});