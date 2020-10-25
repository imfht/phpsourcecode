<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-07-13 08:45:39
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-13 08:45:44
 */

namespace common\models\searchs;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\DdArticleCategory;

/**
 * DdArticleCategorySearch represents the model behind the search form of `common\models\DdArticleCategory`.
 */
class DdArticleCategorySearch extends DdArticleCategory
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'displayorder'], 'integer'],
            [['title', 'type'], 'safe'],
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
     * Creates data provider instance with search query applied.
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = DdArticleCategory::find();

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
            'displayorder' => $this->displayorder,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'type', $this->type]);

        return $dataProvider;
    }
}
