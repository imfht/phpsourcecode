<?php

error_reporting(E_ALL ^ E_NOTICE);

if (!extension_loaded("iup")){
    die("iup extension is unavailable");
};

function btn_exit_cb($self)
{
  IupMessage("Hello World Message", "Hello world from IUP.");
  /* Exits the main loop */
  return IUP_CLOSE;
}

function main()
{
    IupOpen();
    
    IupSetGlobal("UTF8MODE","Yes");

    $button = IupButton("OK", NULL);

    $vbox = IupVbox($button);

    $dlg = IupDialog($vbox);

    IupSetAttribute($dlg, "TITLE", "Hello World 3");

    /* Registers callbacks */
    IupSetCallback($button, "ACTION", "btn_exit_cb");

    IupShowXY($dlg, IUP_CENTER, IUP_CENTER);

    IupMainLoop();

    IupClose();

}

main();