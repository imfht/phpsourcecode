<?php
namespace MiniFranske\FsMediaGallery\ViewHelpers\Widget;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Frans Saris <franssaris@gmail.com>
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

/**
 * Class PaginateViewHelper
 */
class PaginateViewHelper extends AbstractWidgetViewHelper
{

    /**
     * @var \MiniFranske\FsMediaGallery\ViewHelpers\Widget\Controller\PaginateController
     */
    protected $controller;

    /**
     * @param \MiniFranske\FsMediaGallery\ViewHelpers\Widget\Controller\PaginateController $controller
     * @return void
     */
    public function injectController(
        \MiniFranske\FsMediaGallery\ViewHelpers\Widget\Controller\PaginateController $controller
    ) {
        $this->controller = $controller;
    }
    
    /**
     * Initialize arguments.
     *
     * @api
     * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('objects', 'mixed', 'Object', true);
        $this->registerArgument('as', 'string', 'as', true);
        $this->registerArgument('itemsBefore', 'integer', 'itemsBefore', false, null);
        $this->registerArgument('itemsAfter', 'integer', 'itemsBefore', false, null);
        $this->registerArgument('configuration', 'array', 'configuration', false, array('itemsPerPage' => 10, 'insertAbove' => false, 'insertBelow' => true, 'maximumNumberOfLinks' => 99));
        $this->registerArgument('widgetId', 'string', 'Widget-ID');
    }

    /**
     * main render function
     *
     * @return string|\TYPO3\CMS\Extbase\Mvc\ResponseInterface
     */
    public function render() {
        $objects = $this->arguments['objects'];
        if (!($objects instanceof QueryResultInterface || $objects instanceof ObjectStorage || is_array($objects))) {
            throw new \UnexpectedValueException('Supplied file object type ' . gettype($objects) . ' must be QueryResultInterface or ObjectStorage or be an array.', 1454510731);
        }
        return $this->initiateSubRequest();
    }
}