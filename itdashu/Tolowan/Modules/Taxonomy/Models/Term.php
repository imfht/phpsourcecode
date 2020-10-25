<?php
namespace Modules\Taxonomy\Models;

use Phalcon\Mvc\Model;
use Core\Config;

class Term extends Model
{
    public $id;
    public $name;
    public $description;
    public $widget;
    public $type;
    protected $attach;
    public function setAttach($attach){
        $this->attach = attachToString($attach);
    }
    public function getAttach(){
        return attachToArray($this->attach);
    }
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

}
