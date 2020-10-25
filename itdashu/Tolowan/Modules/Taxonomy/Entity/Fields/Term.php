<?php
namespace Modules\Taxonomy\Entity\Fields;

use Modules\Entity\Entity\Fields\Field;
use Phalcon\Mvc\Model\Relation;
use Modules\Taxonomy\Entity\Term as TermModel;

class Term extends Field
{
    public function initialize()
    {
        $this->belongsTo('value', '\Modules\Taxonomy\Entity\Term', 'id', array(
            'alias' => 'term',
            'reusable' => true,
            'foreignKey' => array(
                'action' => Relation::ACTION_CASCADE
            )
        ));
    }

    public static function filterValue($value, $option)
    {
        if (isset($option['maxNum']) && $option['maxNum'] > 1) {
            $output = array();
            if ($value) {
                foreach ($value as $m) {
                    if (isset($option['valueType']) && $option['valueType'] == 'id') {
                        $output[] = $m->value;
                    } else {
                        $term = $m->term;
                        if ($term) {
                            $output[] = $term->name;
                        }

                    }
                }
                return implode(',', $output);
            }
        } else {
            if ($value) {
                if ($option['valueType'] == 'id') {
                    return $value->value;
                } else {
                    $term = $value->term;
                    if ($term) {
                        return $term->name;
                    }

                }
            }
        }
        return false;
    }

    public function setValue($value)
    {
        $options = $this->_options;
        if ($options['valueType'] == 'id') {
            $termModel = TermModel::findFirst($value);
            if ($termModel) {
                $this->value = $value;
            }
        } else {
            $termModel = TermModel::findFirstByName($value);
            if ($termModel) {
                $this->value = $termModel->id;
            } else {
                if (isset($options['addTerm']) && $options['addTerm'] === true && isset($options['taxonomy'])) {
                    $termModel = new TermModel();
                    $termModel->name = $value;
                    $termModel->description = $value;
                    $termModel->parent = isset($options['parent']) ? $options['parent'] : 0;
                    $termModel->widget = 10;
                    $termModel->contentModel = $options['taxonomy'];
                    if ($termModel->save()) {
                        $this->value = $termModel->id;
                    }
                }
            }
        }
    }
}
