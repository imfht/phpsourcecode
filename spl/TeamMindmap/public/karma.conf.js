// Karma configuration
// Generated on Wed Dec 03 2014 11:28:25 GMT+0800 (CST)

module.exports = function(config) {
  config.set({

    // base path that will be used to resolve all patterns (eg. files, exclude)
    basePath: '',


    // frameworks to use
    // available frameworks: https://npmjs.org/browse/keyword/karma-adapter
    frameworks: ['jasmine', 'requirejs'],


    // list of files / patterns to load in the browser
    files: [
      //导入与angular相关的库文件
      {pattern: 'packages/bower/jquery/dist/jquery.min.js',included:false},
      {pattern: 'packages/bower/bootstrap/dist/js/bootstrap.js',included:false},
      {pattern: 'packages/bower/angular/angular.js',included:false},
      {pattern: 'packages/bower/angular-route/angular-route.js',included:false},
      {pattern: 'packages/bower/angular-ui-router/release/angular-ui-router.js', included: false},
      {pattern: 'packages/bower/angular-mocks/angular-mocks.js',included: false},
      {pattern: 'packages/bower/angular-animate/angular-animate.js',included: false},
      {pattern: 'packages/bower/angular-bootstrap/ui-bootstrap.js',included: false},
      {pattern: 'packages/bower/angular-bootstrap/ui-bootstrap-tpls.js',included: false},
      {pattern: 'packages/bower/angularjs-slider/dist/rzslider.min.js',included: false},
      {pattern: 'packages/bower/angular-cookies/angular-cookies.js',included: false},
      {pattern: 'packages/bower/bxslider-4/jquery.bxslider.js', included: false},
      {pattern: 'packages/bower/angular-animate/angular-animate.min.js', included: false},
      {pattern: 'packages/bower/angular-bootstrap/ui-bootstrap-tpls.min.js', included: false},
      {pattern: 'packages/bower/raphael/raphael-min.js', included: false},
      {pattern: 'packages/bower/markdown/lib/markdown.js', included: false},
      {pattern: 'packages/pagedown-converter/Markdown.Converter.js', included: false},
      {pattern: 'packages/pagedown-converter/Markdown.Sanitizer.js', included: false},
      {pattern: 'packages/bower/angular-toasty/js/ng-toasty.js', included: false},
      {pattern: 'packages/bower/bootstrap-markdown/js/bootstrap-markdown.js', included: false},

      {pattern: 'packages/bower/ng-file-upload/angular-file-upload.js', included: false},
      {pattern: 'packages/bower/ng-file-upload/angular-file-upload-shim.min.js', included: false},

      {pattern: 'packages/bower/angular-deckgrid/angular-deckgrid.js', included: false},


      //导入其他文件
      {pattern: 'ngApp/common/localResizeIMG-2/build/*.js',included: false},

      //导入ngApp文件
      {pattern: 'ngApp/mainApp.js', included: false},
      {pattern: 'ngApp/library/**/*.js', included: false},
      {pattern: 'ngApp/**/js/*.js', included: false},
      {pattern: 'ngApp/**/js/**/*.js', included: false},

      //导入测试文件
      {pattern: 'test/TeamMindmap/**/unit/*.js',included:false},
      {pattern: 'test/TeamMindmap/library/*.js',included:false},
      {pattern: 'test/TeamMindmap/library/**/*.js',included:false},
      {pattern: 'test/TeamMindmap/**/unit/**/*.js',included:false},


      //导入main文件
      'test/TeamMindmap/test-main.js'
    ],


    // list of files to exclude
    exclude: [
      //重构前的排除文件, 完成重构后删除
      'ngApp/project-main.js',
      'ngApp/personal-main.js',

      'ngApp/ng-require-mainApp.js'
    ],


    // preprocess matching files before serving them to the browser
    // available preprocessors: https://npmjs.org/browse/keyword/karma-preprocessor
    preprocessors: {
    },


    // test results reporter to use
    // possible values: 'dots', 'progress'
    // available reporters: https://npmjs.org/browse/keyword/karma-reporter
    reporters: ['progress'],


    // web server port
    port: 9876,


    // enable / disable colors in the output (reporters and logs)
    colors: true,


    // level of logging
    // possible values: config.LOG_DISABLE || config.LOG_ERROR || config.LOG_WARN || config.LOG_INFO || config.LOG_DEBUG
    logLevel: config.LOG_INFO,


    // enable / disable watching file and executing tests whenever any file changes
    autoWatch: true,


    // start these browsers
    // available browser launchers: https://npmjs.org/browse/keyword/karma-launcher
    browsers: ['Chrome'],


    // Continuous Integration mode
    // if true, Karma captures browsers, runs the tests and exits
    singleRun: false
  });
};
