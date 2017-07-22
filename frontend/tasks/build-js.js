'use strict';

const webpackStream = require('webpack-stream');
const webpackConfig = require('./configs/webpack.config');
const plumber = require('gulp-plumber');
const notify = require('gulp-notify');
const gulplog = require('gulplog');


module.exports = (gulp, plugins, config) => (callback) => {
  let firstBuildReady = false;
  function done(err, stats) {
     firstBuildReady = true;

     if (err) { // hard error, see https://webpack.github.io/docs/node.js-api.html#error-handling
       return;  // emit('error', err) in webpack-stream
     }

     gulplog[stats.hasErrors() ? 'error' : 'info'](stats.toString({
       colors: true,
       chunks:false
     }));

   }

  return gulp.src(config.paths.js)
    .pipe(plumber({ errorHandler:function(err){
      return notify("Hey dawg, error occured in Webpack, let's repair it")
    }}))
    .pipe(webpackStream(webpackConfig,null,done))
    .pipe(gulp.dest(`${config.paths.dist}/${config.output.js}`))
    .on('data', function() {
       if (firstBuildReady) {
         callback();
       }
     });
};
