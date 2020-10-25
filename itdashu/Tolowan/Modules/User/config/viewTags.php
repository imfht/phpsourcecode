<?php
$settings = array(
    'user' => array(
        'fun' => '\Modules\User\Library\ViewTags::user',
        'type' => 'function',
    ),
    'user_list' => array(
        'fun' => '\Modules\User\Library\Model::find',
        'type' => 'function',
    ),
);
