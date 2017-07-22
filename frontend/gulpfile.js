'use strict';

const gulp = require('gulp');
const plugins = require('gulp-load-plugins')();
const config = require('./tasks/configs/main.config');
const register = require(`./tasks/utils/register`);
const gutil = require('gulp-util');

const onerror = function(err) {
  console.log(err.toString());
  gutil.beep();
  this.emit('end');
};

register(gulp, plugins, config, onerror)({
  'build:js': 'build-js',
  'clean:svg': 'clean-svg',
  'watch': 'watch',
  'serve': 'serve',
  "build:svg":"build-svg",
  "build:styles":"build-styles"
});

gulp.task('build', gulp.series('clean:svg','build:svg','build:styles',gulp.parallel('build:js')));


gulp.task('default', gulp.series('build', gulp.parallel('serve','watch')));
