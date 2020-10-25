var allTestFiles = [];
var TEST_REGEXP = /(spec|test)\.js$/i;

var pathToModule = function(path) {
  return path.replace(/^\/base\//, '').replace(/\.js$/, '');
};

Object.keys(window.__karma__.files).forEach(function(file) {
  if (TEST_REGEXP.test(file)) {
    // Normalize paths to RequireJS module names.
    allTestFiles.push( file );
    //console.log(file);
  }
});


requirejs.config({
  baseUrl: '/base',
  paths: {
    'angular': 'packages/bower/angular/angular',
    'jquery': 'packages/bower/jquery/dist/jquery.min',
    'bootstrap': 'packages/bower/bootstrap/dist/js/bootstrap',
    'bxslider': 'packages/bower/bxslider-4/jquery.bxslider',
    'angularMocks':'packages/bower/angular-mocks/angular-mocks',
    'angularUIRouter': 'packages/bower/angular-ui-router/release/angular-ui-router',
    'angularAnimate': 'packages/bower/angular-animate/angular-animate.min',
    'angularBootstrap': 'packages/bower/angular-bootstrap/ui-bootstrap',
    'angularBootstrapTemplate': 'packages/bower/angular-bootstrap/ui-bootstrap-tpls.min',
    'angularCookies': 'packages/bower/angular-cookies/angular-cookies',
    'angularToasty': 'packages/bower/angular-toasty/js/ng-toasty',
    'localResize': 'ngApp/common/localResizeIMG-2/build/localResize.angular.min',
    'perfectScrollbar': 'packages/bower/perfect-scrollbar/src/perfect-scrollbar',
    'mainApp': 'ngApp/mainApp',
    'commonJS': 'ngApp/common/js',
    'projectJS': 'ngApp/project/js',
    'personalJS': 'ngApp/personal/js',
    'personalNotificationJS': 'ngApp/personalNotification/js',
    'personalMessageJS': 'ngApp/personalMessage/js',
    'raphael': 'packages/bower/raphael/raphael-min',
    'markdownEditorJS': 'ngApp/markdownEditor/js',
    'markdownJs': 'packages/bower/markdown/lib/markdown',
    'markdownConverterJS': 'packages/pagedown-converter/Markdown.Converter',
    'markdownSanitizerJS': 'packages/pagedown-converter/Markdown.Sanitizer',
    'mindmapJS': 'ngApp/mindmap/js',
    'ngFileUploadShim': 'packages/bower/ng-file-upload/angular-file-upload-shim.min',
    'ngFileUpload': 'packages/bower/ng-file-upload/angular-file-upload',
    'angularDeckgrid': 'packages/bower/angular-deckgrid/angular-deckgrid',
    'bootstrapMarkdown': 'packages/bower/bootstrap-markdown/js/bootstrap-markdown',
    'libraryJS': 'ngApp/library'
  },
  shim: {
    'angular': {
      deps: ['jquery'],
      exports: 'angular'
    },
    'angularMocks': {
      deps: ['angular'],
      exports: 'angularMocks'
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
  },

  // dynamically load all test files
  deps: allTestFiles,

  // we have to kickoff jasmine, as it is asynchronous
  callback: window.__karma__.start
});