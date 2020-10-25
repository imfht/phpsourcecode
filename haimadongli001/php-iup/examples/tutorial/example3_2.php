<?php

error_reporting(E_ALL ^ E_NOTICE);

if (!extension_loaded("iup")){
    die("iup extension is unavailable");
};

function exit_cb($self){

    return IUP_CLOSE;
}

function main()
{
    IupOpen();
    
    IupSetGlobal("UTF8MODE","Yes");
    
    $multitext = IupText(NULL);

    IupSetAttribute($multitext, "MULTILINE", "YES");

    IupSetAttribute($multitext, "EXPAND", "YES");

    $item_open = IupItem("Open", NULL);
    $item_saveas = IupItem("Save As", NULL);

    $item_exit = IupItem("Exit", NULL);
    IupSetCallback($item_exit, "ACTION", "exit_cb");

    $file_menu = IupMenu($item_open);
    IupAppend($file_menu,$item_saveas);
    IupAppend($file_menu,IupSeparator());
    IupAppend($file_menu,$item_exit);

    $sub1_menu = IupSubmenu("File", $file_menu);
    
    $menu = IupMenu($sub1_menu);

    $vbox = IupVbox($multitext);

    $dlg = IupDialog($vbox);

    IupSetAttributeHandle($dlg, "MENU", $menu);

    IupSetAttribute($dlg, "TITLE", "Simple Notepad");

    IupSetAttribute($dlg, "SIZE", "QUARTERxQUARTER");

    IupShowXY($dlg, IUP_CENTER, IUP_CENTER);

    IupSetAttribute($dlg, "USERSIZE", NULL);

    IupMainLoop();

    IupClose();

}

main();