const Path = require('path');
const { JavascriptWebpackConfig, CssWebpackConfig } = require('@silverstripe/webpack-config');
const CopyWebpackPlugin = require('copy-webpack-plugin');

const PATHS = {
  ROOT: Path.resolve(),
  SRC: Path.resolve('client/src'),
  DIST: Path.resolve('client/dist'),
  MODULES: 'node_modules',
};

const config = [
  // JavaScript
  new JavascriptWebpackConfig('js', PATHS, 'silverstripe/htmleditor-hugerte')
    .setEntry({
      bundle: `${PATHS.SRC}/bundle.js`,
      // default plugins
      'HugeRTE_sslink': `${PATHS.SRC}/plugins/HugeRTE_sslink.js`,
      'HugeRTE_sslink-external': `${PATHS.SRC}/plugins/HugeRTE_sslink-external.js`,
      'HugeRTE_sslink-email': `${PATHS.SRC}/plugins/HugeRTE_sslink-email.js`,
      'HugeRTE_sslink-phone': `${PATHS.SRC}/plugins/HugeRTE_sslink-phone.js`,
      // asset-admin plugins
      'HugeRTE_ssmedia': `${PATHS.SRC}/plugins/HugeRTE_ssmedia.js`,
      'HugeRTE_ssembed': `${PATHS.SRC}/plugins/HugeRTE_ssembed.js`,
      'HugeRTE_sslink-file': `${PATHS.SRC}/plugins/HugeRTE_sslink-file.js`,
      // cms plugins
      'HugeRTE_sslink-internal': `${PATHS.SRC}/plugins/HugeRTE_sslink-internal.js`,
      'HugeRTE_sslink-anchor': `${PATHS.SRC}/plugins/HugeRTE_sslink-anchor.js`,
    })
    .mergeConfig({
      plugins: [
        new CopyWebpackPlugin({
          patterns: [
            // Copy npm and custom hugerte content into the same dist directory
            {
              from: `${PATHS.MODULES}/hugerte`,
              to: `${PATHS.DIST}/hugerte`
            },
            {
              from: `${PATHS.SRC}/hugerte`,
              to: `${PATHS.DIST}/hugerte`
            },
          ]
        }),
      ],
    })
    .getConfig(),
  // sass to css
  new CssWebpackConfig('css', PATHS)
    .setEntry({
      editor: `${PATHS.SRC}/styles/editor.scss`,
    })
    .getConfig(),
];

// Use WEBPACK_CHILD=js or WEBPACK_CHILD=css env var to run a single config
module.exports = (process.env.WEBPACK_CHILD)
  ? config.find((entry) => entry.name === process.env.WEBPACK_CHILD)
  : config;
