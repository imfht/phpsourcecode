<?php
namespace Modules\Taxonomy\Entity;

use Modules\Entity\Entity\Manager;

class TermManager extends Manager
{
    protected $_entityId = 'term';
    protected $_module = 'taxonomy';
    public $columns = array(
        'id','name','contentModel','description','parent','widget','other','attach','changed','created'
    );

    public function menuTabs(){
        $links = array();
        $contentModelList = $this->getContentModelList();
        foreach($contentModelList as $key => $contentModel){
            $links[] = array(
                'href' => array(
                    'for' => 'adminEntityAdd',
                    'entity' => $this->_entityId,
                    'contentModel' => $key
                ),
                'name' => $contentModel['modelName']
            );
        }
        return $links;
    }

}