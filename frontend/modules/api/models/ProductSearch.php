<?php

namespace frontend\modules\api\models;

use common\models\Product;
use common\models\Tag;
use yii\data\ActiveDataProvider;

/**
 * ProductSearch represents the model behind the search form of `common\models\Product`.
 */
class ProductSearch extends Product
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'category_id', 'price'], 'integer'],
            [['name', 'slug', 'description', 'tags'], 'safe'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Product::find()->distinct();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->tags){
            $query->innerJoinWith('selectedTags');
            $query->andFilterWhere(['in',Tag::tableName().'.[[id]]',$this->tags]);
        }

        if (intval($this->category_id)){
            $query->andFilterWhere([
                Product::tableName().'.[[category_id]]' => intval($this->category_id),
            ]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            Product::tableName().'.[[id]]' => $this->id,
            Product::tableName().'.[[price]]' => $this->price,
        ]);

        $query->andFilterWhere(['like', Product::tableName().'.[[name]]', $this->name])
            ->andFilterWhere(['like', Product::tableName().'.[[description]]', $this->description]);

        /*
        $sql = $query->createCommand()->rawSql;
        $sql = str_replace('FROM',"\nFROM",$sql);
        $sql = str_replace('AND', "\nAND",$sql);
        $sql = str_replace('LEFT',"\nLEFT",$sql);
        $sql = str_replace('INNER',"\nINNER",$sql);
        $sql = str_replace('WHERE',"\nWHERE",$sql);
        $sql = str_replace('GROUP',"\nGROUP",$sql);
        $sql = str_replace('ORDER',"\nORDER",$sql);
        $sql = str_replace('LIMIT',"\nLIMIT",$sql);
        */

        return $dataProvider;
    }

    public function formName()
    {
        return 's';
    }
}
