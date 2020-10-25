<?php
namespace ArminVieweg\PhpexcelService\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2014 Armin Ruediger Vieweg <armin@v.ieweg.de>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Phpexcel Service
 *
 * @package ArminVieweg\PhpexcelService\Service
 */
class Phpexcel extends \TYPO3\CMS\Core\Service\AbstractService {

	/**
	 * Returns instance of basic class 'PHPExcel'
	 *
	 * @return \PHPExcel
	 */
	public function getPhpExcel() {
		return $this->getInstanceOf('PHPExcel');
	}

	/**
	 * Creates and returns instance of given class name.
	 *
	 * @param string $className
	 * @return object
	 */
	public function getInstanceOf($className) {
		if (func_num_args() > 1) {
			$constructorArguments = func_get_args();
			array_shift($constructorArguments);

			$reflectedClass = new \ReflectionClass($className);
			$instance = $reflectedClass->newInstanceArgs($constructorArguments);
		} else {
			$instance = GeneralUtility::makeInstance($className);
		}
		return $instance;
	}
}