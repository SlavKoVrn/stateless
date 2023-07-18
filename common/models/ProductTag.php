<?php

namespace common\models;

/**
 * This is the model class for table "product_tag".
 *
 * @property int $id
 * @property int|null $product_id
 * @property int|null $tag_id
 */
class ProductTag extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_tag';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'tag_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'tag_id' => 'Tag ID',
        ];
    }
}
