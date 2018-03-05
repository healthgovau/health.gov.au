var gulp               = require('gulp');
var del                = require('del');
var gulpLoadPlugins    = require('gulp-load-plugins');
var bs                 = require('browser-sync');
var kss                = require('kss');
var path               = require('path');
var gcmq               = require('gulp-group-css-media-queries');
var gemq               = require('gulp-extract-media-queries');
var cleanCSS           = require('gulp-clean-css');
var runSequence        = require('run-sequence');
var del                = require('del');
var fs                 = require('fs');

var options = {};

options.rootPath = {
  project     : __dirname + '/',
  styleGuide  : __dirname + '/styleguide/',
  theme       : __dirname + '/'
};

options.theme = {
  root  : options.rootPath.theme,
  css   : options.rootPath.theme + 'css/',
  sass  : options.rootPath.theme + 'sass/',
  js    : options.rootPath.theme + 'js/'
};

var sassFiles = [
  options.theme.sass + '**/*.scss',
  // Do not open Sass partials as they will be included as needed.
  '!' + options.theme.sass + '**/_*.scss',
  // Hide additional files
  '!' + options.theme.sass + 'vendors/uikit.scss'
];

// Set the URL used to access the Drupal website under development. This will
// allow Browser Sync to serve the website and update CSS changes on the fly.
options.drupalURL = 'http://health.local';
// options.drupalURL = 'http://localhost'

// Define the style guide paths and options.
options.styleGuide = {
  source: [
    options.theme.sass,
    options.theme.css + 'style-guide/'
  ],
  destination: options.rootPath.styleGuide,

  builder: 'builder/twig',

  // The css and js paths are URLs, like '/misc/jquery.js'.
  // The following paths are relative to the generated style guide.
  css: [
    path.relative(options.rootPath.styleGuide, options.theme.css + 'styles.css'),
    path.relative(options.rootPath.styleGuide, options.theme.css + 'ckeditor.css'),
    path.relative(options.rootPath.styleGuide, options.theme.css + 'ie8.css'),
    path.relative(options.rootPath.styleGuide, options.theme.css + 'style-guide/kss-only.css')
  ],
  js: [
  ],

  homepage: 'homepage.md',
  title: 'Parkes Style Guide'
};

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

// Clean style guide files.
gulp.task('clean:styleguide', function() {
  // You can use multiple globbing patterns as you would with `gulp.src`
  return del([
      options.styleGuide.destination + '*.html',
      options.styleGuide.destination + 'public',
      options.theme.css + '**/*.twig'
    ], {force: true});
});

// Clean CSS files.
gulp.task('clean:css', function() {
  return del([
      options.theme.css + '**/*.css',
      options.theme.css + '**/*.map',
      '!' + options.theme.css + 'fontawesome.css',
      '!' + options.theme.css + 'print.css'
    ], {force: true});
});

/**
 * Generate styles with soucemap and uncompressed.
 */
gulp.task('styles:dev', function(callback) {
  runSequence(
    'clean:css',
    'styles:generate:dev',
    'css:break-at-media',
    callback);
});

/**
 * Generate styles without sourcemap and compress them.
 */
gulp.task('styles:prod', function(callback) {
  runSequence(
    'clean:css',
    'styles:generate:prod',
    'css:break-at-media',
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
 * Break a css into separate files for each break point.
 */
gulp.task('css:break-at-media', function() {
  return gulp.src(options.theme.css + '/styles.css')
    .pipe(gcmq())
    .pipe(gemq())
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
 * Generate css without soucemap.
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

gulp.task('styleguide', ['clean:styleguide'], function() {
  return kss(options.styleGuide);
});

gulp.task('browser-sync', function() {
  bs.init({
    proxy: options.drupalURL,
    open: false
  });
});

// Watch files for changes & reload
gulp.task('watch', ['browser-sync'], function(){
  gulp.watch([options.theme.sass + '/**/*.scss'], ['styles:dev', bs.reload]);
  gulp.watch(['./templates/*.php', './*.php']).on('change', bs.reload);
});

// Watch and reload
gulp.task('default', ['styles:dev', 'watch']);