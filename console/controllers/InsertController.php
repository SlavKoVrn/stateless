<?php

namespace console\controllers;

use common\models\Category;
use common\models\Product;
use common\models\ProductTag;
use common\models\Tag;
use Faker\Factory;
use yii\console\Controller;

class InsertController extends Controller
{
    public function actionIndex()
    {
        $faker = Factory::create('ru_RU');
        for ($i = 1; $i <= 10; $i++) {
            $tag = new Tag;
            $tag->setAttributes([
                'name' => $faker->realText(22),
            ]);
            $tag->save();
            echo "$tag->id. $tag->name\n";
        }
        for ($i = 1; $i <= 30; $i++) {
            $category = new Category;
            $category->setAttributes([
                'name' => $faker->realText(22),
                'description' => $faker->realText(1000),
            ]);
            $category->save();
            echo "$category->id. $category->name - $category->slug\n";
        }
        for ($i = 1; $i <= 100; $i++) {
            $product = new Product;
            $product->setAttributes([
                'name' => $faker->realText(22),
                'category_id' => random_int(1, 30),
                'price' => random_int(100, 1000),
                'description' => $faker->realText(1000),
            ]);
            $product->save();
            for ($j = 1; $j <= 10; $j++) {
                $product_tag = new ProductTag;
                $product_tag->setAttributes([
                    'product_id' => $product->id,
                    'tag_id' => random_int(1, 10),
                ]);
                $product_tag->save();
            }
            echo "$product->id. $product->name - $product->slug\n";
        }
    }

}
