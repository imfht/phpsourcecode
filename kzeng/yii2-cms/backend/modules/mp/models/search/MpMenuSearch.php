<?php
namespace backend\modules\mp\models\search;

use backend\modules\mp\models\MpMenu;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class MpMenuSearch extends MpMenu {
    public function rules() {
        return [
        ];
    }

    // public function scenarios() {
    //     return Model::scenarios();
    // }

    public function search($params) {
        $query = MpMenu::find();

        // $query->with(['roles']);

        // if (!Yii::$app->user->isSuperadmin) {
        //     $query->where(['superadmin' => 0]);
        // }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
        ]);

        // if (!($this->load($params) && $this->validate())) {
        //     return $dataProvider;
        // }

        // if ($this->gridRoleSearch) {
        //     $query->joinWith(['roles']);
        // }

        $query->andFilterWhere([
            'id' => $this->id,
            // 'superadmin' => $this->superadmin,
            // 'status' => $this->status,
            // Yii::$app->yee->auth_item_table . '.name' => $this->gridRoleSearch,
            // 'registration_ip' => $this->registration_ip,
            // 'created_at' => $this->created_at,
            // 'updated_at' => $this->updated_at,
            // 'email_confirmed' => $this->email_confirmed,
        ]);

        // $query->andFilterWhere(['like', 'nickname', $this->nickname])

            // ;

        return $dataProvider;
    }
}