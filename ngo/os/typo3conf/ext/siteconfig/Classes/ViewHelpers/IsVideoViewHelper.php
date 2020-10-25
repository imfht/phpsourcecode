<?php
namespace Jykj\Siteconfig\ViewHelpers;
    /***************************************************************
     *  Copyright notice
     *
     *  (c) 2013 Frans Saris <frans@beech.it>
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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * ViewHelper for video
 */
class IsVideoViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

    /**
     * @param string $src
     */
    public function initializeArguments()
    {
        $this->registerArgument('src', 'string', 'file path', true);
    }
    
    /**
     * Go through all given classes which implement the mediainterface
     * and use the proper ones to render the media element
     *
     * @param string $src
     * @return int
     */
    public function render() {
        $src = $this->arguments['src'];
        $extension = end(GeneralUtility::trimExplode('.', $src));
        return in_array($extension, array('mp4', 'mov'))?1:0;
    }

}
