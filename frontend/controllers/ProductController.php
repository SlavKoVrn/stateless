<?php

namespace frontend\controllers;

use common\models\Product;

class ProductController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $model = new Product;
        return $this->render('index', compact('model'));
    }

}
