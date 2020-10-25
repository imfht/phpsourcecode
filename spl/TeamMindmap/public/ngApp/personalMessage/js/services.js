/**
 * Created by spatra on 14-12-12.
 */

define(['personalMessageJS/module'], function(personalMessageModule){

  /**
   * 用于实现私信相关功能的服务
   */
  personalMessageModule.factory('MessageService', ['$rootScope', 'ResourceService', 'UserService', 'MemberService',
    function($rootScope, ResourceService, UserService, MemberService){

      /**
       * 按用户来添加收信人
       *
       * @param receiverSet 收信人集合，添加成功的用户被加入到这个集合中
       * @param userMixed 待添加收信人的用户名或电子邮件
       */
      function addOnUser(receiverSet, userMixed){
        var users = userMixed.split('|'); //允许通过`|`作为分隔符一次添加多个收信人

        for(var i = 0; i < users.length; ++i ){
          UserService.exist(users[i])
            .then(function(resp){
              var userInfo = resp.data;
              receiverSet[ userInfo.id ] = userInfo;
            }, function(resp){
              $rootScope.$emit('message:error',{
                title: '收信人添加失败',
                msg: '[' + userMixed + '] 并不是任何用户的用户名或邮箱地址'
              });
              console.error(resp.data);
            });
        }//End of `for`
      }//End of --> addOnUser

      /**
       * 按项目来添加收信人， 即所选择项目的创建者和成员均会被添加到待收信人集合中.
       *
       * @param receiverSet 收信人集合，添加成功的用户被加入到这个集合中
       * @param projectMixed 待添加的项目的id
       */
      function addOnProject(receiverSet, projectMixed){

        MemberService.accessor['setParentResourceId']({
          projectId: projectMixed
        });

        MemberService.accessor.get()
          .then(function(resp){
            var projectUsers = resp.data.members;
            projectUsers.push( resp.data.creater );

            projectUsers.forEach(function(item){
              receiverSet[ item.id ] = item;
            });

          }, function(resp){
            $rootScope.$emit('message:error', {
              title: '收信人添加失败',
              msg: resp.data.error
            });
            console.log(resp.data);
          });
      }//End of --> addOnProject

      //添加收信人的方法组成的数组
      var sendMethods = [
        {id: 1, label: '按用户', method: addOnUser},
        {id: 2, label: '按项目', method: addOnProject}
      ];

      return {
        accessor: ResourceService.getResourceAccessor({
          resourceName: 'messages'
        }),
        getSendMethods: function(){
          return sendMethods;
        },
        addReceiver: function(methodId, receiverSet, mixed){
          var targetMethod;
          for(var i = 0; i < sendMethods.length; ++i ){
            if( sendMethods[i]['id'] == methodId ){
              targetMethod = sendMethods[i];
              break;
            }
          }

          if( ! targetMethod ){
            throw '错误的添加方法';
          }

          targetMethod['method'](receiverSet, mixed);
        }
      };
  }]);//End of --> MessageService

});