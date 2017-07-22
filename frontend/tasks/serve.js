'use strict';

const bs = require('browser-sync');

module.exports = (gulp, plugins, config) => () => {
  bs.init({ server: './' })
  bs.watch(['./public/**/*.*','*.html']).on('change', bs.reload);

};
