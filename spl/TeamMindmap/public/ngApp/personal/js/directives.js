/**
 * Created by spatra on 14-12-11.
 */


define(['personalJS/module'], function(personalModule){

  /**
   * 实现个人模块导航栏的高亮
   */
  personalModule.directive('personalSecondNav', ['$rootScope', '$state', 'personalModuleBaseUrl',
    function($rootScope, $state, personalModuleBaseUrl){

      return {
        restrict: 'EA',
        templateUrl: personalModuleBaseUrl + 'tpls/second-nav.html',
        scope: {
          navItems: '=navItems'
        },
        link: function(scope){


          scope.navSet = {};
          //提取ur—router状态的前两级作为高亮的识别字段
          function fetchValidateState(sourceUrl){
            var parties = sourceUrl.split('.');

            return [
              parties[0],
              parties[1]
            ].join('.');
          }

          for(var i = 0; i < scope.navItems.length; ++i ){
            var currentItem = scope.navItems[i];
            scope.navSet[ fetchValidateState(currentItem['uiSref']) ] = currentItem;
          }

          //根据ui—router状态实现高亮
          var lastItem = {};
          function highLight(stateName){
            lastItem['active'] = false;
            lastItem = scope.navSet[ fetchValidateState(stateName) ];
            lastItem['active'] = true;
          }

          //首次初始化时实现高亮
          highLight($state.current.name);


          $rootScope.$on('$stateChangeSuccess', function(event, toState){
            var states = toState.name.split('.');

            if( states[0] === 'personal' ){
              //如果不是处于`personal`的子状态则不进行操作
              highLight(toState.name);
            }
          });
        }
      };
    }]);//End of --> personalSecondNav


  personalModule.directive('thirdNav', ['$rootScope', '$state', 'personalModuleBaseUrl',
    function($rootScope, $state, personalModuleBaseUrl){

      return {
        restrict: 'EA',
        templateUrl: personalModuleBaseUrl + 'tpls/third-nav.html',
        scope: {
          navItems: '=',
          baseState: '='
        },
        link: function(scope){

          function highLight(stateName){
            var parties = stateName.split('.');
            var lastPart = parties[ parties.length - 1 ];

            for(var i = 0; i < scope.navItems.length; ++i ){
              if( lastPart === scope.navItems[i]['state'] ){
                scope.navItems[i]['active'] = true;
              }
              else{
                scope.navItems[i]['active'] = false;
              }
            }
          }

          highLight($state.current.name);

          $rootScope.$on('$stateChangeSuccess', function(event, toState){

            if( scope.baseState.indexOf( toState.name ) !== 1 ){
              highLight(toState.name);
            }
          });
        }
      };
  }]);//End of --> thirdNav

});