/**
 * Created by rockyren on 15/5/18.
 */


var allTestFiles = [];
var TEST_REGEXP = /(spec|test)\.js$/i;



Object.keys(window.__karma__.files).forEach(function(file) {
  if (TEST_REGEXP.test(file)) {
    // Normalize paths to RequireJS module names.
    allTestFiles.push( file );
  }
});


requirejs.config({
  baseUrl: '/base',
  paths: {

    'angular': 'packages/bower/angular/angular',
    'jquery': 'packages/bower/jquery/dist/jquery',
    'bootstrap': 'packages/bower/bootstrap/dist/js/bootstrap',
    'angularMocks': 'packages/bower/angular-mocks/angular-mocks',
    'angularUIRouter': 'packages/bower/angular-ui-router/release/angular-ui-router',
    'angularBootstrap': 'packages/bower/angular-bootstrap/ui-bootstrap.min',
    'angularBootstrapTemplate': 'packages/bower/angular-bootstrap/ui-bootstrap-tpls.min',

    'libraryJS': 'ngApp/library',
    'noteJS': 'ngApp/note/js'

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
    'angularMocks': {
      deps: ['angular'],
      exports: 'angularMocks'
    },
    'angularUIRouter': {
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
    }
  },

  // dynamically load all test files
  deps: allTestFiles,

  // we have to kickoff jasmine, as it is asynchronous
  callback: window.__karma__.start
});