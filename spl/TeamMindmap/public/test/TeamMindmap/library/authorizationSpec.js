/**
 * Created by spatra on 15-3-18.
 */

define(['libraryJS/authorization/ng-require', 'angular', 'angularMocks'], function(testModule){

  beforeEach(module(testModule));

  describe('ProjectTaskAuthorizationService', function(){
    var ProjectTaskAuthorizationService = null, LoginStatusService = null;
    var mockProjectData = null;

    beforeEach(inject(function(_ProjectTaskAuthorizationService_, _LoginStatusService_, _$cookieStore_){
      ProjectTaskAuthorizationService = _ProjectTaskAuthorizationService_;

      LoginStatusService = _LoginStatusService_;
      _$cookieStore_.put('loginData', {
        personalInfo: {id: 1}
      });
      LoginStatusService.init();

      mockProjectData = {
        'creater': {id: 1},
        'editable': true
      };

    }));

    it('method: setProjectInfo', function(){
      ProjectTaskAuthorizationService.setProjectInfo(mockProjectData);

      expect(ProjectTaskAuthorizationService.getProjectInfo()).toEqual(mockProjectData);
    });

    it('method: buildChecker', function(){
      ProjectTaskAuthorizationService.setProjectInfo(mockProjectData);

      var checker = ProjectTaskAuthorizationService.buildChecker();

      expect(checker({creater_id: mockProjectData['creater']['id']})).toBeTruthy();
      expect(checker({creater_id: mockProjectData['creater']['id'] + 1})).toBeTruthy();

      mockProjectData['editable'] = false;
      ProjectTaskAuthorizationService.setProjectInfo(mockProjectData);
      checker = ProjectTaskAuthorizationService.buildChecker();
      expect(checker({creater_id: mockProjectData['creater']['id']})).toBeTruthy();

      mockProjectData['creater']['id'] = 10010;
      ProjectTaskAuthorizationService.setProjectInfo(mockProjectData);
      checker = ProjectTaskAuthorizationService.buildChecker();
      expect(checker({creater_id: mockProjectData['creater']['id'] + 3})).toBeFalsy();
    });
  });

});