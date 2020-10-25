/**
 * Created by spatra on 15-6-3.
 */

define(['routeApp/module', 'library/classHelper'], function(module, classHelper){
  /**
   * 资源访问的辅助类
   */
  module.factory('ResourceAccessorHelper', ['ResourceService', function(ResourceService){
    var self = {
      modifyWithStatus: function(callBack, scope, statusName){
        if( typeof callBack !== 'function' ) throw '错误的函数调用： RouteService -> modifyWithStatus';

        scope[statusName] = true;

        return callBack().success(function(data){
          scope[statusName] = false;
          return data;
        }).error(function(data){
          scope[statusName] = false;
          return data;
        });
      },
      accessorModifyWithStatus: function(accessorOpts, scope, statusName, errorMsg){
        if( !Array.isArray(accessorOpts) || accessorOpts.length !== 3 ||  typeof accessorOpts[1] !== 'function' ){
          throw '错误的函数调用： RouteService -> accessorModifyWithStatus';
        }

        statusName = statusName || 'editing';
        errorMsg = errorMsg || '操作失败';

        return self.modifyWithStatus(function(){
          return accessorOpts[1].apply(accessorOpts[0], accessorOpts[2]);
        }, scope, statusName).error(function(data){
          var errorMsg = (data && data.error) || errorMsg;

          if( typeof errorMsg === 'object' ){
            var joins = [];
            for(var prop in errorMsg ){
              if( errorMsg.hasOwnProperty(prop) ) joins.push(errorMsg[prop]);
            }
            alert( joins.join(';') );
          } else {
            alert(errorMsg);
          }

          console.error(data);
        });
      },
      getAccessorWithStatus: function(accessor){
        if( accessor instanceof ResourceService.getFactoryFunction() ){
          var accessorWithStatus = {},
            accessorMethods = ['get', 'show', 'store', 'update', 'destroy'];

          accessorMethods.forEach(function(item){
            accessorWithStatus[item] = (function(){
              return function(){
                var mixed = Array.prototype.shift.call(arguments),
                  scope = mixed, statusName = undefined, errorMsg = undefined;

                if( !mixed['$id'] ){
                  scope = mixed['scope'];
                  statusName = mixed['statusName'];
                  errorMsg = mixed['errorMsg']
                }

                return self.accessorModifyWithStatus(
                  [accessor, accessor[item], arguments], scope, statusName, errorMsg
                );
              };
            })();
          });

          return accessorWithStatus;
        } else {
          throw '错误的函数调用： RouteService -> getAccessorWithStatus (accessor参数错误)'
        }
      }
    };

    return self;
  }]);

  /**
   * 路线
   */
  module.factory('RouteService', ['ResourceService', 'ResourceAccessorHelper',
    function(ResourceService, ResourceAccessorHelper){
      return {
        accessor: ResourceService.getResourceAccessor({
          resourceName: 'route'
        }),
        getMyRoutes: function(){
          var self = this;

          return self.accessor.get({
            params: {type: 'mine'}
          });
        },
        updateWithStatus: function(routeObj, scope, statusName){
          statusName = statusName || 'updating';
          var self = this;

          return ResourceAccessorHelper.accessorModifyWithStatus([
            self.accessor, self.accessor.update, [routeObj._id, routeObj]
          ], scope, statusName, '保存失败');
        }
      };
    }]);

  /**
   * 路线-日程
   */
  module.factory('RouteDailyService', ['NestedResourceService', 'ResourceAccessorHelper',
      function(NestedResourceService, ResourceAccessorHelper){

        return {
          accessor: NestedResourceService.getResourceAccessor({
            'parentResourceName': 'route',
            'nestedResourceName': 'daily'
          }),
          updateWithParams: function(params, dataObj){
            var self = this;

            self.accessor['setParentResourceId'](params);

            return ResourceAccessorHelper.accessorModifyWithStatus([
              self.accessor, self.accessor.update, [dataObj['_id'], dataObj]
            ], {}, null, '更改失败');
          },
          deleteWithStatus: function(id, scope, statusName){
            var self = this;
            statusName = statusName || 'deleting';

            return ResourceAccessorHelper.accessorModifyWithStatus([
              self.accessor, self.accessor.destroy, [id]
            ], scope, statusName, '删除失败');
          },
          saveWithStatus: function(id, data, scope){
            var self = this;

            return ResourceAccessorHelper.accessorModifyWithStatus([
              self.accessor, self.accessor.update, [id, data]
            ], scope, 'updating', '保存失败');
          },
          create: function(remark, scope){
            var self = this;

            return ResourceAccessorHelper.accessorModifyWithStatus([
              self.accessor, self.accessor.store, [{remark: remark}]
            ], scope, 'deleting', '新建失败');
          }
        };
      }]
  );

  /**
   * 路线-交通方式
   */
  module.factory('RouteTransportationService', ['NestedResourceService', 'ResourceAccessorHelper',
      function(NestedResourceService, ResourceAccessorHelper){
        var accessorWithStatus = null;

        return {
          accessor: NestedResourceService.getResourceAccessor({
            'parentResourceName': 'route',
            'nestedResourceName': 'transport'
          }),
          getAccessorWithStatus: function(){
            if( accessorWithStatus === null ){
              accessorWithStatus = ResourceAccessorHelper.getAccessorWithStatus(this.accessor);
            }

            return accessorWithStatus;
          }
        };
      }]
  );

  /**
   * 路线-游记
   */
  module.factory('RouteNoteService', ['NestedResourceService', 'ResourceAccessorHelper',
    function(NestedResourceService, ResourceAccessorHelper){

      return {
        accessor: NestedResourceService.getResourceAccessor({
          'parentResourceName': 'route',
          'nestedResourceName': 'note'
        }),
        /**
         * 保存游记
         * @param data
         * @param scope
         * @returns {*}
         */
        saveNode: function(data, scope){
          var self = this;

          data.loc = JSON.stringify(data.loc);
          var postData = new FormData();

          for(var prop in data){
            if( data.hasOwnProperty(prop) && prop !== 'images'){
              postData.append(prop, data[prop]);
            }
          }

          if( data['images'] && data['images'].length ){
            data['images'].forEach(function(item, index){
              postData.append('images[' + index + ']', item);
            });
          }

          return ResourceAccessorHelper.modifyWithStatus(function(){
            return self.accessor.store(postData, {
              transformRequest: angular.identity,
              headers: {'Content-Type': undefined}
            });
          }, scope, 'creating');
        }
      };
    }]
  );

  /**
   * 景点
   */
  module.factory('SightService', ['ResourceService',
    function(ResourceService){
      var modifiedCallback = null;

      return {
        accessor: ResourceService.getResourceAccessor({
          resourceName: 'sight'
        }),
        setCreatedCallback: function(callback){
          modifiedCallback = callback;
        },
        runCreatedCallback: function(data, callback){
          if( typeof modifiedCallback === 'function' ){
            modifiedCallback(data, callback);
          } else {
            callback && callback();
          }

          modifiedCallback = null;
        }
      };
  }]);
  return module;
});