--TEST--
Check for php_snowflake presence
--SKIPIF--
<?php if (!extension_loaded("php_snowflake")) print "skip"; ?>
--FILE--
<?php 
if (strlen(PhpSnowFlake::nextId(999)) == 32) {
    echo 'php_snowflake extension is available'
}
?>
--EXPECT--
php_snowflake extension is available
