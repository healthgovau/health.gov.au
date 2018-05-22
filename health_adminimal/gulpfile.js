var gulp               = require('gulp');
var del                = require('del');
var gulpLoadPlugins    = require('gulp-load-plugins');
var path               = require('path');
var cleanCSS           = require('gulp-clean-css');
var runSequence        = require('run-sequence');
var fs                 = require('fs');
var minify             = require('gulp-minify');
var rename             = require("gulp-rename");

var options = {};

options.rootPath = {
  theme       : __dirname + '/'
};

options.theme = {
  root  : options.rootPath.theme,
  css   : options.rootPath.theme + 'css/',
  sass  : options.rootPath.theme + 'sass/'
};

var sassFiles = [
  options.theme.sass + '**/*.scss',
  '!' + options.theme.sass + '**/_*.scss',
];

// Set the URL used to access the Drupal website under development. This will
// allow Browser Sync to serve the website and update CSS changes on the fly.
options.drupalURL = 'http://beta-saas.local';

var $ = gulpLoadPlugins();

var AUTOPREFIXER_BROWSERS = [
  'ie >= 8',
  'ie_mob >= 10',
  'ff >= 30',
  'chrome >= 34',
  'safari >= 7',
  'opera >= 23',
  'ios >= 7',
  'android >= 4.4',
  'bb >= 10'
];

// Clean all directories.
gulp.task('clean', ['clean:css']);

// Clean CSS files.
gulp.task('clean:css', function() {
  return del([
      options.theme.css + '**/*.css',
      options.theme.css + '**/*.map',
    ], {force: true});
});

/**
 * Generate styles with soucemap and uncompressed.
 */
gulp.task('styles:dev', function(callback) {
  runSequence(
    'clean:css',
    'styles:generate:dev',
    callback);
});

/**
 * Generate styles without sourcemap and compress them.
 */
gulp.task('styles:prod', function(callback) {
  runSequence(
    'clean:css',
    'styles:generate:prod',
    'css:compress',
    callback);
});

/**
 * Compress the css.
 */
gulp.task('css:compress', function(){
  return gulp.src(options.theme.css + '/*.css')
    .pipe(cleanCSS({compatibility: 'ie8'}))
    .pipe(gulp.dest(options.theme.css));
});

/**
 * Generate css with source map.
 */
gulp.task('styles:generate:dev', function(){
  return gulp.src(sassFiles)
    .pipe($.sourcemaps.init())
    .pipe($.sass({
      sourcemap: true,
      precision: 10
    }).on('error', $.sass.logError))
    .pipe($.autoprefixer(AUTOPREFIXER_BROWSERS))
    .pipe($.sourcemaps.write('./'))
    .pipe($.size({title: 'Styles'}))
    .pipe(gulp.dest(options.theme.css));
});

/**
 * Generate css without sourcemap.
 */
gulp.task('styles:generate:prod', function(){
  return gulp.src(sassFiles)
    .pipe($.sass({
      precision: 10,
      sourcemap: false
    }).on('error', $.sass.logError))
    .pipe($.autoprefixer(AUTOPREFIXER_BROWSERS))
    .pipe($.size({title: 'Styles (production)'}))
    .pipe(gulp.dest(options.theme.css));
});

// Watch and reload
gulp.task('prod', ['styles:prod']);

// Watch and reload
gulp.task('default', ['styles:dev']);