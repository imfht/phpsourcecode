<?php
function PrintWelcomeMesg()
{
?>
	<p>Welcome to Bug Tracker upgrade interface!!</p>
	<p>This script will guide you through the upgrade. These are the steps you 
	   will be going through in this script:
	</p>
	<ul>
		<li>Update database schema.</li>
		<li>Remove old strings and insert new strings.</li>
		<li>Update the version information in the database.</li>
	</ul>
	<p><b><font size="3" color="red">Pleaes Backup Your Database Before Upgrade!!</font></b></p>
	<p>It is very important that you follow the on-screen instructions precisely and 
		not skip over any steps. 
	</p>
<?php
}
PrintWelcomeMesg();
?>

