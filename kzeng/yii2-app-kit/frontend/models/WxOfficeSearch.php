<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\WxOffice;

/**
 * WxOfficeSearch represents the model behind the search form about `frontend\models\WxOffice`.
 */
class WxOfficeSearch extends WxOffice
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['office_id', 'scene_id', 'member_cnt', 'visable', 'is_jingxiaoshang', 'role', 'status', 'is_selfOperated', 'score'], 'integer'],
            [['gh_id', 'title', 'branch', 'region', 'address', 'manager', 'mobile', 'pswd'], 'safe'],
            [['lat', 'lon', 'lat_bd09', 'lon_bd09'], 'number'],
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
        $query = WxOffice::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'office_id' => $this->office_id,
            'scene_id' => $this->scene_id,
            'member_cnt' => $this->member_cnt,
            'lat' => $this->lat,
            'lon' => $this->lon,
            'lat_bd09' => $this->lat_bd09,
            'lon_bd09' => $this->lon_bd09,
            'visable' => $this->visable,
            'is_jingxiaoshang' => $this->is_jingxiaoshang,
            'role' => $this->role,
            'status' => $this->status,
            'is_selfOperated' => $this->is_selfOperated,
            'score' => $this->score,
        ]);

        $query->andFilterWhere(['like', 'gh_id', $this->gh_id])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'branch', $this->branch])
            ->andFilterWhere(['like', 'region', $this->region])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'manager', $this->manager])
            ->andFilterWhere(['like', 'mobile', $this->mobile])
            ->andFilterWhere(['like', 'pswd', $this->pswd]);

        return $dataProvider;
    }
}
