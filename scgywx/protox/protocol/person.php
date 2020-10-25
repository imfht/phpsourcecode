<?php
class person_protocol extends protox
{
	protected $fields = array(
		'name' => 'string',
		'age' => 'int',
		'phone' => 'string',
		'address' => 'string',
		'family' => 'array.person_family_members',
		'qq' => 'string|optional',
		'email' => 'string|optional',
	);
}

class person_family_members_protocol extends protox
{
	protected $fields = array(
		'type' => 'string',
		'name' => 'string',
	);
}