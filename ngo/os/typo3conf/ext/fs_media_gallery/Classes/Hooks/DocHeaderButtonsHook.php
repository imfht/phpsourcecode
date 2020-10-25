<?php
namespace MiniFranske\FsMediaGallery\Hooks;

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

use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Hook to add extra button to DocHeaderButtons in file list
 */
class DocHeaderButtonsHook extends \MiniFranske\FsMediaGallery\Service\AbstractBeAlbumButtons
{

    /**
     * Create button
     *
     * @param string $title
     * @param string $shortTitle
     * @param string $icon
     * @param string $url
     * @param bool $addReturnUrl
     * @return string|array
     */
    protected function createLink($title, $shortTitle, $icon, $url, $addReturnUrl = true)
    {
        return [
            'title' => $title,
            'icon' => $icon,
            'url' => $url . ($addReturnUrl ? '&returnUrl=' . rawurlencode($_SERVER['REQUEST_URI']) : '')
        ];
    }

    /**
     * Get buttons
     *
     * @param array $params
     * @param ButtonBar $buttonBar
     * @return array
     */
    public function moduleTemplateDocHeaderGetButtons($params, ButtonBar $buttonBar)
    {
        $buttons = $params['buttons'];

        if (GeneralUtility::_GP('M') === 'file_FilelistList' || GeneralUtility::_GP('route') === '/file/FilelistList/') {
            foreach ($this->generateButtons(GeneralUtility::_GP('id')) as $buttonInfo) {
                $button = $buttonBar->makeLinkButton();
                $button->setIcon($buttonInfo['icon']);
                $button->setTitle($buttonInfo['title']);
                if (strpos($buttonInfo['url'], 'alert') === 0) {
                    $button->setOnClick($buttonInfo['url'] . ';return false;');
                } else {
                    $button->setHref($buttonInfo['url']);
                }
                $buttons['left'][2][] = $button;
            }
        }

        return $buttons;
    }
}