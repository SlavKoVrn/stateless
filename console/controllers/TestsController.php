<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\gii\generators\crud\Generator;

class TestsController extends Controller
{
    private $overwrite = true;
    private function setFixture($model)
    {
        $modelFixture =<<<FIXTURE
<?php
namespace common\\fixtures;
use common\\models\\{$model};
use yii\\test\\ActiveFixture;

class {$model}Fixture extends ActiveFixture
{
    public \$modelClass = {$model}::class;
}
FIXTURE;
        $fileFixture = Yii::getAlias('@common').'/fixtures/'.$model.'Fixture.php';
        if ($this->overwrite){
            file_put_contents($fileFixture,$modelFixture);
        }elseif (!is_file($fileFixture)){
            file_put_contents($fileFixture,$modelFixture);
        }
    }

    private function setFixtureData($model)
    {
        $lowerModel = strtolower($model);
        $modelName = "common\\models\\".$model;
        $allModels = $modelName::find()->all();
        $modelsArray = [];
        foreach ($allModels as $currentModel){
            $modelsArray[] = $currentModel->attributes;

        }
        $fileData = Yii::getAlias('@frontend').'/tests/_data/'.$lowerModel.'.php';
        $fileContent = "<?php\nreturn ".var_export($modelsArray, true).";";
        if ($this->overwrite){
            file_put_contents($fileData, $fileContent);
        }elseif (!is_file($fileData)){
            file_put_contents($fileData, $fileContent);
        }
    }

    private function setTest($model)
    {
        $lowerModel = strtolower($model);
        $route = \yii\helpers\Inflector::camel2id($model, '-');

        $testContent =<<<TEST_CONTENT
<?php
namespace frontend\\tests\\functional;

use common\\fixtures\\{$model}Fixture;
use common\\fixtures\\UserFixture;
use frontend\\tests\\FunctionalTester;
use Faker\Factory;

class {$model}Cest
{
    public function _fixtures()
    {
        return [
            '{$lowerModel}' => [
                'class' => {$model}Fixture::class,
                'dataFile' => codecept_data_dir() . '{$lowerModel}.php',
            ],
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'user.php',
            ],
        ];
    }

    public function get{$model}(FunctionalTester \$I)
    {
        \$I->expectTo('get {$model}');
        \$I->sendGET('/api/{$route}');
        \$I->seeResponseCodeIs(200);
    }

    public function post{$model}(FunctionalTester \$I)
    {
        \$I->sendPOST('/auth',[
            'username' => 'admin',
            'password' => '123456'
        ]);
        \$responseContent = \$I->grabResponse();
        \$jsonResponse = json_decode(\$responseContent, true);

        \$I->expectTo('post {$model}');
        \$faker = Factory::create('ru_RU');
        \$name = \$faker->realText(22);
        \$description = \$faker->realText(1000);
        \$I->sendPOST('/api/{$route}?access-token='.\$jsonResponse['token'],[
            'name' => \$name,
            'description' => \$description,
        ]);
        \$I->seeResponseCodeIs(201);
        
        \$I->sendGET('/api/{$route}?s[name]='.\$name);
        \$I->seeResponseCodeIs(200);
        \$I->seeResponseContainsJson([
            'name' => \$name,
            'description' => \$description,
        ]);
        \$I->seeHttpHeader('X-Pagination-Total-Count', 1);
    }

}
TEST_CONTENT;
        $fileTest = Yii::getAlias('@frontend').'/tests/api/'.$model.'Cest.php';
        file_put_contents($fileTest,$testContent);
    }

    private function setSearch($model)
    {
        $generator = new Generator;
        $generator->modelClass = "common\\models\\".$model;
        $rules = $generator->generateSearchRules();
        $rules = implode(",\n            ", $rules);
        $searchConditions = $generator->generateSearchConditions();
        $searchConditions = implode("\n        ", $searchConditions);
        $searchModel =<<<SEARCH_MODEL
<?php
namespace frontend\\modules\\api\\models;
use common\\models\\{$model};
use yii\\data\\ActiveDataProvider;
class {$model}Search extends {$model}
{
    public function rules()
    {
        return [
            {$rules}
        ];
    }
    public function search(\$params)
    {
        \$query = {$model}::find();
        \$dataProvider = new ActiveDataProvider([
            'query' => \$query,
        ]);
        \$this->load(\$params);
        if (!\$this->validate()) {
            return \$dataProvider;
        }
        {$searchConditions}
        return \$dataProvider;
    }
    public function formName()
    {
        return 's';
    }
}
SEARCH_MODEL;
        $fileSearch = Yii::getAlias('@frontend').'/modules/api/models/'.$model.'Search.php';
        if ($this->overwrite){
            file_put_contents($fileSearch,$searchModel);
        }elseif (!is_file($fileSearch)){
            file_put_contents($fileSearch,$searchModel);
        }
    }

    private function setController($model)
    {
        $controller =<<<CONTROLLER
<?php
namespace frontend\\modules\\api\\controllers;
use common\\models\\{$model};
use frontend\\modules\\api\\models\\{$model}Search;
use common\\rbac\\Rbac;
use Yii;
use yii\\helpers\\Url;
use yii\\filters\\AccessControl;
use yii\\filters\\auth\QueryParamAuth;
use yii\\filters\\auth\\HttpBasicAuth;
use yii\\filters\\auth\\HttpBearerAuth;
use yii\\web\\ForbiddenHttpException;
use yii\\web\\ServerErrorHttpException;

class {$model}Controller extends \\yii\\rest\\ActiveController
{
    public \$modelClass = {$model}::class;

    public function behaviors()
    {
        \$behaviors = parent::behaviors();
        \$behaviors['authenticator']['only'] = ['create', 'update', 'delete'];
        \$behaviors['authenticator']['authMethods'] = [
            HttpBasicAuth::class,
            HttpBearerAuth::class,
            QueryParamAuth::class,
        ];
        \$behaviors['access'] = [
            'class' => AccessControl::class,
            'only' => ['create', 'update', 'delete'],
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];
        return \$behaviors;
    }

    public function actions(){
        \$actions = parent::actions();
        unset(\$actions['create']);
        \$actions['index']['prepareDataProvider'] = [\$this,'prepareDataProvider'];
        return \$actions;
    }
    public function prepareDataProvider()
    {
        \$requestParams = Yii::\$app->getRequest()->getBodyParams();
        if (empty(\$requestParams)) {
            \$requestParams = Yii::\$app->getRequest()->getQueryParams();
        }
        \$searchModel = new {$model}Search;
        return \$searchModel->search(\$requestParams);
    }

    public function actionCreate()
    {
        \$model = new {$model};
        \$model->user_id = Yii::\$app->user->id;

        \$model->load(Yii::\$app->getRequest()->getBodyParams(), '');
        if (\$model->save()) {
            \$response = Yii::\$app->getResponse();
            \$response->setStatusCode(201);
            \$id = implode(',', \$model->getPrimaryKey(true));
            \$response->getHeaders()->set('Location', Url::toRoute(['view', 'id' => \$id], true));
        } elseif (!\$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }
        return \$model;
    }

    public function checkAccess(\$action, \$model = null, \$params = [])
    {
        if (in_array(\$action,['update','delete'])){
            if (Yii::\$app->user->can(Rbac::MANAGE_PRODUCT,['product' => \$model])){
                throw new ForbiddenHttpException('Forbidden');
            }
        }
    }
}
CONTROLLER;
        $fileController = Yii::getAlias('@frontend').'/modules/api/controllers/'.$model.'Controller.php';
        if ($this->overwrite){
            file_put_contents($fileController,$controller);
        }elseif (!is_file($fileController)){
            file_put_contents($fileController,$controller);
        }
    }

    public function actionIndex()
    {
        $models = ['User', 'Product', 'Category', 'Tag', 'ProductTag'];
        $models = ['Product'];
        foreach ($models as $model){
            $this->setFixture($model);
            $this->setFixtureData($model);
            $this->setTest($model);
            $this->setSearch($model);
            $this->setController($model);
            echo "$model\n";
        }
    }

}
