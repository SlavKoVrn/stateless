<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\gii\generators\model\Generator as ModelGenerator;
use yii\helpers\Inflector;

class GenerateController extends Controller
{
    public function actionIndex($table)
    {
        if ($this->tableExists($table)){
            $this->generateModel($table);
            $this->generateInsertController($table);
        }else{
            $this->generateMigration($table);
            echo "php yii migrate\n";
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
        $use =<<<USE
use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\SluggableBehavior;
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
}
