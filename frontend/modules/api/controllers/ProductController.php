<?php

namespace frontend\modules\api\controllers;

use common\models\Product;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Response;

class ProductController extends \yii\rest\ActiveController
{
    public $modelClass = Product::class;

    public function actions(){
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionIndex(){

        Yii::$app->response->format = Response::FORMAT_JSON;

        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        return Yii::createObject([
            'class' => ActiveDataProvider::class,
            'query' => Product::find(),
            'pagination' => [
                'params' => $requestParams,
            ],
            'sort' => [
                'params' => $requestParams,
            ],
        ]);

    }
}
