
module.exports = function(grunt){
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    //移动字体文件，建立RequireJS配置文件
    shell: {
      copyBootstrapFonts: {
        command: 'cp public/packages/bower/bootstrap/fonts/* public/fonts/'
      },
      copyAwesomeFonts: {
        command: 'cp public/packages/bower/font-awesome/fonts/* public/fonts/'
      },
      'createrRequireJSConfigFile': {
        command: 'cp public/ngApp/ng-require-mainApp.js public/build.conf.js'
      }
    },
    //合并CSS文件
    concat: {
      css: {
        src: [
          //默认样式
          'public/packages/bower/bootstrap/dist/css/bootstrap.min.css',
          'public/css/nav-style.css',

          //插件样式
          'public/packages/bower/bxslider-4/jquery.bxslider.css',
          'public/ngApp/common/localResizeIMG-2/build/localResize.css',
          'public/packages/bower/angular-toasty/css/ng-toasty.css',

          //通用样式
          'public/css/app-common.css',
          'public/css/ngCommon/*.css',

          //ngApp模块样式
          'public/ngApp/*/css/*.css',

          //font-awesome字符库可能会与其他样式库冲突,所以放在最后
          'public/packages/bower/font-awesome/css/font-awesome.css',

          //Bootstrap Markdown的样式文件
          'public/packages/bower/bootstrap-markdown/css/bootstrap-markdown.min.css'
        ],
        dest: 'public/css/concat.tmp.css'
      }
    },
    //压缩CSS文件
    cssmin: {
      css:{
        src: 'public/css/concat.tmp.css',
        dest: 'public/css/min.css'
      }
    },
    //打包RequireJS文件（注意，AngularJS App由RequireJS加载）
    requirejs: {
      compile: {
        options: {
          baseUrl: './public',
          name: "ngApp/ng-require-mainApp",
          optimize: "uglify",
          mainConfigFile: "public/build.conf.js",
          out: "public/ngApp/ng-main.min.js"
        }
      }
    },
    //清理构建过程中生成的临时文件
    clean: {
      js: ['public/build.conf.js'],
      css: ['public/css/concat.tmp.css']
    }
  });

  grunt.loadNpmTasks('grunt-shell');
  grunt.loadNpmTasks('grunt-contrib-requirejs');
  grunt.loadNpmTasks('grunt-css');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-clean');

  grunt.registerTask('default',['shell', 'requirejs', 'concat', 'cssmin', 'clean']);
  grunt.registerTask('css',['shell', 'concat', 'cssmin', 'clean']);
};

