module.exports = function(grunt){
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    //移动字体文件，建立RequireJS配置文件
    shell: {
      copyAwesomeFonts: {
        command: 'cp public/packages/bower/font-awesome/fonts/* public/fonts/'
      }
    },
    //合并CSS文件
    concat: {
      css: {
        src: [
          //通用样式
          'public/css/reset.css',
          'public/css/common-style.css',
          'public/css/top-nav.css',

          //ngApp模块样式
          'public/css/route-app-style.css',
          'public/css/route-app-directive.css',

          //font-awesome字符库可能会与其他样式库冲突,所以放在最后
          'public/packages/bower/font-awesome/css/font-awesome.css'
        ],
        dest: 'public/css/concat.tmp.css'
      }
    },
    //压缩CSS文件
    cssmin: {
      css:{
        src: 'public/css/concat.tmp.css',
        dest: 'public/css/app.min.css'
      }
    },
    //打包RequireJS文件（注意，AngularJS App由RequireJS加载）
    requirejs: {
      compile: {
        options: {
          baseUrl: './public',
          name: "js/route-app-ng",
          optimize: "uglify",
          mainConfigFile: "public/js/require-global-config.js",
          out: "public/route-app-ng.min.js"
        }
      }
    },
    //清理构建过程中生成的临时文件
    clean: {
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