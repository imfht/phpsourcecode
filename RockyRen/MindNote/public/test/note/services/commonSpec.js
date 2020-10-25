/**
 * Created by rockyren on 15/5/19.
 */

define(['noteJS/ng-require', 'angularMocks'], function(noteModuleName){
  describe("Unit:note的common service测试", function(){
    beforeEach( module(noteModuleName));

    var ResourceListService;
    beforeEach(inject(function(_ResourceListService_){
      ResourceListService = _ResourceListService_;
    }));

    describe('ResourceListService 服务', function() {

      describe('SingleResourceListHandle测试', function(){
        var notebooks, notebooksHandle;
        beforeEach(function(){
          //测试的笔记本列表
          notebooks = [];
          initNotebook(notebooks, 1, 4);

          //笔记本列表的管理对象
          notebooksHandle = ResourceListService.SingleResourceListHandle(notebooks);

        });
        it('测试getResource方法', function(){
          var notebook2 = notebooksHandle.getResource(2);
          expect(notebook2.id).toEqual(2);

          var notebook4 = notebooksHandle.getResource(4);
          expect(notebook4.id).toEqual(4);

          var notebook100 = notebooksHandle.getResource(100);
          expect(notebook100).toBeNull();
        });
        it('测试deleteResource方法', function(){
          var cancelResource = notebooksHandle.deleteResource(3);
          expect(cancelResource.id).toEqual(3);

          var notebook3 = notebooksHandle.getResource(3);
          expect(notebook3).toBeNull();
        });
        it('测试addResource方法', function(){
          notebooksHandle.addResource({
            id: 10,
            name: 'notebook10'
          });
          var notebook10 = notebooksHandle.getResource(10);
          expect(notebook10.id).toEqual(10);
        });
      });

      describe('GroupListHandle测试', function(){
        var singleNotebooks, singleNotebooksHandle, groups, groupsHandle;
        beforeEach(function(){
          //测试的笔记本列表
          singleNotebooks = [];
          initNotebook(singleNotebooks, 1, 4);

          //测试的笔记本组列表
          groups = [
            {
              id: 1,
              name: 'group1',
              notebooks: []
            },
            {
              id: 2,
              name: 'group2',
              notebooks: []
            },
            {
              id: 3,
              name: 'group3',
              notebooks: []
            }
          ];
          initNotebook(groups[0].notebooks, 5, 6);
          initNotebook(groups[1].notebooks, 7, 8);
          initNotebook(groups[2].notebooks, 9, 10);

          singleNotebooksHandle = ResourceListService.SingleResourceListHandle(singleNotebooks);
          groupsHandle = ResourceListService.GroupListHandle(groups, singleNotebooks);





        });

        it('测试getGroup方法', function(){
          var group2 = groupsHandle.getGroup(2);
          expect(group2.id).toEqual(2);

          var group10 = groupsHandle.getGroup(10);
          expect(group10).toBeNull();


        });

        it('测试deleteGroup方法', function(){

          var cancelGroup = groupsHandle.deleteGroup(3);
          expect(cancelGroup.id).toEqual(3);

          var group3 = groupsHandle.getGroup(3);
          expect(group3).toBeNull();
        });

        it('测试addGroup方法', function(){
          groupsHandle.addGroup({
            id: 11,
            name: 'group11'
          });

          var group11 = groupsHandle.getGroup(11);
          expect(group11.id).toEqual(11);
          expect(group11.notebooks).not.toBeNull();
        });

        it('测试getNotebook方法', function(){
          var notebook5 = groupsHandle.getNotebook(1, 5);
          expect(notebook5.id).toEqual(5);
          var notebook6 = groupsHandle.getNotebook(1, 6);
          expect(notebook6.id).toEqual(6);

          var notebook7 = groupsHandle.getNotebook(1, 7);
          expect(notebook7).toBeNull();


        });

        it('测试deleteNotebook方法', function(){
          var notebook7 = groupsHandle.getNotebook(2, 7);
          expect(notebook7.id).toEqual(7);

          var cancelNotebook = groupsHandle.deleteNotebook(2, 7);
          expect(cancelNotebook.id).toEqual(7);

          notebook7 = groupsHandle.getNotebook(2, 7);
          expect(notebook7).toBeNull();
        });

        it('测试addNotebook方法', function(){
          var notebook20 = groupsHandle.getNotebook(1, 20);
          expect(notebook20).toBeNull();

          groupsHandle.addNotebook(1, {
            id: 20,
            name: 'notebook20'
          });

          var notebook20 = groupsHandle.getNotebook(1, 20);
          expect(notebook20.id).toEqual(20);
        });

        it('测试translateNotebook方法', function(){
          //将group1中的notebook6放到group2
          var notebook6InGroup1 = groupsHandle.getNotebook(1, 6);
          expect(notebook6InGroup1.id).toBe(6);

          var notebook6InGroup2 = groupsHandle.getNotebook(2, 6);
          expect(notebook6InGroup2).toBeNull();

          groupsHandle.translateNotebook(1, 2, 6);

          var notebook6InGroup1 = groupsHandle.getNotebook(1, 6);
          expect(notebook6InGroup1).toBeNull();

          var notebook6InGroup2 = groupsHandle.getNotebook(2, 6);
          expect(notebook6InGroup2.id).toEqual(6);

        });

        it('测试moveOutGroup方法', function(){
          //将group3中的notebook9放到singleNotebooks中
          var notebook9 = groupsHandle.getNotebook(3, 9);
          expect(notebook9.id).toEqual(9);

          var notebook9InSingle = singleNotebooksHandle.getResource(9);
          expect(notebook9InSingle).toBeNull();

          groupsHandle.moveOutGroup(3, 9);

          notebook9 = groupsHandle.getNotebook(3, 9);
          expect(notebook9).toBeNull();

          notebook9InSingle = singleNotebooksHandle.getResource(9);
          expect(notebook9InSingle.id).toEqual(9);

        });

        it('测试moveInGroup方法', function(){
          //将notebook1放到group2中
          var notebook1InSingle = singleNotebooksHandle.getResource(1);
          expect(notebook1InSingle.id).toEqual(1);

          var notebook1InGroup2 = groupsHandle.getNotebook(2, 1);
          expect(notebook1InGroup2).toBeNull();

          groupsHandle.moveInGroup(2, 1);

          notebook1InSingle = singleNotebooksHandle.getResource(1);
          expect(notebook1InSingle).toBeNull();

          notebook1InGroup2 = groupsHandle.getNotebook(2, 1);
          expect(notebook1InGroup2.id).toEqual(1);




        });
      })


    });

    /**
     * 用于初始化笔记本列表
     * @param notebookList
     * @param from
     * @param to
     */
    function initNotebook( notebookList, from, to ){
      for(var i=from; i<=to; i++){
        var notebook = {id: i, name: 'notebook' + i};
        notebookList.push(notebook);
      }
    }



  });

});