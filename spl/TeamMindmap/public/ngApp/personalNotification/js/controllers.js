/**
 * Created by spatra on 14-12-12.
 */

define(['personalNotificationJS/module'], function(notificationModule){

  /**
   * 用于管理个人模块，个人通知的控制器
   */
  notificationModule.controller('NotificationController', ['$scope', '$rootScope', '$state', 'projectList', 'notificationList', 'ClassHelperService', 'NotificationService', 'ScrollService', 'PaginationService', 'typeLabelSet',
    function($scope, $rootScope, $state, projectList, notificationList, ClassHelperService, NotificationService, ScrollService, PaginationService, typeLabelSet){
      $scope.typeLabelSet = typeLabelSet;

      //初始化所使用到的变量
      $scope.projectList = projectList;
      $scope.notificationList = notificationList.data;

      //页面状态
      $scope.showCondition = {
        project_id: null,
        read: 0
      };

      ScrollService.init('body', 'notificationLoadMore');

      var resourceGetMethod = PaginationService.convertMethodToNormalFun(NotificationService.accessor, 'get');
      $scope.pagination = PaginationService.createPagination('scroll', {
        currentPage: 1,
        itemsPerPage: notificationList.per_page,
        totalItems: notificationList.total,
        resourceList: $scope.notificationList,
        resourceGetMethod:  resourceGetMethod,
        getResourceOps: $scope.showCondition,
        eventName: 'scroll:notificationLoadMore'
      });

      $scope.pagination.init();

      $scope.readCondition = false;


      $scope.goToState = function(notification) {
        var systemId = 1;
        if(notification.type_id !== systemId) {

          var curStateObj = NotificationService.getStateObj(notification, $scope.typeLabelSet);
          $state.go(curStateObj.stateName, curStateObj.stateParams);
        }
      };


      //select的重新加载
      $scope.reloadByProject = function(){
        $scope.pagination.getResource({
          'resetCurrentPage': true
        });
      };

      //read的脏值查询,如果有改变则重新加载
      $scope.$watch('readCondition', function(newVal, oldVal){
        if(newVal !== oldVal) {
          //1为已读,0为未读
          $scope.showCondition.read = ($scope.readCondition === true ? 1 : 0);

          $scope.notificationList.length = 0;

          $scope.pagination.getResource({
            'resetCurrentPage': true
          })
        }

      });

      //设为已读
      $scope.setRead = function(notification){
        NotificationService.setRead(notification.id, true)
          .success(function(){
            $scope.pagination.getResource();
            $scope.$emit('unread:update');
          })
          .error(function(data){
            console.error(data);
          });
      };

  }]);//End of --> NotificationController
});