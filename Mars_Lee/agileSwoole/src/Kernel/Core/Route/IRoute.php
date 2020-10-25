<?php

namespace Kernel\Core\Route;


interface IRoute
{
	public function add(string $method, string $path, $closure) : IRoute;
	public function getRouter();
}