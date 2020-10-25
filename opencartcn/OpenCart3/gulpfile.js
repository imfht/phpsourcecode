/**
 * @copyright        2018 opencart.cn - All Rights Reserved
 * @author:          http://www.guangdawangluo.com
 * @created:         2018-06-29 14:59:58
 * @modified by:     Pu shuo <pushuo@opencart.cn>
 * @modified:        2018-07-25 16:57:45
 */

const gulp        =  require('gulp'),
  sass         =  require('gulp-sass'),
  watch        =  require('gulp-watch'),
  browserSync  =  require('browser-sync'),
  uglify       =  require('gulp-uglify'), // js
  rename       =  require('gulp-rename'), // 重命名
  concat       =  require('gulp-concat'), // 合并
  webp         =  require('gulp-webp'), // webp
  fs           =  require('fs'), // 判断文件
  reload       =  browserSync.reload;

const path = {
  theme: 'upload/catalog/view/theme/',
  admin: 'upload/admin/view/',
  pc: 'upload/catalog/view/theme/default/',
}

gulp.task('browser', () => {
  fs.open('./config.js', 'r', (err, fd) => {
    if (err) return console.log(err + '\n提示: 复制网站根目录下 config.sample.js 为 config.js, 打开然后根据注释信息配置好自己的对应参数, 否则无法使用此 Task');
    let config = require('./config.js');

    browserSync.init({
      proxy: config.browserSync.proxy,
      port: config.browserSync.port,
      notify: false, // 刷新是否提示
      open: false,
      host: config.browserSync.host
    });

    gulp.watch(path.pc + 'scss/**/**/*.scss', ['pc_sass']);
  	gulp.watch(path.theme + "**/**/**/**/*.{twig,js}").on('change', reload);
  });
});

gulp.task('pc_sass', () => {
	gulp.src(path.pc + 'scss/stylesheet.scss')
	.pipe(sass({ outputStyle: 'compressed' }).on('error', sass.logError))
	.pipe(gulp.dest(path.pc + 'stylesheet'))
	.pipe(reload({stream: true}));
});

gulp.task('admin_sass', () => {
  gulp.src(path.admin + 'scss/stylesheet.scss')
  .pipe(sass({ outputStyle: 'compressed' }).on('error', sass.logError))
  .pipe(gulp.dest(path.admin + 'stylesheet'))
  .pipe(reload({stream: true}));
});

gulp.task('watch', ['pc_sass'], () => {
  gulp.watch(path.pc + 'scss/**/**/*.scss', ['pc_sass']);
  gulp.watch(path.admin + 'scss/**/**/*.scss', ['admin_sass']);
});

gulp.task('default', ['browser']);
