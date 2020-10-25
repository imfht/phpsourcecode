<?php
namespace Modules\Entity\Entity;

use Core\Config;

class FieldManager
{
    public static function getFields()
    {
        return array();
    }

    public static function toFormEntity($fields)
    {
        $defaultForm = array(
            'formId' => 'nodeEdit',
            'form' => array(
                'method' => 'post',
                'class' => array(),
                'accept-charset' => 'utf-8',
                'role' => 'form',
            ),
            'settings' => array()
        );
        $baseForm = array();
        $baseForm['form'] = isset($fields['form']) ? $fields['form'] : $defaultForm['form'];
        $baseForm['formId'] = isset($fields['formId']) ? $fields['formId'] : $defaultForm['formId'];
        $baseForm['settings'] = isset($fields['settings']) ? $fields['settings'] : $defaultForm['settings'];
        foreach ($fields as $fieldKey => $field) {
            if (isset($field['widget'])) {
                foreach ($field as $fk => $fv) {
                    $baseForm[$fieldKey][$fk] = $fv;
                }
            }
        }
        return $baseForm;
    }

    public static function updateDb($field)
    {

    }

    public static function modelsManager()
    {
        global $di;
        $modelsManager = array();
        $fieldTypes = Config::cache('fieldType');
        $entityManager = $di->getShared('entityManager');
        $entitys = Config::cache('entitys');
        foreach ($entitys as $entityKey => $entity) {
            //获取内容模型列表
            $contentModelList = $entityManager->get($entityKey)->getContentModelList();
            foreach ($contentModelList as $cmKey => $cm) {
                $fields = array();
                if (isset($cm['fields'])) {
                    if (is_array($cm['fields'])) {
                        $fields = $cm['fields'];
                    } elseif (is_string($cm['fields'])) {
                        $fields = Config::get($cm['fields'], array());
                    }
                }
                foreach ($fields as $fieldKey => $field) {
                    if (isset($field['field']) && isset($fieldTypes[$field['field']]) && isset($fieldTypes[$field['field']]['createModelManager']) && is_callable($fieldTypes[$field['field']]['createModelManager'])) {
                        $createModelManager = $fieldTypes[$field['field']]['createModelManager'];
                        $modelsManager[$entity . '_field_' . $fieldKey] = call_user_func_array($createModelManager, array('entity' => $entity, 'field' => $fieldKey));
                    } else {
                        $entityFieldTableName = $entityKey . '_field_' . $fieldKey;
                        $modelsManager[$entityFieldTableName] = array(
                            'entity' => isset($field['model']) ? $field['model'] : 'Models\\' . ucfirst($entityKey) . 'Field' . ucfirst($fieldKey),
                            'columns' => array($entityFieldTableName . '.id', $entityFieldTableName . '.eid', $entityFieldTableName . '.value'),
                        );
                        if(isset($field['fullTextSearch']) && $field['fullTextSearch'] === true){
                            $modelsManager[$entityFieldTableName]['columns'][] = $entityFieldTableName.'.full_text';
                        }
                    }
                }
            }
        }
        return $modelsManager;
    }
}