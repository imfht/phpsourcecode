/**
 * Created by spatra on 15-3-4.
 */

/**
 * 此模块用于提供用户相关的服务，如登入登出、用户信息查询、检查用户是否存在
 */
define(['angular', 'libraryJS/resourceAccessor/ng-require', 'angularCookies'],
  function(angular, libResourceAccessorModule){

    var module = angular.module('TeamMindmap.library.userManager', [libResourceAccessorModule, 'ngCookies']);

    /**
     * 用户相关服务，包含下列方法：
     *
     * exist: 传入用户的用户名或电子邮件地址，即可查询该用户是否存在, 取得用户的基本信息
     */
    module.factory('UserService', ['$http', function($http){
      var baseUrl = 'api/user/';

      return {
        exist: function(queryKey){
          return $http.get(baseUrl + 'exist/' + queryKey);
        },
        info: function(userId){
          return $http.get(baseUrl + 'info/' + userId);
        }
      };

    }]);//End of --> ng-factory: UserService

    /**
     * 用户管理用户登陆状态的服务，可以用于获取用户信息.
     *
     * 使用示范，获取用户的电子邮件地址地址：
     * var email LoginStatusService.get('userInfo.email');
     *
     * 所对应的JSON为： {userInfo: {email: 'xx@qq.com'}}
     *
     * 具体的数据后台通过cookie设定，前端获取后删除该cookie
     */
    module.factory('LoginStatusService', ['$rootScope', '$cookieStore', '$window', '$http', 'ClassHelperService',
      function($rootScope, $cookieStore, $window, $http, ClassHelperService){

      var loginData = null;

      return {
        init: function(key){
          key = key || 'loginData';
          loginData = $cookieStore.get(key);
          $cookieStore.remove(key);

          if( ! loginData ){
            throw '无法获取用户信息';
          }

          $rootScope.$broadcast("loginStatus:init");

          $rootScope.$on('personalInfo:update', this.updatePersonalInfo);
        },
        get: function(item, separator){
          if( !this.isLogined() ){
            throw '请先登陆！';
          }

          separator = separator || '.';

          var arr = item.split(separator), rtn = loginData;
          for(var i = 0, length = arr.length; i < length; ++i ){
            rtn = rtn[ arr[i] ];
          }

          return rtn;
        },
        logout: function(){
          $rootScope.$broadcast("loginStatus:logout");
          $window.location = this.get('uri.logout');
          loginData = null;
        },
        //更新个人信息
        updatePersonalInfo: function(){
          $http.get('api/personal/info')
            .success(function(data){ ClassHelperService.update(data, loginData['personalInfo']) })
            .error(function(data){ console.error(data); });
        },
        isLogined: function(){
          return loginData !== null;
        }
      };

    }]);//End of --> ng-factory: LoginStatusService


    /**
     * 管理用户的未读信息
     */
    module.factory('UserUnreadService', ['$rootScope', '$http', '$state', 'LoginStatusService', 'ClassHelperService',
      function($rootScope, $http, $state, LoginStatusService, ClassHelperService){

      function updateUnreadStatistics(){
        $http.get('ng/unread')
          .success(function(data){
            var unread = LoginStatusService.get('unread');

            if( ! ClassHelperService.objectEquals(unread, data) ){

              if( unread.notification < data.notification || unread.message < data.message ){
                $rootScope.$emit('message:info', {
                  title: '阅读通知',
                  msg: '有新的通知或私信，点此查阅',
                  onClick: function(){
                    $state.go('personal.notification');
                  }
                });
              }

              unread.notification = data.notification;
              unread.message = data.message;
            }

          })
          .error(function(data){
            $rootScope.$emit('message:error', {
              title: '请检查网络',
              msg: '更新未读私信或通知失败'
            });
            console.error(data);
          });
      }

      return {
        /**
         * 当用户登陆信息初始化后，注册相应的事件监听
         */
        init: function(){
          $rootScope.$on('unread:update', updateUnreadStatistics);
          $rootScope.$on('task:reload', updateUnreadStatistics);
        },
        update: updateUnreadStatistics
      };
    }]);//End of --> ng-factory: UserUnreadService


    return module.name;
});