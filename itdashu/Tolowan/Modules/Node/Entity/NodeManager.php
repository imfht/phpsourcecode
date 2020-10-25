<?php
namespace Modules\Node\Entity;

use Modules\Entity\Entity\Manager;

class NodeManager extends Manager
{
    protected $_entityId = 'node';
    protected $_module = 'node';
    protected $_source = 'node';
    public $columns = array(
        'id','state','contentModel','created','changed','uid','comment','hot','top','essence'
    );

    public function menuTabs()
    {
        $links = array();
        $contentModelList = $this->getContentModelList();
        foreach ($contentModelList as $key => $contentModel) {
            $links[] = array(
                'href' => array(
                    'for' => 'adminEntityAdd',
                    'entity' => $this->_entityId,
                    'contentModel' => $key,
                ),
                'name' => $contentModel['modelName'],
            );
        }
        return $links;
    }

    public function saveBefore()
    {
        $data = $this->entityForm->getData();
        if (!isset($data['uid']) || empty($data['uid'])) {
            $data['uid'] = $this->user->id;
        }
        $this->entityForm->setData($data);
        parent::saveBefore($this->entityForm);
    }
}
