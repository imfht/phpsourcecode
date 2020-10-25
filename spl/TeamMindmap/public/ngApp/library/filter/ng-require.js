/**
 * Created by spatra on 15-3-4.
 */

define(['angular', 'libraryJS/classHelper/ng-require'], function(angular, classHelperModule){

  var filerModule = angular.module('TeamMindmap.library.filter', [classHelperModule]);
  var baseUrl = 'ngApp/library/filter/';
  /**
   * 此指令用于按条件过滤
   */
  filerModule.directive('filterOnCond', ['ClassHelperService',
    function(ClassHelperService){

      return {
        restrict: 'EA',
        replace: true,
        templateUrl: baseUrl + 'filter-on-cond.html',
        scope:{
          conditionObj: '=',
          conditions: '='
        },
        link: function(scope){
          //为避免选择标记影响原有对象，故此处使用深度赋值后的对象
          scope.conditions = ClassHelperService.clone(scope.conditions);

          var lastItems = {};
          for( var item in scope.conditions ){
            lastItems[item] = scope.conditions[item][0];
            lastItems[item].selected = true;
          }

          scope.setSelected = function(currentCond, condName){
            lastItems[condName].selected = false;
            currentCond.selected = true;
            lastItems[condName] = currentCond;

            scope.conditionObj[condName] = currentCond.cond;
          };
        }
      };
    }]);//End of --> ng-directive: filterOnCond

  /**
   * 当列表过滤完全基于后台实现时，此服务用于获取后台提供的过滤条件对象.
   *
   * 对象的格式：
   * {
   *    "过滤条件1":
   *    [
   *      {"cond": "过滤条件1的某个值", "label": "过滤条件1的某个值的文本显示"},
   *      //注意是个数组...
   *    ],
   *    "过滤条件2":
   *    //........
   * }
   */
  filerModule.factory('BackendFilterService', ['$http', function($http){
    var baseUrl = 'api/backend-filter/';

    return {
      getMethods: function(query){
        return $http.get(baseUrl + query);
      }
    };
  }]);//End of --> BackendFilterService

  return filerModule.name;
});