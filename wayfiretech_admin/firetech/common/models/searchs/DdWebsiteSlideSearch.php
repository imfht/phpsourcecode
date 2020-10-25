<?php

namespace common\models\searchs;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\DdWebsiteSlide;

/**
 * DdWebsiteSlideSearch represents the model behind the search form of `common\models\DdWebsiteSlide`.
 */
class DdWebsiteSlideSearch extends DdWebsiteSlide
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['images', 'title', 'description', 'menuname', 'menuurl', 'createtime', 'updatetime'], 'safe'],
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
        $query = DdWebsiteSlide::find();

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
            'id' => $this->id,
            'createtime' => $this->createtime,
            'updatetime' => $this->updatetime,
        ]);

        $query->andFilterWhere(['like', 'images', $this->images])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'menuname', $this->menuname])
            ->andFilterWhere(['like', 'menuurl', $this->menuurl]);

        return $dataProvider;
    }
}
