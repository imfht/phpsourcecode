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
var src='../src',
    dist='../../static/admin';
// 编译adminlte全部scss 并压缩
gulp.task('adminlte_scss', function(){
    gulp.src('css/adminlte/*.scss')
        .pipe(sass())
        .pipe(rename({suffix: '.min'}))//rename压缩后的文件名
        .pipe(minifyCss())
        .pipe(gulp.dest(dist+'/css'));

})
// 编译iframe全部scss 并压缩
gulp.task('iframe_scss', function(){
    gulp.src('css/iframe/*.scss')
        .pipe(sass())
        .pipe(concat('main.css'))//合并css
        .pipe(rename({suffix: '.min'}))//rename压缩后的文件名
        .pipe(minifyCss())
        .pipe(gulp.dest(dist+'/css'));

        //console.log(gulp.src(src+'/**/css/**/*.scss'));
});
// 编译核心adminlte_js 并压缩、合并
gulp.task('adminlte_js', function() {
  gulp.src('js/adminlte/*.js')
    .pipe(plumber())
    .pipe(babel({
      presets: ['es2015']
    }))
    .pipe(concat('adminlte.js'))//合并js
    .pipe(rename({suffix: '.min'}))//rename压缩后的文件名
    .pipe(uglify())
    .pipe(gulp.dest(dist+'/js'));
});
// 编译核心js 并压缩、合并
gulp.task('main_js', function() {
  gulp.src('js/main/*.js')
    .pipe(plumber())
    .pipe(babel({
      presets: ['es2015']
    }))
    .pipe(concat('main.js'))//合并js
    .pipe(rename({suffix: '.min'}))//rename压缩后的文件名
    .pipe(uglify())
    .pipe(gulp.dest(dist+'/js'));
});
// CSS文件直接copy
gulp.task('css', function () {
    gulp.src('css/**/*.css')
    .pipe(copy())
    .pipe(gulp.dest(dist+'/css'));
});
// 图片文件直接copy
gulp.task('images', function () {
    gulp.src('images/**/*')
    .pipe(copy())
    .pipe(gulp.dest(dist+'/images'));
});
// 第三方资源库不编译的直接copy
gulp.task('lib', function () {
    gulp.src('lib/**/*')
    .pipe(copy())
    .pipe(gulp.dest(dist+'/lib'));
});
// 自动刷新
gulp.task('server', function() {
    browserSync.init({
        proxy: "www.a.com", // 指定代理url
        notify: false, // 刷新不弹出提示
    });

    // 监听scss文件编译
    gulp.watch('css/adminlte/*.scss', ['adminlte_scss']);
    // 监听scss文件编译
    gulp.watch('css/iframe/*.scss', ['iframe_scss']);
    // 监听其他不编译的文件 有变化直接copy
    gulp.watch('images/**/*.!(jpg|jpeg|png|gif|bmp|svg)', ['images']);
    gulp.watch('lib/**/*.!(jpg|jpeg|png|gif|bmp|svg|css|js)', ['lib']);   
    // 监听核心js文件变化后刷新页面
    gulp.watch('js/adminlte/*.js', ['adminlte_js']).on("change", reload);
    gulp.watch('js/main/*.js', ['main_js']).on("change", reload);
    // 监听css文件变化后刷新页面
    gulp.watch('css/*.css').on("change", reload);
});
// 监听事件
gulp.task('default', ['adminlte_scss', 'iframe_scss', 'adminlte_js', 'main_js', 'css','images', 'lib', 'server'])