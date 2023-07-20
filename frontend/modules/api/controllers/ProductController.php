<?php

namespace frontend\modules\api\controllers;

use common\models\Product;
use frontend\modules\api\models\ProductSearch;
use Yii;

class ProductController extends \yii\rest\ActiveController
{
    public $modelClass = Product::class;

    public function actions(){
        $actions = parent::actions();
        unset($actions['create'],$actions['update'],$actions['delete']);
        $actions['index']['prepareDataProvider'] = [$this,'prepareDataProvider'];
        return $actions;
    }

    public function prepareDataProvider()
    {
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }
        $searchModel = new ProductSearch;
        return $searchModel->search($requestParams);
    }

}
