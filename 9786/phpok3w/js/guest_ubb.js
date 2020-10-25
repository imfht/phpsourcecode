if(navigator.appName == "Microsoft Internet Explorer")
{
    isIE=true;
}
else
{
    isIE=false;
}


function AddText(NewCode) 
{
    var obj = document.getElementById("Content");
	if(document.all){
        insertAtCaret(obj, NewCode);
        obj.focus();
    }
    else
    {
        obj.value += NewCode;
        obj.focus();
    }
}

function insertAtCaret (textEl, text){
    if (textEl.createTextRange && textEl.caretPos)
    {
        var caretPos = textEl.caretPos;
        caretPos.text += caretPos.text.charAt(caretPos.text.length - 2) == ' ' ? text + ' ' : text;
    }
    else if(textEl)
    {
        textEl.value += text;
    }
    else
    {
        textEl.value = text;
    }
}

function ubbFormat(what) 
{
    if (document.selection && document.selection.type == "Text")
    {
        var range = document.selection.createRange();
        range.text = "["+what+"]" + range.text + "[/"+what+"]";
    }
    else
    {
        txt=window.prompt("ÇëÊäÈëÄÚÈÝ","");     
        if (txt!=null) {           
                AddTxt="["+what+"]"+txt;
                AddText(AddTxt);
                AddText("[/"+what+"]");
        }       
    } 
}

function ubbInsert(what) 
{
    if (document.selection && document.selection.type == "Text")
    {
        var range = document.selection.createRange();
        range.text = "["+what+"]" + range.text + "[/"+what+"]";
    }
    else
    {
        txt=window.prompt("ÇëÊäÈëÄÚÈÝ","");     
        if (txt!=null) {           
                AddTxt="["+what+"]"+txt;
                AddText(AddTxt);
                AddText("[/"+what+"]");
        }       
    } 
}

function chsize(size) {
    if (document.selection && document.selection.type == "Text")
    {
        var range = document.selection.createRange();
        range.text = "[size=" + size + "]" + range.text + "[/size]";
    }
    else
    {                       
        txt=window.prompt("ÇëÊäÈëÄÚÈÝ",""); 
        if (txt!=null)
        {             
            AddTxt="[size="+size+"]"+txt;
            AddText(AddTxt);
            AddText("[/size]");
        }        
    }
}

function chfont(font) {
    if (document.selection && document.selection.type == "Text") {
    var range = document.selection.createRange();
    range.text = "[face=" + font + "]" + range.text + "[/face]";
    }
    else
    {                  
        txt=window.prompt("ÇëÊäÈëÄÚÈÝ","");
        if (txt!=null)
        {             
            AddTxt="[face=" + font + "]"+txt;
            AddText(AddTxt);
            AddText("[/face]");
        }        
    }  
}

function chcolor(color) {
    if (document.selection && document.selection.type == "Text") {
    var range = document.selection.createRange();
    range.text = "[color=" + color + "]" + range.text + "[/color]";
    }
    else
    {  
    txt=window.prompt("ÇëÊäÈëÄÚÈÝ","");
        if(txt!=null) {
            AddTxt="[color=" + color + "]"+txt;
            AddText(AddTxt);
            AddText("[/color]");
        }
    }
}