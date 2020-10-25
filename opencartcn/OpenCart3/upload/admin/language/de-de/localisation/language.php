<?php
/**
 * @version		$Id: language.php 5287 2018-06-17 20:59:37Z mic $
 * @package		Language Translation German Backend
 * @author		mic - http://osworx.net
 * @copyright	2018 OSWorX
 * @license		GPL - www.gnu.org/copyleft/gpl.html
 */

// Heading
$_['heading_title']		= 'Sprachen';

// Text
$_['text_success']		= 'Datensatz erfolgreich geändert';
$_['text_list']			= 'Übersicht';
$_['text_add']			= 'Neu';
$_['text_edit']			= 'Bearbeiten';

// Column
$_['column_name']		= 'Name';
$_['column_code']		= 'Code';
$_['column_sort_order']	= 'Reihenfolge';
$_['column_action']		= 'Aktion';

// Entry
$_['entry_name']		= 'Name';
$_['entry_code']		= 'Code';
$_['entry_locale']		= 'Lokale Einst.';
$_['entry_status']		= 'Status';
$_['entry_sort_order']	= 'Reihenfolge';

// Help
$_['help_locale']		= 'Wird zur Browsererkennung verwendet, Beispiel: de,de_de,de-DE,de_DE<br />(mehrere mit Komma trennen)';
$_['help_status']		= 'Im Shop bei Sprachenauswahl zeigen/verbergen';

// Error
$_['error_permission']	= 'Keine Rechte für diese Aktion';
$_['error_exists']		= 'Sprache schon hinzugefügt';
$_['error_name']		= 'Name muss zwischen 3 und 32 Buchstaben lang sein';
$_['error_code']		= 'Sprachcode muss zwei Zeichen lang sein';
$_['error_locale']		= 'Lokale Einst. erforderlich';
$_['error_default']		= 'Sprache kann nicht gelöscht werden da als Standardsprache des Shops definiert';
$_['error_admin']		= 'Sprache ist aktuell die Standardsprache der Verwaltung und kann daher nicht gelöscht werden';
$_['error_store']		= 'Sprache kann nicht gelöscht werden da sie aktuell %s Shop(s) zugeordnet ist';
$_['error_order']		= 'Sprache kann nicht gelöscht werden weil sie noch %s Auftrag/Aufträgen zugeordnet ist';