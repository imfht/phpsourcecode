  set $app_default 0;
if ( $app ~*  ^default$ ) {
   set $app_default 1;
}
if ( $host ~*  ^{{this_ip}} ) {
         set $app_default 1$app_default;
      
}
  if ( $app_default = 11 ){
       set $app {{default_app}};
  }