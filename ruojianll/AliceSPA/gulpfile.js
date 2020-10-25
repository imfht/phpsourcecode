var gulp = require('gulp');

var jshint = require('gulp-jshint');
var sass = require('gulp-sass');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var browserSync = require('browser-sync');
// Compile Our Sass
gulp.task('css', function() {
    return gulp.src('client/dev/css/**/*.css')
//        .pipe(sass())
        .pipe(gulp.dest('client/public/css'));

});



// Concatenate & Minify JS
gulp.task('scripts-lib', function() {
    return gulp.src('client/dev/js/lib/**/*.js')
        .pipe(concat('client/dev/js/temp/lib.js'))
        .pipe(rename('lib.min.js'))
        .pipe(gulp.dest('client/public/js/'));
});
gulp.task('scripts', function() {
    return gulp.src('client/dev/js/app/**/*.js')
        .pipe(concat('client/dev/js/temp/app.js'))
        .pipe(rename('app.min.js'))
        //.pipe(uglify())
        .pipe(gulp.dest('client/public/js/'));
});

gulp.task('template', function() {
    var str = gulp.src('client/dev/html/**/*.html')
        .pipe(gulp.dest('client/public/'));

	return str;

});
gulp.task('data', function() {
    var str = gulp.src('client/dev/data/**/*.*')
        .pipe(gulp.dest('client/public/data/'));

	return str;

});
gulp.task('map', function() {
    var str = gulp.src('client/dev/css/**/*.map')
        .pipe(gulp.dest('client/public/css/'));

    return str;

});
gulp.task('font',function(){
    var str = gulp.src('client/dev/fonts/**/*.map')
        .pipe(gulp.dest('client/public/fonts/'));

    return str;
});

gulp.task('browser-sync', function() {
    browserSync.init(["public/css/**/*.css", "public/js/**/*.js","public/**/*.html"], {
        server: {
            baseDir: "./public/"
        }
    });
});

// Watch Files For Changes
gulp.task('dev', function() {
    gulp.watch('client/dev/js/app/**/*.js', ['scripts']);
    gulp.watch('client/dev/js/lib/**/*.js', ['scripts-lib']);
    gulp.watch('client/dev/css/**/*.css', ['css']);
    gulp.watch('client/dev/html/**/*.html',['template']);
    gulp.watch('client/dev/data/**/*.*',['data']);
    gulp.watch('client/dev/css/**/*.map',['map']);
    gulp.watch('client/dev/fonts/**/*.*',['font']);
});