<?php
return array(
    'functions' => array(
        // URLs
        'route',
        'action',
        'asset',  
        'url',      
        'link_to',
        'link_to_asset',
        'link_to_route',
        'link_to_action',
        'secure_asset',
        'secure_url',
        // Translation
        'trans',
        'trans_choice',
        // Miscellaneous
        'csrf_token',
        'in_array'
    ),
    'filters' => array(
        // Strings
        'camel_case',
        'snake_case',
        'studly_case',
        'str_finish',
        'str_plural',
        'str_singular'
    ),
    'alias_shortcuts' => array(
        'config'    => 'config_get',
        'lang'      => 'lang_get',
        'logged_in' => 'auth_check',
    ),
);