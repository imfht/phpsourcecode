<?php
namespace SyApp\DI;

class CyclicTwo {
	/** @Autowired SyApp\DI\CyclicOne */
	public $one;
}