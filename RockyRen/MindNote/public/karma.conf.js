// Karma configuration
// Generated on Mon May 18 2015 20:20:02 GMT+0800 (CST)

module.exports = function(config) {
  config.set({

    // base path that will be used to resolve all patterns (eg. files, exclude)
    basePath: '',


    // frameworks to use
    // available frameworks: https://npmjs.org/browse/keyword/karma-adapter
    frameworks: ['jasmine', 'requirejs'],


    // list of files / patterns to load in the browser
    files: [
      //导入外部文件
      {pattern: 'packages/bower/angular/angular.js', included: false},
      {pattern: 'packages/bower/angular-ui-router/release/angular-ui-router.js', included: false},
      {pattern: 'packages/bower/bootstrap/dist/js/bootstrap.js', included: false},
      {pattern: 'packages/bower/jquery/dist/jquery.js', included: false},
      {pattern: 'packages/bower/angular-mocks/angular-mocks.js', included: false},
      {pattern: 'packages/bower/angular-bootstrap/ui-bootstrap.min.js', included: false},
      {pattern: 'packages/bower/angular-bootstrap/ui-bootstrap-tpls.min.js', included: false},


      //导入ngApp文件
      {pattern: 'ngApp/library/**/*.js', included: false},
      {pattern: 'ngApp/note/js/**/*.js', included: false},
      {pattern: 'ngApp/note/js/*.js', included: false},


      //导入测试文件
      {pattern: 'test/library/*.js', included: false},
      {pattern: 'test/note/**/*.js', included: false},
      {pattern: 'test/note/*.js', included: false},

      //导入main文件
      'test/test-main.js'
    ],


    // list of files to exclude
    exclude: [
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
