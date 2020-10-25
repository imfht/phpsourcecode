/**
 * Created by spatra on 15-3-14.
 */

define(['projectJS/module'], function(projectModule){

  projectModule.factory('MemberService', ['NestedResourceService', function(NestedResourceService){
    return {
      accessor: NestedResourceService.getResourceAccessor({
        parentResourceName: 'project',
        nestedResourceName: 'member'
      }),
      /**
       * 返回项目的创建者和项目成员，置于同一个数组中
       * @param $stateParams
       * @returns {*|webdriver.promise.Promise}
       */
      getCreaterAndMembersToArray: function($stateParams){
        var self = this;

        self.accessor['setParentResourceId']($stateParams);

        return self.accessor.get()
          .then(function(resp){
            var data = resp.data;
            var arr = [ data['creater'] ];

            Array.prototype.push.apply(arr, data['members']);
            resp.data = arr;

            return resp;
          }, function(resp){
            return resp;
          });
      }
    };
  }]);

  projectModule.factory('RoleService', ['ResourceService', function(ResourceService){
    return {
      accessor: ResourceService.getResourceAccessor({
        'resourceName': 'role'
      })
    };
  }]);

});
