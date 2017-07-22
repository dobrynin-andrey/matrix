'use strict';
const plumber = require('gulp-plumber');
const notify = require('gulp-notify');

module.exports = (gulp, plugins, config) => () => {
  return gulp.src(`${config.paths.svg_sprite}`)
    .pipe(plumber({errorHandler:notify.onError(function(err){
      return {
        title:"Hey guy, error occured in durring SVG, let's repair it",
        message:err.message
      }
})
}
))
    .pipe(plugins.svgSprite({
      shape :
       {         
         spacing :
         {
             padding : 2
         }
       },
      mode: {
           dest:'',
           css:{
             dest:'public/css',
             sprite:'../images/sprite.svg',
             bust: true,
             dimensions:     true,
             render:config.preprocessor == 'sass' ? {
               scss:{
                  dest : '../../styles/service/sprite.scss'
               }
             }:
             {
               less:{
                  dest : '../../styles/service/sprite.less'
               }
             }              
           }
      },
      svg: {
        xmlDeclaration: false,
        doctypeDeclaration: false
      }
    }))
    .pipe(gulp.dest(`.`))
};
