<?php
namespace backend\modules\mp\models\search;

use backend\modules\mp\models\MpFans;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class MpFansSearch extends MpFans {
    public $subscribe_time_operand;

    public function rules() {
        return [
            [['id', 'sex'], 'integer'],
            [['subscribe_time_operand', 'subscribe_time', 'nickname', 'openid', 'province', 'city'], 'string'],
        ];
    }

    public function scenarios() {
        return Model::scenarios();
    }

    public function search($params) {
        $query = MpFans::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
            ],
            'sort' => [
                'defaultOrder' => [
                    'subscribe_time' => SORT_DESC,
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'sex' => $this->sex,
        ]);
        
        $query->andFilterWhere([($this->subscribe_time_operand) ? $this->subscribe_time_operand : '=', 'subscribe_time', ($this->subscribe_time) ? strtotime($this->subscribe_time) : null]);

        $query
            ->andFilterWhere(['like', 'nickname', $this->nickname])
            ->andFilterWhere(['like', 'openid', $this->openid])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'province', $this->province])
            ->andFilterWhere(['like', 'country', $this->country]);

        return $dataProvider;
    }
}