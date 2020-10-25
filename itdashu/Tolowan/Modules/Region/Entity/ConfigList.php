<?php 
namespace Modules\Region\Entity;

class ConfigList{
    public static function links($event, $entity){
        $links = $entity->getLinks();
        echo "string";
        $links['addBlock'] = array(
                'href' => array(
                    'for' => 'adminRegionBlockAddList',
                    'region' => $entity->id,
                ),
                'data-target' => 'right_handle',
                'icon' => 'info',
                'name' => '添加区块'
            );
        $links['sortBlock'] = array(
                'href' => array(
                    'for' => 'adminRegionBlockSort',
                    'region' => $entity->id,
                ),
                'data-target' => 'right_handle',
                'icon' => 'info',
                'name' => '区块列表'
        );
        $entity->setLinks($links);
    }
}