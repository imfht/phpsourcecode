<?php
/**
 * Created by PhpStorm.
 * @author Luficer.p <81434146@qq.com>
 * Date: 16/11/3
 * Time: 下午12:50
 */
$router->get("/users","Users@index");


$router->get("/users/id/:id","Users@view");

$router->get("/users/update/id/:id","Users@update");

$router->post("/users/update/id/:id","Users@update");

$router->get("/users/create","Users@create");

$router->post("/users/create","Users@create");

$router->get("/users/delete/id/:id","Users@delete");
