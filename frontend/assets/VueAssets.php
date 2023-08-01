<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class VueAssets extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        '/frontend/web/webpack/app.js',
    ];
    /*
    public $css = [
        'css/site.css',
        'https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons',
        'https://unpkg.com/vuetify@0.17.4/dist/vuetify.min.css',
    ];
    public $js = [
        'app.js',
        'https://unpkg.com/vue/dist/vue.js',
        'https://unpkg.com/vuetify@0.17.4/dist/vuetify.min.js',
    ];
    */

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];
}