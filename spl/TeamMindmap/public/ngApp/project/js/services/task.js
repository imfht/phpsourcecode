/**
 * Created by spatra on 15-3-14.
 */

define(['projectJS/module'], function(projectModule){
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
            console.error(resp);;
          });
      }
    };
  }]);

  projectModule.factory('TaskService', ['$cacheFactory', '$rootScope', 'NestedResourceService', 'DatetimeCheckRangeService', 'PaginationService', 'ClassHelperService',
    function($cacheFactory, $rootScope, NestedResourceService, DatetimeCheckRangeService, PaginationService, ClassHelperService){
      //缓存对象应该是在此处生成，而不是每次调用方法时生成
      var showCacheObj = $cacheFactory('taskCache', { capacity: 10 });

      //过滤方法组成的集合，key对应过滤方法名，value为对应的过滤函数，函数的参数包括：待检测对象、条件
      var filterMethods = {
        'priority_id': function(checkObj, cond){
          return checkObj['priority_id'] === cond;
        },
        'datetimes': function(checkObj, cond){
          return DatetimeCheckRangeService.checkRange(checkObj['expected_at'], cond);
        }
      };

      return {
        accessor: NestedResourceService.getResourceAccessor({
          parentResourceName: 'project',
          nestedResourceName: 'task'
        }),
        showWithCache: function(taskId){
          return this.accessor.show(taskId, {
            cache: showCacheObj
          });
        },
        finished: function(taskId){
          return this.accessor.update(taskId, {
            status: 'finished'
          });
        },
        getTaskSet: function(stateParams, statusList){
          var self = this;
          self.accessor['setParentResourceId'](stateParams);

          function getLabelIdSet(statusList){
            var labelIdSet = {};
            for(var i=0; i<statusList.length; i++){
              labelIdSet[statusList[i].name] = statusList[i].id;
            }
            return labelIdSet;
          }

          var labelIdSet = getLabelIdSet(statusList);

          return self.accessor.get({
            params: {
              per_page: 10,
              page: 1,
              group: true
            }
          })
            .then(function(resp){
              return self._divideTaskByStatus( resp.data, labelIdSet );
            }, function(data){
              $rootScope.$emit('message:error',{
                title: '获取任务列表失败',
                msg: data.error
              });
            });
        },

        _divideTaskByStatus: function(sourceTasks, labelIdSet){

          var taskSet = {};

          for(var statusLabel in sourceTasks) {
            var taskSetByStatus = {};
            var statusId = labelIdSet[statusLabel];

            var taskStatusInfo = sourceTasks[statusLabel];

            taskSetByStatus.data = taskStatusInfo.data;
            taskSetByStatus.current_page = taskStatusInfo.current_page;
            taskSetByStatus.per_page = taskStatusInfo.per_page;
            taskSetByStatus.total =  taskStatusInfo.total;

            taskSet[statusId] = taskSetByStatus;

          }
          return taskSet;

        },
        convertToCondItems: function(source, sourceCond, sourceLabel){
          var result = [];

          source.forEach(function(item){
            result.push({
              'cond': item[sourceCond],
              'label': item[sourceLabel]
            });
          });

          return result;
        },
        //根据条件动态生成过滤函数
        buildFiltersFun: function(condObj){

          return function(checkObj){
            for(var condItem in condObj ){
              if( ! filterMethods[ condItem ](checkObj, condObj[condItem]) ){
                return false;
              }
            }

            return true;
          };
        }
      };

    }]);

  projectModule.factory('TaskPaginationHelperService', ['PaginationService', 'TaskService', 'ClassHelperService', function(PaginationService, TaskService, ClassHelperService){
    var paginationHelper = function(taskSet, taskStatusSet, stateParams, conditionObj){
      TaskService.accessor['setParentResourceId'](stateParams);

      this.paginationSet = {};
      this.getOpsSet = {};
      this.idLabelSet = this._getIdLabelSet(taskStatusSet);

      for(var curStatusId in taskSet){
        var taskStatusInfo = taskSet[curStatusId];

        //初始化getOpsSet
        this.getOpsSet[curStatusId] = {
          status: this.idLabelSet[curStatusId]
        };
        ClassHelperService['update'](conditionObj, this.getOpsSet);

        var curPagination = PaginationService.createPagination('scroll', {
          currentPage: taskStatusInfo['current_page'],
          itemsPerPage: taskStatusInfo['per_page'],
          totalItems: taskStatusInfo['total'],
          resourceList: taskStatusInfo['data'],
          resourceGetMethod: PaginationService.convertMethodToNormalFun(TaskService.accessor, 'get'),
          getResourceOps: this.getOpsSet[curStatusId],
          eventName: 'scroll:taskStatusLoadMore' + curStatusId
        });
        curPagination.init();
        this.paginationSet[curStatusId] = curPagination;
      }
    };

    paginationHelper.prototype._getIdLabelSet = function(taskStatusSet){
      var idLabelSet = {};
      for(var id in taskStatusSet){
        var label = taskStatusSet[id].name;
        idLabelSet[id] = label;
        //labelIdSet[taskStatusSet[id].name] = id;
      }
      return idLabelSet;
    };

    /**
     * 请求任务数据
     * @param resourceObj
     *          可能属性:
     *          currentStatusId: 任务状态id,没有指定时全部pagination都请求
     *          conditionObj: condition条件对象,可不指定
     *
     *          changeStatusObj:没有指定时为非状态转换的getResource
     *                          有指定时,必有属性sourceStatusId,targetStatusId
     *
     */
    paginationHelper.prototype.getResource = function(resourceObj){
      resourceObj = resourceObj || {};
      if(!resourceObj.hasOwnProperty('conditionObj')){
        resourceObj.conditionObj = {};
      }


      if(!resourceObj.hasOwnProperty('changeStatusObj')){
        //有指定currentStatusId时,特定状态的任务
        if(resourceObj.hasOwnProperty('currentStatusId')){

          this._getParticularResource(resourceObj['currentStatusId'], resourceObj.conditionObj);
        }
        else{
          for(var curStatusId in this.paginationSet){
            this._getParticularResource(curStatusId, resourceObj.conditionObj, true);

          }
        }
      }
      //如果是from to的状态改变
      else{
        this._getParticularResource(resourceObj['changeStatusObj']['sourceStatusId'], resourceObj.conditionObj);
        this._getParticularResource(resourceObj['changeStatusObj']['targetStatusId'], resourceObj.conditionObj);
      }

    };

    paginationHelper.prototype._getParticularResource = function(curStatusId, conditionObj, resetCurrentPage){
      var curGetOps = this.getOpsSet[curStatusId];

      if(conditionObj.hasOwnProperty('priority_id')){
        ClassHelperService.update(conditionObj, curGetOps);
      }else{
        if(curGetOps.hasOwnProperty('priority_id')){
          delete curGetOps['priority_id'];
        }
      }

      if(resetCurrentPage){
        this.paginationSet[curStatusId].getResource({
          resetCurrentPage: true
        });
      }else{
        this.paginationSet[curStatusId].getResource();

      }
    };

    return {
      createPagionationHelper: function(taskSet, taskStatusSet, stateParams, conditionObj){
        return new paginationHelper(taskSet, taskStatusSet, stateParams, conditionObj);
      }



    }
  }]);//End of --> ng-factory: TaskPaginationHelperService

  /**
   * 方法 getStatusSet 会返回以Id为索引的类数组对象, 该对象以Promise对象形式封装
   */
  projectModule.factory('TaskStatusService', ['$rootScope', 'ResourceService', function($rootScope, ResourceService){
    return {
      accessor: ResourceService.getResourceAccessor({
        'resourceName': 'task-status'
      }),
      getStatusSet: function(){
        return this.accessor.get()
          .then(function(resp){
            var statusSet = {};

            resp.data.forEach(function(elem){
              statusSet[ elem.id ] = elem;
            });

            return statusSet;
          }, function(data){
            $rootScope.$emit('message:error', {
              title: '任务状态数据加载失败',
              msg: data.error
            });
          });
      }
    };
  }]);//End of --> ng-factory: TaskStatusService

  /**
   * TaskPriorityService 用于对获取或处理任务的优先级信息
   */
  projectModule.factory('TaskPriorityService', ['ResourceService', function(ResourceService) {
    return {
      accessor: ResourceService.getResourceAccessor({
        'resourceName': 'task-priority'
      })
    };
  }]);//End of --> TaskPriorityService


  /**
   * 用于处理任务页面按状态进行分页加载所需要的滚动事件监听
   */
  projectModule.factory('StatusScrollService', ['ScrollService', function(ScrollService){
    return {
      init: function(){
        var $statusPanel = angular.element.find('.status-panel');
        $statusPanel.each(function(){
          console.log($(this));
        });

      }
    };//End of --> return
  }]);//End of --> StatusScrollService
});