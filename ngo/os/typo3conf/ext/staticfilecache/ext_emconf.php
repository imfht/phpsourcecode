<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "staticfilecache".
 *
 * Auto generated 03-03-2020 14:23
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
  'title' => 'StaticFileCache',
  'description' => 'Transparent StaticFileCache solution using mod_rewrite and mod_expires. Increase performance for static pages by a factor of 230!!',
  'category' => 'fe',
  'version' => '10.1.0',
  'state' => 'stable',
  'clearcacheonload' => true,
  'author' => 'StaticFileCache Team',
  'author_email' => 'tim@fruit-lab.de',
  'author_company' => 'StaticFileCache Team',
  'constraints' => 
  array (
    'depends' => 
    array (
      'typo3' => '9.5.0-10.9.99',
      'php' => '7.2.0-0.0.0',
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
      'SFC\\Staticfilecache\\' => 'Classes',
    ),
  ),
  'uploadfolder' => false,
  'createDirs' => NULL,
);

