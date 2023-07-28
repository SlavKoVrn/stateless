<?php
namespace console\controllers;

use common\models\Good;
use Faker\Factory;
use yii\console\Controller;

class GoodController extends Controller
{
    public function actionIndex()
    {
        $date = new \DateTime('now', new \DateTimeZone('Europe/Moscow'));
        $time = $date->format('Y-m-d H:i:s');
        $faker = Factory::create('ru_RU');
        for ($i = 1; $i <= 100; $i++) {
            $good = new Good;
            $good->setAttributes([
                'user_id' => 1, // admin:123456
                'category_id' => random_int(1, 30),
                'price' => random_int(100, 1000),
                'name' => $faker->realText(22),
                'description' => $faker->realText(1000),
            ]);
            $good->save();
            echo "$good->id. $good->name - $good->slug
";
        }
    }

}