<?php
use Core\Config;

function nodeSearch($query)
{
    global $di;
    $nodeEntity = $di->getShared('entityManager')->get('node');
    $nodeFields = $nodeEntity->getFields();
    $searchFields = array('default' => array(), 'fullText' => array());
    $nodeForm = $di->get('form')->create($nodeFields);
    foreach ($nodeFields as $key => $element) {
        $elementOptions = $element->getUserOptions();
        if (isset($elementOptions['search']) && $elementOptions['search'] === true) {
            if (isset($elementOptions['fullTextSearch']) && $elementOptions['fullTextSearch'] === true) {
                $searchFields['fullText'][] = $key;
            } else {
                $searchFields['search'][] = $key;
            }
        }
    }
}
function nodeRender($node,$template=null){
    if(!is_null($template)){
        $template = array(
            'node',
            'node-'.$template,
            'node-'.$node->contentModel,
            'node-'.$node->contentModel.'-'.$template,
            'node-'.$node->id,
        );
    }
    return array(
        '#templates' => array(
            'node',
            'node-'.$node->contentModel,
            'node-'.$node->id,
        ),
        'data' => $node
    );
}
function nodeList($query)
{
    global $di;
    $node = $di->getShared('entityManager')->getEntity('node');
    return $node->find($query);
}
function nodeTypeList()
{
    $nodeTypeList = Config::get('m.node.entityNodeContentModelList');
    $output = array();
    foreach ($nodeTypeList as $key => $nodeType) {
        $output[$key] = $nodeType['modelName'];
    }
    return $output;
}
