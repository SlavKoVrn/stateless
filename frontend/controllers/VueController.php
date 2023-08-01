<?php

namespace frontend\controllers;

use common\models\Product;

class VueController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $model = new Product;
        $price_min = Product::find()->min('price');
        $price_max = Product::find()->max('price');
        return $this->render('index', compact('model','price_min','price_max'));
    }

}
