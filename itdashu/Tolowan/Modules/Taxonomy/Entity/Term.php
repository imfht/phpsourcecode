<?php
namespace Modules\Taxonomy\Entity;

use Core\Config;
use Modules\Entity\Entity\EntityModel;

class Term extends EntityModel
{
    protected $_source = 'term';

    protected $_table = 'term';

    protected $_entity = 'term';

    protected $_entityId = 'term';
    protected $entityClassName = '\Modules\Taxonomy\Entity\Term';

    protected $_module = 'taxonomy';

    protected $other;

    public function getChildren()
    {
        $query = array(
            'conditions' => 'parent = :parent:',
            'bind' => array(
                'parent' => $this->id,
            ),
            'order' => 'widget',
        );
        $output = self::find($query);
        return $output;
    }
    public function getOther(){
        if(is_string($this->other)){
            return $this->unserialize($this->other);
        }elseif (is_array($this->other)){
            return $this->other;
        }
        return array();
    }
    public function setOther($value){
        if(is_array($value)){
            $this->other = $this->serialize($value);
        }
    }
    public function beforeValidation(){
        if(empty($this->other)){
            $this->other = '';
        }
        if(is_array($this->other)){
            $this->other = $this->serialize($this->other);
        }
    }
}
