<?php
namespace frontend\tests\functional;

use common\fixtures\ProductFixture;
use common\fixtures\UserFixture;
use frontend\tests\FunctionalTester;
use Faker\Factory;

class ProductCest
{
    public function _fixtures()
    {
        return [
            'product' => [
                'class' => ProductFixture::class,
                'dataFile' => codecept_data_dir() . 'product.php',
            ],
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'user.php',
            ],
        ];
    }

    public function getProduct(FunctionalTester $I)
    {
        $I->expectTo('get Product');
        $I->sendGET('/api/product');
        $I->seeResponseCodeIs(200);
    }

    public function postProduct(FunctionalTester $I)
    {
        $I->sendPOST('/auth',[
            'username' => 'admin',
            'password' => '123456'
        ]);
        $responseContent = $I->grabResponse();
        $jsonResponse = json_decode($responseContent, true);
        echo 'auth = '.print_r($jsonResponse,true);

        $I->expectTo('post Product');
        $faker = Factory::create('ru_RU');
        $name = $faker->realText(22);
        $description = $faker->realText(1000);
        $I->sendPOST('/api/product?access-token='.$jsonResponse['token'],[
            'name' => $name,
            'description' => $description,
        ]);
        $I->seeResponseCodeIs(201);
        
        $I->sendGET('/api/product?s[name]='.$name);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'name' => $name,
            'description' => $description,
        ]);
        $responseContent = $I->grabResponse();
        $jsonResponse = json_decode($responseContent, true);
        $count = count($jsonResponse);
        $I->seeHttpHeader('X-Pagination-Total-Count', strval($count));
    }

}