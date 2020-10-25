<?php
$count = 10;
while(--$count >= 0)
{
	//file_put_contents("/tmp/crontab.log", "testzzzzz\n", FILE_APPEND);
	echo "testzzzzz\n";
	sleep(1);
}
