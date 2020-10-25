<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ZkEvent;

/**
 * ZkEventSearch represents the model behind the search form about `backend\models\ZkEvent`.
 */
class ZkEventSearch extends ZkEvent
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event_id'], 'integer'],
            [['title', 'desc', 'create_time'], 'safe'],
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
        $query = ZkEvent::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'event_id' => $this->event_id,
            'create_time' => $this->create_time,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'desc', $this->desc]);

        return $dataProvider;
    }
}
