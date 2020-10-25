<?php
namespace Modules\Taxonomy\Library;

use Core\Config;
use Core\Model;
use Modules\Taxonomy\Models\Term;
use Modules\Taxonomy\Models\EntityTerm;

class Form{

    //术语排序
    public static function saveTermSort($hierarchy)
    {
        $widget = 1;
        $start = 1;
        global $di;
        //Config::printCode($hierarchy);
        $db = $di->getShared('db');
        foreach($hierarchy as $key => $value){
            $db->begin();
            $term = Term::findFirst($key);
            if($start == 1){
                $widget = $term->widget;
                if($term->parent != 0){
                    $term->parent = 0;
                }
                $start++;
                $state = $term->save();
            }else{
                $term->parent = 0;
                $term->widget = $widget;
                $state = $term->save();
            }
            if($state){
                $db->commit();
            }else{
                $db->rollback();
            }
            $widget++;
            if(!empty($value)){
                $widget = self::_saveTermSort($value,$widget,$term->id);
            }
        }
        return true;
    }
    protected static function _saveTermSort($hierarchy,$widget,$parent)
    {
        global $di;
        $db = $di->getShared('db');
        foreach($hierarchy as $key => $value){
            $db->begin();
            $term = Term::findFirst($key);
            $term->widget = $widget;
            $term->parent = $parent;
            $state = $term->save();
            if($state){
                $db->commit();
            }else{
                $db->rollback();
            }
            $widget++;
            $parent = $term->id;
            if(!empty($value)){
                self::_saveTermSort($value,$widget,$parent);
            }
        }
        return $widget;
    }
    //术语排序结束
    public static function termInit(&$key,&$element){
        if($element['widget'] == 'Select'){
            $element = self::formTermOptions($element);
        }
    }
    /**
     * @param $param
     * @return mixed
     */
    public static function formTermOptions($element)
    {
        global $di;
        $query = array();
        if (isset($element['limit'])) {
            $query['limit'] = $element['limit'];
        }
        if (isset($element['taxonomy'])) {
            $query['andWhere'][] = array(
                'conditions' => 'contentModel = :contentModel:',
                'bind' => array('contentModel' => $element['taxonomy'])
            );
        }
        $termEntity = $di->getShared('entityManager')->get('term');
        $termList = $termEntity->find($query);
        //$termList = $termList->toArray();
        //print_r($termList);
        $termList = $termList->toArray();
        $termList = self::reSort($termList);
        $termList = self::_termTree($termList);
        $output = self::termOptions($termList);
        $element['options'] = $output;
        return $element;
    }
    public static function reSort($data)
    {
        $output = array();
        foreach ($data as $value) {
            $output[$value['id']] = $value;
        }
        return $output;
    }

    /**
     * @param $data
     * @return mixed
     */
    public static function _termTree($data)
    {
        $tree = array();
        foreach ($data as $term) {
            if (isset($data[$term['parent']])) {
                $data[$term['parent']]['son'][$term['id']] = &$data[$term['id']];
            } else {
                $tree[$term['id']] = &$data[$term['id']];
            }
        }
        return $tree;
    }

    /**
     * @param $data
     * @param $str
     * @return mixed
     */
    public static function termOptions($data, $str = '')
    {
        $output = array();
        foreach ($data as $value) {
            $output[$value['id']] = $str . $value['name'];
            if (isset($value['son'])) {
                $output += self::termOptions($value['son'], $str . '--');
            }
        }
        return $output;
    }
    // 保存实体术语
    public static function saveEntityTerm(&$element)
    {
        global $di;
        $settings = $element->getUserOptions();
        if ($settings['maxNum'] < 1 || !isset($settings['eid'])) {
            return false;
        }
        $id = false;
        $entity = $settings['id'];
        if(is_string($entity)){
            $id = intval($entity);
        }elseif(is_array($entity) && isset($entity['id'])){
            $id = $entity['id'];
        }elseif(is_object($entity) && isset($entity->id)){
            $id = $entity->id;
        }
        if($id == false){
            return false;
        }
        $taxonomyType = Config::get('m.taxonomy.type');
        //获取当前节点术语
        $nodeTermList = EntityTerm::findByEid($settings['eid'].$id);
        $value = $element->getValue();
        $value = trim($value,',;');
        if (!is_array($value)) {
            $value = explode(',', $value);
        }
        if (count($value) > $settings['maxNum']) {
            $di->getShared('flash')->error('术语数量过多。');
            return false;
        }
        $tlist = array();
        if ($settings['valueType'] == 'name') {
            foreach ($value as $value) {
                if (!empty($value)) {
                    $value = trim($value);
                    $term = Term::findFirstByName($value);
                    if (!$term && $settings['addTerm'] == true) {
                        $term = new Term();
                        $term->name = $value;
                        $term->description = $value;
                        $term->parent = 0;
                        $term->widget = 0;
                        $term->type = $settings['taxonomy'];
                        if (!$term->create()) {
                            return false;
                        }
                    } elseif ($term && $term->type != $settings['taxonomy']) {
                        break;
                    }
                    if (isset($term->id) && !empty($term->id) && isset($taxonomyType[$term->type])) {
                        if (isset($taxonomyType[$settings['taxonomy']])) {
                            $tlist[$term->id] = $term->id;
                        }
                    }
                }
            }
            $term = null;
        } elseif ($settings['valueType'] == 'id') {
            foreach ($value as $key => $value) {
                $value[$key] = trim($value);
                $value[$key] = intval($value);
                $term = Term::findFirst($value);
                if ($term && isset($taxonomyType[$term->type]) && $term->type == $settings['taxonomy']) {
                    $tlist[$value] = $value;
                }
            }
        }
        foreach ($nodeTermList as $nterm) {
            if (!in_array($nterm->tid, $tlist)) {
                $nterm->delete();
            } else {
                unset($tlist[$nterm->tid]);
            }
        }
        if (!empty($tlist)) {
            foreach ($tlist as $value) {
                $nodeTerm = new EntityTerm();
                $nodeTerm->tid = $value;
                $nodeTerm->eid = $settings['eid'].$id;
                if ($nodeTerm->save() == false) {
                    foreach ($nodeTerm->getMessages() as $message) {
                        $di->getShared('flash')->error($message);
                    }
                    return false;
                }
            }
        }
        return true;
    }
}