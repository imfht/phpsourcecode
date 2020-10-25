<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\manager;

/**
 * ManagerSearch represents the model behind the search form about `app\models\manager`.
 */
class ManagerSearch extends manager
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password', 'name'], 'safe'],
            [['id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
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
        $query = manager::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
