<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\img;

/**
 * ImgSearch represents the model behind the search form about `app\models\img`.
 */
class ImgSearch extends img
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['size', 'name', 'url'], 'safe'],
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
        $query = img::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'size', $this->size])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'url', $this->url]);

        return $dataProvider;
    }
}
