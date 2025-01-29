<?php

namespace common\models;

use Yii;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\FileHelper;

/**
 * This is the model class for table "news".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $slug
 * @property string|null $image
 * @property string|null $text
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class News extends \yii\db\ActiveRecord
{
    public $imageFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'news';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['text'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['title', 'slug', 'image'], 'string', 'max' => 255],
            [['title', 'text'], 'required'],
            [['imageFile'], 'file', 'extensions' => 'png, jpg'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Ид',
            'title' => 'Титул',
            'slug' => 'Линк',
            'image' => 'Картинка',
            'text' => 'Текст',
            'created_at' => 'Создано',
            'updated_at' => 'Изменено',
            'imageFile' => 'Картинка',
        ];
    }

    public function behaviors()
    {
        return [
            'SluggableBehavior' => [
                'class' => SluggableBehavior::class,
                'attribute' => 'title',
                'immutable' => true,
                'ensureUnique' => true,
            ],
            [
                'class' => TimestampBehavior::class,
                'value' => new \yii\db\Expression('NOW()'),
            ],
        ];
    }

    public function upload()
    {
        if (is_file($this->imageFile->tempName)){
            try{
                $subDir = '/images/'.Yii::$app->user->id;
                $targetDirectory = Yii::getAlias('@static').$subDir;
                if (!is_dir($targetDirectory)){
                    FileHelper::createDirectory($targetDirectory, 0755, true);
                }
                $this->imageFile->saveAs("$targetDirectory/{$this->imageFile->name}");
                $this->image = "/static{$subDir}/{$this->imageFile->name}";
                return true;
            }catch (\Exception $e){
                return false;
            }
        }
        return false;
    }

}
