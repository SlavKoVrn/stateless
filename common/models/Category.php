<?php

namespace common\models;

use yii\behaviors\SluggableBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "category".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $slug
 * @property string|null $description
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description'], 'string'],
            [['name', 'slug'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Ид',
            'name' => 'Категория',
            'slug' => 'Ссылка',
            'description' => 'Описание',
        ];
    }

    public function behaviors()
    {
        return [
            'SluggableBehavior' => [
                'class' => SluggableBehavior::class,
                'attribute' => 'name',
                'immutable' => true,
                'ensureUnique' => true,
            ],
        ];
    }

    public function getFullName()
    {
        return $this->id.'. '.$this->name;
    }

    public static function getCategories()
    {
        return ArrayHelper::map(self::find()->all(),'id','fullName');
    }
}
