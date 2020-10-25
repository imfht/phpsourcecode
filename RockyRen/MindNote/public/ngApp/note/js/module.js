/**
 * Created by rockyren on 15/5/16.
 */

define(['angular', 'libraryJS/resourceHelper/resourceHelper', 'mindmapJS/ng-require',
  'angularBootstrap', 'angularBootstrapTemplate', 'textAngular'],
  function(angular, resourceHelper, mindmapModule){
  var noteModule = angular.module('note', [resourceHelper, mindmapModule, 'ui.bootstrap', 'ui.bootstrap.tpls', 'textAngular']);

  noteModule.controller('noteController', ['$scope', function($scope){
    $scope.note = 'note scope';
  }]);

  noteModule.constant('noteModuleBaseUrl', 'public/ngApp/note/tpls/');


  return noteModule;
});