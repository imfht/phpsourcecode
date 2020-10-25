<?php
namespace MiniFranske\FsMediaGallery\ViewHelpers\Widget\Controller;

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
 * PaginateController
 */
class PaginateController extends \TYPO3\CMS\Fluid\ViewHelpers\Widget\Controller\PaginateController
{

    /**
     * @param integer $currentPage
     * @return void
     */
    public function indexAction($currentPage = 1)
    {
        $itemsBefore = null;
        $itemsAfter = null;

        // set current page
        $this->currentPage = (integer)$currentPage;
        if ($this->currentPage < 1) {
            $this->currentPage = 1;
        }
        if ($this->currentPage > $this->numberOfPages) {
            // set $modifiedObjects to NULL if the page does not exist
            $modifiedObjects = null;
        } else {
            // modify query
            $itemsPerPage = (integer)$this->configuration['itemsPerPage'];
            if ($itemsPerPage < 1) {
                $itemsPerPage = 1;
            }
            if (is_a($this->objects, '\TYPO3\CMS\Extbase\Persistence\Generic\QueryResult')) {
                $query = $this->objects->getQuery();
                $query->setLimit($itemsPerPage);
                if ($this->currentPage > 1) {
                    $query->setOffset((integer)($itemsPerPage * ($this->currentPage - 1)));
                }
                $modifiedObjects = $query->execute();
            } else {
                $offset = 0;
                if ($this->currentPage > 1) {
                    $offset = ((integer)($itemsPerPage * ($this->currentPage - 1)));
                }
                if (!empty($this->widgetConfiguration['itemsBefore'])) {
                    $itemsBefore = array_slice($this->objects, 0, $offset);
                }
                if (is_array($this->objects)) {
                    $modifiedObjects = array_slice($this->objects, $offset,
                        (integer)$this->configuration['itemsPerPage']);
                } else {
                    $modifiedObjects = array_slice($this->objects->toArray(), $offset,
                        (integer)$this->configuration['itemsPerPage']);
                }
                if (!empty($this->widgetConfiguration['itemsAfter'])) {
                    $itemsAfter = array_slice($this->objects, $offset + (integer)$this->configuration['itemsPerPage']);
                }
            }
        }
        $this->view->assign('contentArguments', [
            $this->widgetConfiguration['itemsBefore'] => $itemsBefore,
            $this->widgetConfiguration['as'] => $modifiedObjects,
            $this->widgetConfiguration['itemsAfter'] => $itemsAfter
        ]);
        $this->view->assign('configuration', $this->configuration);
        $this->view->assign('pagination', $this->buildPagination());
    }

}
