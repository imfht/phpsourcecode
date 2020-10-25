/**
 * Created by spatra on 14-12-3.
 */

define(['projectJS/ng-require', 'angular', 'angularMocks'], function(currentModule){

  describe('Unit: 项目模块services测试', function(){
    beforeEach( module(currentModule) );

    var $timeout;
    beforeEach(inject(function(_$timeout_){
      $timeout = _$timeout_;
    }));

    describe('ProjectService', function(){
      var ProjectService = null, $httpBackend = null;
      var testBaseUrl = '';

      beforeEach(inject(function(_ProjectService_, _$httpBackend_){
        ProjectService = _ProjectService_;
        testBaseUrl = ProjectService.accessor.getBaseUrl();
        $httpBackend = _$httpBackend_;
      }));

      it('method: getAllProjects', function(){
        var mockParams = {
          per_page: 100,
          page: 1,
          options: 'al;'
        };
        var mockRespData = {
          data: 'mock'
        };


        $httpBackend.expectGET(RegExp(testBaseUrl)).respond(200, mockRespData);
        ProjectService.getAllProjects().then(function(data){
          expect(data).toEqual(mockRespData.data);
        });
        $httpBackend.flush();

      });

      it('method: getCreaterAndMembers', function(){
        var testProjectId = 3;
        var mockRespData = {
          members: [],
          creater: {}
        };

        $httpBackend.expectGET(testBaseUrl + '/' + testProjectId).respond(200, mockRespData);
        ProjectService.getCreaterAndMembers(testProjectId).then(function(data){
          expect(data).toBeDefined();
          expect(data instanceof Array).toBeTruthy();
        });
        $httpBackend.flush();
      });

    });

    describe('TaskService', function(){
      var $httpBackend, TaskService, NestedResourceService;
      var mockProjectId = 1;
      var testBaseUrl = 'api/project/' + mockProjectId + '/task';

      var taskSetMock = [
        {
          id: "1",
          name: "exampleOne",
          status_id: "1"
        },
        {
          id: "2",
          name: "exampleTwo",
          status_id: "1"
        },
        {
          id: "3",
          name: "exampleThree",
          status_id: "2"
        },
        {
          id: "4",
          parent_id: "1", //由于有父级id，此任务不应该被归入
          name: "exampleFour",
          status_id: "3"
        }
      ];

      beforeEach(inject(function(_$httpBackend_, _TaskService_, _NestedResourceService_){
        $httpBackend = _$httpBackend_;
        NestedResourceService = _NestedResourceService_;
        TaskService = _TaskService_;

        TaskService.accessor['setParentResourceId']({ projectId: mockProjectId });
      }));

      it('测试是成功生成了资源访问器（Accessor）', function(){
        expect(TaskService.accessor).toBeDefined();
        expect( TaskService.accessor instanceof NestedResourceService.getFactoryFunction() ).toBe(true);
      });

      it('Method: ShowWithCache', function(){
        var mockData = {
          id: 1, name: 'mock'
        };

        $httpBackend.expectGET(testBaseUrl + '/' + mockData.id).respond(200, mockData);

        TaskService.showWithCache(mockData.id);
        TaskService.showWithCache(mockData.id); //再次请求， 但并没有发出请求而是从缓存中读取

        $httpBackend.flush();
      });

      it('Method: finished', function(){
        var mockData = { id: 1, status: 'finished' };

        $httpBackend.expectPUT(testBaseUrl + '/' + mockData.id, {
          status: mockData.status
        }).respond(200);

        TaskService.finished(mockData.id);

        $httpBackend.flush();
      });

      it('method: convertToCondItems', function(){
        var mockData = [
          {'id': 1, 'name': 'changed'},
          {'id': 2, 'name': 'mock'}
        ];

        var result = TaskService.convertToCondItems(mockData, 'id', 'name');

        var one = result[0];

        expect(one['cond']).toEqual(mockData[0]['id']);
        expect(one['label']).toEqual(mockData[0]['name']);
      });

      it('method: buildFiltersFun', function(){
        var mockCondObj = {
          'priority_id': '1',
          'datetimes': 'today'
        };

        var filterFun = TaskService.buildFiltersFun(mockCondObj);
        var mockObj = {
          'priority_id': '1',
          'expected_at': new Date
        };

        expect( filterFun(mockObj)).toBeTruthy();
      });

    });//End of --> TaskService

    describe('MemberService', function(){
      var $httpBackend, MemberService, NestedResourceService;
      var mockProjectId = 1;

      beforeEach(inject(function(_$httpBackend_, _MemberService_, _NestedResourceService_){
        $httpBackend = _$httpBackend_;
        NestedResourceService = _NestedResourceService_;
        MemberService = _MemberService_;

        MemberService.accessor['setParentResourceId']({ projectId: mockProjectId });
      }));

      it('测试是成功生成了资源访问器（Accessor）', function(){
        expect( MemberService.accessor).toBeDefined();
        expect( MemberService.accessor instanceof NestedResourceService.getFactoryFunction() ).toBe(true);
      });
    });//End of --> MemberService

  });
});