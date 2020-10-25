<?php

error_reporting(E_ALL ^ E_NOTICE);

if (!extension_loaded("iup")){
    die("iup extension is unavailable");
};

function main()
{
    IupOpen();
    
    IupSetGlobal("UTF8MODE","Yes");

    $multitext = IupText(NULL);
    
    $vbox = IupVbox($multitext);
    
    IupSetAttribute($multitext, "MULTILINE", "YES");
    
    IupSetAttribute($multitext, "EXPAND", "YES");
    
    $dlg = IupDialog($vbox);
    
    IupSetAttribute($dlg, "TITLE", "Simple Notepad");
    
    IupSetAttribute($dlg, "SIZE", "QUARTERxQUARTER");
    
    IupShowXY($dlg, IUP_CENTER, IUP_CENTER);
    
    IupSetAttribute($dlg, "USERSIZE", NULL);
    
    IupMainLoop();
    
    IupClose();
    
}

main();