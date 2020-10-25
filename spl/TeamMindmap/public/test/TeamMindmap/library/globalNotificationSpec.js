/**
 * Created by spatra on 15-3-7.
 */

define(['libraryJS/globalNotification/ng-require', 'angular', 'angularMocks'], function(currentModule, angular){

  describe('GlobalNotification', function(){

    describe('Provider', function(){
      var globalNotificationProvider;

      beforeEach(function(){
        angular.module('testModule', []).config(function(GlobalNotificationProvider){
          globalNotificationProvider = GlobalNotificationProvider;
        });

        module(currentModule, 'testModule');
        inject(function(GlobalNotification){
          //注入GlobalNotification
        });
      });

      it('method: setOptions', function(){
        expect(function(){
          globalNotificationProvider.setOptions('a string')
        }).toThrow();

        globalNotificationProvider.setOptions({
          title: 'mockTitle'
        });
        expect(globalNotificationProvider._options['title']).toEqual('mockTitle');
        expect(globalNotificationProvider._options['msg']).toBeDefined();
      });

      it('method: getTemplateUri', function(){
        expect(globalNotificationProvider.getTemplateUri())
          .toEqual('ngApp/library/globalNotification/notification-template.html');
      });

    });

    describe('Service', function(){
      var GlobalNotification, scope;

      beforeEach(module(currentModule));
      beforeEach(inject(function(_GlobalNotification_, _$rootScope_){
        GlobalNotification = _GlobalNotification_;
        scope = _$rootScope_.$new();
      }));

      it('method: init', function(){
        spyOn(scope, '$on');
        GlobalNotification.init(scope);
        expect(scope.$on).toHaveBeenCalled();
      });

    });

  });//End of --> describe: GlobalNotification
});