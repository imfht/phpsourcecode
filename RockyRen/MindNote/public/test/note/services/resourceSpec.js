/**
 * Created by rockyren on 15/5/18.
 */

define(['noteJS/ng-require', 'angularMocks'], function(noteModuleName){
  describe('Unit: note的resource服务', function(){
    beforeEach( module(noteModuleName) );

    var $httpBackend, NoteService, CatalogueService;
    beforeEach(inject(function(_$httpBackend_, _NoteService_, _CatalogueService_){
      $httpBackend = _$httpBackend_;
      NoteService = _NoteService_;
      CatalogueService = _CatalogueService_;
    }));

    describe('NoteService', function(){
      var noteAccessor;
      beforeEach(function(){
        noteAccessor = NoteService.accessor;
        noteAccessor.setParentId({
          groupId: 1,
          notebookId: 2,
          noteId: 100      //添加一个多余的id
        });
      });

      it('get方法测试', function(){
        $httpBackend.expectGET('/api/group/1/notebook/2/notes')
          .respond(200);
        noteAccessor.get();
        $httpBackend.flush();
      });

      it('show方法测试', function(){
        $httpBackend.expectGET('/api/group/1/notebook/2/note/3')
          .respond(200);
        noteAccessor.show(3);
        $httpBackend.flush();
      });

      it('store方法测试', function(){
        $httpBackend.expectPOST('/api/group/1/notebook/2/note')
          .respond(200);
        noteAccessor.store();
        $httpBackend.flush();
      });

      it('update方法测试', function(){
        $httpBackend.expectPUT('/api/group/1/notebook/2/note/3')
          .respond(200);
        noteAccessor.update(3);
        $httpBackend.flush();
      });

      it('delete方法测试', function(){
        $httpBackend.expectDELETE('/api/group/1/notebook/2/note/3')
          .respond(200);
        noteAccessor.destroy(3);
        $httpBackend.flush();
      });
    });

    describe('NoteService测试(无group和notebook)', function(){
      var noteAccessor;
      beforeEach(function(){
        noteAccessor = NoteService.accessor;
        noteAccessor.setParentId({
          groupId: null,
          notebookId: null
        });
      });

      it('get方法测试', function(){
        $httpBackend.expectGET('/api/notes')
          .respond(200);
        noteAccessor.get();
        $httpBackend.flush();
      });
    });

    describe('CatalogueService测试', function(){
      var CatalogueAccessor;
      beforeEach(function(){
        CatalogueAccessor = CatalogueService.accessor;
      });

      it('get方法测试', function(){
        $httpBackend.expectGET('/api/catalogues')
          .respond(200);

        CatalogueAccessor.get();
        $httpBackend.flush();
      });
    });

  });
});