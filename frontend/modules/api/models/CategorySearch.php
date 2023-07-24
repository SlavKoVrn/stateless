<?php
namespace frontend\modules\api\models;
use common\models\Category;
use yii\data\ActiveDataProvider;

class CategorySearch extends Category
{
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name', 'slug', 'description'], 'safe']
        ];
    }
    public function search($params)
    {
        $query = Category::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            //->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'description', $this->description]);

        $sql = $query->createCommand()->rawSql;

        return $dataProvider;
    }
    public function formName()
    {
        return 's';
    }
}