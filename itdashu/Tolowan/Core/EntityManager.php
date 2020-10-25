<?php
namespace Core;

use Core\Config;

class EntityManager
{
    public $entityList;
    public $entityFields;
    public $contentModelList;

    public function __construct()
    {
        $this->entityList = Config::cache('entitys');
    }

    public function getEntityFields($entity, $contentModel = null)
    {
        if (!isset($this->entityList[$entity])) {
            return false;
        }
        if (!is_null($contentModel)) {
            if ($this->entityFields[$entity][$contentModel]) {
                return $this->entityFields[$entity][$contentModel];
            }
        } else {
            if ($this->entityFields[$entity]['all']) {
                return $this->entityFields[$entity]['all'];
            }
        }
        $entityContentModelInfo = $this->entityList[$entity];
        $baseFields = Config::get($entityContentModelInfo['module'] . $entity . 'BaseFields');
        $fields = $baseFields;
        $contentModelList = $this->getContentModelList($entity);
        if (!is_null($contentModel)) {
            if (!isset($contentModelList[$contentModel])) {
                return false;
            }
            if (is_array($contentModelList[$contentModel]['fields'])) {
                $this->entityFields[$entity][$contentModel] = userArrayMerge($baseFields, $contentModelList[$contentModel]['fields']);
                return $this->entityFields[$entity][$contentModel];
            }
            $contentModelFields = Config::get($contentModelList[$contentModel]['fields']);
            $this->entityFields[$entity][$contentModel] = userArrayMerge($baseFields, $contentModelFields);
            return $this->entityFields[$entity][$contentModel];
        } else {
            foreach ($contentModelList as $key => $value) {
                if (!is_array($value['fields'])) {
                    $value['fields'] = Config::get($value['fields']);
                }
                $this->entityFields[$entity][$key] = userArrayMerge($baseFields, $value['fields']);
                $fields = userArrayMerge($fields, $value['fields']);
            }
            $this->entityFields[$entity]['all'] = $fields;
            return $fields;
        }
    }

    public function getContentModelList($entity)
    {
        if (isset($this->contentModelList[$entity])) {
            return false;
        }
        if (!isset($this->entityList[$entity])) {
            return false;
        }
        $module = $this->entityList[$entity]['module'];
        $output = Config::get('m.' . $module . '.entity' . ucfirst($entity) . 'ContentModelList', array());
        $this->contentModelList[$entity] = $output;
        return $output;
    }

}