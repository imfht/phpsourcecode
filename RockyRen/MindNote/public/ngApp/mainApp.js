/**
 * Created by rockyren on 15/5/16.
 */
define(['angular', 'noteJS/ng-require', 'angularUIRouter'], function(angular, noteModule){
  var app = angular.module('mindnote', [noteModule, 'ui.router']);

  app.config(['$stateProvider', '$urlRouterProvider', 'noteModuleBaseUrl', function($stateProvider, $urlRouterProvider, noteBaseUrl){

    $urlRouterProvider.otherwise('mindnote/list/all/notes');

    $stateProvider
      .state('mindnote', {
        url: '/mindnote',
        title: 'MindNote',
        views: {
          '': {
            templateUrl: noteBaseUrl + 'layout.html',
            controller: 'MindnoteController'
          },
          'catalogue@mindnote': {
            templateUrl: noteBaseUrl + 'catalogue.html'
          }
        },
        resolve: {
          catalogueInfo: ['CatalogueService', function(CatalogueService){
            return CatalogueService.accessor.get()
              .then(function(resp){
                return resp.data;
              }, function(data){
                console.error('获取目录失败');
              });
          }]
        }
      })
      .state('mindnote.list', {
        url: '/list/{type}/{typeId}',
        views: {
          'notes@mindnote': {
            templateUrl: noteBaseUrl + 'notes.html',
            controller: 'NoteListController'
          }
        },
        resolve: {
          notes: ['NoteService', '$stateParams', function(NoteService, $stateParams){
            //根据type（'all' | 'group' | 'notebook'）取得笔记列表
            //return NoteService.getNotesByType($stateParams);
            NoteService.setNotesParentByType($stateParams);
            return NoteService.accessor.get()
              .then(function(resp){
                return resp.data;
              }, function(resp){
                console.error(resp.data.message);
              })
          }]
        }
      })
      .state('mindnote.list.noteInfo', {
        url: '/note/info/:noteId',
        views: {
          'content@mindnote': {
            templateUrl: noteBaseUrl + 'noteInfo.html',
            controller: 'NoteInfoController'
          }
        },
        resolve: {
          noteInfo: ['NoteService', '$stateParams', function(NoteService, $stateParams){
            //NoteService.setNoteParentIdByType($stateParams);
            return NoteService.accessor.show($stateParams.noteId)
              .then(function(resp){
                return resp.data;
              }, function(resp){
                console.error(resp.data.message);
              })
          }]
        }
      })
      //.state('mindnote.list.noteEdit', {
      //  url: '/note/edit/:noteId',
      //  views: {
      //    'content@mindnote': {
      //      templateUrl: noteBaseUrl + 'noteAdd.html',
      //      controller: 'NoteEditController'
      //    }
      //  },
      //  resolve: {
      //    noteInfo: ['NoteService', '$stateParams', function(NoteService, $stateParams){
      //
      //      //当stateParams为new时，采用noteinfo为null
      //      if($stateParams.noteId != 'new'){
      //        return NoteService.accessor.show($stateParams.noteId)
      //          .then(function(resp){
      //            return resp.data;
      //          }, function(resp){
      //            console.error(resp.data.message);
      //          })
      //      }else{
      //        return null;
      //      }
      //
      //    }]
      //  }
      //
      //})
      .state('mindnote.list.noteAdd', {
        url: '/note/new',
        views: {
          'content@mindnote': {
            templateUrl: noteBaseUrl + 'noteAdd.html',
            controller: 'NoteAddController'
          }
        }
      })
      .state('mindnote.list.mapInfo', {
        url: '/map/info/:noteId',
        views: {
          'content@mindnote': {
            templateUrl: noteBaseUrl + 'mapInfo.html',
            controller: 'mapInfoController'
          }
        },
        resolve: {
          noteInfo: ['NoteService', '$stateParams', function(NoteService, $stateParams){
            //NoteService.setNoteParentIdByType($stateParams);
            return NoteService.accessor.show($stateParams.noteId)
              .then(function(resp){
                return resp.data;
              }, function(resp){
                console.error(resp.data.message);
              })
          }]
        }
      })
      .state('mindnote.list.mapAdd', {
        url: '/map/new',
        views:{
          'content@mindnote': {
            templateUrl: noteBaseUrl + 'mapAdd.html',
            controller: 'mapAddController'
          }
        }
      })


  }]);


  return app;
});