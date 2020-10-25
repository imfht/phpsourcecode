/**
 * Created by spatra on 15-3-5.
 */

define(['libraryJS/pagination/ng-require', 'angular', 'angularMocks'], function(currentModule){

  beforeEach(module(currentModule));

  describe('PaginationService', function(){
    var PaginationService, $rootScope, $timeout;
    var PaginationTypes = ['number', 'scroll'];

    beforeEach(inject(function(_PaginationService_, _$rootScope_, _$timeout_){
      PaginationService = _PaginationService_;
      $rootScope = _$rootScope_;
      $timeout = _$timeout_;
    }));

    describe('method: createPagination', function(){
      it('type: singlePag', function(){
        var mockOps = {
          currentPage: 1,
          itemsPerPage: 10,
          totalItems: 5,
          resourceList: [],
          resourceGetMethod: function(){},
          getResourceOps: {}
        };

        function checkSingleStatus(type){
          var pagObj = PaginationService.createPagination(type, mockOps)
          expect(pagObj.isSingle()).toBeTruthy();
          pagObj.totalItems += pagObj.itemsPerPage;
          expect(pagObj.isSingle()).toBeFalsy();
        }

        PaginationTypes.forEach(function(item){
          checkSingleStatus(item);
        });

      });//End of --> type: singlePag

      describe('', function(){

        var $http, $httpBackend;
        var resourceBaseUrl = 'resource/getUrl';
        var mockOps = null;

        beforeEach(inject(function(_$http_, _$httpBackend_){
          $http = _$http_;
          $httpBackend = _$httpBackend_;

          mockOps = {
            currentPage: 1,
            itemsPerPage: 5,
            totalItems: 25,
            resourceList: [],
            resourceGetMethod: function(getOps){
              return $http.get(resourceBaseUrl, getOps);
            },
            getResourceOps: {
              'fileCond': 'mockValue'
            }
          };
        }));

        it('method: makePagQueryOps', function(){
          var numObj = PaginationService.createPagination('number', mockOps);

          expect(numObj.init()).toBeTruthy();
          expect(numObj.makePagQueryOps()).toEqual({
            per_page: mockOps.itemsPerPage,
            page: mockOps.currentPage
          });

          var scrollObj = PaginationService.createPagination('scroll', mockOps);
          expect(scrollObj.init()).toBeTruthy();
          expect(scrollObj.makePagQueryOps()).toEqual({
            per_page: mockOps.itemsPerPage * mockOps.currentPage,
            page: 1
          });
        });

        describe('method: makeResourceGetOps', function(){
          var mockOtherOps = {
            'mockOtherOps': 'mockOtherOps'
          };

          function checkMakeResourceGetOps(objType){
            var obj = PaginationService.createPagination(objType, mockOps);

            var expectObj = {
              params: {
                'mockOtherOps': 'mockOtherOps',
                'page': mockOps.currentPage,
                'per_page': mockOps.itemsPerPage
              }
            };

            expect(obj.init()).toBeTruthy();
            expect(obj.makeResourceGetOps(mockOtherOps)).toEqual(expectObj);

            mockOtherOps = {
              params: {
                'mockOtherOps': 'mockOtherOps'
              }
            };

            expect(obj.makeResourceGetOps(mockOtherOps)).toEqual(expectObj);
          }

          PaginationTypes.forEach(function(item){
            it(item, function(){
              checkMakeResourceGetOps(item);
            });
          });

        });

        describe('method: getResource', function(){
          var mockRespData = {
            per_page: 5,
            current_page: 1,
            total: 23,
            data: ['mockItem']
          };

          function getRequestUrl(obj){
            var queryOps = obj.makeResourceGetOps(mockOps.getResourceOps)['params'];

            return resourceBaseUrl + '?fileCond=' + queryOps['fileCond']
              + '&page=' + queryOps['page'] + '&per_page=' + queryOps['per_page'];
          }

          function checkGetResource(objType){
            var obj = PaginationService.createPagination(objType, mockOps);

            expect(obj.init()).toBeTruthy();

            $httpBackend
              .expectGET(getRequestUrl(obj))
              .respond(200, mockRespData);
            obj.getResource();
            $httpBackend.flush();

            expect(obj.state).toEqual('canLoadMore');
            expect(obj.itemsPerPage).toEqual(mockRespData['per_page']);
            expect(obj.totalItems).toEqual(mockRespData['total']);
            expect(mockOps.resourceList).toEqual(mockRespData['data']);
          }

          PaginationTypes.forEach(function(item){
            it(item, function(){
              checkGetResource(item);
            });
          });

        });//End of --> describe: method: getResource

        describe('method: checkLoadAll', function(){

          function check(objType, mockOps){
            var obj = PaginationService.createPagination(objType, mockOps);

            spyOn(obj, 'listenChange');
            obj.checkLoadAll();
            expect(obj.state).toEqual('canLoadMore');
            expect(obj.listenChange).toHaveBeenCalled();

            obj.totalItems %= obj.itemsPerPage;
            obj.checkLoadAll();
            expect(obj.state).toEqual('single');

            obj.totalItems = obj.itemsPerPage * 1.5;
            obj.currentPage = 2;
            obj.checkLoadAll();
            expect(obj.state).toEqual('loadedAll');
          }

          PaginationTypes.forEach(function(item){
            var mockOps = {
              currentPage: 1,
              itemsPerPage: 10,
              totalItems: 15
            };

            it(item, function(){
              check(item, mockOps);
            });
          });
        });//End of --> describe: checkLoadAll

      });


    });//End of --> describe: `method: createPagination`

  });//End of --> describe: PaginationService

});//End of --> requirejs: define