<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'name' => 'ProГород Киров',
    'language' => 'ru-RU',
    'defaultRoute' => 'news',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'modules' => [
        'api' => [
            'class' => 'frontend\modules\api\Module',
        ],
        'news' => [
            'class' => 'frontend\modules\news\Module',
        ],
    ],
    'components' => [
        'request' => [
            'baseUrl' => '',
            'csrfParam' => '_csrf-frontend',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'response' => [
            'formatters' => [
                'json' => [
                    'class' => 'yii\web\JsonResponseFormatter',
                    'prettyPrint' => YII_DEBUG,
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                ],
            ],
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'POST auth'=> 'api/site/login',
                'GET profile' => 'api/profile/index',
                [
                    'class'=>\yii\rest\UrlRule::class,
                    'pluralize'=>false,
                    'controller' => ['api/category','api/product'],
                ],
                [
                    'class'=>\yii\rest\UrlRule::class,
                    'pluralize'=>false,
                    'controller' => ['api/good'],
                ],
                'news' => 'news/default/index',
                [
                    'class' => \yii\web\UrlRule::class,
                    'pattern' => 'news/<slug>',
                    'route' => 'news/default/view',
                    'defaults' => ['slug' => null]
                ],
            ],
        ],
    ],
    'params' => $params,
];
