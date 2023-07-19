<?php
/**
 * reDream http://redream.ru
 */

namespace common\assets;

use yii\web\AssetBundle;

class DataTableAsset extends AssetBundle
{

    public $sourcePath = '@common/assets/src/dataTable';

    public $css = [
        'jquery.dataTables.css',
    ];

    public $js = [
        'jquery.dataTables.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset'
    ];
}
