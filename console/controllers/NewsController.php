<?php

namespace console\controllers;

use common\models\News;
use Faker\Factory;
use yii\console\Controller;

class NewsController extends Controller
{
    public function actionIndex()
    {
        $faker = Factory::create('ru_RU');
        for ($i = 1; $i <= 100; $i++) {
            $time = time() + (24 * 3600 * $i);
            $news = new News;
            $news->setAttributes([
                'title' => $faker->realText(22),
                'text' => $faker->realText(555),
		        'image' => '/static/images/1/image.jpg',
                'created_at' => date('Y-m-d H:i:s',$time),
                'updated_at' => date('Y-m-d H:i:s',$time),
            ]);
            $news->save();
            echo "$news->id. $news->title\n";
        }
    }

}
