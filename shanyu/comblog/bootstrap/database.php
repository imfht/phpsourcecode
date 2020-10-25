<?php
$dns = 'mysql:host='.getenv('DB_HOST').';dbname='.getenv('DB_DATABASE').';port='.getenv('DB_PORT').';charset=utf8';
return [
    'dsn'=>$dns,
    'user'=>getenv('DB_USERNAME'),
    'pass'=>getenv('DB_PASSWORD'),
];