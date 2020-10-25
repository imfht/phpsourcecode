<?php
namespace Modules\Entity\Library;

use Core\Config;
use Phalcon\Exception;
use Library\Scsw\Scsw;

class EntityManager
{
    protected $_entitys;
    protected $_entityManagers;

    public function __construct()
    {
        $this->_entitys = Config::cache('entitys');
    }

    public function getEntityInfo($entity)
    {
        if (isset($this->_entitys[$entity])) {
            return $this->_entitys[$entity];
        }
        return false;
    }

    public function get($entity)
    {
        if (isset($this->_entityManagers[$entity])) {
            return $this->_entityManagers[$entity];
        }
        if (isset($this->_entitys[$entity])) {
            if (!isset($this->_entitys[$entity]['entityManager']) || !class_exists($this->_entitys[$entity]['entityManager'])) {
                echo $this->_entitys[$entity]['entityManager'];
                throw new Exception('参数错误，不是有效的实体管理类' . $entity);
            }
            $entityClassName = $this->_entitys[$entity]['entityManager'];
            $this->_entityManagers[$entity] = new $entityClassName();
            return $this->_entityManagers[$entity];
        } else {
            throw new Exception('实体类型不存在：' . $entity);
        }
        return false;
    }

    public static function fieldScsw($data = array())
    {
        global $di;
        if (!isset($data['entity']) || !isset($data['field']) || !isset($data['id'])) {
            return '参数错误';
        }
        $fieldName = $data['field'];
        $entity = $di->getShared('entityManager')->get($data['entity']);
        $entityNode = $entity->findFirst($data['id'], true);
        if (!$entityNode) {
            return '实体不存在，id:' . $data['id'];
        }
        //Config::printCode($entityNode->toArray());
        $fieldModel = $entityNode->{$fieldName};
        if ($fieldModel) {
            if(empty($fieldModel->getValue())){
                return '字段值为空，实体ID：'.$data['id'].'，字段名：'.$fieldName;
            }
            $output = Scsw::toString($fieldModel->getValue());
            if(!$output){
                return '分词结果为空，实体ID：'.$data['id'].'，字段名：'.$fieldName.'，分词结果：'.strip_tags($fieldModel->getValue());
            }
            $fieldModel->full_text = $output;
            if ($fieldModel->save()) {
                return true;
            } else {
                return '保存失败';
            }
        } else {
            return '字段不存在';
        }
    }
}