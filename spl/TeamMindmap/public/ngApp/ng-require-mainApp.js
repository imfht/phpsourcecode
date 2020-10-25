/**
 * Created by spatra on 14-12-2.
 */

requirejs.config({
  baseUrl: '/',
  paths: {
    'angular': 'packages/bower/angular/angular',
    'jquery': 'packages/bower/jquery/dist/jquery.min',
    'bootstrap': 'packages/bower/bootstrap/dist/js/bootstrap',
    'bootstrapMarkdown': 'packages/bower/bootstrap-markdown/js/bootstrap-markdown',
    'markdownJs': 'packages/bower/markdown/lib/markdown',
    'bxslider': 'packages/bower/bxslider-4/jquery.bxslider',
    'angularUIRouter': 'packages/bower/angular-ui-router/release/angular-ui-router',
    'angularAnimate': 'packages/bower/angular-animate/angular-animate.min',
    'angularBootstrap': 'packages/bower/angular-bootstrap/ui-bootstrap',
    'angularBootstrapTemplate': 'packages/bower/angular-bootstrap/ui-bootstrap-tpls.min',
    'angularCookies': 'packages/bower/angular-cookies/angular-cookies',
    'angularToasty': 'packages/bower/angular-toasty/js/ng-toasty',
    'localResize': 'ngApp/common/localResizeIMG-2/build/localResize.angular.min',
    'raphael': 'packages/bower/raphael/raphael-min',
    'common_lib': 'js/common/common_lib',
    'mainApp': 'ngApp/mainApp',
    'commonJS': 'ngApp/common/js',
    'projectJS': 'ngApp/project/js',
    'personalJS': 'ngApp/personal/js',
    'globalNotificationJS': 'ngApp/globalNotification/js',
    'personalNotificationJS': 'ngApp/personalNotification/js',
    'personalMessageJS': 'ngApp/personalMessage/js',
    'markdownEditorJS': 'ngApp/markdownEditor/js',
    'markdownConverterJS': 'packages/pagedown-converter/Markdown.Converter',
    'markdownSanitizerJS': 'packages/pagedown-converter/Markdown.Sanitizer',
    'mindmapJS': 'ngApp/mindmap/js',
    'ngFileUploadShim': 'packages/bower/ng-file-upload/angular-file-upload-shim.min',
    'ngFileUpload': 'packages/bower/ng-file-upload/angular-file-upload',
    'angularDeckgrid': 'packages/bower/angular-deckgrid/angular-deckgrid',

    'libraryJS': 'ngApp/library'
  },
  shim: {
    'angular': {
      deps: ['jquery'],
      exports: 'angular'
    },
    'angularUIRouter': {
      deps: ['angular'],
      exports: 'angularUIRouter'
    },

    'jquery': {
      exports: 'jquery'
    },
    'bxslider': {
      deps: ['jquery'],
      exports: 'bxslider'
    },
    'bootstrap': {
      deps: ['jquery'],
      exports: 'bootstrap'
    },
    'angularAnimate': {
      deps: ['angular'],
      exports: 'angularAnimate'
    },
    'angularBootstrap': {
      deps: ['angular'],
      exports: 'angularBootstrap'
    },
    'angularBootstrapTemplate': {
      deps: ['angular'],
      exports: 'angularBootstrapTemplate'
    },
    'angularCookies': {
      deps: ['angular'],
      exports: 'angularCookies'
    },
    'localResize': {
      deps: ['angular'],
      exports: 'localResize'
    },
    'angularToasty': {
      deps: ['angular','angularAnimate'],
      exports: 'angularToasty'
    },
    'bootstrapMarkdown': {
      deps: ['bootstrap', 'markdownJs'],
      exports: 'bootstrapMarkdown'
    },
    'markdownSanitizerJS': {
      deps: ['markdownConverterJS'],
      exports: 'markdownSanitizerJS'
    },
    'ngFileUploadShim': {
      exports: 'ngFileUploadShim'
    },
    'ngFileUpload': {
      deps: ['angular', 'ngFileUploadShim'],
      exports: 'ngFileUpload'
    },
    'angularDeckgrid': {
      deps: ['angular'],
      exports: 'angularDeckgrid'
    }
  }
});

requirejs(['angular', 'mainApp'], function(angular, app){

  angular.element(document).ready(function(){
    angular.bootstrap( document.getElementById('TeamMindmap'), ['TeamMindmap']);
  });
});