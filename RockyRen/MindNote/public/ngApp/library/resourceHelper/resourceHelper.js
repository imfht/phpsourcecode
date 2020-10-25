/**
 * Created by rockyren on 15/5/18.
 */
define(['angular'], function(angular){
  var module = angular.module('resourceHelper', []);

  /**
   * restful资源请求服务
   * 只要返回两种资源对象的工厂方法：单资源工厂 和 嵌套资源工厂
   */
  module.factory('ResourceService', ['$http', function($http){
    /**
     * 单资源请求类，提供5种请求方法
     * @param options
     * @constructor
     */
    function Resource(options){
      this.resourceName = options['resourceName'];
      this.apiPrefix = options['apiPrefix'] || 'api';
    }

    Resource.prototype = {
      constructor: Resource,
      get: function(){
        return $http.get( this.getBaseUrl() + 's');
      },
      show: function(id){
        return $http.get( this.getBaseUrl() + '/' + id);
      },
      store: function(data){
        return $http.post( this.getBaseUrl(), data);
      },
      update: function(id, data){
        return $http.put( this.getBaseUrl() + '/' + id, data);
      },
      destroy: function(id){
        return $http.delete( this.getBaseUrl() + '/' + id);
      },
      getBaseUrl: function(){
        var baseUrl = '/' + this.apiPrefix + '/' + this.resourceName;
        return baseUrl.toLocaleLowerCase();
      }
    };

    function NestedResource(options){
      Resource.call(this, options);
      this.requestUrl = '';
      this.parentResourceName = options['parentResourceName'].split('|');
    }

    NestedResource.prototype = Object.create(Resource.prototype);
    NestedResource.prototype.constructor = NestedResource;

    /**
     * 设置资源的baseUrl
     * @param stateParams: 例子格式: {groupId: 1, notebookId: 2}
     *                    若资源名为note，则baseUrl会转化为/api/group/1/notebook/2/note/
     */
    NestedResource.prototype.setParentId = function(stateParams){
      this.requestUrl = '';

      for(var i=0; i<this.parentResourceName.length; i++){
        var curResourceName = this.parentResourceName[i];

        if(stateParams.hasOwnProperty(curResourceName + 'Id') && stateParams[curResourceName + 'Id']){

          this.requestUrl += curResourceName + '/' + stateParams[curResourceName + 'Id'] + '/';
        }
      }

    };
    NestedResource.prototype.getBaseUrl = function(){
      var baseUrl = '/' + this.apiPrefix + '/' + this.requestUrl + this.resourceName;

      return baseUrl;
    };


    return {
      resourceFactory: function(options){
        return new Resource(options);
      },
      nestedResourceFactory: function(options){
        return new NestedResource(options);
      }

    }
  }]);




  return module.name;
});