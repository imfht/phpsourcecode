<?php

error_reporting(E_ALL ^ E_NOTICE);

if (!extension_loaded("iup")){
    die("iup extension is unavailable");
};

// sleep(10);
$dialogs = array();

/********************************** Utilities *****************************************/
function str_find($haystack, $needle, $offset = 0, $casesensitive = 1){
    
    if(empty($haystack) || $needle === ""){
        return false;
    }

    if($offset > strlen($haystack)){
        $offset = 0;
    }

    if($casesensitive){
        $pos = strpos($haystack, $needle, $offset);
    }else{
        $pos = stripos($haystack, $needle, $offset);
    }

    return $pos;

}

function new_file($ih)
{
  $dlg = IupGetDialog($ih);
  $multitext = IupGetDialogChild($dlg, "MULTITEXT");

  IupSetAttribute($dlg, "TITLE", "Untitled - Simple Notepad");
  IupSetAttribute($multitext, "FILENAME", NULL);
  IupSetAttribute($multitext, "DIRTY", "NO");
  IupSetAttribute($multitext, "VALUE", "");
}

function open_file($ih, $filename)
{
    global $dialogs;
    $config = $dialogs["CONFIG"];

    $str = file_get_contents($filename);
    if ($str)
    {
        $dlg = IupGetDialog($ih);
        $multitext = IupGetDialogChild($dlg, "MULTITEXT");
        $config = IupGetAttribute($multitext, "CONFIG");
        echo basename($filename);
        IupSetAttribute($dlg, "TITLE", basename($filename)." - Simple Notepad");
        IupSetStrAttribute($multitext, "FILENAME", $filename);
        IupSetAttribute($multitext, "DIRTY", "NO");
        IupSetStrAttribute($multitext, "VALUE", $str);
        IupConfigRecentUpdate($config, $filename);
    }
}


function save_file($multitext)
{
    $filename = IupGetAttribute($multitext, "FILENAME");

    $str = IupGetAttribute($multitext, "VALUE");
    $count = IupGetInt($multitext, "COUNT");

    if (file_put_contents($filename, $str)){
        IupSetAttribute($multitext, "DIRTY", "NO");
    }
}

function saveas_file($multitext, $filename)
{
    global $dialogs;
    $config = $dialogs["CONFIG"];

    $str = IupGetAttribute($multitext, "VALUE");
    $count = IupGetInt($multitext, "COUNT");
    if (file_put_contents($filename, $str))
    {
        IupSetAttribute(IupGetDialog($multitext), "TITLE", basename($filename)." - Simple Notepad");
        IupSetStrAttribute($multitext, "FILENAME", $filename);
        IupSetAttribute($multitext, "DIRTY", "NO");
        IupConfigRecentUpdate($config, $filename);
    }
}

function save_check($ih)
{
    $multitext = IupGetDialogChild($ih, "MULTITEXT");

    if (IupGetInt($multitext, "DIRTY"))
    {
        switch (IupAlarm("Warning", "File not saved! Save it now?", "Yes", "No", "Cancel"))
        {
            case 1:  /* save the changes and continue */
                // save_file($multitext);
                $filename = IupGetAttribute($multitext, "FILENAME");
                if (!$filename)
                    item_saveas_action_cb($multitext);
                else
                    save_file($multitext);
                break;
            case 2:  /* ignore the changes and continue */
                break;
            case 3:  /* cancel */
                return 0;  
        }
    }
    return 1;
}


/********************************** Callbacks *****************************************/

function dropfiles_cb($ih, $filename)
{
    if (save_check($ih))
        open_file($ih, $filename);

    return IUP_DEFAULT;
}

function multitext_valuechanged_cb($multitext)
{
    IupSetAttribute($multitext, "DIRTY", "YES");
    return IUP_DEFAULT;
}

function file_menu_open_cb($ih)
{
    $item_revert = IupGetDialogChild($ih, "ITEM_REVERT");
    $item_save = IupGetDialogChild($ih, "ITEM_SAVE");
    $multitext = IupGetDialogChild($ih, "MULTITEXT");
    $filename = IupGetAttribute($multitext, "FILENAME");
    $dirty = IupGetInt($multitext, "DIRTY");

    if ($dirty)
        IupSetAttribute($item_save, "ACTIVE", "YES");
    else
        IupSetAttribute($item_save, "ACTIVE", "NO");

    if ($dirty && $filename)
        IupSetAttribute($item_revert, "ACTIVE", "YES");
    else
        IupSetAttribute($item_revert, "ACTIVE", "NO");
    return IUP_DEFAULT;
}


function edit_menu_open_cb($ih)
{
    $clipboard = IupClipboard();
    $item_paste = IupGetDialogChild($ih, "ITEM_PASTE");
    $item_cut = IupGetDialogChild($ih, "ITEM_CUT");
    $item_delete = IupGetDialogChild($ih, "ITEM_DELETE");
    $item_copy = IupGetDialogChild($ih, "ITEM_COPY");

    $multitext = IupGetDialogChild($ih,"MULTITEXT");

    if( !IupGetInt($clipboard, "TEXTAVAILABLE"))
    {
        IupSetAttribute($item_paste, "ACTIVE", "NO");
    }else{
        IupSetAttribute($item_paste, "ACTIVE", "YES");
    }

    if(!IupGetAttribute($multitext, "SELECTEDTEXT"))
    {
        IupSetAttribute($item_cut, "ACTIVE", "NO");
        IupSetAttribute($item_delete, "ACTIVE", "NO");
        IupSetAttribute($item_copy, "ACTIVE", "NO");
    }
    else 
    {
        IupSetAttribute($item_cut, "ACTIVE", "YES");
        IupSetAttribute($item_delete, "ACTIVE", "YES");
        IupSetAttribute($item_copy, "ACTIVE", "YES");
    }

    IupDestroy($clipboard);

    return IUP_DEFAULT;
}

function config_recent_cb($ih){

    global $dialogs;

    $filename = IupGetAttribute($ih,"TITLE");

    // echo $filename;
    $str = file_get_contents($filename);

    if($str){
        $multitext = $dialogs["MULTITEXT"];
        IupSetAttribute($multitext,"VALUE",$str);
        IupSetStrAttribute($multitext, "FILENAME", $filename);
    }

    // var_dump(IupGetAttribute($ih,"APP_FILENAME"));

    return IUP_DEFAULT;
}

function dialog_key_any_cb($ih,$c){
    if($c == K_cN){
        item_new_action_cb($ih);
    }else if($c == K_cO){
        item_open_action_cb($ih);
    }else if($c == K_cS){
        item_save_action_cb($ih);
    }else if($c == K_cF){
        item_find_action_cb($ih);
    }else if($c == K_cG){
        item_goto_action_cb($ih);
    }else 
    return IUP_CONTINUE;
}
function multitext_caret_cb($ih,$lin,$col){

    $lbl_statusbar = IupGetDialogChild($ih, "STATUSBAR");
    IupSetAttribute($lbl_statusbar, "TITLE", "Lin $lin, Col $col");
    return IUP_DEFAULT;
}

function item_new_action_cb($item_new)
{
  if (save_check($item_new))
    new_file($item_new);

  return IUP_DEFAULT;
}

function item_open_action_cb($item_open)
{
  $filedlg;

  if (!save_check($item_open))
    return IUP_DEFAULT;

  $filedlg = IupFileDlg();
  IupSetAttribute($filedlg, "DIALOGTYPE", "OPEN");
  IupSetAttribute($filedlg, "EXTFILTER", "Text Files|*.txt|All Files|*.*|");
  IupSetAttributeHandle($filedlg, "PARENTDIALOG", IupGetDialog($item_open));

  IupPopup($filedlg, IUP_CENTERPARENT, IUP_CENTERPARENT);
  if (IupGetInt($filedlg, "STATUS") != -1)
  {
    $filename = IupGetAttribute($filedlg, "VALUE");
    open_file($item_open, $filename);
  }

  IupDestroy($filedlg);
  return IUP_DEFAULT;
}

function item_saveas_action_cb($item_saveas)
{
    $multitext = IupGetDialogChild($item_saveas, "MULTITEXT");
    $filedlg = IupFileDlg();
    IupSetAttribute($filedlg, "DIALOGTYPE", "SAVE");
    IupSetAttribute($filedlg, "EXTFILTER", "Text Files|*.txt|All Files|*.*|");
    IupSetAttributeHandle($filedlg, "PARENTDIALOG", IupGetDialog($item_saveas));
    IupSetStrAttribute($filedlg, "FILE", IupGetAttribute($multitext, "FILENAME"));

    IupPopup($filedlg, IUP_CENTERPARENT, IUP_CENTERPARENT);

    if (IupGetInt($filedlg, "STATUS") != -1)
    {
        $filename = IupGetAttribute($filedlg, "VALUE");
        saveas_file($multitext, $filename);
    }

    IupDestroy($filedlg);
    return IUP_DEFAULT;
}

function item_save_action_cb($item_save)
{
    $multitext = IupGetDialogChild($item_save, "MULTITEXT");
    $filename = IupGetAttribute($multitext, "FILENAME");
    if (!$filename)
        item_saveas_action_cb($item_save);
    else
        save_file($multitext);
    return IUP_DEFAULT;
}

function item_revert_action_cb($item_revert)
{
    $multitext = IupGetDialogChild($item_revert, "MULTITEXT");
    $filename = IupGetAttribute($multitext, "FILENAME");
    open_file($item_revert, $filename);
    return IUP_DEFAULT;
}

function item_exit_action_cb($item_exit)
{
    global $dialogs;
    $dlg = IupGetDialog($item_exit);
    $config = $dialogs["CONFIG"];

    if (!save_check($item_exit))
        return IUP_IGNORE;  /* to abort the CLOSE_CB callback */

    IupConfigDialogClosed($config, $dlg, "MainWindow");
    IupConfigSave($config);
    IupDestroy($config);
    return IUP_CLOSE;
}

function goto_ok_action_cb($bt_ok){

    $line_count = IupGetInt($bt_ok, "TEXT_LINECOUNT");

    $txt = IupGetDialogChild($bt_ok,"LINE_TEXT");
    $line = IupGetInt($txt,"VALUE");

    if($line < 1 || $line >= $line_count){
        IupMessage("Error", "Invalid line number.");
        return IUP_DEFAULT;
    }

    IupSetAttribute(IupGetDialog($bt_ok),"STATUS", "1");

    return IUP_CLOSE;
}

function goto_cancel_action_cb($bt_cancel){

    IupSetAttribute(IupGetDialog($bt_cancel), "STATUS", "0");

    return IUP_CLOSE;
}

function item_goto_action_cb($item_goto){

    global $dialogs;

    $multitext = $dialogs["MULTITEXT"];

    $line_count = IupGetInt($multitext,"LINECOUNT");

    $lbl = IupLabel(NULL);
    IupSetAttribute($lbl,"TITLE","Line Number [1-".$line_count."]");
    $txt = IupText(NULL);
    IupSetAttribute($txt, "MASK", IUP_MASK_UINT);  /* unsigned integer numbers only */
    IupSetAttribute($txt, "NAME", "LINE_TEXT");
    IupSetAttribute($txt, "VISIBLECOLUMNS", "20");
    $bt_ok = IupButton("OK", NULL);
    $dialogs["BUTTONOK"] = $bt_ok;

    IupSetInt($bt_ok, "TEXT_LINECOUNT", $line_count);
    IupSetAttribute($bt_ok, "PADDING", "10x2");
    IupSetCallback($bt_ok, "ACTION", "goto_ok_action_cb");
    $bt_cancel = IupButton("Cancel", NULL);
    IupSetCallback($bt_cancel, "ACTION", "goto_cancel_action_cb");
    IupSetAttribute($bt_cancel, "PADDING", "10x2");

    $hbox = IupHbox(IupFill(),$bt_ok,$bt_cancel);
    // IupAppend($hbox,$bt_ok);
    // IupAppend($hbox,$bt_cancel);
    IupSetAttributes($hbox,"NORMALIZESIZE=HORIZONTAL");

    $vbox = IupVbox($lbl,$txt,$hbox);
    // IupAppend($vbox,$txt);
    // IupAppend($vbox,$hbox);
    IupSetAttribute($vbox, "MARGIN", "10x10");
    IupSetAttribute($vbox, "GAP", "5");

    $dlg = IupDialog($vbox);
    IupSetAttribute($dlg,"TITLE","Go To Line");
    IupSetAttribute($dlg, "DIALOGFRAME", "Yes");
    IupSetAttributeHandle($dlg, "DEFAULTENTER", $bt_ok);
    IupSetAttributeHandle($dlg, "DEFAULTESC", $bt_cancel);
    IupSetAttributeHandle($dlg, "PARENTDIALOG", IupGetDialog($item_goto));

    IupPopup($dlg, IUP_CENTERPARENT, IUP_CENTERPARENT);

    if(IupGetInt($dlg, "STATUS") === 1){
        $line = IupGetInt($txt,"VALUE");
        $pos = IupTextConvertLinColToPos($multitext,$line,0);
        IupSetInt($multitext, "CARETPOS", $pos);
        IupSetInt($multitext, "SCROLLTOPOS", $pos);
    }

    IupDestroy($dlg);

    return IUP_DEFAULT;
}

function find_next_action_cb($bt_next){

    global $dialogs;

    $multitext = $dialogs["MULTITEXT"];

    $str = IupGetAttribute($multitext,"VALUE");
    $find_pos = IupGetInt($multitext, "FIND_POS");

    $txt = IupGetDialogChild($bt_next,"FIND_TEXT");
    $str_to_find = IupGetAttribute($txt,"VALUE");

    $find_case = IupGetDialogChild($bt_next,"Find_CASE");
    $casesensitive = IupGetInt($find_case,"VALUE");

    $pos = str_find($str, $str_to_find, $find_pos, $casesensitive);

    if($pos === false){
        /* try again from the start */
        $find_pos = 0;

        $pos = str_find($str, $str_to_find, $find_pos, $casesensitive);

        if($pos === false){
            IupMessage("Warning", "Text not found.");
            return IUP_DEFAULT;
        }
    }

    $end_pos = $pos + strlen($str_to_find);
    IupSetInt($multitext,"FIND_POS",$end_pos);

    IupSetFocus($multitext);
    IupSetAttribute($multitext,"SELECTIONPOS",$pos.":".$end_pos);

    list($lin,$col) = IupTextConvertPosToLinCol($multitext,$pos);
    $pos = IupTextConvertLinColToPos($multitext,$lin,0); /* position at col=0, just scroll lines */
    IupSetInt($multitext, "SCROLLTOPOS", $pos);

    return IUP_DEFAULT;
}

function find_close_action_cb($bt_close){

    IupHide(IupGetDialog($bt_close));

    return IUP_DEFAULT;

}
function item_find_action_cb($item_find){

    global $dialogs;

    $dlg = $dialogs["FIND_DIALOG"];

    if(empty($dlg)){

        $multitext = $dialogs["MULTITEXT"];

        $txt = IupText(NULL);
        IupSetAttribute($txt, "NAME", "FIND_TEXT");
        IupSetAttribute($txt, "VISIBLECOLUMNS", "20");

        $find_case = IupToggle("Case sensitive",NULL);
        IupSetAttribute($find_case,"NAME","Find_CASE");

        $bt_next = IupButton("Find Next",NULL);
        IupSetAttribute($bt_next,"PADDING","10x2");
        IupSetCallback($bt_next,"ACTION","find_next_action_cb");

        $bt_close = IupButton("Close",NULL);
        IupSetAttribute($bt_close,"PADDING","10x2");
        IupSetCallback($bt_close,"ACTION","find_close_action_cb");

        $hbox = IupHbox(IupFill(),$bt_next,$bt_close);
        // IupAppend($hbox,$bt_next);
        // IupAppend($hbox,$bt_close);
        IupSetAttributes($hbox,"NORMALIZESIZE=HORIZONTAL");

        $label = IupLabel("Find What:");
        $vbox = IupVbox($label,$txt,$find_case,$hbox);
        // IupAppend($vbox,$txt);
        // IupAppend($vbox,$find_case);
        // IupAppend($vbox,$hbox);

        IupSetAttribute($vbox,"MARGIN", "10x10");
        IupSetAttribute($vbox,"GAP", "5");

        $dlg = IupDialog($vbox);
        IupSetAttribute($dlg,"TITLE", "Find");
        IupSetAttribute($dlg, "DIALOGFRAME", "Yes");
        IupSetAttributeHandle($dlg, "DEFAULTENTER", $bt_next);
        IupSetAttributeHandle($dlg, "DEFAULTESC", $bt_close);
        IupSetAttributeHandle($dlg, "PARENTDIALOG", IupGetDialog($item_find));

        $dialogs["FIND_DIALOG"] = $dlg;
    }

    IupShowXY($dlg, IUP_CENTER, IUP_CENTER);

    return IUP_DEFAULT;
}

function item_copy_action_cb($item_copy)
{
    $multitext = IupGetDialogChild($item_copy, "MULTITEXT");
    $clipboard = IupClipboard();
    IupSetAttribute($clipboard, "TEXT", IupGetAttribute($multitext, "SELECTEDTEXT"));
    IupDestroy($clipboard);
    return IUP_DEFAULT;
}

function item_paste_action_cb($item_paste) 
{
    $multitext = IupGetDialogChild($item_paste, "MULTITEXT");
    $clipboard = IupClipboard();
    IupSetAttribute($multitext, "INSERT", IupGetAttribute($clipboard, "TEXT"));
    IupDestroy($clipboard);
    return IUP_DEFAULT;
}

function item_cut_action_cb($item_cut) 
{
    $multitext = IupGetDialogChild($item_cut, "MULTITEXT");
    $clipboard = IupClipboard();
    IupSetAttribute($clipboard, "TEXT", IupGetAttribute($multitext, "SELECTEDTEXT"));
    IupSetAttribute($multitext, "SELECTEDTEXT", "");
    IupDestroy($clipboard);
    return IUP_DEFAULT;
}

function item_delete_action_cb($item_delete) 
{
    $multitext = IupGetDialogChild($item_delete, "MULTITEXT");
    IupSetAttribute($multitext, "SELECTEDTEXT", "");
    return IUP_DEFAULT;
}

function item_select_all_action_cb($item_select_all) 
{
    $multitext = IupGetDialogChild($item_select_all, "MULTITEXT");
    IupSetFocus($multitext);
    IupSetAttribute($multitext, "SELECTION", "ALL");
    return IUP_DEFAULT;
}

function item_font_action_cb($ih){
    global $dialogs;
    $config = $dialogs["CONFIG"];

    $fontdlg = IupFontDlg();

    $multitext = IupGetDialogChild($ih,"MULTITEXT");

    $font = IupGetAttribute($multitext,"FONT");

    IupSetAttribute($fontdlg,"VALUE",$font);

    IupPopup($fontdlg, IUP_CENTER,IUP_CENTER);

    if(IupGetInt($fontdlg,"STATUS") == 1){
        $font = IupGetAttribute($fontdlg,"VALUE");

        IupSetAttribute($multitext,"FONT",$font);

        IupConfigSetVariableStr($config,"MainWindow","Font",$font);
    }

    IupDestroy($fontdlg);

    return IUP_DEFAULT;
}

function item_about_action_cb($ih){
    IupMessage("About", "   Simple Notepad\n\nAutors:\n   Gustavo Lyrio\n   Antonio Scuri");
    return IUP_DEFAULT;
}

/********************************** Main *****************************************/

function main()
{
    global $dialogs;

    IupOpen();

    IupImageLibOpen();
    
    IupSetGlobal("UTF8MODE","Yes");

    $config = IupConfig();
    $dialogs["CONFIG"] = $config;
    IupSetAttribute($config,"APP_NAME","simple_notepad");
    IupConfigLoad($config);
    
    $multitext = IupText(NULL);

    $dialogs["MULTITEXT"] = $multitext;

    IupSetAttribute($multitext, "MULTILINE", "YES");
    IupSetAttribute($multitext, "EXPAND", "YES");
    IupSetAttribute($multitext, "NAME", "MULTITEXT");
    IupSetAttribute($multitext, "DIRTY", "NO");
    IupSetCallback($multitext, "CARET_CB", "multitext_caret_cb");
    IupSetCallback($multitext, "VALUECHANGED_CB", "multitext_valuechanged_cb");
    IupSetCallback($multitext, "DROPFILES_CB", "dropfiles_cb");

    $font = IupConfigGetVariableStr($config,"MainWindow","Font");
    if(!empty($font)){
        IupSetAttribute($multitext,"FONT",$font);
    }

    $lbl_statusbar = IupLabel("Lin 1, Col 1");
    IupSetAttribute($lbl_statusbar, "NAME", "STATUSBAR");  
    IupSetAttribute($lbl_statusbar, "EXPAND", "HORIZONTAL");
    IupSetAttribute($lbl_statusbar, "PADDING", "10x5");

    $item_new = IupItem("New\tCtrl+N", NULL);
    IupSetAttribute($item_new, "IMAGE", "IUP_FileNew");
    IupSetCallback($item_new, "ACTION", "item_new_action_cb");
    $btn_new = IupButton(NULL, NULL);
    IupSetAttribute($btn_new, "IMAGE", "IUP_FileNew");
    IupSetAttribute($btn_new, "FLAT", "Yes");
    IupSetCallback($btn_new, "ACTION", "item_new_action_cb");
    IupSetAttribute($btn_new, "TIP", "New (Ctrl+N)");
    IupSetAttribute($btn_new, "CANFOCUS", "No");

    $item_open = IupItem("&Open...\tCtrl+O", NULL);
    IupSetAttribute($item_open, "IMAGE", "IUP_FileOpen");
    IupSetCallback($item_open, "ACTION", "item_open_action_cb");
    $btn_open = IupButton(NULL, NULL);
    IupSetAttribute($btn_open, "IMAGE", "IUP_FileOpen");
    IupSetAttribute($btn_open, "FLAT", "Yes");
    IupSetCallback($btn_open, "ACTION", "item_open_action_cb");
    IupSetAttribute($btn_open, "TIP", "Open (Ctrl+O)");
    IupSetAttribute($btn_open, "CANFOCUS", "No");

    $item_save = IupItem("Save\tCtrl+S", NULL);
    IupSetAttribute($item_save, "NAME", "ITEM_SAVE");
    IupSetAttribute($item_save, "IMAGE", "IUP_FileSave");
    IupSetCallback($item_save, "ACTION", "item_save_action_cb");
    $btn_save = IupButton(NULL, NULL);
    IupSetAttribute($btn_save, "IMAGE", "IUP_FileSave");
    IupSetAttribute($btn_save, "FLAT", "Yes");
    IupSetCallback($btn_save, "ACTION", "item_save_action_cb");
    IupSetAttribute($btn_save, "TIP", "Save (Ctrl+S)");
    IupSetAttribute($btn_save, "CANFOCUS", "No");

    $item_saveas = IupItem("Save &As...", NULL);
    IupSetAttribute($item_saveas, "NAME", "ITEM_SAVEAS");
    IupSetCallback($item_saveas, "ACTION", "item_saveas_action_cb");

    $item_revert = IupItem("Revert", NULL);
    IupSetAttribute($item_revert, "NAME", "ITEM_REVERT");
    IupSetCallback($item_revert, "ACTION", "item_revert_action_cb");
    
    $item_exit = IupItem("E&xit", NULL);
    IupSetCallback($item_exit, "ACTION", "item_exit_action_cb");

    $item_find = IupItem("&Find...\tCtrl+F", NULL);
    IupSetAttribute($item_find, "IMAGE", "IUP_EditFind");
    IupSetCallback($item_find, "ACTION", "item_find_action_cb");
    $btn_find = IupButton(NULL, NULL);
    IupSetAttribute($btn_find, "IMAGE", "IUP_EditFind");
    IupSetAttribute($btn_find, "FLAT", "Yes");
    IupSetCallback($btn_find, "ACTION", "item_find_action_cb");
    IupSetAttribute($btn_find, "TIP", "Find (Ctrl+F)");
    IupSetAttribute($btn_find, "CANFOCUS", "No");

    $item_cut = IupItem("Cut\tCtrl+X", NULL);
    IupSetAttribute($item_cut, "NAME", "ITEM_CUT");
    IupSetAttribute($item_cut, "IMAGE", "IUP_EditCut");
    IupSetCallback($item_cut, "ACTION", "item_cut_action_cb");
    $item_copy = IupItem("Copy\tCtrl+C", NULL);
    IupSetAttribute($item_copy, "NAME", "ITEM_COPY");  
    IupSetAttribute($item_copy, "IMAGE", "IUP_EditCopy");
    IupSetCallback($item_copy, "ACTION", "item_copy_action_cb");
    $item_paste = IupItem("Paste\tCtrl+V", NULL);
    IupSetAttribute($item_paste, "NAME", "ITEM_PASTE");
    IupSetAttribute($item_paste, "IMAGE", "IUP_EditPaste");
    IupSetCallback($item_paste, "ACTION", "item_paste_action_cb");
    $item_delete = IupItem("Delete\tDel", NULL);
    IupSetAttribute($item_delete, "IMAGE", "IUP_EditErase");  
    IupSetAttribute($item_delete, "NAME", "ITEM_DELETE");
    IupSetCallback($item_delete, "ACTION", "item_delete_action_cb");
    $item_select_all = IupItem("Select All\tCtrl+A", NULL);
    IupSetCallback($item_select_all, "ACTION", "item_select_all_action_cb");

    $btn_cut = IupButton(NULL, NULL);
    IupSetAttribute($btn_cut, "IMAGE", "IUP_EditCut");
    IupSetAttribute($btn_cut, "FLAT", "Yes");
    IupSetCallback($btn_cut, "ACTION", "item_cut_action_cb");
    IupSetAttribute($btn_cut, "TIP", "Cut (Ctrl+X)");
    IupSetAttribute($btn_cut, "CANFOCUS", "No");
    $btn_copy = IupButton(NULL, NULL);
    IupSetAttribute($btn_copy, "IMAGE", "IUP_EditCopy");
    IupSetAttribute($btn_copy, "FLAT", "Yes");
    IupSetCallback($btn_copy, "ACTION", "item_copy_action_cb");
    IupSetAttribute($btn_copy, "TIP", "Copy (Ctrl+C)");
    IupSetAttribute($btn_copy, "CANFOCUS", "No");
    $btn_paste = IupButton(NULL, NULL);
    IupSetAttribute($btn_paste, "IMAGE", "IUP_EditPaste");
    IupSetAttribute($btn_paste, "FLAT", "Yes");
    IupSetCallback($btn_paste, "ACTION", "item_paste_action_cb");
    IupSetAttribute($btn_paste, "TIP", "Paste (Ctrl+V)");
    IupSetAttribute($btn_paste, "CANFOCUS", "No");

    $toolbar_hb = IupHbox(
      $btn_new,
      $btn_open,
      $btn_save,
      IupSetAttributes(IupLabel(NULL), "SEPARATOR=VERTICAL"),
      $btn_cut,
      $btn_copy,
      $btn_paste,
      IupSetAttributes(IupLabel(NULL), "SEPARATOR=VERTICAL"),
      $btn_find
    );
    IupSetAttribute($toolbar_hb, "MARGIN", "5x5");
    IupSetAttribute($toolbar_hb, "GAP", "2");

    $item_goto = IupItem("&Go To...\tCtrl+G", NULL);
    IupSetCallback($item_goto, "ACTION", "item_goto_action_cb");
    $item_font = IupItem("&Font...", NULL);
    IupSetCallback($item_font, "ACTION", "item_font_action_cb");
    $item_about = IupItem("&About...", NULL);
    IupSetCallback($item_about, "ACTION", "item_about_action_cb");

    $recent_menu = IupMenu();

    $file_menu = IupMenu(
        $item_new,
        $item_open,
        $item_save,
        $item_saveas,
        $item_revert,
        IupSeparator(),
        IupSubmenu("Recent &Files", $recent_menu),
        $item_exit
    );
    
    $edit_menu = IupMenu(
        $item_cut,
        $item_copy,
        $item_paste,
        $item_delete,
        IupSeparator(),
        $item_find,
        $item_goto,
        IupSeparator(),
        $item_select_all
    );

    $format_menu = IupMenu(
        $item_font
    );

    $help_menu = IupMenu(
        $item_about
    );

    IupSetCallback($file_menu, "OPEN_CB", "file_menu_open_cb");
    IupSetCallback($edit_menu, "OPEN_CB", "edit_menu_open_cb");

    $sub_menu_file = IupSubmenu("&File", $file_menu);
    $sub_menu_edit = IupSubmenu("&Edit", $edit_menu);
    $sub_menu_format = IupSubmenu("F&ormat", $format_menu);
    $sub_menu_help = IupSubmenu("&Help", $help_menu);

    $menu = IupMenu(
        $sub_menu_file,
        $sub_menu_edit,
        $sub_menu_format,
        $sub_menu_help
    );

    $vbox = IupVbox(
        $toolbar_hb,
        $multitext,
        $lbl_statusbar
    );

    $dlg = IupDialog($vbox);

    $dialogs["DIALOG"] = $dlg;

    IupSetAttributeHandle($dlg, "MENU", $menu);

    IupSetAttribute($dlg, "TITLE", "Simple Notepad");

    IupSetAttribute($dlg, "SIZE", "HALFxHALF");

    IupSetCallback($dlg, "CLOSE_CB", "item_exit_action_cb");

    IupSetCallback($dlg, "DROPFILES_CB", "dropfiles_cb");


    IupSetCallback($edit_menu, "OPEN_CB", "edit_menu_open_cb");

    /* parent for pre-defined dialogs in closed functions (IupMessage) */
    IupSetAttributeHandle(NULL, "PARENTDIALOG", $dlg);

    // IupSetCallback($dlg, "K_cO", "item_open_action_cb");
    // IupSetCallback($dlg, "K_cS", "item_saveas_action_cb");
    // IupSetCallback($dlg, "K_cF", "item_find_action_cb");
    // IupSetCallback($dlg, "K_cG", "item_goto_action_cb");
    IupSetCallback($dlg, "K_ANY", "dialog_key_any_cb");

    IupConfigRecentInit($config, $recent_menu, "config_recent_cb", 10);

    IupConfigDialogShow($config, $dlg, "MainWindow");

    /* initialize the current file */
    new_file($dlg);

    IupMainLoop();

    IupClose();

}

main();
