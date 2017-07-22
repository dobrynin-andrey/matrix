const autoprefixer = require('autoprefixer');
const inlineSvg = require('postcss-inline-svg');
const flexbugs = require('postcss-flexbugs-fixes');
const assets = require('postcss-assets');

let config = {
  tasks: './tasks',
  preprocessor: "less",
  isDevelopment: !process.env.NODE_ENV || process.env.NODE_ENV == 'development',
  postcssConfig :[
    autoprefixer({ browsers: ['last 2 versions'] }),
    assets({
      baseUrl:'/',
      loadPaths:['svg-icons/','images/']
    }),
    inlineSvg(),
    flexbugs()
  ],
  paths: {
    //JS
    entry:'./js/index.js',
    js: './js/**/**/*.{js,jsx}',
    //SVG
    svg_sprite: './svg-sprite/**/*.svg',
    //DIST
    dist: './public',    
  },
  output: {
    js: 'js',
    css: 'css',
    images: 'images',
    svg:'svg',
    sprite:'sprite'
  }
};
//STYLES
config.paths.styles = config.preprocessor == "sass" ? ['./styles/*.scss', './styles/**/*.css'] : ['./styles/**.less','./styles/layout/**.less','./styles/pages/**.less','./styles/plugins/**.less','./styles/service/**.less',];
config.paths.indexStyle = config.preprocessor == "sass" ? ['./styles/index.scss'] : ['./styles/index.less'];

module.exports = config;