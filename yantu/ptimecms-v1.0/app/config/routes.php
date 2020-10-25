<?php
$router = new \Phalcon\Mvc\Router();

//Define a route
$router->addGet(
    "/{alias:[a-zA-Z0-9_-]+}",
    array(
        "controller" => "index",
        "action"     => "alias",
        "alias"     =>1
    )
);

$router->addGet(
    "/{object:[a-z]+}/{object_id:[0-9]+}",
    array(
        "controller" => "index",
        "action"     => "show",
        "object"    => 1,
        "object_id" => 2
    )
);

$router->addGet(
    "/category/([0-9]+)",
    array(
        "controller" => "index",
        "action"     => "list",
        "category_id"    =>1
    )
);

$router->addGet(
    "/search/{content}",
    array(
        "controller" => "index",
        "action"     => "search",
        "content"   =>1
    )
);
$router->addGet(
    "/search",
    array(
        "controller" => "index",
        "action"     => "search"
    )
);

$router->addGet(
    "/{object:[a-z]+}/more",
    array(
        "controller" => "index",
        "action"     => "more",
        "object"    => 1
    )
);

$router->addGet(
    "/init",
    array(
        "controller" => "index",
        "action"     => "init"
    )
);

$router->addGet(
    "/{object:[a-z]+}/{object_id:[0-9]+}/comment",
    array(
        "controller" => "comment",
        "action"     => "index",
        "object"     => 1,
        "object_id"  => 2
    )
);

$router->addPost(
    "/{object:[a-z]+}/{object_id:[0-9]+}/comment",
    array(
        "controller" => "comment",
        "action"     => "store",
        "object"     => 1,
        "object_id"  => 2
    )
);

$router->addDelete(
    "/{object:[a-z]+}/{object_id:[0-9]+}/comment/{comment_id:[0-9]+}",
    array(
        "controller" => "comment",
        "action"     => "destroy",
        "object"     => 1,
        "object_id"  => 2,
        "comment_id" => 3
    )
);


