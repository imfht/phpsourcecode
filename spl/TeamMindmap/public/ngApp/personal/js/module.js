/**
 * Created by spatra on 14-12-2.
 */

define([
    'angular',
    'personalNotificationJS/ng-require', 'personalMessageJS/ng-require',
    'libraryJS/classHelper/ng-require', 'libraryJS/navigationBar/ng-require',
     'libraryJS/resourceAccessor/ng-require', 'libraryJS/userManager/ng-require',
    'libraryJS/globalNotification/ng-require',
    'angularUIRouter', 'localResize'],
  function(angular, personalNotificationModule, personalMessageModule,
           classHelperModule, navigationBarModule, resourceAccessorModule, userManagerModule, globalNotificationModule){

    var personalModule = angular.module('TeamMindmap.personal', [
      personalNotificationModule, personalMessageModule,
      classHelperModule, navigationBarModule, resourceAccessorModule, userManagerModule, globalNotificationModule,
      'ui.router','localResizeIMG'
    ]);

    personalModule.constant('personalModuleBaseUrl', 'ngApp/personal/');

    personalModule.config(['$stateProvider', 'personalModuleBaseUrl', 'GlobalNotificationProvider',
      function($stateProvider, baseUrl, GlobalNotificationProvider){

        $stateProvider
          .state('personal', {
            url: '/personal',
            views: {
              '': {
                templateUrl: baseUrl + 'tpls/personal-layout.html'
              },
              'notification@personal': {
                templateUrl: GlobalNotificationProvider.getTemplateUri()
              },
              'main@personal': {
                templateUrl: baseUrl + 'tpls/personal-template.html'
              },
              'second-nav@personal': {
                template: '<personal-second-nav nav-items="navItems">{{ navItems }}</personal-second-nav>',
                controller: ['$scope', function($scope){
                  //用户初始化个人模块的左侧二级导航栏
                  $scope.navItems = [
                    {label: '资料', uiSref:'personal.information.setting'},
                    {label: '通知', uiSref:'personal.notification'},
                    {label: '私信', uiSref:'personal.message.list'}
                  ];
                }]
              }
            },
            abstract: true
          })
          .state('personal.information',{
            url: '/information',
            views: {
              'third-nav@personal': {
                template: '<third-nav base-state="baseState" nav-items="navItems"></third-nav>',
                controller: ['$scope', function($scope){
                  //初始化三级导航栏
                  $scope.baseState = 'personal.information';
                  $scope.navItems = [
                    {label: '个人设置', state: 'setting'},
                    {label: '修改密码', state: 'password'}
                  ];
                }]
              }
            }
          })
          .state('personal.information.setting',{
            url: '/setting',
            title: '个人设置',
            views: {
              'third-title@personal': {
                template: '<div class="third-title"><h3>用户设置</h3><hr/></div>'
              },
              'main-content@personal': {
                templateUrl: baseUrl + 'tpls/info-setting.html',
                controller: 'PersonalSettingController'
              }
            },
            resolve:{
              'userInfo': ['LoginStatusService', function(LoginStatusService){
                return LoginStatusService.get('personalInfo');
              }]
            }
          })
          .state('personal.information.password',{
            url: '/password',
            title: '修改密码',
            views: {
              'third-title@personal': {
                template: '<div class="third-title"><h3>密码修改</h3><hr/></div>'
              },
              'main-content@personal': {
                templateUrl: baseUrl + 'tpls/password-setting.html',
                controller: 'PasswordEditController'
              }
            }
          })
        ;
    }]);

    return personalModule;
});