<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\model\Generator */
/* @var $tableName string full table name */ 
/* @var $className string class name */
/* @var $queryClassName string query class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */

echo "<?php\n";
?>
<?php 


?>
namespace <?= $generator->ns ?>;

use Yii;

/**
 * This is the model class for table "<?= $generator->generateTableName($tableName) ?>".
 *
<?php foreach ($tableSchema->columns as $column): ?>
 * @property <?= "{$column->phpType} \${$column->name}\n" ?>
<?php endforeach; ?>
<?php if (!empty($relations)): ?>
 *
<?php foreach ($relations as $name => $relation): ?>
 * @property <?= $relation[1] . ($relation[2] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?>
<?php endforeach; ?>
<?php endif; ?>
 */
class <?= $className ?> extends \backend\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '<?= $generator->generateTableName($tableName) ?>';
    }
<?php if ($generator->db !== 'db'): ?>

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('<?= $generator->db ?>');
    }
<?php endif; ?>

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [<?= "\n            " . implode(",\n            ", $rules) . "\n        " ?>];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
<?php foreach ($labels as $name => $label): ?>
            <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
<?php endforeach; ?>
        ];
    }
<?php foreach ($relations as $name => $relation): ?>

    /**
     * @return \yii\db\ActiveQuery
     */
    public function get<?= $name ?>()
    {
        <?= $relation[0] . "\n" ?>
    }
<?php endforeach; ?>
<?php if ($queryClassName): ?>
<?php
    $queryClassFullName = ($generator->ns === $generator->queryNs) ? $queryClassName : '\\' . $generator->queryNs . '\\' . $queryClassName;
    echo "\n";
?>
    /**
     * @inheritdoc
     * @return <?= $queryClassFullName ?> the active query used by this AR class.
     */
    public static function find()
    {
        return new <?= $queryClassFullName ?>(get_called_class());
    }
<?php endif; ?>

  /**
     * 返回数据库字段信息，仅在生成CRUD时使用，如不需要生成CRUD，请注释或删除该getTableColumnInfo()代码
     * COLUMN_COMMENT可用key如下:
     * label - 显示的label
     * inputType 控件类型, 暂时只支持text,hidden  // select,checkbox,radio,file,password,
     * isEdit   是否允许编辑，如果允许编辑将在添加和修改时输入
     * isSearch 是否允许搜索
     * isDisplay 是否在列表中显示
     * isOrder 是否排序
     * udc - udc code，inputtype为select,checkbox,radio三个值时用到。
     * 特别字段：
     * id：主键。必须含有主键，统一都是id
     * create_date: 创建时间。生成的代码自动赋值
     * update_date: 修改时间。生成的代码自动赋值
     */
    public function getTableColumnInfo(){
        return array(
        <?php
            foreach ($tableSchema->columns as $column){
                $allowNull = $column->allowNull == true ? 'true' : 'false';
                $autoIncrement = $column->autoIncrement == true ? 'true' : 'false';
                $isPrimaryKey = $column->isPrimaryKey == true ? 'true' : 'false';
                $inputtype = $column->isPrimaryKey == true ? 'hidden' : 'text';
                $unsigned = $column->unsigned == true ? 'true' : 'false';
                $searchble = $column->isPrimaryKey == true ? 'true' : 'false'; // 默认下只有主键提供搜索
                //$defaultValue = $column->phpType == 'string' ? '"'.$column->defaultValue.'"' : $column->defaultValue;
                 echo "'{$column->name}' => array(
                        'name' => '{$column->name}',
                        'allowNull' => {$allowNull},
//                         'autoIncrement' => {$autoIncrement},
//                         'comment' => '{$column->comment}',
//                         'dbType' => \"{$column->dbType}\",
                        'defaultValue' => '$column->defaultValue',
                        'enumValues' => ". json_encode($column->enumValues).",
                        'isPrimaryKey' => {$isPrimaryKey},
                        'phpType' => '{$column->phpType}',
                        'precision' => '{$column->precision}',
                        'scale' => '{$column->scale}',
                        'size' => '{$column->size}',
                        'type' => '{$column->type}',
                        'unsigned' => $unsigned,
                        'label'=>\$this->getAttributeLabel('{$column->name}'),
                        'inputType' => '$inputtype',
                        'isEdit' => true,
                        'isSearch' => $searchble,
                        'isDisplay' => true,
                        'isSort' => true,
//                         'udc'=>'',
                    ),\n\t\t" ;
             } 
         ?>
        );
        
    }
 
}
