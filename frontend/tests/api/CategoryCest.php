<?php
namespace frontend\tests\functional;

use common\fixtures\CategoryFixture;
use common\fixtures\UserFixture;
use Faker\Factory;
use frontend\tests\FunctionalTester;

class CategoryCest
{
    public function _fixtures()
    {
        return [
            'category' => [
                'class' => CategoryFixture::class,
                'dataFile' => codecept_data_dir() . 'category.php',
            ],
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'user.php',
            ],
        ];
    }

    public function getCategory(FunctionalTester $I)
    {
        $I->expectTo('get Category');
        $I->sendGET('/api/category');
        $I->seeResponseCodeIs(200);
    }

    public function postCategory(FunctionalTester $I)
    {
        $I->sendPOST('/auth',[
            'username' => 'admin',
            'password' => '123456'
        ]);
        $responseContent = $I->grabResponse();
        $jsonResponse = json_decode($responseContent, true);

        $I->expectTo('post Category');
        $faker = Factory::create('ru_RU');
        $name = $faker->realText(22);
        $description = $faker->realText(1000);
        $I->sendPOST('/api/category?access-token='.$jsonResponse['token'],[
            'name' => $name,
            'description' => $description,
        ]);
        $responseContent = $I->grabResponse();
        $jsonResponse = json_decode($responseContent, true);
        echo 'post = '.print_r($jsonResponse,true);
        $I->seeResponseCodeIs(201);

        $I->sendGET('/api/category');
        $I->seeHttpHeader('X-Pagination-Total-Count', '31');

        $I->expectTo('search:'.$name);
        $I->sendGET('/api/category',[
            's[name]'=>$name
        ]);
        $responseContent = $I->grabResponse();
        $jsonResponse = json_decode($responseContent, true);
        echo 'response = '.print_r($responseContent,true);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'name' => $name
        ]);
        $I->seeHttpHeader('X-Pagination-Total-Count', '1');
    }

}