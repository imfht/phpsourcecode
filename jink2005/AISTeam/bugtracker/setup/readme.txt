Welcome to Bug Tracker!!

How to setup (Use setup wizard):
1. Un-tar the bug.tgz and put it in your web document root:

	# tar zxpf bug.tgz
	# mv bug /usr/local/apache/htdocs/data
	
	The /usr/local/apache/htdocs/data is your web document root.

2. Edit the include/config.php to setup the database information. You only
   have to edit the following information:

	$GLOBALS['BR_dbserver'] = "127.0.0.1";      // IP Address of database server
	$GLOBALS['BR_dbuser'] = "alex";             // User name to access the database
	$GLOBALS['BR_dbpwd'] = "abc123";            // Database password
	$GLOBALS['BR_dbname'] = "bugdb";            // Database name for bug tracker

	$GLOBALS["SYS_PROJECT_PATH"] = "/home/www/bug"; // Real path in the system
	$GLOBALS["SYS_URL_ROOT"] = "/bug";          // HTML URL path of index.php

3. Connection to setup wizard page and follow the instructions:

	http://your_ip/bug/setup/index.php
Done!!

Regards,
Alex Wang
