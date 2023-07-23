<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;

class FixturesController extends Controller
{
    public function actionIndex()
    {
        $models = ['Product','Category','Tag','ProductTag'];
        foreach ($models as $model){
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
            file_put_contents($fileFixture,$modelFixture);
            $modelName = "common\\models\\".$model;
            $lowerModel = strtolower($model);
            $allModels = $modelName::find()->all();
            $modelsArray = [];
            foreach ($allModels as $currentModel){
                $modelsArray[] = $currentModel->attributes;

            }
            $fileData = Yii::getAlias('@frontend').'/tests/_data/'.$lowerModel.'.php';
            $fileContent = "<?php\nreturn ".var_export($modelsArray, true).";";
            file_put_contents($fileData, $fileContent);
            $testContent =<<<TEST_CONTENT
<?php
namespace frontend\\tests\\functional;

use common\\fixtures\\{$model}Fixture;
use frontend\\tests\\FunctionalTester;

class {$model}Cest
{
    public function _fixtures()
    {
        return [
            '{$lowerModel}' => [
                'class' => {$model}Fixture::class,
                'dataFile' => codecept_data_dir() . '{$lowerModel}.php',
            ],
        ];
    }

    public function get{$model}(FunctionalTester \$I)
    {
        \$I->expectTo('get {$model}');
        \$I->sendGET('/{$lowerModel}');
        \$I->seeResponseCodeIs(200);
    }

}
TEST_CONTENT;
            $fileTest = Yii::getAlias('@frontend').'/tests/api/'.$model.'Cest.php';
            file_put_contents($fileTest,$testContent);
            echo "$fileFixture\n";
        }
    }

}
