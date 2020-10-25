/**
 * Created by spatra on 15-4-19.
 */

var require = {
  baseUrl: '/',
  paths: {
    'library': 'js/library',
    'basic': 'js/library/basic',
    'angular': 'packages/bower/angularjs/angular',
    'angularUIRouter': 'packages/bower/angular-ui-router/release/angular-ui-router',
    'routeApp': 'js/routeApp',
    'ngFileUploadShim': 'packages/bower/ng-file-upload/ng-file-upload-shim',
    'ngFileUpload': 'packages/bower/ng-file-upload/ng-file-upload'
  },
  shim: {
    'angular': {
      exports: 'angular'
    },
    'angularUIRouter': {
      deps: ['angular'],
      exports: 'angularUIRouter'
    },
    'ngFileUploadShim': {
      exports: 'ngFileUploadShim'
    },
    'ngFileUpload': {
      deps: ['angular', 'ngFileUploadShim'],
      exports: 'ngFileUpload'
    }
  }
};
