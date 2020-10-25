/**
 * Created by spatra on 15-3-4.
 */

/**
 * 此模块提供 RESTFull 风格的后台访问服务
 */

define(['angular', 'libraryJS/classHelper/ng-require', 'angularUIRouter'], function(angular, libClassHelperModule){

  var module = angular.module('TeamMindmap.library.resourceAccessor', [libClassHelperModule, 'ui.router']);

  /**
   * 资源访问服务, 使用范例:
   *
   * var accessor = ResourceService.getAccessor({
   *  'resourceName': 'student',
   *  'apiPrefix': 'api_prefix'
   *  });
   *
   *  var list;
   *  accessor.get().success(data){
   *   list = data;
   *  });
   *
   * 则会向地址: /api_prefix/student的发送REST风格的交互请求, 通过方法 getBaseUrl 可以获得此地址
   * 方法包含: get/show/update/destroy(这些方法都返回 HTTP Promise对象)
   *
   * 注意: apiPrefix为非必填项, 默认为`api`
   *
   * 可以通过方法: getFactoryFunction 得到工厂构造函数
   */
  module.factory('ResourceService', ['$http', function($http){

    function resourceFactory(options){
      this.resourceName = options['resourceName'];
      this.apiPrefix = options['apiPrefix'] || 'api';
    }

    resourceFactory.prototype = {
      constructor: resourceFactory,
      get: function(getOps){
        return $http.get( this.getBaseUrl(), getOps );
      },
      store: function(resourceData){
        return $http.post( this.getBaseUrl(), resourceData);
      },
      show: function(resourceId, getOps){
        return $http.get( this.getBaseUrl() + '/' + resourceId, getOps );
      },
      update: function(resourceId, resourceData){
        return $http.put( this.getBaseUrl() + '/' + resourceId, resourceData);
      },
      destroy: function(resourceId){
        return $http.delete( this.getBaseUrl() + '/' + resourceId );
      },
      getBaseUrl: function(){
        if( ! this.baseUrl ){
          this.baseUrl = [
            this.apiPrefix, this.resourceName
          ].join('/');
        }

        return this.baseUrl;
      }
    };

    return {
      getResourceAccessor: function(options){
        return new resourceFactory(options);
      },
      getFactoryFunction: function(){
        return resourceFactory;
      }
    }

  }]);//End of --> ng-factory: ResourceService

  /**
   * 嵌套资源访问服务, 在CURD的使用上与ResourceService基本一致.
   *
   * 所谓的嵌套资源, 例如具体的项目会包含一定的任务,任务必须属于某一项目下,则任务是项目的嵌套资源.
   *
   * 使用范例:
   *
   * var accessor = NestedResourceService.getAccessor({
   *  'parentResourceName': 'project',
   *  'nestedResourceName': 'task',
   *  apiPrefix': 'api' //api为默认值, 可不填写
   * });
   *
   * accessor.setParentResourceId({
   *  $stateParams, // $stateParams也可以不必是ng自带服务,但需要是具有`projectId`属性的对象
   *  'projectId' // 此处可不填写,默认由 `parentResourceName` 加上 `Id`组合而成,用于访问$stateParams中的url参数
   * });
   *
   * 注意，如果不显式设定父级资源，则会尝试自动通过$stateParams来自动获取，其所访问的命名形式为：  `parentResourceName` 加上 `Id` 组合而成
   *
   * 如果出现了三层以上的多级嵌套，则可写多个父级资源。例如，父级资源(使用"|"来分隔)： "project|discussion", 嵌套资源："comments",
   * setParentResourceId处也采用同样的命名方式.
   *
   * 则会向地址: /api/project/1/task 的发送REST风格的交互请求(此处假定`projectId`的值为:1), 通过方法 getBaseUrl 可以获得此地址
   * 方法包含: get/show/update/destroy. get/show/update/destroy(这些方法都返回 HTTP Promise对象)
   *
   *  var list;
   *  accessor.get().success(data){
   *   list = data;
   *  });
   *
   *  备注: NestResourceService实际上是基于对ResourceService中 getBaseUrl 方法的扩展.
   */
  module.factory('NestedResourceService', ['$http', '$stateParams', 'ResourceService', 'ClassHelperService',
    function($http, $stateParams, ResourceService, ClassHelperService){

      function nestResourceFactory(options){
        this.parentResourceNames = options['parentResourceName'].split('|');
        this.nestedResourceName = options['nestedResourceName'];
        this.apiPrefix = options['apiPrefix'] || 'api';
      }

      ClassHelperService.extend(nestResourceFactory, ResourceService.getFactoryFunction());

      ClassHelperService.extendOrOverloadMethod(nestResourceFactory, 'setParentResourceId', function(stateParams, parentResourceIdDesces){
        var self = this;
        var requestUrlArray = [self.apiPrefix];

        if( parentResourceIdDesces ){
          //如果用户设定了特殊的描述则执行此处

          if( parentResourceIdDesces.indexOf('*') !== -1 &&
            parentResourceIdDesces.length == self.parentResourceNames.length ){

            parentResourceIdDesces = parentResourceIdDesces.split('|');
            for(var i = 0, length = parentResourceIdDesces.length; i < length; ++i ){
              if( parentResourceIdDesces[i] === '*' ){
                parentResourceIdDesces[i] = self.parentResourceNames[i] + 'Id';
              }

              requestUrlArray.push(self.parentResourceNames[i]);
              requestUrlArray.push(stateParams[ parentResourceIdDesces[i] ]);
            }
          }

        }
        else{

          self.parentResourceNames.forEach(function(item){
            requestUrlArray.push(item);
            requestUrlArray.push(stateParams[ item + 'Id' ]);
          });
        }

        requestUrlArray.push(self.nestedResourceName);

        self.parentResourceId = 'Init';
        self.requestUrl = requestUrlArray.join('/');
      });

      ClassHelperService.extendOrOverloadMethod(nestResourceFactory, 'getBaseUrl', function(){
        var self = this;

        if( ! self.parentResourceId ){
          self['setParentResourceId']($stateParams);
        }

        return self.requestUrl;
      });

      return {
        getResourceAccessor: function(options){
          return new nestResourceFactory(options);
        },
        getFactoryFunction: function(){
          return nestResourceFactory;
        }
      };
    }]);//End of --> ng-factory: NestedResourceService

  return module.name;
});