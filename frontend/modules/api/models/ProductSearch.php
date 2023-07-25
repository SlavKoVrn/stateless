<?php
namespace frontend\modules\api\models;
use common\models\Product;
use common\models\Tag;
use yii\data\ActiveDataProvider;
class ProductSearch extends Product
{
    public $priceFrom;
    public $priceTo;

    public function rules()
    {
        return [
            [['id', 'category_id', 'user_id', 'price'], 'integer'],
            [['name', 'slug', 'description', 'tags', 'priceFrom', 'priceTo'], 'safe']
        ];
    }
    public function search($params)
    {
        $query = Product::find()->distinct();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        if (!$this->validate()) {
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
        ]);

        if ($this->priceFrom and $this->priceTo){
            $query->andFilterWhere([ 'between',Product::tableName().'.[[price]]',
                intval($this->priceFrom),intval($this->priceTo)]);
        }

        $query->andFilterWhere(['like', Product::tableName().'.[[name]]', $this->name])
            ->andFilterWhere(['like', Product::tableName().'.[[description]]', $this->description]);

        return $dataProvider;
    }
    public function formName()
    {
        return 's';
    }
}