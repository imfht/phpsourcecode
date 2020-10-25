/**
 * Created by spatra on 15-3-18.
 */

define(['angular', 'libraryJS/userManager/ng-require', 'angularUIRouter'],
  function(angular, userManagerModule){

    var currentModule = angular.module('TeamMindmap.library.authorization', [userManagerModule, 'ui.router']);

    currentModule.factory('ProjectTaskAuthorizationService', ['$stateParams', 'LoginStatusService',
      function($stateParams, LoginStatusService){

        var projectInfo = null;

        return {
          setProjectInfo: function(info){
            projectInfo = info;
          },
          getProjectInfo: function(){
            return projectInfo;
          },
          buildChecker: function(){
            if( projectInfo === null ) throw '没有设定任务所在的项目信息';

            var currentUserId = LoginStatusService.get('personalInfo.id');

            if( projectInfo['editable'] === true ){
              return function(){ return true; };
            }
            else{
              return function(taskInfo){
                return taskInfo['creater_id'] == currentUserId;
              }
            }
          }
        };
    }]);

    return currentModule.name;
});