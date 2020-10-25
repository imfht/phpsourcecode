/**
 * Created by rockyren on 15/3/18.
 */
define(['projectJS/module'], function(projectModule){
  /**
   * 工作台控制器
   */
  projectModule.controller('DesktopMindmapController', ['$scope', '$modal', '$stateParams', 'TaskService','MemberService', 'taskSet', 'currentProjectInfo', 'taskChecker',
    function($scope, $modal, $stateParams, TaskService, MemberService, taskSet, currentProjectInfo, taskChecker){
      $scope.taskFirstLevelList = taskSet;
      $scope.currentProjectInfo = currentProjectInfo;

      //根据$stateParams设置父级资源Project的id
      TaskService.accessor['setParentResourceId']($stateParams);
      MemberService.accessor['setParentResourceId']($stateParams);

      //根据节点的id获取其状态id
      function getStatusId(id){
        for(var i=0;i<$scope.taskFirstLevelList.length;i++){
          if($scope.taskFirstLevelList[i].id === id){
            return $scope.taskFirstLevelList[i].status_id;
          }
        }
        return null;
      }

      $scope.checkNodeAuthority = taskChecker;

      $scope.deleteTask = function(id) {
        $scope.handleNode.setEnableRender(false);
        var nodeStatusId = getStatusId(id);

        TaskService.accessor.destroy(id)
          .success(function(){
            $scope.handleNode.deleteNode();
            $scope.$emit('message:success', {
              title: '任务删除成功'
            });
            $scope.$emit('task:reload', {
              currentStatusId: nodeStatusId
            });
            $scope.handleNode.setEnableRender(true);

          })
          .error(function(data){
            $scope.$emit('message:error', {
              title: '删除任务失败',
              msg: data.error
            });
            console.error(data.error);
          });
      };


      $scope.createTask = function(parentTaskInfo) {
        $scope.handleNode.setEnableRender(false);
        $scope.createTaskModalInstance = $modal.open({
          templateUrl: 'ngApp/project/tpls/createTaskModal.html',
          controller: 'CreateTaskModalController',
          size: 'lg',
          resolve: {
            parentTaskInfo: function(){
              return parentTaskInfo || null;
            },
            currentProjectInfo: function(){
              return currentProjectInfo || null;
            },
            projectMemberList: function(){
              MemberService.accessor['setParentResourceId']($stateParams);

              return MemberService.accessor.get()
                .then(function(resp){
                  var projectMembers = resp.data.members;
                  projectMembers.unshift(resp.data.creater);
                  return projectMembers;
                },
                function(resp) {
                  console.error(resp.data);
                  $scope.$emit('message:error',{
                    title: '加载成员列表出错'
                  });
                });
            },
            parentMemberList:function(){
              if( parentTaskInfo === null ){
                return null;
              }
              else{
                TaskService.accessor['setParentResourceId']($stateParams);

                return TaskService.accessor.show(parentTaskInfo['id'])
                  .then(function(resp){
                    return resp.data['appointed_member'];
                  }, function(resp){
                    console.error(resp);
                    $scope.$emit('message:error', {
                      'title': '出错了',
                      msg: resp.data.error
                    });
                  });
              }
            },
            priorityList: ['TaskPriorityService', function(TaskPriorityService){
              return TaskPriorityService.accessor.get()
                .then(function(resp){
                  return resp.data;
                }, function(resp){
                  console.error(resp.data);
                  $scope.$emit('message:error',{
                    msg: '加载任务优先级出错'
                  });
                });
            }]
          }
        });

        $scope.createTaskModalInstance.result
          .then(function (taskData) {;
            //设置新添加节点的parent_id
            if(parentTaskInfo) {
              taskData.parent_id = parentTaskInfo.id;
            }

            TaskService.accessor.store(taskData)
              .success(function (data) {

                $scope.handleNode.addNode(taskData.name, data.id, data.data);
                $scope.$emit('message:success',{
                  title: '任务创建成功'
                });
                $scope.$emit('task:reload', {
                  currentStatusId: 1
                });

                $scope.handleNode.setEnableRender(true);
              })
              .error(function () {
                $scope.$emit('message:error',{
                  title: '任务创建失败'
                });
              });
          }, function(){
            $scope.handleNode.setEnableRender(true);
          });

      };//End of the $scope.createTask function

      /**
       * 重新设置父节点的请求
       * @param parentAndChildInfo: {parentId, childId}
       */
      $scope.changeParentTask = function(parentAndChildInfo){
        $scope.handleNode.setEnableRender(false);

        TaskService.accessor.update(parentAndChildInfo.childId, {
          parent_id: parentAndChildInfo.parentId
        })
          .then(function(resp){
            if( resp.data ){
              return resp.data;
            }
            else{
              $scope.$emit('message:success', {
                title: '操作成功'
              });

              $scope.handleNode.setParentNode(parentAndChildInfo.parentId, parentAndChildInfo.childId);
              $scope.handleNode.setEnableRender(true);

              return null;
            }
          }, function(resp){
            console.error(resp);
            $scope.$emit('message:error', {
              title: '修改失败',
              msg: resp.data.error
            });
          })
          .then(function(data){
            if( data === null ) return;

            $scope.createReselectModalInstance = $modal.open({
              templateUrl: 'ngApp/project/tpls/task-reselect-handler.html',
              controller: 'ReselectHandlerController',
              size: 'sm',
              resolve: {
                candidatedHandlers: function(){
                  return data['memberList'];
                }
              }
            });

            $scope.createReselectModalInstance.result.then(function(handler){
              TaskService.accessor.update(parentAndChildInfo.childId, {
                parent_id: parentAndChildInfo.parentId,
                handler_id: handler.id
              }).success(function(){
                $scope.handleNode.setEnableRender(true);
                $scope.handleNode.setParentNode(parentAndChildInfo.parentId, parentAndChildInfo.childId);
                $scope.$emit('message:success', {title: '操作成功'});
              })
                .error(function(data){
                  $scope.$emit('message:error', {title: '操作失败', msg: data.error});
                  $scope.handleNode.setEnableRender(true);
                  console.error(data);
                });
            }, function(){
              $scope.handleNode.setEnableRender(true);
            });


          });

      };

      $scope.requestHandle = {
        deleteTask: $scope.deleteTask,
        createTask: $scope.createTask,
        changeParentTask: $scope.changeParentTask
      }

    }]);// End of --> DesktopMindmapController

  projectModule.controller('TaskMoreMindmapController', ['$scope', 'taskInfo', 'currentProjectInfo',
    function($scope, taskInfo, currentProjectInfo){
      $scope.currentProjectInfo = currentProjectInfo;

      $scope.taskInfo = taskInfo;


      $scope.breadcrumbNeedObj = {
        curTaskId: taskInfo.baseInfo.id,
        parentTaskId: taskInfo.baseInfo.parent_id,
        curTaskName: taskInfo.baseInfo.name,
        projectId: currentProjectInfo.baseInfo.id,
        projectName: currentProjectInfo.baseInfo.name
      };

    }]);//End of --> TaskMoreMindmapController

});
