const path = require('path');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
const WebpackAssetsManifest = require('webpack-assets-manifest');
const FileManagerPlugin = require('filemanager-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const { ProvidePlugin } = require('webpack');

const {
  wwwDir,
  projectname,
  sassLoader,
  imageLoader,
  fontLoader,
  jsLoader,
  cssLoader,
  aliasData,
} = require('./webpack.common');

require('dotenv').config();

module.exports = (env, argv) => {
  if (env != undefined && env.deploy) {
    process.env = {
      wwwDir: wwwDir,
      projectname: projectname,
    };
  }

  var pluginPath = process.env.wwwDir;
  var component = '';

  if (argv.mode === 'development') {
    component = '/components/com_aesirx_bi';
  }

  var config = {
    entry: './assets/index.js',

    plugins: [
      new ProvidePlugin({
        process: 'process/browser',
      }),
      new HtmlWebpackPlugin({
        inject: false,
        filename: pluginPath + '/administrator' + component + '/tmpl/dashboards/default.php',
        template: './extensions/administrator/tmpl/dashboards/default.php',
        minify: false,
      }),

      new FileManagerPlugin({
        events: {
          onStart: {
            copy: [
              {
                source: path.resolve(__dirname, './extensions/administrator'),
                destination: pluginPath + '/administrator' + component,
              },
              {
                source: path.resolve(__dirname, './extensions/plugins'),
                destination: pluginPath + '/plugins',
              },
              {
                source: path.resolve(__dirname, './extensions/install.php'),
                destination:
                  pluginPath +
                  (argv.mode === 'development' ? '/administrator' + component : '') +
                  '/install.php',
              },
              {
                source: path.resolve(__dirname, './extensions/aesirx_bi.xml'),
                destination:
                  pluginPath +
                  (argv.mode === 'development' ? '/administrator' + component : '') +
                  '/aesirx_bi.xml',
              },
            ],
          },
          onEnd: {
            copy: [
              {
                source: path.resolve(__dirname, './assets/images/'),
                destination: pluginPath + '/administrator' + component + '/assets/images/',
              },
            ],
          },
        },
      }),

      new WebpackAssetsManifest({
        entrypoints: true,
        publicPath: '/administrator/components/com_aesirx_bi/assets/',
      }),
    ],
    output: {
      path: pluginPath + '/administrator' + component + '/assets/',
      publicPath: '/administrator/components/com_aesirx_bi/assets/',
      clean: true,
    },

    module: {
      rules: [sassLoader, cssLoader, jsLoader, imageLoader, fontLoader],
    },
    optimization: {
      splitChunks: {
        maxInitialRequests: Infinity,
        minSize: 0,
        cacheGroups: {
          vendor: {
            test: /[\\/]node_modules[\\/]/,
            name(module) {
              // get the name. E.g. node_modules/packageName/not/this/part.js
              // or node_modules/packageName
              const packageName = module.context.match(/[\\/]node_modules[\\/](.*?)([\\/]|$)/)[1];

              // npm package names are URL-safe, but some servers don't like @ symbols
              return `npm.${packageName.replace('@', '')}`;
            },
          },
        },
      },
    },
    resolve: {
      alias: aliasData,
    },
  };

  let name = '[name]';

  if (argv.mode === 'development') {
    config.devtool = 'source-map';

    if (env != undefined && env.deploy) {
      name = '[name].[contenthash]';
    }

    config.plugins.push(
      new BrowserSyncPlugin(
        // BrowserSync options
        {
          proxy: 'http://joomla4.local/',
          notify: true,
        }
      ),
      new MiniCssExtractPlugin({
        filename: 'css/' + name + '.css',
      })
    );
  }

  if (argv.mode === 'production') {
    name = '[name].[contenthash]';

    config.optimization.minimize = true;
    config.optimization.minimizer = [
      new TerserPlugin({
        terserOptions: {
          compress: {
            drop_console: true,
          },
        },
      }),
    ];

    config.plugins.push(
      new MiniCssExtractPlugin({
        filename: 'css/' + name + '.css',
      })
    );

    config.plugins.push(
      new FileManagerPlugin({
        events: {
          onEnd: {
            archive: [
              {
                source: './dist/',
                destination: './dist/' + process.env.projectname + '.zip',
              },
            ],
          },
        },
      })
    );
  }

  config.output.filename = 'js/' + name + '.js';
  config.output.chunkFilename = 'js/' + name + '.js';

  return config;
};
