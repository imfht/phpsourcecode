/**
 * Created by spatra on 15-3-4.
 */

define(['angular', 'libraryJS/classHelper/ng-require'], function(angular, classHelperModule){
  var pagModule = angular.module('TeamMindmap.library.pagination', [classHelperModule]);
  var baseUrl = 'ngApp/library/pagination/';
  /**
   * 该服务用于滚动条滚动相关的事件机制
   */
  pagModule.factory('ScrollService', ['$rootScope',
    function($rootScope){

      return {
        /**
         * 初始化滚动条服务,当滚动条到底部时, 自根作用域往下广播一个事件
         * @param jQueryLiteElement 滚动的jQuery元素或对应的 jQuery 选择符
         * @param identification 向下的事件名
         * @param transObj 传给事件的对象
         */
        init: function(jQueryLiteElement, identification, transObj){
          if( typeof jQueryLiteElement !== 'object' ){
            jQueryLiteElement = angular.element(jQueryLiteElement);
          }

          var elementDom  = jQueryLiteElement.get(0);
          var elementOffsetHeight, elementScrollTop, elementScrollHeight;

          elementDom.onmousewheel = function(){
            //HTML元素的视口高度
            elementOffsetHeight = elementDom.offsetHeight;
            //HTML元素的文档高度（视口高度加上滚动高度）
            elementScrollHeight = elementDom.scrollHeight;
            //滚动条的垂直位置
            elementScrollTop = elementDom.scrollTop;

            if(elementScrollTop + elementOffsetHeight === elementScrollHeight){
              $rootScope.$broadcast('scroll:' + identification,  transObj);
            }
          };
        }
      };
  }]);//End of --> ng-factory: ScrollService

  /**
   *
   */
  pagModule.factory('PaginationService', ['$rootScope', 'ClassHelperService',
    function($rootScope, ClassHelperService){

      function Pagination(options){
        var copiedOpts = ClassHelperService.clone(options);

        this.currentPage = copiedOpts['currentPage'];
        this.itemsPerPage = copiedOpts['itemsPerPage'];
        this.totalItems = copiedOpts['totalItems'];

        this.state = 'canLoadMore';
        this.checkLoadAll();

        this._resourceList = options['resourceList'];
        this._resourceGetMethod = copiedOpts['resourceGetMethod'];
        this._getResourceOps = options['getResourceOps'];
        this._ngPromiseHandle = options['ngPromiseHandle'];
        this._listener = null;
      }

      Pagination.prototype = {
        constructor: Pagination,
        isSingle: function(){
          return this.totalItems <= this.itemsPerPage;
        },
        listenChange: function(){
          throw "不能直接使用Pagination";
        },
        init: function(){
          var self = this;

          if( self.isSingle() ){
            return false;
          }
          else{
            self.listenChange();
            return true;
          }
        },
        cancelListening: function(){
          var self = this;

          if( self._listener !== null ){
            var listeners = self._listener;


            if(  ! (listeners instanceof Array) ){
              listeners = [self._listener];
            }

            listeners.forEach(function(item){
              item();
            });

            self._listener = null;
          }

        },
        _isLoadedAll: function() {
          var self = this;
          return (self.currentPage == Math.ceil(self.totalItems / self.itemsPerPage))
                || self.totalItems == 0;
        },
        checkLoadAll: function(){
          var self = this;

          if( self._isLoadedAll() ){
            if( self.isSingle() ){
              self.state = 'single';
            }
            else{
              self.state = 'loadedAll';
            }
          }
          else{
            self.state = 'canLoadMore';
          }

          self.listenChange();
        },
        /**
         * 生成分页的 `get`查询参数
         */
        makePagQueryOps: function(){
          throw '不能在pagination中调用方法xxx';
        },
        /**
         * 生成请求资源的 `get`参数，实际上是传递给 ng 当中 $http.get 的第二个参数
         *
         * @param baseOps 除去分页之外的其他请求资源的参数(如果不是 {params: **}形式的，会被当成只有 `get` 查询参数，
         *                并被存放于最后生成对象的 params 属性中).
         * @param pagOps 与分页相关的请求资源的参数, 可选参数（如果不传入则调用 makePagQueryOps， 得到的结果当成缺省参数）
         * @returns {{}}
         */
        makeResourceGetOps: function(baseOps, resetCurrentPage, pagOps){
          var self = this;

          pagOps = pagOps || self.makePagQueryOps(resetCurrentPage);

          return self.resourceGetOpsHelper(baseOps, pagOps);
        },
        resourceGetOpsHelper: function(baseOps, pagOps){

          var resultOps = {};

          if( baseOps['params'] !== undefined && baseOps['params'] !== null ){
            resultOps = ClassHelperService.clone(baseOps);
          }
          else{
            resultOps['params'] = ClassHelperService.clone(baseOps);
          }

          ClassHelperService.update(pagOps, resultOps['params']);

          return resultOps;
        },
        getResource: function(options){
          var self = this;
          options = options || {};

          self.cancelListening();

          var getOps = self.makeResourceGetOps(self._getResourceOps, options['resetCurrentPage']);

          self.state = 'loading';


          var ngPromise = self._resourceGetMethod(getOps).then(function(resp){
            return self._resourceSuccessCallBack(resp);
          }, function(resp){
            return self._resourceFailCallBack(resp);
          });

          if( typeof self._ngPromiseHandle === 'function' ){
            self._ngPromiseHandle(ngPromise);
          }
        },
        update: function(options){
          this.getResource(options);
        },
        _resourceSuccessCallBack: function(resp){
          var self = this;
          self._resourceList.length = 0;
          Array.prototype.push.apply(self._resourceList, resp.data.data);

          self.currentPage = resp.data['current_page'];
          self.itemsPerPage = resp.data['per_page'];
          self.totalItems = resp.data['total'];

          self.checkLoadAll();

          return resp;
        },
        _resourceFailCallBack: function(resp){
          self.state = 'loadedFailure';

          return resp;
        },

        getState: function(){
          return this.state;
        }
      };


      /**
       * NumberPagination
       *
       * @param options
       * @constructor
       */
      function NumberPagination(options){
        Pagination.call(this, options);
      }

      ClassHelperService.extend(NumberPagination, Pagination);

      ClassHelperService.extendOrOverloadMethod(NumberPagination, 'listenChange', function(){
        var self = this;
        if( self._listener ){
          self.cancelListening();
        }

        self._listener = $rootScope.$on('Pagination:numberLoadMore', function(e, pageInfo){

          if( pageInfo ){
            ClassHelperService.update(pageInfo, self);
          }

          self.getResource();
        });

      });

      ClassHelperService.extendOrOverloadMethod(NumberPagination, 'makePagQueryOps', function(resetCurrentPage){
        var self = this;

        if( resetCurrentPage === true ){
          self.currentPage = 1;
          self.checkLoadAll();
        }

        return {
          per_page: this.itemsPerPage,
          page: this.currentPage
        };

      });

      /**
       * ScrollPagination
       *
       * @param options
       * @constructor
       */
      function ScrollPagination(options){
        Pagination.call(this, options);

        this._eventName = options['eventName'];
        this._per_page_step = options['itemsPerPage'];
        this._type = options['type'] || 'loadAll';

      }

      ClassHelperService.extend(ScrollPagination, Pagination);

      ClassHelperService.extendOrOverloadMethod(ScrollPagination, 'listenChange', function(){
        var self = this;
        if( self._isLoadedAll() ) {
          return;
        }

        if( self._listener ){
          self.cancelListening();
        }

        self._listener = $rootScope.$on(self._eventName, function(e){


          if( self._type === 'loadAll' ){
            self.itemsPerPage += self._per_page_step;
          }
          else if( self._type === 'loadPartition' ){
            ++self.currentPage;
          }

          self.getResource();
        });
      });

      ClassHelperService.extendOrOverloadMethod(ScrollPagination, 'makePagQueryOps', function(resetCurrentPage){
        if( resetCurrentPage === true ){
          this.currentPage = 1;
          this.itemsPerPage = this._per_page_step;
          this.checkLoadAll();
        }

        return {
          per_page: this.itemsPerPage,
          page: this.currentPage
        };

      });

      ClassHelperService.extendOrOverloadMethod(ScrollPagination, '_resourceSuccessCallBack', function(resp){
        var self = this;

        if( self._type === 'loadAll' ){
          return ScrollPagination.uber._resourceSuccessCallBack.apply(self, arguments);
        }
        else if( self._type === 'loadPartition' ){
          if( self._resourceList.length % self.itemsPerPage !== 0 ){
            self._resourceList.length = (self.currentPage - 1) * self.itemsPerPage;
          }

          Array.prototype.push.apply(self._resourceList, resp.data.data);

          self.currentPage = resp.data['current_page'];
          self.itemsPerPage = resp.data['per_page'];
          self.totalItems = resp.data['total'];

          self.checkLoadAll();

          return resp;
        }
      });

      ClassHelperService.extendOrOverloadMethod(ScrollPagination, 'update', function(options){
        var self = this;

        if( self._type === 'loadAll' || !options['added'] ){
          ScrollPagination.uber.update.apply(self, arguments);
        }
        else{
          if( self.totalItems && self._isLoadedAll() && self._resourceList.length % self.itemsPerPage === 0 ){
            ++self.currentPage;
          }

          self.getResource(options);
        }
      });

      /*

       */
      return {
        createPagination: function(type, ops){
          var newObj = null;

          switch (type){
            case 'scroll':
              newObj = new ScrollPagination(ops);
              break;
            case 'number':
              newObj = new NumberPagination(ops);
              break;
            default :
              throw '错误的Pagination类型';
              break;
          }

          return newObj;
        },
        resourceGetOpsHelper: Pagination.prototype.resourceGetOpsHelper,
        convertMethodToNormalFun: function(obj, method){
          if(typeof(method) ===  'string'){
            return function(){
              return obj[method].apply(obj, arguments);
            }
          }else if(typeof(method) === 'function'){
            return function() {
              return method.apply(obj, arguments);
            }
          }

        }
      };
  }]);//End of --> ng-factory: PaginationService

  pagModule.directive('numberPagination', ['$rootScope',
    function($rootScope){

      return {
        restrict: 'EA',
        templateUrl: baseUrl + 'number-pagination.html',
        replace: false,
        scope: {
          paginationObj: '='
        },
        link: function(scope){
          var lastInfo = {
            currentPage: scope.paginationObj.currentPage,
            itemsPerPage: scope.paginationObj.itemsPerPage
          };

          scope.change = function(){
            if( lastInfo.currentPage != scope.paginationObj.currentPage
              || lastInfo.itemsPerPage != scope.paginationObj.itemsPerPage ){

              $rootScope.$broadcast('Pagination:numberLoadMore');

              lastInfo.currentPage = scope.paginationObj.currentPage;
              lastInfo.itemsPerPage = scope.paginationObj.itemsPerPage;
            }

          };
        }
      };
  }]);//End of --> ng-directive: numberPagination

  return pagModule.name;
});
