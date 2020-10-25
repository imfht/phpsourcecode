/**
 * Created by spatra on 14-12-5.
 */

define(['projectJS/module'], function(projectModule){

  /**
   * 任务查看的控制器
   */
  projectModule.controller('TaskController',
    ['$rootScope', '$scope', '$stateParams', '$state', 'taskSet', 'taskStatusSet', 'TaskService', '$modal', 'MemberService', 'taskConditions', 'TaskPriorityService', 'InitRightHeightService','ClassHelperService', 'TaskPaginationHelperService', 'currentProjectInfo',
      function($rootScope, $scope, $stateParams, $state, taskSet, taskStatusSet, TaskService, $modal, MemberService, taskConditions, TaskPriorityService, InitRightHeightService, ClassHelperService, TaskPaginationHelperService, currentProjectInfo){

        //设置父级资源
        TaskService.accessor['setParentResourceId']($stateParams);
        /*
        初始化控制器作用域内使用到的变量
         */
        $scope.taskStatusSet = taskStatusSet;   //任务状态的数据集合
        $scope.taskSet = taskSet;


        $scope.currentProjectId = $stateParams['projectId'];  //设置当前的项目id
        $scope.conditions = taskConditions;   //加载任务的条件筛选项
        $scope.conditionObj = {};   //按条件筛选任务时使用的添加集合对象

        $scope.paginationHelper = TaskPaginationHelperService.createPagionationHelper(taskSet, taskStatusSet, $stateParams, $scope.conditionObj);


        //任务内容高度设置
        InitRightHeightService.init('task-content');

        /*
          建立监听，当其他部分的操作导致任务列表数据发生变动时，此处会刷新显示
         */
        $rootScope.$on('task:reload', function(event, transObj){
          $scope.paginationHelper.getResource(transObj);
        });

        /*
          监听筛选条件的变化，并执行重新
         */

        $scope.$watch('conditionObj', function(){
          $scope.paginationHelper.getResource({
            conditionObj: $scope.conditionObj
          });

        }, true);
        /*
          当用户点击指定任务时，加载详细的任务信息
         */
        $scope.showBriefTask = function(taskObj){
          if( taskObj.loadDetails ) { return };

          //TaskService.showWithCache( taskObj.id )
          TaskService.accessor.show(taskObj.id)
            .success(function(data){
              $.extend(taskObj, data);
              taskObj.loadDetails = true;
            })
            .error(function(data){
              $scope.$emit('message:error',{
                title: '获取具体任务信息失败',
                msg: data.error
              });

            });
        };

        /*
          创建新的任务
         */
        $scope.createTask = function() {
          $scope.createTaskModalInstance = $modal.open({
            templateUrl: 'ngApp/project/tpls/createTaskModal.html',
            controller: 'CreateTaskModalController',
            size: 'lg',
            resolve: {
              projectMemberList: function(){
                return MemberService.accessor.get()
                  .then(function(resp){
                    var projectMembers = resp.data.members;
                    projectMembers.unshift(resp.data.creater);
                    return projectMembers;
                  },
                  function(){
                    $scope.$emit('message:error',{
                      title: '加载成员列表出错'
                    });
                  });
              },
              priorityList: function(){
                return TaskPriorityService.accessor.get()
                  .then(function(resp){ return resp.data; }, function(resp){
                    console.error(resp.data);
                  });
              },
              parentTaskInfo: function(){
                return null;
              },
              parentMemberList: function(){
                return null;
              },
              currentProjectInfo: function(){
                return currentProjectInfo;
              }
            }
          });//End of --> $scope.createTaskModalInstance

          //用于处理新建任务所关联的模态框
          $scope.createTaskModalInstance.result
            .then(function(taskData){
              TaskService.accessor.store(taskData)
                .success(function(data){
                  $scope.$emit('message:success',{
                    title: '任务创建成功'
                  });
                  $scope.$emit('task:reload', {
                    //@workaround:暂时设死为1
                    currentStatusId: 1,
                    conditionObj: {
                      priorityId: data['priority_id']
                    }
                  });

                })
                .error(function(){
                  $scope.$emit('message:error',{
                    title: '任务创建失败'
                  });
                });
            });//End of --> $scope.createTaskModalInstance.result


        };//End of --> $scope.createTask

        //跳转到task列表的状态
        $scope.goToTaskState = function(){
          $state.go('project.show.task', $stateParams);
        };

  }]);  //End of --> TaskController

  /**
   * 具体任务框的控制器
   */
  projectModule.controller('TaskInfoController', ['$scope', '$rootScope', '$location', 'projectMembersAndCreater', '$stateParams', 'taskStatusList', '$state', 'TaskService', 'taskPriorityList',
  function($scope, $rootScope, $location, projectMembersAndCreater, $stateParams, taskStatusList, $state, TaskService, taskPriorityList){
    /*
      初始化一些需要用到的作用域变量
     */
    $scope.showInput = false;
    $scope.currentProjectId = $stateParams['projectId'];
    $scope.curStateName = $state.current.name;
    var mainStatePattern = /project.show(\.[\w]+)/;
    $scope.mainStateName = $state.current.name.match(mainStatePattern)[0];
    $scope.taskStatusList = taskStatusList;
    $scope.projectMembers = projectMembersAndCreater;
    $scope.changingMember = false;
    $scope.taskPriorityList = taskPriorityList;
    $scope.editing = false;

    var lastStatusId;
    var appointMemberHashSet = {};

    /**
     * 监听任务成员的改变，并将此修改发送到Server端
     * @param newValue
     */
    function watchingMembers(newValue, oldValue){
      if( newValue.length === 0 && oldValue.length !== 0){
        $scope.$emit('message:error', {
          title: '出错了',
          msg: '任务至少应该具备一个参与者！'
        });
        $scope.appointed_member = oldValue;
        return;
      }

      $scope.changingMember = true;
      var added = [], removed = [];

      var newHashSet = {};
      newValue.forEach(function(item){
        var itemId = item['id'];

        if( ! appointMemberHashSet.hasOwnProperty(itemId) ){
          added.push(parseInt(itemId));
        }

        newHashSet[itemId] = item;
      });

      for(var prop in appointMemberHashSet ){
        if( !newHashSet.hasOwnProperty(prop) ){
          removed.push(parseInt(prop));

          if( prop == $scope.currentSpecificTask['handler']['id'] ){
            $scope.$emit('message:error', {
              title: '出错了',
              msg: '不能删除执行者'
            });
            $scope.appointed_member = oldValue;
            return;
          }
        }
      }

      appointMemberHashSet = newHashSet;

      if( added.length === 0 && removed.length === 0 ){
        $scope.changingMember = false;
        return;
      }

      appointMemberHashSet = newHashSet;

      var putData = {appointed_member: {}};
      if( added.length ) putData['appointed_member']['add'] = added;
      if( removed.length ) putData['appointed_member']['delete'] = removed;

      TaskService.accessor.update($scope.currentSpecificTask.baseInfo.id, putData)
        .success(function(){
          $scope.$emit('message:success', {title: '操作成功', msg:'更改任务成员成功'});
          $scope.changingMember = false;
        })
        .error(function(data){
          console.error(data);
          $scope.$emit('message:error', {title: '操作失败', msg: data.error});
          $scope.changingMember = false;
        });
    }

    /**
     * 在必要时重新加载任务信息
     */
    var loadTaskInfo = function() {
      TaskService.accessor['setParentResourceId']($stateParams);
      TaskService.accessor.show($stateParams.taskId)
        .success(function(data){
          $scope.currentSpecificTask = data;
          $scope.editable = $scope.currentSpecificTask.editable;
          $scope.handler = data.handler;
          $scope.appointed_member = $scope.currentSpecificTask.appointed_member;
          $scope.newTaskDescription = $scope.currentSpecificTask.baseInfo.description;

          lastStatusId = $scope.currentSpecificTask.taskStatus.id;
          $scope.appointed_member.forEach(function(item){
            appointMemberHashSet[ item['id'] ] = item;
          });
          $scope.$watch('appointed_member', watchingMembers, true);
        })
        .error(function(data){
          $scope.$emit('message:error', {
            'title': '出错了',
            'msg': data.error
          });
          $state.go('project.show.task');
          console.error(data);
        });
    };

    loadTaskInfo();


    ////监听task:reload,刷新任务内容
    //$rootScope.$on('task:reload', function(){
    //  if($stateParams.taskId){
    //    loadTaskInfo();
    //  }
    //});

    /**
     * 在改变任务状态的时候执行
     * @param taskStatusId
     */
    $scope.changeTaskStatus = function(taskStatusId){

      TaskService.accessor.update($scope.currentSpecificTask.baseInfo.id, {
        'status_id': taskStatusId
      })
        .success(function(){
          $scope.$emit('task:reload', {
            changeStatusObj: {
              sourceStatusId: lastStatusId,
              targetStatusId: taskStatusId
            }
          });
          $scope.$emit('message:success', {
            title: '任务状态改变成功'
          });
          lastStatusId = taskStatusId;
        })
        .error(function(data){
          if( data.error ){
            $scope.$emit('message:error',{
              title: '出错啦!',
              msg: data.error
            });
          }
          else{
            $scope.$emit('message:error',{
              title: '出错啦!',
              msg: data.error
            });
          }
        });
    };


    $scope.editingDescriptin = false;
    /**
     * 设置进入任务描述编辑框时，对相关状态变量进行设定
     */
    $scope.enterEditingDescription = function(){
      $scope.editing = $scope.editingDescriptin = true;
    };
    /**
     * 当用户的焦点离任务描述编辑框的时候，自动进行提交
     */
    $scope.saveDescription = function(){
      $scope.editing = false;
      $scope.editingDescriptin = false;

      //如果没有更改则直接退出
      if( $scope.newTaskDescription == $scope.currentSpecificTask['baseInfo']['description']) return;


      if( $scope.newTaskDescription.replace(/^(\s|\u00A0)+/,'').replace(/(\s|\u00A0)+$/,'') == '' ){
        //如果修改的结果为空，则不请求后台更改，并提示出错信息

        $scope.newTaskDescription = $scope.currentSpecificTask['baseInfo']['description'];
        $scope.$emit('message:error', {title: '保存失败', msg: '项目描述不能为空'});
        $scope.editing = false;
      }
      else{
        //对应真正有修改的情况，向后台发起请求
        var lastContent = $scope.currentSpecificTask['baseInfo']['description'];
        $scope.currentSpecificTask['baseInfo']['description'] = $scope.newTaskDescription;

        TaskService.accessor.update($scope.currentSpecificTask.baseInfo.id, {
          description: $scope.newTaskDescription
        }).error(function(data){
          console.error(data);
          $scope.$emit('message:error', {title: '修改任务描述失败', msg: data.error});
          $scope.currentSpecificTask['baseInfo']['description'] = lastContent;
        });
      }
    };

    /**
     * 在改变任务优先级的时候执行
     * @param newPriorityId
     */
    $scope.changePriority = function(newPriorityId){
      $scope.editing = true;
      TaskService.accessor.update($scope.currentSpecificTask.baseInfo.id, {
        'priority_id': newPriorityId
      }).success(function(){
        $scope.editing = false;
        $scope.$emit('message:success', {'title': '操作成功', msg: '任务优先级已修改'});
        $scope.$emit('task:reload');
      }).error(function(data){
        console.error(data);
        $scope.editing = false;
        $scope.$emit('message:error', {'title': '修改失败', msg: data.error});
      });
    };
  }]);//End of --> TaskInfoController
});