const path = require('path');
const webpack = require('webpack-stream').webpack;
const config = require('./main.config');
let isDevelopment = !process.env.NODE_ENV || process.env.NODE_ENV == "development";
console.log(isDevelopment);

let pluginsProd = [
    new webpack.optimize.UglifyJsPlugin({
        compress: {
            screw_ie8: true,
            warnings: false
        },
        output: {
            comments: false
        },
        sourceMap: false,
    }),
    new webpack.optimize.OccurrenceOrderPlugin(true),
    new webpack.optimize.DedupePlugin(),    
];
let pluginsDev = [        
    new webpack.NoErrorsPlugin()
];

let  plugins = isDevelopment ? pluginsProd : pluginsDev;

module.exports = {
    watch:isDevelopment,
    entry:config.paths.entry,
    devtool: 'eval',
    output: {
        publicPath: '/public/js/',
        filename: 'bundle.js'
    },
    resolve: {
        extensions: ['', '.js', '.jsx','.json']
    },
    plugins: [new webpack.DefinePlugin({
        'NODE_ENV': JSON.stringify(process.env.NODE_ENV)
    }),
     ...plugins
    ],
    module: {
        loaders: [{
            test: /\.jsx?$/,
            loaders: ['babel'],
            include: path.join(__dirname, '../../js'),
            exclude: /node_modules/
        }]
    }
};
