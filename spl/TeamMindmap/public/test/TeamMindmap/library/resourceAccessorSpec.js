/**
 * Created by spatra on 15-3-5.
 */

define(['libraryJS/resourceAccessor/ng-require', 'angular', 'angularMocks'], function(currentModule){

  //注入当前的ngModule
  beforeEach( module(currentModule) );

  //对服务: ResourceService 进行测试
  describe('ResourceService', function(){
    var $httpBackend, ResourceService, testAccessor;
    var testBaseUrl = 'api_prefix/testResource';

    var mockData = {
      id: 123,
      name: 'mock',
      label: '测试'
    };

    //进行一些测试前的初始化工作
    beforeEach(inject(function(_$httpBackend_, _ResourceService_){
      $httpBackend = _$httpBackend_;
      ResourceService = _ResourceService_;
      testAccessor = ResourceService.getResourceAccessor({
        resourceName: 'testResource',
        apiPrefix: 'api_prefix'
      })
    }));

    afterEach(function(){
      $httpBackend.flush();
    });

    it('method: get', function(){
      var mockList = ['a', 'b', 'c'];
      $httpBackend.expectGET(testBaseUrl).respond(200, mockList);

      testAccessor.get()
        .success(function(data){
          expect(data).toEqual(mockList);
        });
    });

    it('method: store', function(){
      $httpBackend.expectPOST(testBaseUrl, mockData).respond(200, {id: mockData.id});

      testAccessor.store(mockData)
        .success(function(data){
          expect(data.id).toEqual(mockData.id);
        });
    });

    it('method: update', function(){
      $httpBackend.expectPUT(testBaseUrl + '/' + mockData.id, mockData).respond(200);

      testAccessor.update(mockData.id, mockData);
    });

    it('method: destroy', function(){
      $httpBackend.expectDELETE(testBaseUrl + '/' + mockData.id).respond(200);

      testAccessor.destroy(mockData.id);
    });

    it('method: show', function(){
      $httpBackend.expectGET(testBaseUrl + '/' + mockData.id).respond(200, mockData);

      testAccessor.show(mockData.id);

    });
  });// End of --> ResourceService

  //对服务: NestedResourceService 进行测试
  describe('NestedResourceService', function(){
    var $httpBackend, NestedResourceService, testAccessor;
    var testParentResourceName = 'testParent', testParentResourceId = 1 ,
      testNestResourceName = 'testNested', mockStateParams = {}, testApiPrefix = 'api_prefix';

    var testBaseUrl = [testApiPrefix, testParentResourceName,
      testParentResourceId, testNestResourceName]
      .join('/');

    var mockData = {
      id: 1,
      name: 'mock',
      label: '测试'
    };

    function initParentResource(){
      mockStateParams[ testParentResourceName + 'Id' ] = testParentResourceId;

      testAccessor['setParentResourceId'](mockStateParams);
    }

    beforeEach(inject(function(_$httpBackend_, _NestedResourceService_){
      $httpBackend = _$httpBackend_;
      NestedResourceService = _NestedResourceService_;
      testAccessor = NestedResourceService.getResourceAccessor({
        'parentResourceName': testParentResourceName,
        'nestedResourceName': testNestResourceName,
        apiPrefix: testApiPrefix
      });
    }));

    //注意, 在这里实现刷新
    afterEach(function(){
      $httpBackend.flush();
    });

    it('method: get', function(){
      initParentResource();
      var mockGetData = ['a', 'b', 'c'];
      $httpBackend.expectGET(testBaseUrl).respond(200, mockGetData);

      testAccessor.get()
        .success(function(data){
          expect(data).toEqual(mockGetData);
        });
    });

    it('method: show', function(){
      initParentResource();
      $httpBackend.expectGET(testBaseUrl + '/' + mockData.id).respond(200, mockData);

      testAccessor.show(mockData.id)
        .success(function(data){
          expect(data).toEqual(mockData);
        });
    });

    it('method: update', function(){
      initParentResource();
      $httpBackend.expectPUT(testBaseUrl + '/' + mockData.id).respond(200);

      testAccessor.update(mockData.id, mockData);
    });

    it('method: store', function(){
      initParentResource();
      $httpBackend.expectPOST(testBaseUrl, mockData).respond(200, {id: mockData.id});

      testAccessor.store(mockData)
        .success(function(data){
          expect(data.id).toEqual(mockData.id);
        });
    });

    it('method: destroy', function(){
      initParentResource();
      $httpBackend.expectDELETE(testBaseUrl + '/' + mockData.id).respond(200);

      testAccessor.destroy(mockData.id);
    });

  });//End of --> NestedResourceService

});