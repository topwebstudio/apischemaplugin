//process.env.WEBPACK_PUBLIC_PATH = "/assets/js/old";
process.env.WEBPACK_PUBLIC_PATH = '/assets/build/';


var Encore = require('@symfony/webpack-encore');

Encore.configureRuntimeEnvironment('dev')
        .setOutputPath('public/assets/build')
        .setPublicPath('/assets/build/')

        .enableVersioning()

        .cleanupOutputBeforeBuild()

        .addEntry('admin', './assets/admin/config.js')


        .autoProvideVariables({
            $: 'jquery',
            jQuery: 'jquery'
        })
        .enableSourceMaps(!Encore.isProduction()).enableSassLoader();




const config = Encore.getWebpackConfig();

const path = require('path');


// https://www.fomfus.com/articles/how-to-use-ckeditor-4-with-webpack

config.watchOptions = {
    poll: true
};

config.module = {
   plugins: [
     
    ],
    
    loaders: [
      {
        loader: 'ckeditor5',
        query: {
          path: '/lib/ckeditor5'
       
        }
      }
    ]
 
  
};

module.exports = config;
 
 
 const CKEditorWebpackPlugin = require( '@ckeditor/ckeditor5-dev-webpack-plugin' );
