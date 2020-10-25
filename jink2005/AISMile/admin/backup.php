<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

define('_PS_ADMIN_DIR_', getcwd());

include(_PS_ADMIN_DIR_.'/../config/config.inc.php');

if (!Context::getContext()->employee->isLoggedBack())
	Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminLogin'));

$tabAccess = Profile::getProfileAccess(Context::getContext()->employee->id_profile, Tab::getIdFromClassName('AdminBackup'));

if ($tabAccess['view'] !== '1')
	die (Tools::displayError('You do not have permission to view here'));

$backupdir = realpath(_PS_ADMIN_DIR_ . '/backups/');

if ($backupdir === false)
	die (Tools::displayError('Backups directory does not exist.'));

if (!$backupfile = Tools::getValue('filename'))
	die (Tools::displayError('No file specified'));

// Check the realpath so we can validate the backup file is under the backup directory
$backupfile = realpath($backupdir.'/'.$backupfile);

if ($backupfile === false OR strncmp($backupdir, $backupfile, strlen($backupdir)) != 0 )
	die (Tools::displayError());

if (substr($backupfile, -4) == '.bz2')
    $contentType = 'application/x-bzip2';
else if (substr($backupfile, -3) == '.gz')
    $contentType = 'application/x-gzip';
else
    $contentType = 'text/x-sql';
$fp = @fopen($backupfile, 'r');

if ($fp === false)
	die (Tools::displayError('Unable to open backup file').' "'.addslashes($backupfile).'"');

// Add the correct headers, this forces the file is saved
header('Content-Type: '.$contentType);
header('Content-Disposition: attachment; filename="'.Tools::getValue('filename'). '"');

ob_clean();
$ret = @fpassthru($fp);

fclose($fp);

if ($ret === false)
	die (Tools::displayError('Unable to display backup file').' "'.addslashes($backupfile).'"');