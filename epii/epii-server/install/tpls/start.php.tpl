
echo "start phpcgi{{i}}\n";
if($is_win)
{
//php-cgi-spawner.exe "php/php-cgi.exe -c php/php.ini" 9000 4+16
runcmd('{{base_root}}/install/php-cgi-spawner.exe  "{{cmd}}  -c {{root}}/php.ini" {{port}} 4+16');
}else{
runcmd('{{cmd}}');
}


