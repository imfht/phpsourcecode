/**
 * Created by rockyren on 14/12/5.
 */
define(['angular','projectJS/controllers/member','angularMocks'],function(){
  describe('Unit: 项目模块member相关的controllers测试',function(){
    beforeEach(module('TeamMindmap.project'));

    var $controller, $rootScope, $httpBackend;
    beforeEach(inject(function(_$controller_,_$rootScope_,_$httpBackend_){
      $controller = _$controller_;
      $rootScope = _$rootScope_;
      $httpBackend = _$httpBackend_;
      $httpBackend.flush();
    }));

    describe('ProjectMemberController',function(){
      var ProjectMemberController, scope;
      beforeEach(function(){
        scope = $rootScope.$new();

        ProjectMemberController = $controller('ProjectMemberController',{$scope: scope});
      });
    });

  });
});