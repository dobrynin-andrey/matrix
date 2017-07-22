'use strict';
var clean = require('gulp-clean');

module.exports = (gulp, plugins, config) => () => {
        return gulp.src(`./${config.paths.dist}/${config.output.sprite}/*.svg`)
        .pipe(plugins.clean())
};
