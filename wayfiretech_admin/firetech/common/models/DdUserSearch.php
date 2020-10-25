<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\DdUser;

/**
 * DdUserSearch represents the model behind the search form of `common\models\DdUser`.
 */
class DdUserSearch extends DdUser
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'gender', 'address_id', 'wxapp_id', 'create_time', 'update_time'], 'integer'],
            [['open_id', 'nickName', 'avatarUrl', 'country', 'province', 'city'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = DdUser::find();

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

        // grid filtering conditions
        $query->andFilterWhere([
            'user_id' => $this->user_id,
            'gender' => $this->gender,
            'address_id' => $this->address_id,
            'wxapp_id' => $this->wxapp_id,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'open_id', $this->open_id])
            ->andFilterWhere(['like', 'nickName', $this->nickName])
            ->andFilterWhere(['like', 'avatarUrl', $this->avatarUrl])
            ->andFilterWhere(['like', 'country', $this->country])
            ->andFilterWhere(['like', 'province', $this->province])
            ->andFilterWhere(['like', 'city', $this->city]);

        return $dataProvider;
    }
}
