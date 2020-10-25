<?php

error_reporting(E_ALL ^ E_NOTICE);

if (!extension_loaded("iup")){
    die("iup extension is unavailable");
};

sleep(6);

function btn_exit_cb($self)
{
  /* Exits the main loop */
  return IUP_CLOSE;
}

function main()
{
    IupOpen();
    
    IupSetGlobal("UTF8MODE","Yes");

    $label =  IupLabel("Hello world from IUP.");

    $button = IupButton("OK", NULL);

    $vbox = IupVbox($label);

    IupAppend($vbox,$button);

    $dlg = IupDialog($vbox);

    IupSetAttribute($dlg, "TITLE", "Hello World 4");

    /* Registers callbacks */
    IupSetCallback($button, "ACTION", "btn_exit_cb");

    IupShowXY($dlg, IUP_CENTER, IUP_CENTER);

    IupMainLoop();

    IupClose();

}

main();