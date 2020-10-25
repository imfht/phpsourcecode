// 通用gulp文件，不推荐使用通用方法，建议在各模块目录单独建立

var gulp        = require('gulp'),
    sass        = require('gulp-sass'),
    minifyCss   = require('gulp-minify-css'),
    plumber     = require('gulp-plumber'),
    babel       = require('gulp-babel'),
    uglify      = require('gulp-uglify'),
    copy        = require('gulp-contrib-copy'),
    concat      = require('gulp-concat'),
    rename      = require('gulp-rename'),
    browserSync = require('browser-sync').create(),
    reload      = browserSync.reload;
    
// 定义源代码的目录和编译压缩后的目录
var src='src',
    dist='static';
// 编译全部scss 并压缩
gulp.task('scss', function(){
    gulp.src([src+'/**/css/**/*.scss','!'+src+'/common/css','!'+src+'/common/css/**','!'+src+'/admin/css','!'+src+'/admin/css/**',])
        .pipe(sass())
        .pipe(rename({suffix: '.min'}))//rename压缩后的文件名
        .pipe(minifyCss())
        .pipe(gulp.dest(dist));

        //console.log(gulp.src(src+'/**/css/**/*.scss'));
})

// 编译模块js 并压缩不合并
gulp.task('mod_js', function() {
  gulp.src([src+'/**/js/**/*.js','!'+src+'/common/js','!'+src+'/common/js/**','!'+src+'/admin/js','!'+src+'/admin/js/**',])
    .pipe(plumber())
    .pipe(babel({
      presets: ['es2015']
    }))
    //.pipe(concat('main.js'))//合并js
    .pipe(rename({suffix: '.min'}))//rename压缩后的文件名
    .pipe(uglify())
    .pipe(gulp.dest(dist));
});
// CSS文件直接copy
gulp.task('css', function () {
    gulp.src(src+'/**/css/**/*.css')
    .pipe(copy())
    .pipe(gulp.dest(dist));
});
// 图片文件直接copy
gulp.task('images', function () {
    gulp.src(src+'/**/images/**/*')
    .pipe(copy())
    .pipe(gulp.dest(dist));
});
// 第三方资源库不编译的直接copy
gulp.task('lib', function () {
    gulp.src(src+'/**/lib/**/*')
    .pipe(copy())
    .pipe(gulp.dest(dist));
});
// 自动刷新
gulp.task('server', function() {
    browserSync.init({
        proxy: "www.a.com", // 指定代理url
        notify: false, // 刷新不弹出提示
    });

    // 监听scss文件编译
    gulp.watch(src+'/**/css/*.scss', ['scss']);
    // 监听其他不编译的文件 有变化直接copy
    gulp.watch(src+'/**/images/**/*.!(jpg|jpeg|png|gif|bmp|svg)', ['images']);
    gulp.watch(src+'/**/lib/**/*.!(jpg|jpeg|png|gif|bmp|svg|css|js)', ['lib']);   
    // 监听css文件变化后刷新页面
    gulp.watch(src+'/**/css/*.css').on("change", reload);
});
// 监听事件
gulp.task('default', ['scss', 'mod_js', 'css','images', 'lib', 'server'])