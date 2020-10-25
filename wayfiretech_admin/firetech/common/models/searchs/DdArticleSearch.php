<?php

namespace common\models\searchs;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\DdArticle;
/**
 * DdArticleSearch represents the model behind the search form of `common\models\DdArticle`.
 */
class DdArticleSearch extends DdArticle
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'ishot', 'pcate', 'ccate', 'incontent', 'displayorder', 'createtime', 'edittime', 'click'], 'integer'],
            [['template', 'title', 'description', 'content', 'thumb', 'source', 'author', 'linkurl', 'type', 'credit'], 'safe'],
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
        $query = DdArticle::find();

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
            'ishot' => $this->ishot,
            'pcate' => $this->pcate,
            'ccate' => $this->ccate,
            'incontent' => $this->incontent,
            'displayorder' => $this->displayorder,
            'createtime' => $this->createtime,
            'edittime' => $this->edittime,
            'click' => $this->click,
        ]);

        $query->andFilterWhere(['like', 'template', $this->template])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'thumb', $this->thumb])
            ->andFilterWhere(['like', 'source', $this->source])
            ->andFilterWhere(['like', 'author', $this->author])
            ->andFilterWhere(['like', 'linkurl', $this->linkurl])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'credit', $this->credit]);

        return $dataProvider;
    }
}
