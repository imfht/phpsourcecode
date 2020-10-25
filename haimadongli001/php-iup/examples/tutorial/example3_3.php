<?php

error_reporting(E_ALL ^ E_NOTICE);

if (!extension_loaded("iup")){
    die("iup extension is unavailable");
};

$multitext = NULL;

function open_cb($self){

    global $multitext;

    $filedlg = IupFileDlg();
    IupSetAttribute($filedlg,"DIALOGTYPE", "OPEN");
    IupSetAttribute($filedlg,"EXTFILTER", "Text Files|*.txt|All Files|*.*|");


    IupPopup($filedlg, IUP_CENTER, IUP_CENTER);

    if (IupGetInt($filedlg, "STATUS") != -1)
    {
        $filename = IupGetAttribute($filedlg, "VALUE");
        $str = file_get_contents($filename);
        if($str === false){
            IupMessage("Error", "Fail when reading from file: ".$filename );
        }else{
            IupSetStrAttribute($multitext, "VALUE", $str);
        }
    }

    IupDestroy($filedlg);

    return IUP_DEFAULT;
}

function saveas_cb($self){
    
    global $multitext;

    $filedlg = IupFileDlg();
    IupSetAttribute($filedlg,"DIALOGTYPE", "SAVE");
    IupSetAttribute($filedlg,"EXTFILTER", "Text Files|*.txt|All Files|*.*|");

    IupPopup($filedlg, IUP_CENTER, IUP_CENTER);

    if (IupGetInt($filedlg, "STATUS") != -1)
    {
        $filename = IupGetAttribute($filedlg, "VALUE");
        $str = IupGetAttribute($multitext, "VALUE");
        $count = IupGetInt($multitext, "COUNT");
        $re = file_put_contents($filename, $str);
        if($re === false){
            IupMessage("Error", "Fail when writing to file: ".$filename);
        }
    }

    IupDestroy($filedlg);

    return IUP_DEFAULT;
}

function font_cb($self){

    $fontdlg = IupFontDlg();

    global $multitext;

    $font = IupGetAttribute($multitext,"FONT");

    IupSetAttribute($fontdlg,"VALUE",$font);

    IupPopup($fontdlg, IUP_CENTER,IUP_CENTER);

    if(IupGetInt($fontdlg,"STATUS") == 1){
        $font = IupGetAttribute($fontdlg,"VALUE");

        IupSetAttribute($multitext,"FONT",$font);
    }

    IupDestroy($fontdlg);

    return IUP_DEFAULT;
}

function about_cb($self){
    IupMessage("About", "   Simple Notepad\n\nAutors:\n   Gustavo Lyrio\n   Antonio Scuri");
    return IUP_DEFAULT;
}

function exit_cb($self){
    return IUP_CLOSE;
}

function main()
{
    IupOpen();
    
    IupSetGlobal("UTF8MODE","Yes");

    global $multitext;
    
    $multitext = IupText(NULL);

    IupSetAttribute($multitext, "MULTILINE", "YES");

    IupSetAttribute($multitext, "EXPAND", "YES");

    $item_open = IupItem("Open", NULL);
    $item_saveas = IupItem("Save As", NULL);
    $item_exit = IupItem("Exit", NULL);
    $item_font = IupItem("Font...", NULL);
    $item_about = IupItem("About...", NULL);

    IupSetCallback($item_exit, "ACTION", "exit_cb");
    IupSetCallback($item_open, "ACTION", "open_cb");
    IupSetCallback($item_saveas, "ACTION", "saveas_cb");
    IupSetCallback($item_font, "ACTION", "font_cb");
    IupSetCallback($item_about, "ACTION", "about_cb");


    $file_menu = IupMenu($item_open);
    IupAppend($file_menu,$item_saveas);
    IupAppend($file_menu,IupSeparator());
    IupAppend($file_menu,$item_exit);

    $format_menu = IupMenu($item_font);

    $help_menu = IupMenu($item_about);

    $sub_menu_file = IupSubmenu("File", $file_menu);
    $sub_menu_format = IupSubmenu("Format", $format_menu);
    $sub_menu_help = IupSubmenu("Help", $help_menu);
    
    $menu = IupMenu($sub_menu_file);
    IupAppend($menu,$sub_menu_format);
    IupAppend($menu,$sub_menu_help);

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