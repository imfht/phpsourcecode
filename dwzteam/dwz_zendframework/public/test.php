<?php 
echo "<br/>a:";
var_dump($_REQUEST['a']);

echo "<br/>b:";
var_dump($_REQUEST['b']);

echo "<br/>c:";
var_dump($_REQUEST['c']);

echo "<br/>d:";
var_dump($_REQUEST['d']);
?>

<form method="post" action="test.php">
	<p>a:
		<input type="text" name="a" value="1"/>
		<input type="text" name="a" value="2"/>
		<input type="text" name="a" value="3"/>
	</p>
	<p>b:
		<input type="text" name="b[]" value="1"/>
		<input type="text" name="b[]" value="2"/>
		<input type="text" name="b[]" value="3"/>
	</p>
	<p>c:
		<input type="text" name="c[1]" value="1"/>
		<input type="text" name="c[2]" value="2"/>
		<input type="text" name="c[3]" value="3"/>
	</p>
	<p>d:
		<input type="text" name="d[a]" value="1"/>
		<input type="text" name="d[b]" value="2"/>
		<input type="text" name="d[c]" value="3"/>
		<input type="text" name="d[#]" value="3"/>
	</p>
	<p><input type="submit" value="submit"/></p>
</form>