'use strict';

module.exports = (gulp, plugins, config) => () => {
  gulp.watch(config.paths.styles, gulp.series('build:styles'));  
  gulp.watch(config.paths.svg_sprite, gulp.series('clean:svg','build:svg'));
};
