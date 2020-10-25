/**
 * Created by spatra on 14-12-4.
 */

define(['projectJS/module'], function(projectModule){

  /**
   * 此控制器用于操作新建任务的模态框
   */
  projectModule.controller('CreateTaskModalController',
    ['$scope', '$modalInstance', '$stateParams', 'NgUIDatePickerService', 'projectMemberList', 'priorityList','parentTaskInfo', 'parentMemberList', 'currentProjectInfo',
      function($scope, $modalInstance, $stateParams, NgUIDatePickerService, projectMemberList, priorityList, parentTaskInfo, parentMemberList, currentProjectInfo){

        /*
        初始化模态框中使用到的变量
         */
        $scope.taskData = {
          priority_id: 1
        };   //任务数据
        $scope.chosen = [];
        $scope.datePicker = NgUIDatePickerService.getDefault();   //时间选择器设定
        $scope.appointed_member = [];       //被添加的成员
        $scope.initProjectMembers = false;  //加载状态
        $scope.priorityList = priorityList; //加载任务优先级信息
        $scope.projectMembers = projectMemberList;  //加载当前项目的成员和创建者
        $scope.parentTaskInfo = parentTaskInfo; //如果存在父级任务，则此初存放信息，否则为null
        $scope.candidatedHandlers = (parentMemberList === null ) ? projectMemberList : parentMemberList;
        $scope.handler = null;

        var appointMemberHashSet = {};
        $scope.$watch('handler', function(newValue, oldValue){
          var newerId = newValue !== null ? newValue['id'] : -1;

          if(newValue !== oldValue && appointMemberHashSet[newerId] === undefined){
            $scope.appointed_member.push(newValue);
            appointMemberHashSet[newerId] = true;
          }
        });

        /**
         * 点击`取消`按钮时执行
         */
        $scope.cancel = function(){
          $modalInstance.dismiss('cancel');
        };

        /**
         * 点击`确定`按钮时执行
         */
        $scope.ok = function(){
          $scope.taskData.expected_at = $scope.datePicker.getDate();

          //转换数据，以符合前后端协定
          $scope.taskData.appointed_member = { 'add': [] };
          for(var member in $scope.appointed_member ){
            console.log($scope.appointed_member);
            $scope.taskData.appointed_member['add'].push( $scope.appointed_member[member]['id'] );
          }


          //console.log($scope.handler);
          $scope.taskData['handler_id'] = $scope.handler.id;

          $modalInstance.close( $scope.taskData );
        };
      }
  ]);//End of --> CreateTaskModalController

  /**
   * 此控制器用于操作新建 任务-讨论 的评论所对应的模态框
   */
  projectModule.controller('AddCommentModalController', ['$scope', '$modalInstance',
    function($scope, $modalInstance){
      $scope.content;

      $scope.cancel = function(){
        $modalInstance.dismiss('cancel');
      };

      $scope.ok = function(){
        $modalInstance.close({
          content: $scope.content
        });
      };
  }]);

  /**
   *
   */
  projectModule.controller('ReselectHandlerController', ['$scope', '$modalInstance', 'candidatedHandlers',
    function($scope, $modalInstance, candidatedHandlers){
      $scope.candidatedHandlers = candidatedHandlers;
      $scope.handler = null;

      $scope.cancel = function(){
        $modalInstance.dismiss('cancel');
      };

      $scope.ok = function(){
        console.log($scope.handler);
        $modalInstance.close($scope.handler);
      };

    }]);
});