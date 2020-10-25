<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-06-25 10:07:50
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-06-25 10:15:39
 */
 
/**
 * This is the template for generating CRUD search class of the specified model.
 */

use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
    $modelAlias = $modelClass . 'Model';
}
$rules = $generator->generateSearchRules();
$labels = $generator->generateSearchLabels();
$searchAttributes = $generator->getSearchAttributes();
$searchConditions = $generator->generateSearchConditions();

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->searchModelClass, '\\')) ?>;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use <?= ltrim($generator->modelClass, '\\') . (isset($modelAlias) ? " as $modelAlias" : "") ?>;
use yii\data\ArrayDataProvider;

/**
 * <?= $searchModelClass ?> represents the model behind the search form of `<?= $generator->modelClass ?>`.
 */
class <?= $searchModelClass ?> extends <?= isset($modelAlias) ? $modelAlias : $modelClass ?>

{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            <?= implode(",\n            ", $rules) ?>,
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
        $query = <?= isset($modelAlias) ? $modelAlias : $modelClass ?>::find();

        // add conditions that should always apply here

   
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return false;
        }

        // grid filtering conditions
        <?= implode("\n        ", $searchConditions) ?>

         
        $data = $query->asArray()->all();
            
        $dataProvider = new ArrayDataProvider([
            'key'=>'id',
            'modelClass'=><?= isset($modelAlias) ? $modelAlias : $modelClass ?>::className(),
            'allModels' => $data,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
               // 'attributes' => ['id'],
            ],
        ]);
           
        return $dataProvider;
    }
}
