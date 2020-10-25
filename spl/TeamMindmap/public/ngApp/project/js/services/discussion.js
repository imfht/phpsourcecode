/**
 * Created by spatra on 15-3-14.
 */

define(['projectJS/module'], function(projectModule){
  /**
   * 此服务用于 项目-讨论-评论 的获取和添加评论等操作
   */
  projectModule.factory('CommentService', ['NestedResourceService', function(NestedResourceService){
    return {
      accessor: NestedResourceService.getResourceAccessor({
        parentResourceName: 'project|discussion',
        nestedResourceName: 'comment'
      })
    };
  }]);

  /**
   * 此服务用于 项目-讨论 的获取和添加评论等操作
   */
  projectModule.factory('DiscussionService', ['$http', 'NestedResourceService',
    function($http, NestedResourceService){

      return {
        accessor: NestedResourceService.getResourceAccessor({
          parentResourceName: 'project',
          nestedResourceName: 'discussion'
        }),
        addComment: function(data, stateParams){
          var self = this;

          self.accessor['setParentResourceId'](stateParams);

          return $http.post([
            self.accessor.getBaseUrl(), stateParams['discussionId'], 'comment'
          ].join('/'), data);
        }
      };

    }]);//End of --> DiscussionService
});