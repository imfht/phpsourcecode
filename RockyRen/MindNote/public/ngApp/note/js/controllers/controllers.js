/**
 * Created by rockyren on 15/6/3.
 */
define(['noteJS/module'], function(noteModule) {
  noteModule.controller('NoteListController', ['$scope', '$rootScope', '$state', '$stateParams', 'notes', 'NoteService', function($scope, $rootScope, $state, $stateParams, notes, NoteService){
    $scope.notes = notes;

    if($scope.notes && $scope.notes.length > 0){
      var firstNote = $scope.notes[0];
      if(firstNote.type == 'note'){
        $state.go('mindnote.list.noteInfo', {
          noteId: firstNote.id
        })
      }else if(firstNote.type == 'map'){
        $state.go('mindnote.list.mapInfo', {
          noteId: firstNote.id
        })
      }

    }

    $scope.$emit('reload:curResourceId', {
      groupId: $stateParams.type == 'group' ? $stateParams.typeId : null,
      notebookId: $stateParams.type == 'notebook'? $stateParams.typeId: null
    });

    //重新加载笔记列表
    $rootScope.$on('reload:notes', function(){

      NoteService.setNotesParentByType($stateParams);
      NoteService.accessor.get()
        .success(function(data){
          $scope.notes = data;
        })
        .error(function(data){
          console.error(data.message);
        });
      //$scope.notes = NoteService.getNotesByType($stateParams);
    });

    $scope.curNoteId = null;
    $rootScope.$on('reload:curNoteId', function(event, obj){
      $scope.curNoteId = obj.noteId;
    });


    $scope.selectNote = function(noteId, noteType){

      if(noteType == 'note'){
        $state.go('mindnote.list.noteInfo', {
          noteId: noteId
        });
      }
      else{
        $state.go('mindnote.list.mapInfo', {
          noteId: noteId
        })
      }

    }

  }]);

  noteModule.controller('NoteInfoController', ['$scope', '$state', '$stateParams', 'noteInfo', 'NoteService', function($scope, $state, $stateParams, noteInfo, NoteService) {
    $scope.noteInfo = noteInfo;

    $scope.$emit('reload:curNoteId', {
      noteId: noteInfo.id
    });

    //修改已有的笔记
    $scope.editNote = function(){
      if($scope.noteInfo.name == ''){
        alert('笔记名不能为空');
        return;
      }
      NoteService.setNotesParentByType($stateParams);
      NoteService.accessor.update($scope.noteInfo.id, {
        name: noteInfo.name,
        content: noteInfo.content
      })
        .success(function(){
          alert("修改成功");
          $scope.$emit("reload:notes");
          $state.go('mindnote.list.noteInfo', {noteId: $stateParams.noteId});
        })
        .error(function(data){
          console.error(data.message);
        });

    };

    $scope.deleteNote = function(){
      NoteService.accessor.destroy($scope.noteInfo.id)
        .success(function(){
          alert('删除成功');
          $state.go('mindnote.list');
          $scope.$emit('reload:notes');
        })
        .error(function(data){
          console.error(data.message);
        });
    };
  }]);

  noteModule.controller('NoteAddController', ['$scope', '$state', '$stateParams', 'NoteService', function($scope, $state, $stateParams, NoteService){
    //将已选笔记设为未选
    $scope.$emit("reload:curNoteId", {
      noteId: null
    });

    $scope.noteInfo = {
      name: null,
      content: null
    };



    //新建新的笔记
    $scope.saveNote = function(){
      var storeData = {
        name: $scope.noteInfo.name ? $scope.noteInfo.name : '无标题笔记',
        content: $scope.noteInfo.content
      };
      if($stateParams.type == 'notebook'){
        storeData.notebook_id = $stateParams.typeId;
      }


      NoteService.accessor.store(storeData)
        .success(function(newNoteInfo){
          $scope.$emit('reload:notes');
          $state.go('mindnote.list.noteInfo', {noteId: newNoteInfo.id});
        })
        .error(function(data){
          console.error(data.message);
        });

    };
  }]);


  noteModule.controller('mapInfoController', ['$scope', '$state', '$stateParams', 'noteInfo', 'NoteService', function($scope, $state, $stateParams, noteInfo,  NoteService){

    $scope.mapTitle = noteInfo.name;
    $scope.mapObj = noteInfo.content;

    $scope.$emit('reload:curNoteId', {
      noteId: $stateParams.noteId
    });


    $scope.editMap = function(){
      NoteService.setNotesParentByType($stateParams);
      NoteService.accessor.update(noteInfo.id, {
        name: $scope.graphMethod.getRootLabel(),
        content: $scope.graphMethod.toJson(),
        type: 'map'
      })
        .success(function(){
          alert("修改成功");
          $scope.$emit("reload:notes");
          $state.go('mindnote.list.mapInfo', {noteId: $stateParams.noteId});
        })
        .error(function(data){
          console.error(data.message);
        });

    };

    $scope.deleteMap = function(){
      NoteService.accessor.destroy(noteInfo.id)
        .success(function(){
          alert('删除成功');
          $state.go('mindnote.list');
          $scope.$emit('reload:notes');
        })
        .error(function(data){
          console.error(data.message);
        });
    };
  }]);

  noteModule.controller('mapAddController', ['$scope', '$state', '$stateParams', 'NoteService', function($scope, $state, $stateParams, NoteService){
    //将已选笔记设为未选
    $scope.$emit("reload:curNoteId", {
      noteId: null
    });

    $scope.mapTitle = '';
    $scope.mapObj = null;


    $scope.saveMap = function() {

      var storeData = {
        name: $scope.graphMethod.getRootLabel(),
        content: $scope.graphMethod.toJson(),
        type: 'map'
      };

      if ($stateParams.type == 'notebook') {
        storeData.notebook_id = $stateParams.typeId;
      }

      NoteService.accessor.store(storeData)
        .success(function(newNoteInfo){
          $scope.$emit('reload:notes');
          $state.go('mindnote.list.mapInfo', {noteId: newNoteInfo.id});

        })
        .error(function(data){
          console.error(data.message);
        });

    }

  }]);
});
