'use strict';

var gulp = require('gulp');
var $ = require('gulp-load-plugins')();
var runSequence = require('run-sequence');

var del = require('del');
var fs = require('fs');
var path = require('path');

var dist="dist";



gulp.task('js', function(){
  return gulp.src('src/Browser/EasyChart/*.js')
      //  .pipe($.sourcemaps.init())
        .pipe($.concat('./Browser/js/EasyChart.js'))
        .pipe($.babel({
            presets: ['env']
        }))

    //    .pipe($.sourcemaps.write('.'))
    //    .pipe(gulp.dest(dist))
        .pipe($.uglify())
        .pipe($.rename('./Browser/js/EasyChart.min.js'))
        .pipe(gulp.dest(dist));


});
gulp.task('css', function(){
  // return gulp.src([
  //   'src/Browser/css/*.css',
  //   'src/Browser/css/*.scss'
  // ])
  //       .pipe($.concat('./Browser/css/EasyChart.css'))
  //       .pipe($.sass())
  //       .pipe(gulp.dest(dist));

});
gulp.task('echarts', function(){
  return gulp.src('src/Browser/echarts/*.js')
        .pipe(gulp.dest(dist+"/Browser/js/"));


});
gulp.task('server', function(){
  return gulp.src([
    'src/Server/**'
  ])
        .pipe(gulp.dest(dist+"/Server/"));
});
gulp.task('image', function(){

});
gulp.task('build', function(cb){
  runSequence('clean',['js','echarts','server'],cb);
});
gulp.task('default', ['build'],function(){
  // 将你的默认的任务代码放在这
});
gulp.task('watch', function(){
    gulp.watch('src/*', ['build']);
});
gulp.task('clean',function (cb) {
  return del([
    dist+'/*'
  ]);
});
