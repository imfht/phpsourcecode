<?php

declare(strict_types=1);
$json = file_get_contents('php://input');
$obj = json_decode($json);
$task_id = $obj->data->task_id;
file_put_contents(__DIR__.'/output/audio/'."$task_id.json", $json);

echo '{"ret": 0,"msg": "ok"}';
