/**
 * Created by spatra on 15-3-14.
 */

define(['projectJS/module'], function(projectModule){

  /**
   * 此服务用于进行与 project 本身相关的信息处理（不包括嵌套资源）
   */
  projectModule.factory('ProjectService', ['ResourceService', function(ResourceService){
    return{
      accessor: ResourceService.getResourceAccessor({ resourceName: 'project' }),
      //返回所有的项目，包括加入的和自己创建的
      getAllProjects: function(){
        var self = this;

        return self.accessor.get({
          params: {
            per_page: 100,
            page: 1,
            option: 'all'
          }
        })
          .then(function(resp){
            return resp.data.data;
          }, function(resp){
            alert('项目列表加载出错');
            console.error(resp.data);
          });
      },
      getCreaterAndMembers: function(projectId){
        var self = this;

        return self.accessor.show(projectId)
          .then(function(resp){
            var rtn = resp.data.members || [];
            rtn.push( resp.data.creater );

            return rtn;
          }, function(resp){
            console.error(resp);
            alert('加载项目成员信息失败!');
          });
      }
    };
  }]);//End of --> ng-factory: ProjectService

  /**
   * 用于处理项目的相关标签
   */
  projectModule.factory('ProjectTagService', ['NestedResourceService', function(NestedResourceService){
    return {
      accessor: NestedResourceService.getResourceAccessor({
        parentResourceName: 'project',
        nestedResourceName: 'tag'
      })
    };
  }]);//End of --> NestedResourceService

  projectModule.factory('InitRightHeightService', ['$window', function($window){
    return {
      init: function(idName, heightPercent){
        heightPercent = heightPercent || 0.8;
        var screenHeight = $window.screen.height;

        //根据屏幕的高度(预设为80%),设置总任务内容的高度,
        var taskContent = $window.document.getElementById(idName);
        taskContent.style.minHeight = Math.floor((screenHeight * heightPercent)) + 'px';

      }
    };
  }]);//End of --> InitRightHeightService
});