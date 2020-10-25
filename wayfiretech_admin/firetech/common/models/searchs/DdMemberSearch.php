<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-22 21:07:09
 * @Last Modified by:   Wang Chunsheng 2192138785@qq.com
 * @Last Modified time: 2020-03-22 21:08:24
 */


namespace common\models\searchs;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\DdMember;

/**
 * DdMemberSearch represents the model behind the search form of `common\models\DdMember`.
 */
class DdMemberSearch extends DdMember
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member_id', 'level', 'gender', 'address_id', 'wxapp_id', 'create_time', 'update_time'], 'integer'],
            [['openid', 'nickName', 'mobile', 'avatarUrl', 'country', 'province', 'city'], 'safe'],
            [['username'], 'string'],
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
        $query = DdMember::find();

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
            'member_id' => $this->member_id,
            'gender' => $this->gender,
            'address_id' => $this->address_id,
            'wxapp_id' => $this->wxapp_id,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'openid', $this->openid])
            ->andFilterWhere(['like', 'nickName', $this->nickName])
            ->andFilterWhere(['like', 'avatarUrl', $this->avatarUrl])
            ->andFilterWhere(['like', 'country', $this->country])
            ->andFilterWhere(['like', 'province', $this->province])
            ->andFilterWhere(['like', 'city', $this->city]);

        return $dataProvider;
    }
}
