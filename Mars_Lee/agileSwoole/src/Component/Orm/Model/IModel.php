<?php

namespace Component\Orm\Model;


use Component\Orm\Query\IQuery;

interface IModel
{
	public function insert(array $data):IQuery;
	public function update(array $data):IQuery;
	public function delete(array $data = []):IQuery;
	public function select(string $fields):IQuery;
}