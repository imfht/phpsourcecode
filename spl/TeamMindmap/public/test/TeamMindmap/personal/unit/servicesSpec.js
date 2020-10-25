/**
 * Created by spatra on 14-12-6.
 */

define(['personalJS/ng-require', 'angular', 'angularMocks'], function(personalModule) {

  describe('Unit: 个人模块 Services 测试', function () {

    beforeEach(module(personalModule));

    var $timeout;
    beforeEach(inject(function(_$timeout_){
      $timeout = _$timeout_;
    }));

    describe('PersonalInfoService', function(){
      var PersonalInfoService, $httpBackend;
      var testBaseUrl = 'api/personal/';
      var mockData = {
        'username': 'mock',
        'email': 'mock'
      };

      function flush(expect, callback){
        $httpBackend.flush();
        $timeout.flush();

        expect(callback).toHaveBeenCalled();
      }

      beforeEach(inject(function(_PersonalInfoService_, _$httpBackend_){
        PersonalInfoService = _PersonalInfoService_;
        $httpBackend = _$httpBackend_;
      }));

      it('method: getInfo', function(){
        $httpBackend.expectGET(testBaseUrl + 'info').respond(200, mockData);

        var mustCalled = jasmine.createSpy('getInfo');
        PersonalInfoService.getInfo()
          .success(function(data){
            expect(data).toEqual(mockData);
            mustCalled();
          });

        flush(expect, mustCalled);
      });

      it('method: updateInfo', function(){
        $httpBackend.expectPUT(testBaseUrl + 'info', mockData).respond(200);

        var mustCalled = jasmine.createSpy('updateInfo');
        PersonalInfoService.updateInfo(mockData)
          .success(function(){
            mustCalled();
          });

        flush(expect, mustCalled);
      });

      it('method: password', function(){
        $httpBackend.expectPUT(testBaseUrl + 'password', mockData).respond(200);

        var mustCalled = jasmine.createSpy('updatePassword');
        PersonalInfoService.updatePassword(mockData)
          .success(function(){
            mustCalled();
          });

        flush(expect, mustCalled);
      });

      it('method: checkUpdatePasswordInfo', function(){
        var mockData = {
          password: '\t\n ',
          newPassword: 'mockPassword',
          newPassword_confirmation: 'mockPassword'
        };

        var mockMsg = {};

        expect(PersonalInfoService.checkUpdatePasswordInfo(mockData, mockMsg)).toBeFalsy();
        expect(mockMsg['password']).toBeDefined();
        mockData['password'] = 'old_password';
        expect(PersonalInfoService.checkUpdatePasswordInfo(mockData, mockMsg)).toBeTruthy();
        expect(mockMsg['password']).toBeUndefined();

        mockData['newPassword_confirmation'] = mockData['newPassword'] + "diff";
        expect(PersonalInfoService.checkUpdatePasswordInfo(mockData, mockMsg)).toBeFalsy();
        expect(mockMsg['newPassword']).toBeDefined();
      });

    });//End of --> PersonalInfoService

  });
});