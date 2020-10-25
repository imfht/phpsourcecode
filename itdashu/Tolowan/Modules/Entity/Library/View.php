<?php
namespace Modules\Entity\Library;

class View{
    public static function adminNodeLink($item){
        global $di;
        $label = $item.
        $url = $di->getShared('url')->get(array(
            'for' => 'entity',
            'entity' => 'node',
            'id' => $item->id
        ));
        $output = '<a href="{{ url([\'for\':\'node\',\'type\':item.type,\'id\':item.node_id]) }}" data-toggle="tooltip" target="_blank" data-placement="right" title="访问{{ item.title }}">';
    }
}