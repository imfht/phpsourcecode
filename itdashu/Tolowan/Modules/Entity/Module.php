<?php
$di->setShared('entityManager', '\Modules\Entity\Library\EntityManager');

//定义函数
function entityList($entity, $query = array())
{
    global $di;
    $entityModel = $di->getShared('entityManager')->get($entity);
    return $entityModel->find($query);
}
function entity_render($entity,$data){
    $output = array(
        '#templates' => array(
            'entity',
            'entity'.ucfirst($entity)
        ),
        'data' => $data
    );
    if(isset($data->contentModel)){
        $output['#templates'][] = 'entity'.ucfirst($entity).ucfirst($data->contentModel);
    }
    return $output;
}