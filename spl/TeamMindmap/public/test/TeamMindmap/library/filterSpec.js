/**
 * Created by spatra on 15-3-7.
 */

define(['libraryJS/filter/ng-require', 'angular', 'angularMocks'], function(currentModule){
  beforeEach( module(currentModule) );

  describe('BackendFilterService', function(){
    var BackendFilterService, $httpBackend;

    beforeEach(inject(function(_$httpBackend_, _BackendFilterService_){
      $httpBackend = _$httpBackend_;
      BackendFilterService = _BackendFilterService_;
    }));

    it('method: getMethods', function(){
      var mockRespData = {
        'key': 'value'
      };
      var mockResourceName = 'mock';

      $httpBackend.expectGET('api/backend-filter/' + mockResourceName).respond(200, mockRespData);
      BackendFilterService.getMethods(mockResourceName);
      $httpBackend.flush();

    });
  });//End of --> describe: BackendFilterService

});//End of --> requirejs: define