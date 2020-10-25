<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\CustomerPform;

use backend\models\Pform;
/**
 * CustomerPformSearch represents the model behind the search form about `backend\models\CustomerPform`.
 */
class CustomerPformSearch extends CustomerPform
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'pform_field_id'], 'integer'],
            [['pform_uid', 'value'], 'safe'],
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
        $pforms = Pform::find()->where(['user_id' => Yii::$app->user->id])->all();
        $pform_uid = array();
        foreach ($pforms as $pform) {
            $pform_uid[] = $pform->uid;
        }

        //$query = CustomerPform::find();
        $query = CustomerPform::find()
                ->where(['in', 'pform_uid', $pform_uid]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC,
                ],
            ],
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
            'pform_field_id' => $this->pform_field_id,
        ]);

        $query->andFilterWhere(['like', 'pform_uid', $this->pform_uid])
            ->andFilterWhere(['like', 'value', $this->value]);

        return $dataProvider;
    }
}
