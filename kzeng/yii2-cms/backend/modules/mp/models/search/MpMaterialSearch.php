<?php
namespace backend\modules\mp\models\search;

use backend\modules\mp\models\MpMaterial;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class MpMaterialSearch extends MpMaterial {
    public $update_time_operand;

    public function rules() {
        return [
            [['update_time', 'update_time_operand'], 'string'],
        ];
    }

    public function scenarios() {
        return Model::scenarios();
    }

    public function search($params) {
        $query = MpMaterial::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
            ],
            'sort' => [
                'defaultOrder' => [
                    'update_time' => SORT_DESC,
                ],
            ],
        ]);

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere([($this->update_time_operand) ? $this->update_time_operand : '=', 'update_time', ($this->update_time) ? strtotime($this->update_time) : null]);

        return $dataProvider;
    }
}