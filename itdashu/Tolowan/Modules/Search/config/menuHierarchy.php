<?php
use Core\Config;
$settings = array(
    'taxonomy' => array(),
);
$taxonomyList = Config::get('m.taxonomy.entityTermContentModelList');
foreach ($taxonomyList as $key => $value) {
    $settings['taxonomy']['adminTerm'.ucfirst($key)] = 'adminTerm'.ucfirst($key);
}