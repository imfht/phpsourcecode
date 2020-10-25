/**
 * Created by spatra on 14-12-14.
 */


define(['personalNotificationJS/ng-require', 'angular', 'angularMocks'], function(currentModule){

  //注入当前的ngModule
  beforeEach(module(currentModule));

  /*
  describe('Unit test: NotificationService', function(){

    var NotificationService, $httpBackend, $timeout;
    var testBaseUrl = '';

    beforeEach(inject(function(_NotificationService_, _$httpBackend_, _$timeout_){
      NotificationService = _NotificationService_;
      testBaseUrl = NotificationService.accessor.getBaseUrl();
      $timeout = _$timeout_;
      $httpBackend = _$httpBackend_;
    }));

    it('method: setRead', function(){
      var mockNotificationId = 3;

      $httpBackend.expectPUT(testBaseUrl + '/' + mockNotificationId, {'read': true}).respond(200);
      NotificationService.setRead(mockNotificationId);
      $httpBackend.flush();

      $httpBackend.expectPUT(testBaseUrl + '/' + mockNotificationId, {'read': false}).respond(200);
      NotificationService.setRead(mockNotificationId, false);
      $httpBackend.flush();
    });

    it('method: getStateObj', function(){
      var mockTypeList = {
        '1': 'system',
        '2': 'project',
        '3': 'projectTask',
        '4': 'ProjectDiscussion'
      };

      var mockNotification = {
        type_id: 2,
        project_id: 7
      };
      expect(NotificationService.getStateObj(mockNotification, mockTypeList)).toEqual({
        stateName: 'project.show.desktop',
        stateParams: {projectId: mockNotification.project_id}
      });

      mockNotification.type_id = '3';
      expect(NotificationService.getStateObj(mockNotification, mockTypeList)).toEqual({
        stateName: 'project.show.' + mockTypeList[mockNotification.type_id] + '.info',
        stateParams: {projectId: mockNotification.project_id}
      });
    });
  });// End of --> describe: Unit test: NotificationService

  describe('Unit test: NotificationTypeService', function(){
    var NotificationTypeService = null, $httpBackend = null, $timeout = null;
    var testBaseUrl = '';

    beforeEach(inject(function(_NotificationTypeService_, _$httpBackend_, _$timeout_){
      NotificationTypeService = _NotificationTypeService_;
      testBaseUrl = NotificationTypeService.accessor.getBaseUrl();
      $httpBackend = _$httpBackend_;
      $timeout = _$timeout_;
    }));

    it('method: getTypeLabelSet', function(){
      var mockRespData = [
        {id: 1, name: 'mockName', label: '测试标签', map: 'mockMap'}
      ];

      var spyObj = {
        fun: function(){}
      };
      spyOn(spyObj, 'fun');

      $httpBackend.expectGET(testBaseUrl).respond(200, mockRespData);
      NotificationTypeService.getTypeLabelSet().then(function(data){
        spyObj.fun();

        expect(data).toEqual({
          1: 'mockName'
        });
      });
      $httpBackend.flush();
      $timeout.flush();
      expect(spyObj.fun).toHaveBeenCalled();
    });


  });//End of --> describe: Unit test: NotificationTypeService

  });// End of --> Unit test: NotificationService
*/

});