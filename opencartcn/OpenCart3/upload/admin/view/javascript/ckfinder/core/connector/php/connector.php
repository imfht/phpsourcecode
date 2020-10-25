<?php
/*
 * CKFinder
 * ========
 * http://cksource.com/ckfinder
 * Copyright (C) 2007-2015, CKSource - Frederico Knabben. All rights reserved.
 *
 * The software, this file and its contents are subject to the CKFinder
 * License. Please read the license.txt file before using, installing, copying,
 * modifying or distribute this file or part of its contents. The contents of
 * this file is part of the Source Code of CKFinder.
 */
require_once __DIR__ . '/vendor/autoload.php';

use CKSource\CKFinder\CKFinder;

session_start();
$permission = isset($_SESSION['image_upload_permission']) ? $_SESSION['image_upload_permission'] : false;
if(!$permission){
	echo 'you have no permission to do this';
	die();
}

$ckfinder = new CKFinder(__DIR__ . '/../../../config.php');

$ckfinder->run();
