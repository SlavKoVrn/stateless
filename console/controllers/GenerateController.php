<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\db\Migration;
use yii\gii\generators\crud\Generator as CrudGenerator;
use yii\gii\generators\model\Generator as ModelGenerator;
use yii\helpers\Inflector;

class GenerateController extends Controller
{
    private function generateTest($model)
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

    public function index(FunctionalTester \$I)
    {
        \$I->sendGET('/api/{$route}');
        \$I->seeResponseCodeIs(200);
        \$I->seeHttpHeader('X-Pagination-Total-Count', '100');
    }

    public function indexWithAuthor(FunctionalTester \$I)
    {
        \$I->sendGET('/api/{$route}?expand=author');
        \$I->seeResponseCodeIs(200);
        \$I->seeResponseContainsJson([
            [
                'author' => [
                    'username' => 'admin',
                ],
            ]
        ]);
        \$responseContent = \$I->grabResponse();
        \$jsonResponse = json_decode(\$responseContent, true);
        echo 'expand=';
        var_dump(\$jsonResponse);
    }

    public function post{$model}AndSearch(FunctionalTester \$I)
    {
        \$I->sendPOST('/auth',[
            'username' => 'admin',
            'password' => '123456'
        ]);
        \$responseContent = \$I->grabResponse();
        \$jsonResponse = json_decode(\$responseContent, true);
        echo 'auth=';
        var_dump(\$jsonResponse);

        \$faker = Factory::create('ru_RU');
        \$name = \$faker->realText(22);
        \$description = \$faker->realText(1000);
        \$I->sendPOST('/api/{$route}?access-token='.\$jsonResponse['token'],[
            'name' => \$name,
            'description' => \$description,
        ]);
        \$I->seeResponseCodeIs(201);
        \$responseContent = \$I->grabResponse();
        \$jsonResponse = json_decode(\$responseContent, true);
        echo 'post=';
        var_dump(\$jsonResponse);

        \$I->sendGET('/api/{$route}?s[name]='.\$name);
        \$I->seeResponseCodeIs(200);
        \$I->seeResponseContainsJson([
            'name' => \$name,
        ]);
        \$count = \$I->grabHttpHeader('X-Pagination-Total-Count', true);
        echo 'count='.\$count."\n";
        \$responseContent = \$I->grabResponse();
        \$jsonResponse = json_decode(\$responseContent, true);
        echo 'search=';
        var_dump(\$jsonResponse);
    }

    public function post{$model}AndView(FunctionalTester \$I)
    {
        \$I->sendPOST('/auth',[
            'username' => 'admin',
            'password' => '123456'
        ]);
        \$responseContent = \$I->grabResponse();
        \$jsonResponse = json_decode(\$responseContent, true);

        \$faker = Factory::create('ru_RU');
        \$name = \$faker->realText(22);
        \$description = \$faker->realText(1000);
        \$I->sendPOST('/api/{$route}?access-token='.\$jsonResponse['token'],[
            'name' => \$name,
            'description' => \$description,
        ]);
        \$responseContent = \$I->grabResponse();
        \$jsonResponse = json_decode(\$responseContent, true);

        \$I->sendGET('/api/{$route}/'.\$jsonResponse['id']);
        \$I->seeResponseCodeIs(200);
        \$I->seeResponseContainsJson([
            'id' => \$jsonResponse['id'],
            'name' => \$jsonResponse['name'],
        ]);
        \$responseContent = \$I->grabResponse();
        \$jsonResponse = json_decode(\$responseContent, true);
        echo 'view=';
        var_dump(\$jsonResponse);
    }

    public function viewNotFound(FunctionalTester \$I)
    {
        \$I->sendGET('/api/{$route}/1000');
        \$I->seeResponseCodeIs(404);
    }

    public function createUnauthorized(FunctionalTester \$I)
    {
        \$I->sendPOST('/api/{$route}', [
            'name' => 'New',
        ]);
        \$I->seeResponseCodeIs(401);
    }

    public function create(FunctionalTester \$I)
    {
        \$I->sendPOST('/auth',[
            'username' => 'admin',
            'password' => '123456'
        ]);
        \$responseContent = \$I->grabResponse();
        \$jsonResponse = json_decode(\$responseContent, true);

        \$I->amBearerAuthenticated(\$jsonResponse['token']);
        \$faker = Factory::create('ru_RU');
        \$name = \$faker->realText(22);
        \$description = \$faker->realText(1000);
        \$I->sendPOST('/api/{$route}', [
            'name' => \$name,
            'description' => \$description,
        ]);
        \$I->seeResponseCodeIs(201);
        \$I->seeResponseContainsJson([
            'name' => \$name,
            'description' => \$description,
        ]);
    }

    public function updateUnauthorized(FunctionalTester \$I)
    {
        \$I->sendPATCH('/api/{$route}/1', [
            'name' => 'New Name',
        ]);
        \$I->seeResponseCodeIs(401);
    }

    public function update(FunctionalTester \$I)
    {
        \$I->sendPOST('/auth',[
            'username' => 'admin',
            'password' => '123456'
        ]);
        \$responseContent = \$I->grabResponse();
        \$jsonResponse = json_decode(\$responseContent, true);

        \$I->amBearerAuthenticated(\$jsonResponse['token']);
        \$I->sendPATCH('/api/{$route}/1', [
            'name' => 'New Name',
        ]);
        \$I->seeResponseCodeIs(200);
        \$I->seeResponseContainsJson([
            'id' => 1,
            'name' => 'New Name',
        ]);
    }

    public function updateForbidden(FunctionalTester \$I)
    {
        \$I->sendPOST('/auth',[
            'username' => 'admin',
            'password' => '123456'
        ]);
        \$responseContent = \$I->grabResponse();
        \$jsonResponse = json_decode(\$responseContent, true);

        \$I->amBearerAuthenticated(\$jsonResponse['token']);
        \$I->sendPATCH('/api/{$route}/2', [
            'name' => 'New Name',
        ]);
        \$I->seeResponseCodeIs(403);
    }

    public function deleteUnauthorized(FunctionalTester \$I)
    {
        \$I->sendDELETE('/api/{$route}/1');
        \$I->seeResponseCodeIs(401);
    }

    public function delete(FunctionalTester \$I)
    {
        \$I->sendPOST('/auth',[
            'username' => 'admin',
            'password' => '123456'
        ]);
        \$responseContent = \$I->grabResponse();
        \$jsonResponse = json_decode(\$responseContent, true);

        \$I->amBearerAuthenticated(\$jsonResponse['token']);
        \$I->sendDELETE('/api/{$route}/1');
        \$I->seeResponseCodeIs(204);
    }

    public function deleteForbidden(FunctionalTester \$I)
    {
        \$I->sendPOST('/auth',[
            'username' => 'admin',
            'password' => '123456'
        ]);
        \$responseContent = \$I->grabResponse();
        \$jsonResponse = json_decode(\$responseContent, true);

        \$I->amBearerAuthenticated(\$jsonResponse['token']);
        \$I->sendDELETE('/api/{$route}/2');
        \$I->seeResponseCodeIs(403);
    }

}
TEST_CONTENT;
        $fileTest = Yii::getAlias('@frontend').'/tests/api/'.$model.'Cest.php';
        file_put_contents($fileTest,$testContent);
        echo "$fileTest\n";
    }

    public function actionIndex($table)
    {
        $className = Inflector::id2camel($table, '_');
        if ($this->tableExists($table)){
            if (!is_file(Yii::getAlias('@common').'/models/'.$className.'.php')){
                $this->generateModel($table);
                $this->generateInsertController($table);
                echo "php yii $table\n";
                return;
            }
            $model = 'common\\models\\'.$className;
            $count = $model::find()->count();
            if ($count){
                echo $table.' '.$count."\n";
                $this->generateFixture($className);
                $this->generateFixtureData($className);
                $this->generateTest($className);
                $this->generateSearch($className);
                $this->generateController($className);
                echo "php -S 127.0.0.1:8080 -t frontend/web\n";
                echo "php vendor/codeception/codeception/codecept run frontend/tests/api/{$className}Cest\n";
                $config =<<<CONFIG
frontend/config/main.php
    'urlManager' => [
        'rules' => [
            [
                'class'=>\\yii\\rest\\UrlRule::class,
                'pluralize'=>false,
                'controller' => ['api/$table'],
            ]
frontend/modules/api/models/{$className}Search.php
            //->andFilterWhere(['like', 'slug', \$this->slug])
CONFIG;
                echo $config."\n";
            }
        }else{
            $this->generateMigration($table);
            echo "php yii migrate\n";
        }
    }

    public function actionDelete($table)
    {
        $className = Inflector::id2camel($table, '_');
        $migration_dir = Yii::getAlias('@console').'/migrations';
        $migrations = scandir($migration_dir);
        foreach ($migrations as $migration){
            if (str_contains($migration, 'create_table_'.$table.'.php')){
                if ($this->tableExists($table)){
                    echo "php yii migrate/down\n";
                    return;
                }
                $fileMigration = $migration_dir.'/'.$migration;
                unlink($fileMigration);
                echo "$fileMigration\n";
            }
        }
        if ($this->tableExists($table)){
            $migration = new Migration;
            $migration->dropTable($table);
        }
        $fileInsertController = Yii::getAlias('@console').'/controllers/'.$className.'Controller.php';
        if (is_file($fileInsertController)){
            unlink($fileInsertController);
            echo "$fileInsertController\n";
        }
        $fileModel = Yii::getAlias('@common').'/models/'.$className.'.php';
        if (is_file($fileModel)){
            unlink($fileModel);
            echo "$fileModel\n";
        }
        $fileFixture = Yii::getAlias('@common').'/fixtures/'.$className.'Fixture.php';
        if (is_file($fileFixture)){
            unlink($fileFixture);
            echo "$fileFixture\n";
        }
        $fileFixtureData = Yii::getAlias('@frontend').'/tests/_data/'.strtolower($className).'.php';
        if (is_file($fileFixtureData)){
            unlink($fileFixtureData);
            echo "$fileFixtureData\n";
        }
        $fileTest = Yii::getAlias('@frontend').'/tests/api/'.$className.'Cest.php';
        if (is_file($fileTest)){
            unlink($fileTest);
            echo "$fileTest\n";
        }
        $fileSearch = Yii::getAlias('@frontend').'/modules/api/models/'.$className.'Search.php';
        if (is_file($fileSearch)){
            unlink($fileSearch);
            echo "$fileSearch\n";
        }
        $fileController = Yii::getAlias('@frontend').'/modules/api/controllers/'.$className.'Controller.php';
        if (is_file($fileController)){
            unlink($fileController);
            echo "$fileController\n";
        }
    }

    private function generateInsertController($table)
    {
        $className = Inflector::id2camel($table, '_');
        $insertControllerContent =<<<CONTROLLER
<?php
namespace console\\controllers;

use common\\models\\{$className};
use Faker\\Factory;
use yii\\console\\Controller;

class {$className}Controller extends Controller
{
    public function actionIndex()
    {
        \$date = new \\DateTime('now', new \\DateTimeZone('Europe/Moscow'));
        \$time = \$date->format('Y-m-d H:i:s');
        \$faker = Factory::create('ru_RU');
        for (\$i = 1; \$i <= 100; \$i++) {
            \${$table} = new {$className};
            \${$table}->setAttributes([
                'user_id' => 1, // admin:123456
                'category_id' => random_int(1, 30),
                'price' => random_int(100, 1000),
                'name' => \$faker->realText(22),
                'description' => \$faker->realText(1000),
            ]);
            \${$table}->save();
            echo "\${$table}->id. \${$table}->name - \${$table}->slug\n";
        }
    }

}
CONTROLLER;
        $fileInsertController = Yii::getAlias('@console').'/controllers/'.$className.'Controller.php';
        file_put_contents($fileInsertController, $insertControllerContent);
        echo "$fileInsertController\n";
    }

    private function generateMigration($table)
    {
        $date = new \DateTime('now', new \DateTimeZone('Europe/Moscow'));
        $time = strtotime($date->format('Y-m-d H:i:s'));
        $migrationName = 'm'.date('ymd',$time).'_'.date('His',$time).'_create_table_'.$table;
        $migrationContent =<<<MIGRATION
<?php
use yii\db\Migration;

class $migrationName extends Migration
{
    public function safeUp()
    {
        \$tableOptions = null;
        if (\$this->db->driverName === 'mysql') {
            \$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        \$this->createTable('{{%$table}}', [
            'id' => \$this->primaryKey(),
            'user_id' => \$this->integer(),
            'category_id' => \$this->integer(),
            'created_at' => \$this->dateTime(),
            'updated_at' => \$this->dateTime(),
            'price' => \$this->integer(),
            'name' => \$this->string(),
            'slug' => \$this->string(),
            'description' => \$this->text(),
        ], \$tableOptions);
        \$this->createIndex('idx-$table-user_id', '{{%$table}}', 'user_id');
        \$this->createIndex('idx-$table-category_id', '{{%$table}}', 'category_id');
        \$this->createIndex('idx-$table-name', '{{%$table}}', 'name');
        \$this->createIndex('idx-$table-slug', '{{%$table}}', 'slug');
    }
    public function safeDown()
    {
        \$this->dropTable('{{%$table}}');
    }
}
MIGRATION;
        $fileMigration = Yii::getAlias('@console').'/migrations/'.$migrationName.'.php';
        file_put_contents($fileMigration,$migrationContent);
        echo "$fileMigration\n";
    }

    private function generateModel($table)
    {
        $className = Inflector::id2camel($table, '_');
        $generator = new ModelGenerator;
        $generator->setAttributes([
            'ns' => 'common\models',
            'tableName' => $table,
            'className' => $className,
        ]);
        $files = $generator->generate();
        $content = $files[0]->content;
        $content = str_replace('extends \\yii\\db\\ActiveRecord',
            'extends \\yii\\db\\ActiveRecord implements Linkable', $content);
        $use =<<<USE
use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\SluggableBehavior;
use yii\web\Linkable;
use yii\web\Link;
use yii\helpers\Url;
USE;
        $content = str_replace('use Yii;',$use,$content);
        $behaviors =<<<BEHAVIORS
public function behaviors() {
		return [
            [
                'class' => TimestampBehavior::class,
                'value' => new Expression('NOW()'),
            ],
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'name',
                'immutable' => true,
                'ensureUnique' => true,
            ],
		];
	} 

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        return [
            'id' => 'id',
            'user_id' => 'user_id',
            'name' => 'name',
            'description' => 'description',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function extraFields()
    {
        return [
            'author' => 'user',
            'category' => 'category',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function formName()
    {
        return 's';
    }

    /**
     * {@inheritdoc}
     */
    public function getLinks()
    {
        return [
            Link::REL_SELF => Url::to(['$table/view', 'id' => \$this->id],true)
        ];
    }

    public function getUser()
    {
        return \$this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getCategory()
    {
        return \$this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
BEHAVIORS;
        $contentModel = str_replace('public function rules()',$behaviors,$content);
        $fileModel = Yii::getAlias('@common').'/models/'.$className.'.php';
        file_put_contents($fileModel,$contentModel);
        echo "$fileModel\n";
    }

    private function tableExists($tableName)
    {
        $tableSchema = Yii::$app->db->getTableSchema($tableName);

        return ($tableSchema !== null);
    }

    private function generateFixture($model)
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
        file_put_contents($fileFixture,$modelFixture);
        echo "$fileFixture\n";
    }

    private function generateFixtureData($model)
    {
        $lowerModel = strtolower($model);
        $modelName = "common\\models\\".$model;
        $allModels = $modelName::find()->all();
        $modelsArray = [];
        foreach ($allModels as $currentModel){
            $modelsArray[$currentModel->id] = $currentModel->attributes;
            if ($currentModel->id == 2){
                $modelsArray[$currentModel->id]['user_id'] = 2;
            }
        }
        $fileFixtureData = Yii::getAlias('@frontend').'/tests/_data/'.$lowerModel.'.php';
        $fileContent = "<?php\nreturn ".var_export($modelsArray, true).";";
        file_put_contents($fileFixtureData, $fileContent);
        echo "$fileFixtureData\n";
    }

    private function generateSearch($model)
    {
        $generator = new CrudGenerator;
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

        \$sql = \$query->createCommand()->rawSql;

        return \$dataProvider;
    }

}
SEARCH_MODEL;
        $fileSearch = Yii::getAlias('@frontend').'/modules/api/models/'.$model.'Search.php';
        file_put_contents($fileSearch,$searchModel);
        echo "$fileSearch\n";
    }

    private function generateController($model)
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
use yii\\filters\\Cors;
class {$model}Controller extends \\yii\\rest\\ActiveController
{
    public \$modelClass = {$model}::class;

    public function behaviors()
    {
        \$behaviors = parent::behaviors();
        \$behaviors['cors']['class'] = Cors::class;
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
            if (!Yii::\$app->user->can(Rbac::MANAGE_PRODUCT,['product' => \$model])){
                throw new ForbiddenHttpException('Forbidden');
            }
        }
    }
}
CONTROLLER;
        $fileController = Yii::getAlias('@frontend').'/modules/api/controllers/'.$model.'Controller.php';
        file_put_contents($fileController,$controller);
        echo "$fileController\n";
    }

}
