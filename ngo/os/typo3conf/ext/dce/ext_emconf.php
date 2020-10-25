<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "dce".
 *
 * Auto generated 03-03-2020 14:15
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
  'title' => 'Dynamic Content Elements (DCE)',
  'description' => 'Best flexform based content elements since 2012. With TCA mapping feature, simple backend view and much more features which makes it super easy to create own content element types.',
  'category' => 'Backend',
  'version' => '2.3.1',
  'state' => 'stable',
  'uploadfolder' => true,
  'createDirs' => '',
  'clearcacheonload' => true,
  'author' => 'Armin Vieweg',
  'author_email' => 'armin@v.ieweg.de',
  'author_company' => '',
  'constraints' => 
  array (
    'depends' => 
    array (
      'php' => '7.1.0-7.3.99',
      'typo3' => '8.7.0-9.5.99',
    ),
    'conflicts' => 
    array (
    ),
    'suggests' => 
    array (
    ),
  ),
  'autoload' => 
  array (
    'psr-4' => 
    array (
      'T3\\Dce\\' => 'Classes',
    ),
  ),
);

