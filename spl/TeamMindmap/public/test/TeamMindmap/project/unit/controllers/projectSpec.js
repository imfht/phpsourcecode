/**
 * Created by spatra on 14-12-4.
 */
define(['angular', 'projectJS/controllers/project', 'angularMocks'], function(angular){

  describe('Unit: 项目模块controllers测试', function() {

    /*
      初始化 Project Module，以及对一些用到的内置服务进行初始化
     */
    beforeEach(module('TeamMindmap.project'));

    var $controller, $rootScope, $httpBackend, $stateParams;
    beforeEach(inject(function(_$controller_, _$rootScope_, _$stateParams_, _$httpBackend_){
      $controller = _$controller_;
      $rootScope = _$rootScope_;
      $httpBackend = _$httpBackend_;
      $stateParams = _$stateParams_;
    }));

    // End of --> `初始化 Project Module，以及对一些用到的内置服务进行初始化`



  });
});

