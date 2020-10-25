var gulp = require('gulp')
var watch = require('gulp-watch')
var clean = require('gulp-clean')
var connect = require('gulp-connect')
var browserSync = require('browser-sync');
var minimist = require('minimist')

var WATCH_PATH = 'system/modules/**/*.(js|css|gif|png|jpg|jpeg)'
var PHP_PATH = 'system/modules/**/*.php'
var CLEAN_PATH = 'static/!(css|font|image|js|login|office)'



function cleanStaticCache() {
  console.log('clean and reload')
  gulp.src(CLEAN_PATH)
    .pipe(clean())
    .pipe(connect.reload())
    .pipe(browserSync.reload({stream:true}))
}

gulp.task('watch', function () {
  return watch(WATCH_PATH, cleanStaticCache)
});

gulp.task('clean', cleanStaticCache);

gulp.task('connect', function() {
  var options = minimist(process.argv.slice(2));
  var host = options.host || 'ibos.cc'
  var port = options.port || 8080

  connect.server({
    livereload: true
  })

  browserSync({
    proxy: host,
    port: port,
    open: true,
    notify: false
  });
})

gulp.task('default', ['connect', 'watch'])
