/**
 * Created by spatra on 14-12-2.
 */

define(['angular', 'angularUIRouter', 'angularCookies' ], function(angular){
  var commonModule = angular.module('TeamMindmap.common', ['ui.router', 'ngCookies']);

  commonModule.constant('commonModuleBaseUrl', 'ngApp/common/');

  return commonModule;
});