/**
 * Created by spatra on 15-3-5.
 */

define(['libraryJS/userManager/ng-require', 'angular', 'angularMocks'], function(currentModule){

  //注入当前的ngModule
  beforeEach(module(currentModule, function($provide){
    $provide.value('$window', {location: ''});
  }));

  describe('LoginStatusService', function(){
    var LoginStatusService, $cookieStore, $rootScope, $window;
    var mockData = {
      personalInfo: {name: 'mock'},
      uri: {'logout': 'logout'}
    };
    var dataKey = 'loginData';

    beforeEach(inject(function(_LoginStatusService_, _$cookieStore_, _$rootScope_, _$window_){
      LoginStatusService = _LoginStatusService_;
      $cookieStore = _$cookieStore_;
      $rootScope = _$rootScope_;
      $window = _$window_;

      $cookieStore.put(dataKey, mockData);
    }));

    it('method: init', function(){
      spyOn($rootScope, '$broadcast');
      spyOn($rootScope, '$on');

      LoginStatusService.init(dataKey);

      expect($rootScope.$broadcast).toHaveBeenCalledWith("loginStatus:init");
      expect($rootScope.$on).toHaveBeenCalledWith("personalInfo:update", LoginStatusService.updatePersonalInfo);
      expect($cookieStore.get(dataKey)).toBeUndefined();
    });

    describe('testing for initialed of LoginStatusService', function(){

      beforeEach(function(){
        LoginStatusService.init(dataKey);
      });

      it('method: get', function(){
        expect(LoginStatusService.get('personalInfo.name')).toEqual( mockData['personalInfo']['name'] );
        expect(LoginStatusService.get('notExist')).toBeUndefined();
      });

      it('method: logout', function(){
        spyOn($rootScope, '$broadcast');

        LoginStatusService.logout();

        expect($rootScope.$broadcast).toHaveBeenCalledWith('loginStatus:logout');
        expect($window.location).toEqual(mockData.uri.logout);
      });

      it('method: isLogined', function(){
        expect(LoginStatusService.isLogined()).toBeTruthy();

        LoginStatusService.logout();

        expect(LoginStatusService.isLogined()).toBeFalsy();
      });

      it('method: updatePersonalInfo', function(){
        var $httpBackend = null;
        var mockRespData = {
          'name': 'mockValue'
        };
        inject(function(_$httpBackend_){
          $httpBackend = _$httpBackend_;
        });

        $httpBackend.expectGET('api/personal/info').respond(200, mockRespData);
        LoginStatusService.updatePersonalInfo();
        $httpBackend.flush();

        expect(LoginStatusService.get('personalInfo')).toEqual(mockRespData);
      });
    });//End of --> describe: testing for initialed of LoginStatusService

  });//End of --> describe: LoginStatusService

  describe('UserUnreadService', function(){
    var UserUnreadService, LoginStatusService, $rootScope, $cookieStore;
    var dataKey = 'loginData';
    var mockLoginData = {
      unread: {
        'notification': 1,
        'message': 2
      }
    };

    beforeEach(inject(function(_UserUnreadService_, _LoginStatusService_, _$rootScope_, _$cookieStore_){
      LoginStatusService = _LoginStatusService_;
      UserUnreadService = _UserUnreadService_;
      $rootScope = _$rootScope_;
      $cookieStore = _$cookieStore_;

      $cookieStore.put(dataKey, mockLoginData);
    }));

    it('method: init', function(){
      spyOn($rootScope, '$on');

      UserUnreadService.init();

      expect($rootScope.$on).toHaveBeenCalledWith('unread:update', UserUnreadService.update);
      expect($rootScope.$on).toHaveBeenCalledWith('task:reload', UserUnreadService.update);

    });

    it('method: update', function(){

      LoginStatusService.init(dataKey);

      var $httpBackend = null;
      var mockRespData = {
        'notification': mockLoginData.unread.notification + 1,
        'message': mockLoginData.unread.message + 1
      };

      inject(function(_$httpBackend_){
        $httpBackend = _$httpBackend_;
      });

      spyOn($rootScope, '$emit');

      $httpBackend.expectGET('ng/unread').respond(200, mockLoginData.unread);
      UserUnreadService.update();
      $httpBackend.flush();
      expect(LoginStatusService.get('unread')).toEqual(mockLoginData.unread);


      $httpBackend.expectGET('ng/unread').respond(404);
      UserUnreadService.update();
      $httpBackend.flush();
      expect($rootScope.$emit).toHaveBeenCalledWith('message:error', {
        title: '请检查网络',
        msg: '更新未读私信或通知失败'
      });


      $httpBackend.expectGET('ng/unread').respond(200, mockRespData);
      UserUnreadService.update();
      $httpBackend.flush();
      expect(LoginStatusService.get('unread')).toEqual(mockRespData);
      //expect($rootScope.$emit).toHaveBeenCalledWith('message:info', {msg: '有新的通知或私信，请查阅'});
    });

  });//End of --> describe: UserUnreadService

});//End of --> requirejs: define