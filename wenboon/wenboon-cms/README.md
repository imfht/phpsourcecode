#apache rewrite
<IfModule mod_rewrite.c>
    RewriteEngine on
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(.*)$ index.php/$1 [QSA,PT,L]
</IfModule>

#nginx rewrite
 if (!-e $request_filename){
         rewrite  ^(.*)$  /index.php?s=$1 last;
         break;
 }
 
 #iis rewrite
[ISAPI_Rewrite]
CacheClockRate 3600
RepeatLimit 32
RewriteRule (?!/phpmyadmin)(?!/Lib)(?!/Uploads)(?!/Admin)(?!/Home)(?!/Public)(?!/index.php)(?!/info.txt)(?!/sitemap.xml)(?!/.html)(?!/.ico)(.*)$ /index.php?s=$1 [I]
