/**
 * Created by spatra on 14-12-3.
 */

define(['projectJS/module'], function(projectModule){

  /**
   * 用于处理用户项目列表的控制器
   */
  projectModule.controller('ProjectListController', ['$scope', 'projectList',
    function($scope, projectList){
      $scope.projects = projectList;

  }]);  //End of --> ProjectListController

  /**
   * 项目设置的控制器
   */
  projectModule.controller('ProjectSettingController', ['$scope', '$stateParams', '$state', 'ProjectService', 'LoginStatusService', 'currentProjectInfo',
    function($scope, $stateParams, $state, ProjectService, LoginStatusService, currentProjectInfo){

      /*
      初始化控制器作用域来使用到的变量
       */
      $scope.projectData = currentProjectInfo.baseInfo;  //项目的基本信息
      $scope.editable = currentProjectInfo.editable;     //当前浏览者是否对项目信息拥有修改的权限
      $scope.isMyProject = $scope.projectData['creater_id'] == LoginStatusService.get('personalInfo.id'); //这个项目是否由当前登录用户所创建
      $scope.sending = false;

      /**
       * 执行更改项目信息的操作
       */
      $scope.submitProject = function(){
        $scope.sending = true;

        var waitingOps = {
          title: '改动保存中...'
        };
        $scope.$emit('message:wait', waitingOps);
        ProjectService.accessor.update($stateParams['projectId'], $scope.projectData)
          .success(function(){
            waitingOps.show({
              type: 'success',
              title: '项目修改成功'
            });
            $scope.sending = false;
          })
          .error(function(data) {
            console.error(data);
            waitingOps.show({
              type: 'error',
              title: '项目修改失败',
              msg: data.error || ''
            });
            $scope.sending = false;
          });
      };

      /**
       * 执行删除项目的操作
       */
      $scope.deleteProject = function(){
        if( ! confirm('删除操作不可恢复！ 您确实要执行吗？ ') ) { return; }

        $scope.sending = true;

        var projectName = $scope.projectData['name'];

        var waitingOps = {
          title: '正在删除...'
        };
        $scope.$emit('message:wait', waitingOps);
        ProjectService.accessor.destroy($stateParams['projectId'])
          .success(function(){
            $state.go('project.list');
            waitingOps.show({
              type: 'success',
              title: '操作成功',
              msg: '项目：“' + projectName + '” 已经被删除'
            });
          })
          .error(function(data){
            waitingOps.show({
              type: 'error',
              title: '操作失败',
              msg: data.error
            });
            console.error( data.error );
            $scope.sending = false;
          });
      };
  }]);//End of --> ProjectSettingController

  /**
   * 项目创建控制器
   * 控制器提供项目创建功能
   */
  projectModule.controller('ProjectCreatingController', ['$scope', '$location', 'ProjectService', 'roleList', 'UserService', 'currUser',
    function ($scope, $location, ProjectService, roleList, UserService, currUser) {

      /*
       初始化控制器作用域来使用到的变量
       */
      $scope.projectData = {
        cover: 'fa-gear',
        memberList: []
      };
      $scope.roleList = roleList;

      /**
       * 添加新的项目成员
       */
      $scope.addMemberInCreate = function(){
        var identify = $.trim( $scope.addMemberIdentify );

        if( identify === currUser['email'] || identify === currUser['username'] ){
          $scope.$emit('message:error',{
            title: '项目成员添加失败',
            msg: '不能添加您自己'
          });
          return ;
        }

        UserService.exist(identify)
          .success(function(data) {
            $scope.projectData.memberList.push({
              'username': data.username,
              'head_image': data.head_image,
              'role_id': $scope.addMemberRole.id,
              'role_label': $scope.addMemberRole.label,
              'user_id': data.id
            });

            $scope.addMemberIdentify = null;
            $scope.hasMember = true;
          })
          .error(function() {
            $scope.$emit('message:error',{
              title: '项目成员添加失败',
              msg: '该成员不存在'
            });
          });
      };

      $scope.canCreate = true;
      /**
       * 创建项目，若成功则跳转到新的项目页面
       */
      $scope.createProject = function(){
        var waitOptions = {
          title: '正在创建项目，请稍候'
        };
        $scope.$emit('message:wait', waitOptions);
        $scope.canCreate = false;
        ProjectService.accessor.store($scope.projectData)
          .success(function(data){
            waitOptions.show({
              type: 'success',
              title: '项目创建成功'
            });
            $location.path('/project/' + data.id + '/desktop');
            $scope.canCreate = true;
          })
          .error(function(data){
            waitOptions.show({
              type: 'error',
              title: data.error || '项目创建失败'
            });
            console.log(data);
            $scope.canCreate = true;
          });
      };
  }]);//End of --> ProjectCreatingController


});