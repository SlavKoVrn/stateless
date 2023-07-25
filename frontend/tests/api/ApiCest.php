<?php
namespace frontend\tests\functional;

use common\fixtures\UserFixture;
use frontend\tests\FunctionalTester;

class ApiCest
{
    public function _fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'api.user.php',
            ],
        ];
    }

    public function badMethod(FunctionalTester $I)
    {
        $I->expectTo('badMethod');
        $I->sendGET('/auth');
        $I->seeResponseCodeIs(404);
    }

    public function wrongCredentials(FunctionalTester $I)
    {
        $I->sendPOST('/auth',[
            'username' => 'erau',
            'password' => 'wrong-password'
        ]);
        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'field' => 'password',
            'message' => 'Incorrect username or password.',
        ]);
    }

    public function getProfileWithoutCredentions(FunctionalTester $I)
    {
        $I->sendGET('/api/profile');
        $I->seeResponseCodeIs(401);
    }

    public function success(FunctionalTester $I)
    {
        $I->expectTo('correct auth');
        $I->sendPOST('/auth',[
            'username' => 'admin',
            'password' => '123456'
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->canSeeResponseJsonMatchesJsonPath('$.token');
        $I->canSeeResponseJsonMatchesJsonPath('$.expired');
        $responseContent = $I->grabResponse();
        $jsonResponse = json_decode($responseContent, true);
        $I->sendGET('/api/profile',[
            'access-token' => $jsonResponse['token'],
        ]);
        $I->seeResponseCodeIs(200);
    }


}