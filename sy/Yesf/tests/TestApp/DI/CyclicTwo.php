<?php
namespace YesfApp\DI;

class CyclicTwo {
	/** @Autowired YesfApp\DI\CyclicOne */
	public $one;
}