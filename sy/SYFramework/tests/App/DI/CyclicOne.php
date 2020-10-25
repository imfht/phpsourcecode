<?php
namespace SyApp\DI;

class CyclicOne {
	/** @Autowired SyApp\DI\CyclicTwo */
	public $two;
}