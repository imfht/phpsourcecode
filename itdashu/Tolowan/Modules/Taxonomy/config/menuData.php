<?php
use Core\Config;
$settings = array(
    'taxonomy' => array(
        'href' => '#',
        'name' => '术语',
        'icon' => 'fa fa-th-large',
    ),
    'adminTermTags' => array(
        'href' => array(
            'for' => 'adminTermList',
            'contentModel' => 'tags',
            'page' => 1,
        ),
        'icon' => 'fa fa-stack-overflow',
        'name' => '标签',
    ),
);
$taxonomyList = Config::get('m.taxonomy.entityTermContentModelList');
foreach ($taxonomyList as $key => $value) {
    $settings['adminTerm' . ucfirst($key)] = array(
        'href' => array(
            'for' => 'adminTermList',
            'contentModel' => $key,
            'page' => 1,
        ),
        'icon' => 'fa fa-stack-overflow',
        'name' => $value['modelName'],
    );
}
