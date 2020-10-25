/**
 * Created by rockyren on 15/5/16.
 */

requirejs.config({
  baseUrl: '/public',
  paths: {
    'angular': 'packages/bower/angular/angular',
    'jquery': 'packages/bower/jquery/dist/jquery',
    'bootstrap': 'packages/bower/bootstrap/dist/js/bootstrap.min',
    'angularUIRouter': 'packages/bower/angular-ui-router/release/angular-ui-router',
    'angularBootstrap': 'packages/bower/angular-bootstrap/ui-bootstrap.min',
    'angularBootstrapTemplate': 'packages/bower/angular-bootstrap/ui-bootstrap-tpls.min',
    'raphael': 'packages/bower/raphael/raphael-min',
    'textAngularSanitize': 'packages/bower/textAngular/dist/textAngular-sanitize.min',
    'textAngular': 'packages/bower/textAngular/dist/textAngular.min',

    'mainApp': 'ngApp/mainApp',
    'noteJS': 'ngApp/note/js',
    'libraryJS': 'ngApp/library',
    'mindmapJS': 'ngApp/library/mindmap/js'

  },
  shim: {
    'angular': {
      deps: ['jquery'],
      exports: 'angular'
    },
    'bootstrap': {
      deps: ['jquery'],
      exports: 'bootstrap'
    },
    'angularUIRouter':{
      deps: ['angular'],
      exports: 'angularUIRouter'
    },
    'angularBootstrap': {
      deps: ['angular'],
      exports: 'angularBootstrap'
    },
    'angularBootstrapTemplate': {
      deps: ['angular'],
      exports: 'angularBootstrapTemplate'
    },
    'textAngularSanitize': {
      deps: ['angular'],
      exports: 'textAngularSanitize'
    },
    'textAngular': {
      deps: ['angular', 'textAngularSanitize'],
      exports: 'textAngular'
    }
  }
});


requirejs(['angular', 'mainApp', 'jquery', 'bootstrap'], function(angular){

  angular.element(document).ready(function(){
    angular.bootstrap( document.getElementById('mindnote'), ['mindnote']);
  });
});