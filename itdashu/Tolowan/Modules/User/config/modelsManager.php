<?php
$settings = array(
    'user' => array(
        'entity' => 'Modules\User\Entity\User',
        'columns' => array('user.id', 'user.name', 'user.email','user.phone', 'user.password', 'user.created', 'user.active','user.email_validate','user.phone_validate','user.changed'),
    ),
    'user_log' => array(
        'entity' => 'Modules\User\Models\UserLog',
        'columns' => array('user_log.id', 'user_log.uid', 'user_log.ip', 'user_log.type','user_log.time'),
    ),
);
