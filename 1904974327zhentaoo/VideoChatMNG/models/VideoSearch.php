<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\video;

/**
 * VideoSearch represents the model behind the search form about `app\models\video`.
 */
class VideoSearch extends video
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'password', 'creator', 'online_number'], 'safe'],
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
        $query = video::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'creator', $this->creator])
            ->andFilterWhere(['like', 'online_number', $this->online_number]);

        return $dataProvider;
    }
}
