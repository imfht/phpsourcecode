/**
 * Created by spatra on 15-3-4.
 */


define(['angular', 'libraryJS/userManager/ng-require'], function(angular, userManagerModule){

  var navBarModule = angular.module('TeamMindmap.library.navigationBar', [userManagerModule]);
  var baseUrl = 'ngApp/library/navigationBar/';

  /**
   * 实现当前站点的主导航栏
   */
  navBarModule.directive('mainTopNav', ['$rootScope', 'LoginStatusService', function($rootScope, LoginStatusService){

    return {
      restrict: 'EA',
      templateUrl: baseUrl + 'mainTopNav.html',
      scope: {
        functionNavItems: '=functionNavItems'
      },
      link: function(scope){
        /*
         对传递进来的栏目进行预处理
         */
        var functionNavItemsSet = {}, lastItem = {};
        for(var i = 0 ; i < scope.functionNavItems.length; ++i ){
          functionNavItemsSet[ scope.functionNavItems[i]['name'] ] = scope.functionNavItems[i];
        }

        scope.personalInfo = LoginStatusService.get('personalInfo');  //获取用户数据
        scope.logoutUri = LoginStatusService.get('uri.logout'); //获取退出的路径
        scope.unread = LoginStatusService.get('unread');  //未读的私信或通知的统计信息

        /*
         高亮当前栏目
         */
        function highLightItem(state){
          lastItem['active'] = false;

          var currItem = state.name.split('.')[0];

          if( currItem ){
            functionNavItemsSet[ currItem ].active = true;
            lastItem = functionNavItemsSet[ currItem ];
          }
        }

        /*
         监听路由事件，自动高亮当前栏目
         */
        $rootScope.$on('$stateChangeSuccess', function(evt, toState, toParams){
          highLightItem(toState);
        });
      }
    };
  }]);//End of --> ng-directive: mainTopNav

  /**
   * 实现依据阅读状态切换
   */
  navBarModule.directive('readStatusSwitch',function(){
    return {
      restrict: 'EA',
      replace: true,
      template: '<div)">' +
      '<span class="unread" ng-class="{\'active\': ! readCondition}" ng-click="toggle()">{{offLabel}}</span>' +
      '<span class="read" ng-class="{\'active\': readCondition}" ng-click="toggle()">{{onLabel}}</span>' +
      '</div>',
      scope: {
        onLabel: '@',
        offLabel: '@',
        readCondition: '='
      },
      link: function(scope){
        scope.toggle = function () {
          scope.readCondition = ! scope.readCondition;
        };


      }
    };
  });//End of --> ng-directive: readStatusSwitch

  /**
   *  指令，模块使用的三级导航栏
   */
  navBarModule.directive('moduleThirdNav', ['$rootScope', '$state',
    function($rootScope, $state){

      return {
        restrict: 'EA',
        templateUrl: baseUrl + 'module-third-nav.html',
        scope: {
          navItems: '=',
          parentStateName: '@'
        },
        link: function(scope){
          scope.navItemSet = {};
          //讲导航项目列表转化成k-v对的集合
          scope.navItems.forEach(function(currNavItem){
            scope.navItemSet[ currNavItem['uiSref'] ] = currNavItem;
          });

          var lastItem = {};
          function highLight(stateName){
            lastItem['active'] = false;

            if( scope.navItemSet[ stateName ] ){
              lastItem = scope.navItemSet[ stateName ];
              lastItem['active'] = true;
            }
          }

          highLight($state.current.name);

          $rootScope.$on('$stateChangeSuccess', function(event, toState){
            if( toState.name.indexOf(scope.parentStateName) !== -1 ){
              highLight(toState.name);
            }
          });
        }
      };
    }]);//End of --> ng-directive: moduleThirdNav

  return navBarModule.name;
});