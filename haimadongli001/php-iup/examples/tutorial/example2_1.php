<?php

error_reporting(E_ALL ^ E_NOTICE);

if (!extension_loaded("iup")){
    die("iup extension is unavailable");
};

function main()
{
    IupOpen();
    
    IupSetGlobal("UTF8MODE","Yes");

    IupMessage("Hello World 1", "Hello world from IUP.");

    IupClose();
}

main();