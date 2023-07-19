<?php

namespace common\models;
use Yii;
use yii\behaviors\SluggableBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property int|null $category_id
 * @property int|null $price
 * @property string|null $name
 * @property string|null $slug
 * @property string|null $description
 */
class Product extends \yii\db\ActiveRecord
{
    public $tags = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'price'], 'integer'],
            [['description'], 'string'],
            [['name', 'slug'], 'string', 'max' => 255],
            ['tags', 'each', 'rule' => ['integer']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Ид',
            'category_id' => 'Категория',
            'price' => 'Цена',
            'name' => 'Товар',
            'slug' => 'Ссылка',
            'description' => 'Описание',
            'tags' => 'Тэги',
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

    public function tagsSave($newTagIds)
    {
        $currentTagIds = $this->getSelectedTagsIds();
        $toInsert = [];
        foreach (array_filter(array_diff($newTagIds,$currentTagIds)) as $tag_id){
            $toInsert[] = $tag_id;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($toInsert){
                foreach ($toInsert as $tag_id){
                    $product_tag = new ProductTag;
                    $product_tag->setAttributes([
                        'product_id' => $this->id,
                        'tag_id' => $tag_id,
                    ]);
                    $product_tag->save();
                }
            }
            if ($toRemove = array_filter(array_diff($currentTagIds,$newTagIds))){
                ProductTag::deleteAll([
                    'product_id'=>$this->id,
                    'tag_id'=>$toRemove
                ]);
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
        }
    }

    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->tags = $this->getSelectedTagsIds();
    }

    public function getSelectedTagsIds()
    {
        return ArrayHelper::map($this->selectedTags,'id','id');
    }

    public function getSelectedTagsName()
    {
        return ArrayHelper::map($this->selectedTags,'id','name');
    }

    public function getSelectedTags()
    {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])
            ->viaTable(ProductTag::tableName(), ['product_id' => 'id']);
    }

    public function fields()
    {
        return [
            'id',
            'name',
            'slug',
            'price',
            'description',
            'category'=>'category',
            'tags'=>'selectedTags',
        ];
    }
}
