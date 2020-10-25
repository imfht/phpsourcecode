/**
 * Created by spatra on 14-12-5.
 */

define(['projectJS/module'], function(projectModule){
  /**
   * 项目成员管理控制器
   */
  projectModule.controller('ProjectMemberController', ['$scope', '$rootScope', '$stateParams', 'MemberService', 'RoleService', 'ClassHelperService', 'roleInfo', 'conditions', 'LoginStatusService',
    function($scope, $rootScope, $stateParams, MemberService, RoleService, ClassHelperService, roleInfo, conditions, LoginStatusService){
      //设置父级资源id
      MemberService.accessor['setParentResourceId']($stateParams);

      $scope.conditions = conditions;
      $scope.roles = roleInfo;
      $scope.conditionObj = {};
      $scope.currentUserId = LoginStatusService.get('personalInfo.id'); //当前用户的id

      /*
       载入成员的有关信息
       */
      var loadMemberList = function(){
        $scope.roleMember = {};

        MemberService.accessor.get()
          .success(function(data){
            $scope.members = data.members || [];
            $scope.creater = data.creater;
            $scope.editable = data.editable;

            for(var i = 0; i < $scope.members.length ;i++) {
              var curMember =$scope.members[i];

              if( $scope.roleMember[curMember.role_id] === undefined ){
                $scope.roleMember[curMember.role_id] = [];
              }

              $scope.roleMember[curMember.role_id].push(curMember);
            }
          })
          .error(function(data){
            $scope.$emit('message:error',{
              title: '获取成员列表失败',
              msg: data.error
            });
          });
      };

      loadMemberList();

      //当有其他修改导致成员列表改变时，重新加载
      $rootScope.$on('member:reload', function(){
        loadMemberList();
      });

      /**
       * 改变成员的角色
       */
      $scope.changeMemberRole = (function(){

        function exchangeMember(roleMember, oldRoleId, newRoleId, targetMember){
          if( roleMember[ newRoleId ] === undefined ){
            roleMember[ newRoleId ] = [];
          }

          for( var i = 0; i < roleMember[oldRoleId].length; ++i ){
            if( ClassHelperService.objectEquals(roleMember[oldRoleId][i], targetMember ) ){
              roleMember[oldRoleId].splice(i, 1);
              break;
            }
          }

          if(  ! roleMember[oldRoleId].length ){
            roleMember[oldRoleId] = undefined;
          }

          roleMember[newRoleId].push(targetMember);
        }

        /**
         * 真正暴露出去的函数，即绑定到`$scope.changeMemberRole`
         */
        return function(curMember, oldRoleId, newRoleId) {
          //请求改变成员的role_id

          MemberService.accessor.update(curMember.id,{
            id: curMember.id,
            role_id: newRoleId
          })
            .success(function(){
              exchangeMember($scope.roleMember, oldRoleId, newRoleId, curMember);
              $scope.$emit('message:success', {
                title: '操作成功',
                msg: '成员角色已成功修改'
              });
            })
            .error(function(data){
              $scope.$emit('message:error', {
                title: '操作失败',
                msg: data.error
              });
              console.error(data);
            });
        };

      })();//End of --> $scope.changeMemberRole

      /**
       * 删除成员
       * @param memberId 待删除成员的id
       */
      $scope.deleteMember = function(memberId){
        if( confirm('确实要删除吗?') ){
          MemberService.accessor.destroy(memberId)
            .success(function(){
              loadMemberList();
              $scope.$emit('message:warning',{
                title: '项目成员删除成功'
              });
            })
            .error(function(data){
              console.error(data);
              $scope.$emit('message:error',{
                title: '项目成员删除失败',
                msg: data.error || ''
              });
            });
        }
      };

    }]);// End of --> ProjectMemberController

  projectModule.controller('ProjectMemberCreatingController', ['$scope', '$state', 'currentProjectId', 'roleInfo', 'MemberService',
    function($scope, $state, currentProjectId, roleInfo, MemberService){
      /*
       初始化用到的变量
       */
      $scope.currentProjectId = currentProjectId; //当前项目id
      $scope.roles = roleInfo;                    //用户角色的信息
      $scope.expectAddMember = {};                //待添加的成员的信息
      $scope.expectAddMember.role_id = $scope.roles[0].id;              //角色
      $scope.sending = false;

      /**
       * 添加新的成员
       */
      $scope.addMember = function(){
        $scope.sending = true;
        var waitingOps = {
          title: '正在请求添加成员'
        };

        $scope.$emit('message:wait', waitingOps);
        MemberService.accessor.store({
          'memberAccount': $scope.expectAddMember.username,
          'role_id': $scope.expectAddMember.role_id
        })
          .success(function(){
            $scope.$emit('member:reload');
            waitingOps.show({
              type: 'success',
              title: '项目成员添加成功'
            });
            $state.go('project.show.member', {
              'projectId': currentProjectId
            });
          })
          .error(function(data){
            waitingOps.show({
              type: 'error',
              title: '项目成员添加失败',
              msg: data.error
            });
          });

      };//End of --> function:addMember

  }]);//End of --> controller:ProjectMemberCreatingController
});
