/**
 * Created by spatra on 15-3-14.
 */

define(['projectJS/module'], function(projectModule){

  projectModule.factory('ProjectSharingService', ['$window', 'NestedResourceService',
    function($window, NestedResourceService){

      return {
        accessor: NestedResourceService.getResourceAccessor({
          parentResourceName: 'project',
          nestedResourceName: 'sharing'
        }),
        getOnStartPaginationConf: function(stateParams){
          var self = this;

          self.accessor['setParentResourceId'](stateParams);

          return self.accessor.get({
            params: {
              per_page: 20,
              page: 1
            }
          });
        },
        getTempUploadUri: function(){
          return ['api', 'file', 'temp', 'resource'].join('/');
        },
        downloadResource: function(resourceId){
          var downloadUrl = ['api', 'file', 'resource-download', resourceId];
          $window.location = downloadUrl.join('/');
        }
      };
    }]);//End of --> ProjectSharingService

});
