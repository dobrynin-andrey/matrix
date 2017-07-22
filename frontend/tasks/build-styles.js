const config = require('./configs/main.config');
const plumber = require('gulp-plumber');
const notify = require('gulp-notify');
const gutil = require('gulp-util');

module.exports = (gulp, plugins, config) => () => {
  var pipeline = gulp.src(`${config.paths.indexStyle}`)
        .pipe(plumber({errorHandler:notify.onError(function(err){          
          return {
            title:"ERROR",
            message:err.message
          }
    })
  }
))
  if(config.isDevelopment){
   pipeline = pipeline
      .pipe(plugins.sourcemaps.init())
  }
       
  pipeline = pipeline
    .pipe(plugins[`${config.preprocessor}`]()) 
    .pipe(plugins.postcss(config.postcssConfig))              
    .pipe(plugins.cssimport());

	if(!config.isDevelopment){
    pipeline = pipeline
        .pipe(plugins.minifyCss())
    }
    pipeline = pipeline
      .pipe(plugins.sourcemaps.write())
      .pipe(plugins.concat('style.css'));

    return pipeline.pipe(gulp.dest(`${config.paths.dist}/${config.output.css}/`));
};
