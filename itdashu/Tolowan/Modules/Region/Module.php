<?php
use Core\COnfig;

$di->getShared('eventsManager')->attach('entity:links', function ($event, $entity) {
    if ($entity->getEntityId() == 'configList' && $entity->contentModel == 'region') {
        $links = $entity->getLinks();
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
});

function regionList(){
    $regionListData = Config::get('m.region.list');
    $output = array();
    foreach ($regionListData as $key => $value){
        $output[$key] = $value['name'];
    }
    return $output;
}

function regionRender($region)
{
    $regionList = Config::get('m.region.list');
    if (!isset($regionList[$region])) {
        return false;
    }
    $blockList = Config::get('m.region.blockList-' . $region);
    $output = array(
        '#templates' => array(
            'region',
            'region-'.$region,
        ),
        '#module' => 'region',
        'region' => $region,
        'regionInfo' => $regionList[$region],
        'blockList' => $blockList
    );
    return $output;
}

function blockRender($block,$region)
{
    global $di;
    if (!is_array($block['contentModel']) && !isset($block['contentModel'])) {
        throw new \Exception('区块数据不合法');
    }
    if($di->getShared('security')->isCanAccess($block) === false){
        return '';
    }
    $contentModel = $block['contentModel'];
    $blockContentModelList = Config::get('m.region.entityBlockContentModelList');
    if (!isset($blockContentModelList[$contentModel])) {
        throw new Exception("区块类型不存在");
    }
    if (!isset($blockContentModelList[$contentModel]['class'])) {
        $block['#templates'] = array(
            'block',
            'block-' . $contentModel,
            'block-'.$region,
            'block-'.$region.'-'.$contentModel,
            'block-'.$region.'-' . $block['id']
        );
        $block['#module'] = 'region';
        return $block;
    } else {
        $contentModelClass = $blockContentModelList[$contentModel];
        $output = new $contentModelClass($block);
        return $output;
    }
    return false;
}