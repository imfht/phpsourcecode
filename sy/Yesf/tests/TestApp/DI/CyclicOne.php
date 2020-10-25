<?php
namespace YesfApp\DI;

class CyclicOne {
	/** @Autowired YesfApp\DI\CyclicTwo */
	public $two;
}