<?php

error_reporting(E_ALL ^ E_NOTICE);

if (!extension_loaded("iup")){
    die("iup extension is unavailable");
};

function main()
{
    IupOpen();

    IupSetGlobal("UTF8MODE","Yes");

    $label = IupLabel("Hello world from IUP.");

    $dlg = IupDialog(IupVbox($label));

    IupSetAttribute($dlg, "TITLE", "Hello World 2");

    IupShowXY($dlg, IUP_CENTER, IUP_CENTER);

    IupMainLoop();

    IupClose();
}

main();