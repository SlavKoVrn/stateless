<?php

namespace common\models;

use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tag".
 *
 * @property int $id
 * @property string|null $name
 */
class Tag extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tag';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Ид',
            'name' => 'Тэг',
        ];
    }

    public function getFullName()
    {
        return $this->id.'. '.$this->name;
    }

    public static function getAllArray()
    {
        return ArrayHelper::map(self::find()->all(),'id','fullName');
    }
}
