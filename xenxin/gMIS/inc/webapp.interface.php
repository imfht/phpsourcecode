<?php
/* WebApp Interface defintion for all of the implement classes
 * v0.1,
 * wadelau@ufqi.com,
 * 2011-07-10 15:27
 */

interface WebAppInterface
{
	function set($key, $value);
	function get($key);

	function setTbl($tbl);
	function getTbl();
		
	function setId($id);
	function getId();

	function setBy($fields, $conditions);
	function getBy($fields, $conditions);
	function execBy($fields, $conditions);
	function rmBy($conditions);

  	function toString($object);

	/*
	function setLang($lang);
	function getLang();
	function checkDB();
	*/
}
?>
