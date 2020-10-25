<?php
namespace MiniFranske\FsMediaGallery\Utility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Frans Saris <franssaris@gmail.com>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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
class StringUtility
{
    /**
     * SlashJS Removed from Core
     *
     * @see https://docs.typo3.org/typo3cms/extensions/core/Changelog/8.1/Deprecation-75621-GeneralUtilityMethods.html
     *
     * @param $string
     * @param bool $extended
     * @param string $char
     * @return mixed
     */
    public static function slashJS($string, $extended = false, $char = '\'')
    {
        if ($extended) {
            $string = str_replace('\\', '\\\\', $string);
        }

        return str_replace($char, '\\' . $char, $string);
    }

}