<?php

namespace frontend\modules\api\controllers;

use common\models\Product;

class ProductController extends \yii\rest\ActiveController
{
    public $modelClass = Product::class;
}
