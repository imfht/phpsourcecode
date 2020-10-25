<?php
namespace App\Http\Controllers\Test;

class PhpTest{
	
	public function testStringMethod(){
		$print = 'printHellow';
		return $this->$print();
	}
	
	private function printHellow(){
		return 'hellow string';
	}
}