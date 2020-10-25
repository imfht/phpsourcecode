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

/**
 * ViewHelper for file
 */
class GetFileUrlViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

    /**
     * @param string $file
     */
    public function initializeArguments()
    {
        $this->registerArgument('file', 'string', 'file path', true);
    }

    /**
     * Resolves the URL of an file
     *
     * @return string
     */
    public function render() {
        $file = $this->arguments['file'];
        
        $returnValue = NULL;

        // because the file value can possibly have link parameters, use explode to split all values
        $fileParts = explode(' ', $file);

        // Get the path relative to the page currently outputted
        if (substr($fileParts[0], 0, 5) === 'file:') {
            $fileUid = substr($fileParts[0], 5);

            if (!empty($fileUid) && \TYPO3\CMS\Core\Utility\MathUtility::canBeInterpretedAsInteger($fileUid)) {
                $fileObject = \TYPO3\CMS\Core\Resource\ResourceFactory::getInstance()->getFileObject($fileUid);

                if ($fileObject instanceof \TYPO3\CMS\Core\Resource\FileInterface) {
                    $returnValue = $fileObject->getPublicUrl();
                }
            }
        } elseif (is_file(PATH_site . $fileParts[0])) {
            $returnValue = $GLOBALS['TSFE']->tmpl->getFileName($fileParts[0]);
        } elseif (\TYPO3\CMS\Core\Utility\GeneralUtility::isValidUrl($fileParts[0])) {
            $returnValue = $fileParts[0];
        }

        return $returnValue;
    }

}
