o_file=$(readlink $0)
c_idr=`pwd`
{{php_cmd}} $(cd `dirname $o_file`; pwd)/epii-server.php $* $c_idr