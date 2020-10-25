var oEditor = window.parent.InnerDialogLoaded();
var FCK = oEditor.FCK;
var FCKLang = oEditor.FCKLang;
var FCKConfig = oEditor.FCKConfig;

// Get the selected flash embed (if available).
var oFakeImage = FCK.Selection.GetSelectedElement();
var oEmbed;

if (oFakeImage) {
    if (oFakeImage.tagName == 'IMG' && oFakeImage.getAttribute('_fckflash'))
        oEmbed = FCK.GetRealElement(oFakeImage);
    else
        oFakeImage = null;
}

window.onload = function () {
    // Translate the dialog box texts.
    oEditor.FCKLanguageManager.TranslatePage(document);

    // Load the selected element information (if any).
    LoadSelection();

    window.parent.SetAutoSize(true);

    // Activate the "OK" button.
    window.parent.SetOkButton(true);
}

function LoadSelection() {
    if (!oEmbed) return;

    GetE('txtUrl').value = GetAttribute(oEmbed, 'src', '');
    GetE('txtWidth').value = GetAttribute(oEmbed, 'width', '');
    GetE('txtHeight').value = GetAttribute(oEmbed, 'height', '');

    // Get Advances Attributes
    GetE('chkAutoPlay').checked = GetAttribute(oEmbed, 'play', 'true') == 'true';
    GetE('chkLoop').checked = GetAttribute(oEmbed, 'loop', 'true') == 'true';
    GetE('chkMenu').checked = GetAttribute(oEmbed, 'menu', 'true') == 'true';
    GetE('cmbScale').value = GetAttribute(oEmbed, 'scale', '').toLowerCase();

}

//#### The OK button was hit.
function Ok() {
    if (GetE('txtUrl').value.length == 0) {
        GetE('txtUrl').focus();

        alert(oEditor.FCKLang.DlgAlertUrl);

        return false;
    }

    if (!oEmbed) {
        oEmbed = FCK.EditorDocument.createElement('EMBED');
        oFakeImage = null;
    }
    UpdateEmbed(oEmbed);

    if (!oFakeImage) {
        oFakeImage = oEditor.FCKDocumentProcessor_CreateFakeImage('FCK__Flash', oEmbed);
        oFakeImage.setAttribute('_fckflash', 'true', 0);
        oFakeImage = FCK.InsertElementAndGetIt(oFakeImage);
    }
    else
        oEditor.FCKUndo.SaveUndoStep();

    oEditor.FCKFlashProcessor.RefreshView(oFakeImage, oEmbed);

    return true;
}

function UpdateEmbed(e) {
    SetAttribute(e, 'type', 'application/x-shockwave-flash');
    SetAttribute(e, 'pluginspage', 'http://www.macromedia.com/go/getflashplayer');

    SetAttribute(e, 'src', GetE('txtUrl').value);
    SetAttribute(e, "width", GetE('txtWidth').value);
    SetAttribute(e, "height", GetE('txtHeight').value);

    // Advances Attributes

    SetAttribute(e, 'scale', GetE('cmbScale').value);

    SetAttribute(e, 'play', GetE('chkAutoPlay').checked ? 'true' : 'false');
    SetAttribute(e, 'loop', GetE('chkLoop').checked ? 'true' : 'false');
    SetAttribute(e, 'menu', GetE('chkMenu').checked ? 'true' : 'false');

}

function SetUrl(url, width, height) {
    GetE('txtUrl').value = url;

    if (width)
        GetE('txtWidth').value = width;

    if (height)
        GetE('txtHeight').value = height;

}