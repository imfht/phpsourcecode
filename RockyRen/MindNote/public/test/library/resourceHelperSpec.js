/**
 * Created by rockyren on 15/5/18.
 */

define(['libraryJS/resourceHelper/resourceHelper', 'angularMocks'], function(resourceHelper){



  describe('Unit: resourceHelper模块测试', function(){
    beforeEach(module(resourceHelper));


    //注入对应服务
    describe('ResourceService', function(){
      var $httpBackend, ResourceService;
      beforeEach(inject(function(_$httpBackend_, _ResourceService_){
        $httpBackend = _$httpBackend_;
        ResourceService = _ResourceService_;
      }));





      describe('Resource类测试', function(){
        var groupResource, groupTest;
        beforeEach(function(){
          //取得resource对象
          groupResource = ResourceService.resourceFactory({resourceName: 'group'});
          groupTest = testResource(groupResource, '/api/group');
        });
        //it('get方法测试', function(){
        //  groupTest.testGet($httpBackend);
        //});

        it('get方法测试', function(){
          $httpBackend.expectGET('/api/groups')
            .respond(200);

          groupResource.get();
          $httpBackend.flush();
        });


        it('show方法测试', function(){
          groupTest.testShow($httpBackend);
        });
        it('store方法测试', function(){
          groupTest.testStore($httpBackend);
        });

        it('update方法测试', function(){
          groupTest.testUpdate($httpBackend);
        });
        it('destroy方法测试', function(){
          groupTest.testDestroy($httpBackend);
        });



        //it('get方法测试', function(){
        //  $httpBackend.expectGET('/api/group/')
        //    .respond(200);
        //  groupResource.get();
        //  $httpBackend.flush();
        //});
        //it('show方法测试', function(){
        //  $httpBackend.expectGET('/api/group/' + '1')
        //    .respond(200);
        //  groupResource.show(1);
        //  $httpBackend.flush();
        //});
        //it('store方法测试', function(){
        //  $httpBackend.expectPOST('/api/group/')
        //    .respond(200);
        //  groupResource.store();
        //  $httpBackend.flush();
        //});
        //
        //it('update方法测试', function(){
        //  $httpBackend.expectPUT('/api/group/' + '1')
        //    .respond(200);
        //  groupResource.update(1);
        //  $httpBackend.flush();
        //});
        //it('destroy方法测试', function(){
        //  $httpBackend.expectDELETE('/api/group/' + '1')
        //    .respond(200);
        //  groupResource.destroy(1);
        //  $httpBackend.flush();
        //});

      });


      describe('NestedResource测试(note资源有notebook无group)', function(){
        var noteResource, noteTest;
        beforeEach(function(){
          noteResource = ResourceService.nestedResourceFactory({
              resourceName: 'note',
              parentResourceName: 'group|notebook'
            });
          noteResource.setParentId({
            notebookId: 2
          });
          noteTest = testResource(noteResource, '/api/notebook/2/note');
        });

        //it('get方法测试', function(){
        //  noteTest.testGet($httpBackend);
        //});

        it('get方法测试', function(){
          $httpBackend.expectGET('/api/notebook/2/notes')
            .respond(200);
          noteResource.get();

          $httpBackend.flush();
        });

        it('show方法测试', function(){
          noteTest.testShow($httpBackend);
        });
        it('store方法测试', function(){
          noteTest.testStore($httpBackend);
        });

        it('update方法测试', function(){
          noteTest.testUpdate($httpBackend);
        });
        it('destroy方法测试', function(){
          noteTest.testDestroy($httpBackend);
        });


      });

      describe('NestedResource测试(note资源有group有notebook)', function(){
        var noteResource, noteTest;
        beforeEach(function(){
          noteResource = ResourceService.nestedResourceFactory({
            resourceName: 'note',
            parentResourceName: 'group|notebook'
          });
          noteResource.setParentId({
            groupId: 1,
            notebookId: 2
          });

          noteTest = testResource(noteResource, '/api/group/1/notebook/2/note');
        });
        //it('get方法测试', function(){
        //  noteTest.testGet($httpBackend);
        //});

        it('get方法测试', function(){
          $httpBackend.expectGET('/api/group/1/notebook/2/notes')
            .respond(200);
          noteResource.get();

          $httpBackend.flush();
        });

        it('show方法测试', function(){
          noteTest.testShow($httpBackend);
        });
        it('store方法测试', function(){
          noteTest.testStore($httpBackend);
        });

        it('update方法测试', function(){
          noteTest.testUpdate($httpBackend);
        });
        it('destroy方法测试', function(){
          noteTest.testDestroy($httpBackend);
        });

      })


    });

  });

  function testResource(resource, expectUrl){
    return {
      //testGet: function($httpBackend){
      //  $httpBackend.expectGET(expectUrl)
      //    .respond(200);
      //
      //  resource.get();
      //  $httpBackend.flush();
      //},
      testShow: function($httpBackend){
        $httpBackend.expectGET(expectUrl + '/' + '1')
          .respond(200);
        resource.show(1);
        $httpBackend.flush();
      },
      testStore: function($httpBackend){
        $httpBackend.expectPOST(expectUrl)
          .respond(200);
        resource.store();
        $httpBackend.flush();
      },
      testUpdate: function($httpBackend){
        $httpBackend.expectPUT(expectUrl + '/' + '1')
          .respond(200);
        resource.update(1);
        $httpBackend.flush();
      },
      testDestroy: function($httpBackend){
        $httpBackend.expectDELETE(expectUrl + '/' + '1')
          .respond(200);
        resource.destroy(1);
        $httpBackend.flush();
      }

    }
  }
});
