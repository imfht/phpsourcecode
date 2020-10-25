<?php
/**
 * 动态模型
 * Class Table
 */
class Table extends CActiveRecord {

    private static $_models = array();

    private $_tbName = "";

    private $_rules = array();

    private $_md = null;

    /**
     * 查询表时调用
     * @param string $tableName
     * @return Table
     */
    public static function model($tableName)
    {
        if(isset(self::$_models[$tableName]))
            return self::$_models[$tableName];
        else
        {
            $model = self::$_models[$tableName] = new Table($tableName, null);
            //$model->setTableName($tableName);
//            print_r($model->tableName());exit;
            $model->_md=new CActiveRecordMetaData($model);
            $model->attachBehaviors($model->behaviors());
            return $model;
        }
    }

    /**
     * 构造函数，需要保存时调用
     * @param string $tableName
     * @param string $scenario
     */
    public function __construct($tableName, $scenario='insert')
    {
        $this->setTableName($tableName);

        parent::__construct($scenario);
    }

    public function tableName()
    {
        return $this->_tbName;
    }

    public function setTableName($tbName)
    {
        $this->_tbName = $tbName;
    }

    /**
     * Returns the meta-data for this AR
     * @return CActiveRecordMetaData the meta for this AR class.
     */
    public function getMetaData()
    {
        if($this->_md!==null)
            return $this->_md;
        else
            return $this->_md=self::model($this->_tbName)->_md;
    }

    /**
     * Refreshes the meta data for this AR class.
     * By calling this method, this AR class will regenerate the meta data needed.
     * This is useful if the table schema has been changed and you want to use the latest
     * available table schema. Make sure you have called {@link CDbSchema::refresh}
     * before you call this method. Otherwise, old table schema data will still be used.
     */
    public function refreshMetaData()
    {
        $finder=self::model($this->_tbName);
        $finder->_md=new CActiveRecordMetaData($finder);
        if($this!==$finder)
            $this->_md=$finder->_md;
    }

    protected function instantiate($attributes)
    {
        $class=get_class($this);
        $model=new $class($this->_tbName, null);
        return $model;
    }

    /**
     * 返回验证规则
     * @return array
     */
    public function rules()
    {
        return $this->_rules;
    }

    /**
     * The following are some examples:
     * <pre>
     * $model->addRule(array('username', 'required'))
     * $model->addRule(array('username', 'length', 'min'=>3, 'max'=>12))
     * $model->addRule(array('password', 'compare', 'compareAttribute'=>'password2', 'on'=>'register'))
     * $model->addRule(array('password', 'authenticate', 'on'=>'login'))
     * </pre>
     *
     * @param array $rule
     */
    public function addRule(array $rule)
    {
        $this->_rules[] = $rule;
    }
}