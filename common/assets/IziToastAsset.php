<?php
/**
 * reDream http://redream.ru
 */

namespace common\assets;

use yii\web\AssetBundle;

/**
 * Class IziToastAsset
 * @package common\assets
 */
class IziToastAsset extends AssetBundle
{

    public $sourcePath = '@common/assets/src/iziToast';

    public $css = [
        'css/iziToast.min.css',
    ];

    public $js = [
        'js/iziToast.min.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset'
    ];
}
