const Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    .addEntry('app', './assets/app.ts')
    .addEntry('dialog', './assets/dialog/index.ts')
    .addEntry('admin_editor', './assets/admin/editor.js')

    .addEntry('stimulus', './assets/bootstrap.js')
    .enableStimulusBridge('./assets/controllers.json')

    .addEntry('editor.worker', './node_modules/monaco-editor/esm/vs/editor/editor.worker.js')
    .addEntry('json.worker', './node_modules/monaco-editor/esm/vs/language/json/json.worker')
    .addEntry('css.worker', './node_modules/monaco-editor/esm/vs/language/css/css.worker')
    .addEntry('html.worker', './node_modules/monaco-editor/esm/vs/language/html/html.worker')
    .addEntry('ts.worker', './node_modules/monaco-editor/esm/vs/language/typescript/ts.worker')

    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()

    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())

    .disableSingleRuntimeChunk()

    .enableTypeScriptLoader()
    .enableSassLoader()

    .addRule({ test: /\.txt/, use: [{loader: 'raw-loader'}] })
;

module.exports = Encore.getWebpackConfig();
