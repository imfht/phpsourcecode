/* FCKeditor & Angel(4ngel) Mod */
var FCK_STATUS_NOTLOADED = window.parent.FCK_STATUS_NOTLOADED = 0;
var FCK_STATUS_ACTIVE = window.parent.FCK_STATUS_ACTIVE = 1;
var FCK_STATUS_COMPLETE = window.parent.FCK_STATUS_COMPLETE = 2;
var FCK_TRISTATE_OFF = window.parent.FCK_TRISTATE_OFF = 0;
var FCK_TRISTATE_ON = window.parent.FCK_TRISTATE_ON = 1;
var FCK_TRISTATE_DISABLED = window.parent.FCK_TRISTATE_DISABLED = -1;
var FCK_UNKNOWN = window.parent.FCK_UNKNOWN = -9;
var FCK_TOOLBARITEM_ONLYICON = window.parent.FCK_TOOLBARITEM_ONLYICON = 0;
var FCK_TOOLBARITEM_ONLYTEXT = window.parent.FCK_TOOLBARITEM_ONLYTEXT = 1;
var FCK_EDITMODE_WYSIWYG = window.parent.FCK_EDITMODE_WYSIWYG = 0;
var FCK_EDITMODE_SOURCE = window.parent.FCK_EDITMODE_SOURCE = 1;
var FCK_IMAGES_PATH = 'images/';
var FCK_SPACER_PATH = 'images/spacer.gif';
var CTRL = 1000;
var SHIFT = 2000;
var ALT = 4000;
String.prototype.Contains = function (A) {
    return (this.indexOf(A) > -1);
};
String.prototype.Equals = function () {
    var A = arguments;
    if (A.length == 1 && A[0].pop) A = A[0];
    for (var i = 0; i < A.length; i++) {
        if (this == A[i]) return true;
    }
    ;
    return false;
};
String.prototype.IEquals = function () {
    var A = this.toUpperCase();
    var B = arguments;
    if (B.length == 1 && B[0].pop) B = B[0];
    for (var i = 0; i < B.length; i++) {
        if (A == B[i].toUpperCase()) return true;
    }
    ;
    return false;
};
String.prototype.ReplaceAll = function (A, B) {
    var C = this;
    for (var i = 0; i < A.length; i++) {
        C = C.replace(A[i], B[i]);
    }
    ;
    return C;
};
Array.prototype.AddItem = function (A) {
    var i = this.length;
    this[i] = A;
    return i;
};
Array.prototype.IndexOf = function (A) {
    for (var i = 0; i < this.length; i++) {
        if (this[i] == A) return i;
    }
    ;
    return-1;
};
String.prototype.StartsWith = function (A) {
    return (this.substr(0, A.length) == A);
};
String.prototype.EndsWith = function (A, B) {
    var C = this.length;
    var D = A.length;
    if (D > C) return false;
    if (B) {
        var E = new RegExp(A + '$', 'i');
        return E.test(this);
    } else return (D == 0 || this.substr(C - D, D) == A);
};
String.prototype.Remove = function (A, B) {
    var s = '';
    if (A > 0) s = this.substring(0, A);
    if (A + B < this.length) s += this.substring(A + B, this.length);
    return s;
};
String.prototype.Trim = function () {
    return this.replace(/(^[ \t\n\r]*)|([ \t\n\r]*$)/g, '');
};
String.prototype.LTrim = function () {
    return this.replace(/^[ \t\n\r]*/g, '');
};
String.prototype.RTrim = function () {
    return this.replace(/[ \t\n\r]*$/g, '');
};
String.prototype.ReplaceNewLineChars = function (A) {
    return this.replace(/\n/g, A);
}
var FCKIECleanup = function (A) {
    if (A._FCKCleanupObj) this.Items = A._FCKCleanupObj.Items; else {
        this.Items = [];
        A._FCKCleanupObj = this;
        FCKTools.AddEventListenerEx(A, 'unload', FCKIECleanup_Cleanup);
    }
};
FCKIECleanup.prototype.AddItem = function (A, B) {
    this.Items.push([A, B]);
};
function FCKIECleanup_Cleanup() {
    if (!this._FCKCleanupObj) return;
    var A = this._FCKCleanupObj.Items;
    while (A.length > 0) {
        var B = A.pop();
        if (B) B[1].call(B[0]);
    }
    ;
    this._FCKCleanupObj = null;
    if (CollectGarbage) CollectGarbage();
}
var s = navigator.userAgent.toLowerCase();
var FCKBrowserInfo = {IsIE: s.Contains('msie'), IsIE7: s.Contains('msie 7'), IsGecko: s.Contains('gecko/'), IsSafari: s.Contains('safari'), IsOpera: s.Contains('opera'), IsMac: s.Contains('macintosh')};
(function (A) {
    A.IsGeckoLike = (A.IsGecko || A.IsSafari || A.IsOpera);
    if (A.IsGecko) {
        var B = s.match(/gecko\/(\d+)/)[1];
    } else A.IsGecko10 = false;
})(FCKBrowserInfo);
var FCKURLParams = {};
(function () {
    var A = document.location.search.substr(1).split('&');
    for (var i = 0; i < A.length; i++) {
        var B = A[i].split('=');
        var C = decodeURIComponent(B[0]);
        var D = decodeURIComponent(B[1]);
        FCKURLParams[C] = D;
    }
})();
var FCKEvents = function (A) {
    this.Owner = A;
    this._RegisteredEvents = {};
};
FCKEvents.prototype.AttachEvent = function (A, B) {
    var C;
    if (!(C = this._RegisteredEvents[A])) this._RegisteredEvents[A] = [B]; else C.push(B);
};
FCKEvents.prototype.FireEvent = function (A, B) {
    var C = true;
    var D = this._RegisteredEvents[A];
    if (D) {
        for (var i = 0; i < D.length; i++) C = (D[i](this.Owner, B) && C);
    }
    ;
    return C;
};
var FCK = {Name: FCKURLParams['InstanceName'], Status: 0, EditMode: 0, Toolbar: null, HasFocus: false, GetLinkedFieldValue: function () {
    return this.LinkedField.value;
}, GetParentForm: function () {
    return this.LinkedField.form;
}, StartupValue: '', IsDirty: function () {
    if (this.EditMode == 1) return (this.StartupValue != this.EditingArea.Textarea.value); else return (this.StartupValue != this.EditorDocument.body.innerHTML);
}, ResetIsDirty: function () {
    if (this.EditMode == 1) this.StartupValue = this.EditingArea.Textarea.value; else if (this.EditorDocument.body) this.StartupValue = this.EditorDocument.body.innerHTML;
}, StartEditor: function () {
    this.TempBaseTag = FCKConfig.BaseHref.length > 0 ? '<base href="' + FCKConfig.BaseHref + '" _fcktemp="true"></base>' : '';
    this.EditingArea = new FCKEditingArea(document.getElementById('xEditingArea'));
    FCKListsLib.Setup();
    this.SetHTML(this.GetLinkedFieldValue(), true);
}, Focus: function () {
    FCK.EditingArea.Focus();
}, SetStatus: function (A) {
    this.Status = A;
    if (A == 1) {
        FCKFocusManager.AddWindow(window, true);
        if (FCKBrowserInfo.IsIE) FCKFocusManager.AddWindow(window.frameElement, true);
        if (FCKConfig.StartupFocus) FCK.Focus();
    }
    ;
    this.Events.FireEvent('OnStatusChange', A);
}, FixBody: function () {
    var A = this.EditorDocument;
    if (!A) return;
    var B = A.body;
    if (!B) return;
    FCKDomTools.TrimNode(B);
    var C = B.firstChild;
    var D;
    while (C) {
        var E = false;
        switch (C.nodeType) {
            case 1:
                if (!FCKListsLib.BlockElements[C.nodeName.toLowerCase()]) E = true;
                break;
            case 3:
                if (D || C.nodeValue.Trim().length > 0) E = true;
        }
        ;
        if (E) {
            var F = C.parentNode;
            if (!D) D = F.insertBefore(A.createElement('p'), C);
            D.appendChild(F.removeChild(C));
            C = D.nextSibling;
        } else {
            if (D) {
                FCKDomTools.TrimNode(D);
                D = null;
            }
            ;
            C = C.nextSibling;
        }
    }
    ;
    if (D) FCKDomTools.TrimNode(D);
}, GetXHTML: function (A) {
    if (FCK.EditMode == 1) return FCK.EditingArea.Textarea.value;
    this.FixBody();
    var B;
    var C = FCK.EditorDocument;
    if (!C) return null;
    B = FCKXHtml.GetXHTML(C.body, false, A);
    if (FCKConfig.IgnoreEmptyParagraphValue && FCKRegexLib.EmptyOutParagraph.test(B)) B = '';
    B = FCK.ProtectEventsRestore(B);
    if (FCKBrowserInfo.IsIE) B = B.replace(FCKRegexLib.ToReplace, '$1');
    return FCKConfig.ProtectedSource.Revert(B);
}, UpdateLinkedField: function () {
    FCK.LinkedField.value = FCK.GetXHTML(FCKConfig.FormatOutput);
    FCK.Events.FireEvent('OnAfterLinkedFieldUpdate');
}, RegisteredDoubleClickHandlers: {}, OnDoubleClick: function (A) {
    var B = FCK.RegisteredDoubleClickHandlers[A.tagName];
    if (B) B(A);
}, RegisterDoubleClickHandler: function (A, B) {
    FCK.RegisteredDoubleClickHandlers[B.toUpperCase()] = A;
}, OnAfterSetHTML: function () {
    FCKDocumentProcessor.Process(FCK.EditorDocument);
    FCKUndo.SaveUndoStep();
    FCK.Events.FireEvent('OnSelectionChange');
    FCK.Events.FireEvent('OnAfterSetHTML');
}, ProtectUrls: function (A) {
    A = A.replace(FCKRegexLib.ProtectUrlsA, '$& _fcksavedurl=$1');
    A = A.replace(FCKRegexLib.ProtectUrlsImg, '$& _fcksavedurl=$1');
    return A;
}, ProtectEvents: function (A) {
    return A.replace(FCKRegexLib.TagsWithEvent, _FCK_ProtectEvents_ReplaceTags);
}, ProtectEventsRestore: function (A) {
    return A.replace(FCKRegexLib.ProtectedEvents, _FCK_ProtectEvents_RestoreEvents);
}, ProtectTags: function (A) {
    var B = FCKConfig.ProtectedTags;
    if (FCKBrowserInfo.IsIE) B += B.length > 0 ? '|ABBR|XML' : 'ABBR|XML';
    var C;
    if (B.length > 0) {
        C = new RegExp('<(' + B + ')(?!\w|:)', 'gi');
        A = A.replace(C, '<FCK:$1');
        C = new RegExp('<\/(' + B + ')>', 'gi');
        A = A.replace(C, '<\/FCK:$1>');
    }
    ;
    B = 'META';
    if (FCKBrowserInfo.IsIE) B += '|HR';
    C = new RegExp('<((' + B + ')(?=\\s|>|/)[\\s\\S]*?)/?>', 'gi');
    A = A.replace(C, '<FCK:$1 />');
    return A;
}, SetHTML: function (A, B) {
    this.EditingArea.Mode = FCK.EditMode;
    if (FCK.EditMode == 0) {
        A = FCKConfig.ProtectedSource.Protect(A);
        A = A.replace(FCKRegexLib.InvalidSelfCloseTags, '$1></$2>');
        A = FCK.ProtectEvents(A);
        A = FCK.ProtectUrls(A);
        A = FCK.ProtectTags(A);
        if (FCKBrowserInfo.IsGecko) {
            A = A.replace(FCKRegexLib.StrongOpener, '<b$1');
            A = A.replace(FCKRegexLib.StrongCloser, '<\/b>');
            A = A.replace(FCKRegexLib.EmOpener, '<i$1');
            A = A.replace(FCKRegexLib.EmCloser, '<\/i>');
        }
        ;
        this._ForceResetIsDirty = (B === true);
        var C = '';
        C = FCKConfig.DocType + '<html dir="' + FCKConfig.ContentLangDirection + '"';
        if (FCKBrowserInfo.IsIE && !FCKRegexLib.Html4DocType.test(FCKConfig.DocType)) C += ' style="overflow-y: scroll"';
        C += '><head><title></title>' + _FCK_GetEditorAreaStyleTags() + '<link href="' + FCKConfig.FullBasePath + 'css/fck_internal.css" rel="stylesheet" type="text/css" _fcktemp="true" />';
        if (FCKBrowserInfo.IsIE) C += FCK._GetBehaviorsStyle();
        C += FCK.TempBaseTag;
        C += '</head><body>';
        if (FCKBrowserInfo.IsGecko && (A.length == 0 || FCKRegexLib.EmptyParagraph.test(A))) C += GECKO_BOGUS; else C += A;
        C += '</body></html>';
        this.EditingArea.OnLoad = _FCK_EditingArea_OnLoad;
        this.EditingArea.Start(C);
    } else {
        FCK.EditorWindow = null;
        FCK.EditorDocument = null;
        this.EditingArea.OnLoad = null;
        this.EditingArea.Start(A);
        if (B) this.ResetIsDirty();
        this.EditingArea.Textarea.focus();
        FCK.Events.FireEvent('OnAfterSetHTML');
    }
    ;
    if (FCKBrowserInfo.IsGecko) window.onresize();
}, HasFocus: false, RedirectNamedCommands: {}, ExecuteNamedCommand: function (A, B, C) {
    FCKUndo.SaveUndoStep();
    FCK.Focus();
    FCK.EditorDocument.execCommand(A, false, B);
    FCK.Events.FireEvent('OnSelectionChange');
    FCKUndo.SaveUndoStep();
}, GetNamedCommandState: function (A) {
    try {
        if (!FCK.EditorDocument.queryCommandEnabled(A)) return -1; else return FCK.EditorDocument.queryCommandState(A) ? 1 : 0;
    } catch (e) {
        return 0;
    }
}, GetNamedCommandValue: function (A) {
    var B = '';
    var C = FCK.GetNamedCommandState(A);
    if (C == -1) return null;
    try {
        B = this.EditorDocument.queryCommandValue(A);
    } catch (e) {
    }
    ;
    return B ? B : '';
}, PasteFromWord: function () {
    FCKDialog.OpenDialog('FCKDialog_Paste', FCKLang.PasteFromWord, 'dialog/fck_paste.html', 400, 330, 'Word');
}, Preview: function () {
    var A = FCKConfig.ScreenWidth * 0.8;
    var B = FCKConfig.ScreenHeight * 0.7;
    var C = (FCKConfig.ScreenWidth - A) / 2;
    var D = window.open('', null, 'toolbar=yes,location=no,status=yes,menubar=yes,scrollbars=yes,resizable=yes,width=' + A + ',height=' + B + ',left=' + C);
    var E;
    E = FCKConfig.DocType + '<html dir="' + FCKConfig.ContentLangDirection + '"><head>' + FCK.TempBaseTag + '<title>' + FCKLang.Preview + '</title>' + _FCK_GetEditorAreaStyleTags() + '</head><body>' + FCK.GetXHTML() + '</body></html>';
    D.document.write(E);
    D.document.close();
}, SwitchEditMode: function (A) {
    var B = (FCK.EditMode == 0);
    var C = FCK.IsDirty();
    var D;
    if (B) {
        if (!A && FCKBrowserInfo.IsIE) FCKUndo.SaveUndoStep();
        D = FCK.GetXHTML(FCKConfig.FormatSource);
        if (D == null) return false;
    } else D = this.EditingArea.Textarea.value;
    FCK.EditMode = B ? 1 : 0;
    FCK.SetHTML(D, !C);
    FCK.Focus();
    FCKTools.RunFunction(FCK.ToolbarSet.RefreshModeState, FCK.ToolbarSet);
    return true;
}, CreateElement: function (A) {
    var e = FCK.EditorDocument.createElement(A);
    return FCK.InsertElementAndGetIt(e);
}, InsertElementAndGetIt: function (e) {
    e.setAttribute('FCKTempLabel', 'true');
    this.InsertElement(e);
    var A = FCK.EditorDocument.getElementsByTagName(e.tagName);
    for (var i = 0; i < A.length; i++) {
        if (A[i].getAttribute('FCKTempLabel')) {
            A[i].removeAttribute('FCKTempLabel');
            return A[i];
        }
    }
    ;
    return null;
}};
FCK.Events = new FCKEvents(FCK);
FCK.GetHTML = FCK.GetXHTML;
function _FCK_ProtectEvents_ReplaceTags(A) {
    return A.replace(FCKRegexLib.EventAttributes, _FCK_ProtectEvents_ReplaceEvents);
};
function _FCK_ProtectEvents_ReplaceEvents(A, B) {
    return ' ' + B + '_fckprotectedatt="' + A.ReplaceAll([/&/g, /'/g, /"/g, /=/g, /</g, />/g, /\r/g, /\n/g], ['&apos;', '&#39;', '&quot;', '&#61;', '&lt;', '&gt;', '&#10;', '&#13;']) + '"';
};
function _FCK_ProtectEvents_RestoreEvents(A, B) {
    return B.ReplaceAll([/&#39;/g, /&quot;/g, /&#61;/g, /&lt;/g, /&gt;/g, /&#10;/g, /&#13;/g, /&apos;/g], ["'", '"', '=', '<', '>', '\r', '\n', '&']);
};
function _FCK_EditingArea_OnLoad() {
    FCK.EditorWindow = FCK.EditingArea.Window;
    FCK.EditorDocument = FCK.EditingArea.Document;
    FCK.InitializeBehaviors();
    if (FCK._ForceResetIsDirty) FCK.ResetIsDirty();
    if (FCKBrowserInfo.IsIE && FCK.HasFocus) FCK.EditorDocument.body.setActive();
    FCK.OnAfterSetHTML();
    if (FCK.Status != 0) return;
    FCK.SetStatus(1);
};
function _FCK_GetEditorAreaStyleTags() {
    var A = '';
    var B = FCKConfig.EditorAreaCSS;
    for (var i = 0; i < B.length; i++) A += '<link href="' + B[i] + '" rel="stylesheet" type="text/css" />';
    return A;
};
(function () {
    var A = window.parent.document;
    var B = A.getElementById(FCK.Name);
    var i = 0;
    while (B || i == 0) {
        if (B && B.tagName.toLowerCase().Equals('input', 'textarea')) {
            FCK.LinkedField = B;
            break;
        }
        ;
        B = A.getElementsByName(FCK.Name)[i++];
    }
})();
var FCKTempBin = {Elements: [], AddElement: function (A) {
    var B = this.Elements.length;
    this.Elements[B] = A;
    return B;
}, RemoveElement: function (A) {
    var e = this.Elements[A];
    this.Elements[A] = null;
    return e;
}, Reset: function () {
    var i = 0;
    while (i < this.Elements.length) this.Elements[i++] = null;
    this.Elements.length = 0;
}};
var FCKFocusManager = FCK.FocusManager = {IsLocked: false, AddWindow: function (A, B) {
    var C;
    if (FCKBrowserInfo.IsIE) C = A.nodeType == 1 ? A : A.frameElement ? A.frameElement : A.document; else C = A.document;
    FCKTools.AddEventListener(C, 'blur', FCKFocusManager_Win_OnBlur);
    FCKTools.AddEventListener(C, 'focus', B ? FCKFocusManager_Win_OnFocus_Area : FCKFocusManager_Win_OnFocus);
}, RemoveWindow: function (A) {
    if (FCKBrowserInfo.IsIE) oTarget = A.nodeType == 1 ? A : A.frameElement ? A.frameElement : A.document; else oTarget = A.document;
    FCKTools.RemoveEventListener(oTarget, 'blur', FCKFocusManager_Win_OnBlur);
    FCKTools.RemoveEventListener(oTarget, 'focus', FCKFocusManager_Win_OnFocus_Area);
    FCKTools.RemoveEventListener(oTarget, 'focus', FCKFocusManager_Win_OnFocus);
}, Lock: function () {
    this.IsLocked = true;
}, Unlock: function () {
    if (this._HasPendingBlur) FCKFocusManager._Timer = window.setTimeout(FCKFocusManager_FireOnBlur, 100);
    this.IsLocked = false;
}, _ResetTimer: function () {
    this._HasPendingBlur = false;
    if (this._Timer) {
        window.clearTimeout(this._Timer);
        delete this._Timer;
    }
}};
function FCKFocusManager_Win_OnBlur() {
    if (typeof(FCK) != 'undefined' && FCK.HasFocus) {
        FCKFocusManager._ResetTimer();
        FCKFocusManager._Timer = window.setTimeout(FCKFocusManager_FireOnBlur, 100);
    }
};
function FCKFocusManager_FireOnBlur() {
    if (FCKFocusManager.IsLocked) FCKFocusManager._HasPendingBlur = true; else {
        FCK.HasFocus = false;
        FCK.Events.FireEvent("OnBlur");
    }
};
function FCKFocusManager_Win_OnFocus_Area() {
    FCK.Focus();
    FCKFocusManager_Win_OnFocus();
};
function FCKFocusManager_Win_OnFocus() {
    FCKFocusManager._ResetTimer();
    if (!FCK.HasFocus && !FCKFocusManager.IsLocked) {
        FCK.HasFocus = true;
        FCK.Events.FireEvent("OnFocus");
    }
};
FCK.Description = "FCKeditor for Internet Explorer 5.5+";
FCK._GetBehaviorsStyle = function () {
    if (!FCK._BehaviorsStyle) {
        var A = FCKConfig.FullBasePath;
        var B;
        B = '<style type="text/css" _fcktemp="true">';
        B += 'INPUT,TEXTAREA,SELECT';
        B += ' { behavior: url(' + A + 'css/behaviors/disablehandles.htc) ; }';
        B += '</style>';
        FCK._BehaviorsStyle = B;
    }
    ;
    return FCK._BehaviorsStyle;
};
function Doc_OnMouseUp() {
    if (FCK.EditorWindow.event.srcElement.tagName == 'HTML') {
        FCK.Focus();
        FCK.EditorWindow.event.cancelBubble = true;
        FCK.EditorWindow.event.returnValue = false;
    }
};
function Doc_OnPaste() {
    return (FCK.Status == 2 && FCK.Events.FireEvent("OnPaste"));
};
function Doc_OnKeyDown() {
    if (FCK.EditorWindow) {
        var e = FCK.EditorWindow.event;
        if (!(e.keyCode >= 16 && e.keyCode <= 18)) Doc_OnKeyDownUndo();
    }
    ;
    return true;
};
function Doc_OnKeyDownUndo() {
    if (!FCKUndo.Typing) {
        FCKUndo.SaveUndoStep();
        FCKUndo.Typing = true;
        FCK.Events.FireEvent("OnSelectionChange");
    }
    ;
    FCKUndo.TypesCount++;
    if (FCKUndo.TypesCount > FCKUndo.MaxTypes) {
        FCKUndo.TypesCount = 0;
        FCKUndo.SaveUndoStep();
    }
};
function Doc_OnDblClick() {
    FCK.OnDoubleClick(FCK.EditorWindow.event.srcElement);
    FCK.EditorWindow.event.cancelBubble = true;
};
function Doc_OnSelectionChange() {
    FCK.Events.FireEvent("OnSelectionChange");
};
FCK.InitializeBehaviors = function (A) {
    this.EditorDocument.attachEvent('onmouseup', Doc_OnMouseUp);
    this.EditorDocument.body.attachEvent('onpaste', Doc_OnPaste);
    if (FCKConfig.TabSpaces > 0) {
        window.FCKTabHTML = '';
        for (i = 0; i < FCKConfig.TabSpaces; i++) window.FCKTabHTML += "&nbsp;";
    }
    ;
    this.EditorDocument.attachEvent("onkeydown", Doc_OnKeyDown);
    this.EditorDocument.attachEvent("ondblclick", Doc_OnDblClick);
    this.EditorDocument.attachEvent("onselectionchange", Doc_OnSelectionChange);
};
FCK.InsertHtml = function (A) {
    A = FCKConfig.ProtectedSource.Protect(A);
    A = FCK.ProtectEvents(A);
    A = FCK.ProtectUrls(A);
    A = FCK.ProtectTags(A);
    FCK.EditorWindow.focus();
    FCKUndo.SaveUndoStep();
    var B = FCK.EditorDocument.selection;
    if (B.type.toLowerCase() == 'control') B.clear();
    A = '<span id="__fakeFCKRemove__">&nbsp;</span>' + A;
    B.createRange().pasteHTML(A);
    FCK.EditorDocument.getElementById('__fakeFCKRemove__').removeNode(true);
    FCKDocumentProcessor.Process(FCK.EditorDocument);
};
FCK.SetInnerHtml = function (A) {
    var B = FCK.EditorDocument;
    B.body.innerHTML = '<div id="__fakeFCKRemove__">&nbsp;</div>' + A;
    B.getElementById('__fakeFCKRemove__').removeNode(true);
};
function FCK_PreloadImages() {
    var A = new FCKImagePreloader();
    A.AddImages(FCKConfig.PreloadImages);
    A.AddImages(FCKConfig.SkinPath + 'fck_strip.gif');
    A.OnComplete = LoadToolbarSetup;
    A.Start();
};
function FCK_Cleanup() {
    this.EditorWindow = null;
    this.EditorDocument = null;
};
FCK.Paste = function () {
    if (FCK._PasteIsRunning) return true;
    if (FCKConfig.ForcePasteAsPlainText) {
        FCK.PasteAsPlainText();
        return false;
    }
    ;
    var A = FCK._CheckIsPastingEnabled(true);
    if (A === false) FCKTools.RunFunction(FCKDialog.OpenDialog, FCKDialog, ['FCKDialog_Paste', FCKLang.Paste, 'dialog/fck_paste.html', 400, 330, 'Security']); else {
        FCK._PasteIsRunning = true;
        FCK.ExecuteNamedCommand('Paste');
        delete FCK._PasteIsRunning;
    }
    ;
    return false;
};
FCK.PasteAsPlainText = function () {
    if (!FCK._CheckIsPastingEnabled()) {
        FCKDialog.OpenDialog('FCKDialog_Paste', FCKLang.PasteAsText, 'dialog/fck_paste.html', 400, 330, 'PlainText');
        return;
    }
    ;
    var A = clipboardData.getData("Text");
    if (A && A.length > 0) {
        A = FCKTools.HTMLEncode(A).replace(/\n/g, '<BR>');
        this.InsertHtml(A);
    }
};
FCK._CheckIsPastingEnabled = function (A) {
    FCK._PasteIsEnabled = false;
    document.body.attachEvent('onpaste', FCK_CheckPasting_Listener);
    var B = FCK.GetClipboardHTML();
    document.body.detachEvent('onpaste', FCK_CheckPasting_Listener);
    if (FCK._PasteIsEnabled) {
        if (!A) B = true;
    } else B = false;
    delete FCK._PasteIsEnabled;
    return B;
};
function FCK_CheckPasting_Listener() {
    FCK._PasteIsEnabled = true;
};
FCK.InsertElement = function (A) {
    FCK.InsertHtml(A.outerHTML);
};
FCK.GetClipboardHTML = function () {
    var A = document.getElementById('___FCKHiddenDiv');
    if (!A) {
        A = document.createElement('DIV');
        A.id = '___FCKHiddenDiv';
        var B = A.style;
        B.position = 'absolute';
        B.visibility = B.overflow = 'hidden';
        B.width = B.height = 1;
        document.body.appendChild(A);
    }
    ;
    A.innerHTML = '';
    var C = document.body.createTextRange();
    C.moveToElementText(A);
    C.execCommand('Paste');
    var D = A.innerHTML;
    A.innerHTML = '';
    return D;
};
FCK.AttachToOnSelectionChange = function (A) {
    this.Events.AttachEvent('OnSelectionChange', A);
};
FCK.CreateLink = function (A) {
    FCK.ExecuteNamedCommand('Unlink');
    if (A.length > 0) {
        var B = 'javascript:void(0);/*' + (new Date().getTime()) + '*/';
        FCK.ExecuteNamedCommand('CreateLink', B);
        var C = this.EditorDocument.links;
        for (i = 0; i < C.length; i++) {
            var D = C[i];
            if (D.href == B) {
                var E = D.innerHTML;
                D.href = A;
                D.innerHTML = E;
                return D;
            }
        }
    }
    ;
    return null;
}
var FCKConfig = FCK.Config = {};
if (document.location.protocol == 'file:') {
    FCKConfig.BasePath = decodeURIComponent(document.location.pathname.substr(1));
    FCKConfig.BasePath = FCKConfig.BasePath.replace(/\\/gi, '/');
    FCKConfig.BasePath = 'file://' + FCKConfig.BasePath.substring(0, FCKConfig.BasePath.lastIndexOf('/') + 1);
    FCKConfig.FullBasePath = FCKConfig.BasePath;
} else {
    FCKConfig.BasePath = document.location.pathname.substring(0, document.location.pathname.lastIndexOf('/') + 1);
    FCKConfig.FullBasePath = document.location.protocol + '//' + document.location.host + FCKConfig.BasePath;
}
;
FCKConfig.EditorPath = FCKConfig.BasePath.replace(/editor\/$/, '');
try {
    FCKConfig.ScreenWidth = screen.width;
    FCKConfig.ScreenHeight = screen.height;
} catch (e) {
    FCKConfig.ScreenWidth = 800;
    FCKConfig.ScreenHeight = 600;
}
;
FCKConfig.ProcessHiddenField = function () {
    this.PageConfig = {};
    var A = window.parent.document.getElementById(FCK.Name + '___Config');
    if (!A) return;
    var B = A.value.split('&');
    for (var i = 0; i < B.length; i++) {
        if (B[i].length == 0) continue;
        var C = B[i].split('=');
        var D = decodeURIComponent(C[0]);
        var E = decodeURIComponent(C[1]);
        if (E.toLowerCase() == "true") this.PageConfig[D] = true; else if (E.toLowerCase() == "false") this.PageConfig[D] = false; else if (E.length > 0 && !isNaN(E)) this.PageConfig[D] = parseInt(E, 10); else this.PageConfig[D] = E;
    }
};
function FCKConfig_LoadPageConfig() {
    var A = FCKConfig.PageConfig;
    for (var B in A) FCKConfig[B] = A[B];
};
function FCKConfig_PreProcess() {
    var A = FCKConfig;
    if (!A.PluginsPath.EndsWith('/')) A.PluginsPath += '/';
    if (typeof(A.EditorAreaCSS) == 'string') A.EditorAreaCSS = [A.EditorAreaCSS];
    var B = A.ToolbarComboPreviewCSS;
    if (!B || B.length == 0) A.ToolbarComboPreviewCSS = A.EditorAreaCSS; else if (typeof(B) == 'string') A.ToolbarComboPreviewCSS = [B];
};
FCKConfig.ToolbarSets = {};
FCKConfig.Plugins = {};
FCKConfig.Plugins.Items = [];
FCKConfig.Plugins.Add = function (A, B, C) {
    FCKConfig.Plugins.Items.AddItem([A, B, C]);
};
FCKConfig.ProtectedSource = {};
FCKConfig.ProtectedSource.RegexEntries = [/<!--[\s\S]*?-->/g, /<script[\s\S]*?<\/script>/gi, /<noscript[\s\S]*?<\/noscript>/gi, /<object[\s\S]+?<\/object>/gi];
FCKConfig.ProtectedSource.Add = function (A) {
    this.RegexEntries.AddItem(A);
};
FCKConfig.ProtectedSource.Protect = function (A) {
    function _Replace(protectedSource) {
        var B = FCKTempBin.AddElement(protectedSource);
        return '<!--{PS..' + B + '}-->';
    };
    for (var i = 0; i < this.RegexEntries.length; i++) {
        A = A.replace(this.RegexEntries[i], _Replace);
    }
    ;
    return A;
};
FCKConfig.ProtectedSource.Revert = function (A, B) {
    function _Replace(m, opener, index) {
        var C = B ? FCKTempBin.RemoveElement(index) : FCKTempBin.Elements[index];
        return FCKConfig.ProtectedSource.Revert(C, B);
    };
    return A.replace(/(<|&lt;)!--\{PS..(\d+)\}--(>|&gt;)/g, _Replace);
}
var FCKDomTools = {MoveChildren: function (A, B) {
    if (A == B) return;
    var C;
    while ((C = A.firstChild)) B.appendChild(A.removeChild(C));
}, TrimNode: function (A, B) {
    this.LTrimNode(A);
    this.RTrimNode(A, B);
}, LTrimNode: function (A) {
    var B;
    while ((B = A.firstChild)) {
        if (B.nodeType == 3) {
            var C = B.nodeValue.LTrim();
            var D = B.nodeValue.length;
            if (C.length == 0) {
                A.removeChild(B);
                continue;
            } else if (C.length < D) {
                B.splitText(D - C.length);
                A.removeChild(A.firstChild);
            }
        }
        ;
        break;
    }
}, RTrimNode: function (A, B) {
    var C;
    while ((C = A.lastChild)) {
        switch (C.nodeType) {
            case 1:
                if (C.nodeName.toUpperCase() == 'BR' && (B || C.getAttribute('type', 2) == '_moz')) {
                    C.parentNode.removeChild(C);
                    continue;
                }
                ;
                break;
            case 3:
                var D = C.nodeValue.RTrim();
                var E = C.nodeValue.length;
                if (D.length == 0) {
                    C.parentNode.removeChild(C);
                    continue;
                } else if (D.length < E) {
                    C.splitText(D.length);
                    A.lastChild.parentNode.removeChild(A.lastChild);
                }
        }
        ;
        break;
    }
}, RemoveNode: function (A, B) {
    if (B) {
        var C;
        while ((C = A.firstChild)) A.parentNode.insertBefore(A.removeChild(C), A);
    }
    ;
    return A.parentNode.removeChild(A);
}, GetFirstChild: function (A, B) {
    if (typeof (B) == 'string') B = [B];
    var C = A.firstChild;
    while (C) {
        if (C.nodeType == 1 && C.tagName.Equals.apply(C.tagName, B)) return C;
        C = C.nextSibling;
    }
    ;
    return null;
}, GetLastChild: function (A, B) {
    if (typeof (B) == 'string') B = [B];
    var C = A.lastChild;
    while (C) {
        if (C.nodeType == 1 && (!B || C.tagName.Equals(B))) return C;
        C = C.previousSibling;
    }
    ;
    return null;
}, GetPreviousSourceElement: function (A, B, C, D) {
    if (!A) return null;
    if (C && A.nodeType == 1 && A.nodeName.IEquals(C)) return null;
    if (A.previousSibling) A = A.previousSibling; else return this.GetPreviousSourceElement(A.parentNode, B, C, D);
    while (A) {
        if (A.nodeType == 1) {
            if (C && A.nodeName.IEquals(C)) break;
            if (!D || !A.nodeName.IEquals(D)) return A;
        } else if (B && A.nodeType == 3 && A.nodeValue.RTrim().length > 0) break;
        if (A.lastChild) A = A.lastChild; else return this.GetPreviousSourceElement(A, B, C, D);
    }
    ;
    return null;
}, GetNextSourceElement: function (A, B, C, D) {
    if (!A) return null;
    if (A.nextSibling) A = A.nextSibling; else return this.GetNextSourceElement(A.parentNode, B, C, D);
    while (A) {
        if (A.nodeType == 1) {
            if (C && A.nodeName.IEquals(C)) break;
            if (!D || !A.nodeName.IEquals(D)) return A;
        } else if (B && A.nodeType == 3 && A.nodeValue.RTrim().length > 0) break;
        if (A.firstChild) A = A.firstChild; else return this.GetNextSourceElement(A, B, C, D);
    }
    ;
    return null;
}, InsertAfterNode: function (A, B) {
    return A.parentNode.insertBefore(B, A.nextSibling);
}, GetParents: function (A) {
    var B = [];
    while (A) {
        B.splice(0, 0, A);
        A = A.parentNode;
    }
    ;
    return B;
}, GetIndexOf: function (A) {
    var B = A.parentNode ? A.parentNode.firstChild : null;
    var C = -1;
    while (B) {
        C++;
        if (B == A) return C;
        B = B.nextSibling;
    }
    ;
    return-1;
}};
var GECKO_BOGUS = '<br type="_moz">';
var FCKTools = {};
FCKTools.CreateBogusBR = function (A) {
    var B = A.createElement('br');
    B.setAttribute('type', '_moz');
    return B;
};
FCKTools.AppendStyleSheet = function (A, B) {
    if (typeof(B) == 'string') return this._AppendStyleSheet(A, B); else {
        var C = [];
        for (var i = 0; i < B.length; i++) C.push(this._AppendStyleSheet(A, B[i]));
        return C;
    }
};
FCKTools.GetElementDocument = function (A) {
    return A.ownerDocument || A.document;
};
FCKTools.GetElementWindow = function (A) {
    return this.GetDocumentWindow(this.GetElementDocument(A));
};
FCKTools.GetDocumentWindow = function (A) {
    if (FCKBrowserInfo.IsSafari && !A.parentWindow) this.FixDocumentParentWindow(window.top);
    return A.parentWindow || A.defaultView;
};
FCKTools.FixDocumentParentWindow = function (A) {
    A.document.parentWindow = A;
    for (var i = 0; i < A.frames.length; i++) FCKTools.FixDocumentParentWindow(A.frames[i]);
};
FCKTools.HTMLEncode = function (A) {
    if (!A) return '';
    A = A.replace(/&/g, '&amp;');
    A = A.replace(/</g, '&lt;');
    A = A.replace(/>/g, '&gt;');
    return A;
};
FCKTools.HTMLDecode = function (A) {
    if (!A) return '';
    A = A.replace(/&gt;/g, '>');
    A = A.replace(/&lt;/g, '<');
    A = A.replace(/&amp;/g, '&');
    return A;
};
FCKTools.RunFunction = function (A, B, C, D) {
    if (A) this.SetTimeout(A, 0, B, C, D);
};
FCKTools.SetTimeout = function (A, B, C, D, E) {
    return (E || window).setTimeout(function () {
        if (D) A.apply(C, [].concat(D)); else A.apply(C);
    }, B);
};
FCKTools.SetInterval = function (A, B, C, D, E) {
    return (E || window).setInterval(function () {
        A.apply(C, D || []);
    }, B);
};
FCKTools.ConvertStyleSizeToHtml = function (A) {
    return A.EndsWith('%') ? A : parseInt(A, 10);
};
FCKTools.ConvertHtmlSizeToStyle = function (A) {
    return A.EndsWith('%') ? A : (A + 'px');
};
FCKTools.CreateEventListener = function (A, B) {
    var f = function () {
        var C = [];
        for (var i = 0; i < arguments.length; i++) C.push(arguments[i]);
        A.apply(this, C.concat(B));
    };
    return f;
};
FCKTools.IsStrictMode = function (A) {
    return ('CSS1Compat' == (A.compatMode || 'CSS1Compat'));
};
FCKTools.CloneObject = function (A) {
    var B = function () {
    };
    B.prototype = A;
    return new B;
};
FCKTools.CancelEvent = function (e) {
    return false;
};
FCKTools._AppendStyleSheet = function (A, B) {
    return A.createStyleSheet(B).owningElement;
};
FCKTools.CreateXmlObject = function (A) {
    var B;
    switch (A) {
        case 'XmlHttp':
            B = ['MSXML2.XmlHttp', 'Microsoft.XmlHttp'];
            break;
        case 'DOMDocument':
            B = ['MSXML2.DOMDocument', 'Microsoft.XmlDom'];
            break;
    }
    ;
    for (var i = 0; i < 2; i++) {
        try {
            return new ActiveXObject(B[i]);
        } catch (e) {
        }
    }
    ;
    if (FCKLang.NoActiveX) {
        alert(FCKLang.NoActiveX);
        FCKLang.NoActiveX = null;
    }
    ;
    return null;
};
FCKTools.DisableSelection = function (A) {
    A.unselectable = 'on';
    var e, i = 0;
    while ((e = A.all[i++])) {
        switch (e.tagName) {
            case 'IFRAME':
            case 'TEXTAREA':
            case 'INPUT':
            case 'SELECT':
                break;
            default:
                e.unselectable = 'on';
        }
    }
};
FCKTools.GetScrollPosition = function (A) {
    var B = A.document;
    var C = { X: B.documentElement.scrollLeft, Y: B.documentElement.scrollTop };
    if (C.X > 0 || C.Y > 0) return C;
    return { X: B.body.scrollLeft, Y: B.body.scrollTop };
};
FCKTools.AddEventListener = function (A, B, C) {
    A.attachEvent('on' + B, C);
};
FCKTools.RemoveEventListener = function (A, B, C) {
    A.detachEvent('on' + B, C);
};
FCKTools.AddEventListenerEx = function (A, B, C, D) {
    var o = {};
    o.Source = A;
    o.Params = D || [];
    o.Listener = function (ev) {
        return C.apply(o.Source, [ev].concat(o.Params));
    };
    if (FCK.IECleanup) FCK.IECleanup.AddItem(null, function () {
        o.Source = null;
        o.Params = null;
    });
    A.attachEvent('on' + B, o.Listener);
    A = null;
    D = null;
};
FCKTools.GetViewPaneSize = function (A) {
    var B;
    var C = A.document.documentElement;
    if (C && C.clientWidth) B = C; else B = top.document.body;
    if (B) return { Width: B.clientWidth, Height: B.clientHeight }; else return { Width: 0, Height: 0 };
};
FCKTools.AppendElement = function (A, B) {
    return A.appendChild(this.GetElementDocument(A).createElement(B));
};
FCKTools.ToLowerCase = function (A) {
    return A.toLowerCase();
}
var FCKeditorAPI;
function InitializeAPI() {
    var A = window.parent;
    if (!(FCKeditorAPI = A.FCKeditorAPI)) {
        var B = 'var FCKeditorAPI = {Version : "2.4.3",VersionBuild : "15657",__Instances : new Object(),GetInstance : function( name ){return this.__Instances[ name ];},_FormSubmit : function(){for ( var name in FCKeditorAPI.__Instances ){var oEditor = FCKeditorAPI.__Instances[ name ] ;if ( oEditor.GetParentForm && oEditor.GetParentForm() == this )oEditor.UpdateLinkedField() ;}this._FCKOriginalSubmit() ;},_FunctionQueue	: {Functions : new Array(),IsRunning : false,Add : function( f ){this.Functions.push( f );if ( !this.IsRunning )this.StartNext();},StartNext : function(){var aQueue = this.Functions ;if ( aQueue.length > 0 ){this.IsRunning = true;aQueue[0].call();}else this.IsRunning = false;},Remove : function( f ){var aQueue = this.Functions;var i = 0, fFunc;while( (fFunc = aQueue[ i ]) ){if ( fFunc == f )aQueue.splice( i,1 );i++ ;}this.StartNext();}}}';
        if (A.execScript) A.execScript(B, 'JavaScript'); else {
            if (FCKBrowserInfo.IsGecko10) {
                eval.call(A, B);
            } else if (FCKBrowserInfo.IsSafari) {
                var C = A.document;
                var D = C.createElement('script');
                D.appendChild(C.createTextNode(B));
                C.documentElement.appendChild(D);
            } else A.eval(B);
        }
        ;
        FCKeditorAPI = A.FCKeditorAPI;
    }
    ;
    FCKeditorAPI.__Instances[FCK.Name] = FCK;
};
function _AttachFormSubmitToAPI() {
    var A = FCK.GetParentForm();
    if (A) {
        FCKTools.AddEventListener(A, 'submit', FCK.UpdateLinkedField);
        if (!A._FCKOriginalSubmit && (typeof(A.submit) == 'function' || (!A.submit.tagName && !A.submit.length))) {
            A._FCKOriginalSubmit = A.submit;
            A.submit = FCKeditorAPI._FormSubmit;
        }
    }
};
function FCKeditorAPI_Cleanup() {
    delete FCKeditorAPI.__Instances[FCK.Name];
};
FCKTools.AddEventListener(window, 'unload', FCKeditorAPI_Cleanup);
var FCKImagePreloader = function () {
    this._Images = [];
};
FCKImagePreloader.prototype = {AddImages: function (A) {
    if (typeof(A) == 'string') A = A.split(';');
    this._Images = this._Images.concat(A);
}, Start: function () {
    var A = this._Images;
    this._PreloadCount = A.length;
    for (var i = 0; i < A.length; i++) {
        var B = document.createElement('img');
        B.onload = B.onerror = _FCKImagePreloader_OnImage;
        B._FCKImagePreloader = this;
        B.src = A[i];
        _FCKImagePreloader_ImageCache.push(B);
    }
}};
var _FCKImagePreloader_ImageCache = [];
function _FCKImagePreloader_OnImage() {
    var A = this._FCKImagePreloader;
    if ((--A._PreloadCount) == 0 && A.OnComplete) A.OnComplete();
    this._FCKImagePreloader = null;
}
var FCKRegexLib = {AposEntity: /&apos;/gi, ObjectElements: /^(?:IMG|TABLE|TR|TD|TH|INPUT|SELECT|TEXTAREA|HR|OBJECT|A|UL|OL|LI)$/i, NamedCommands: /^(?:Cut|Copy|Paste|Unlink|Undo|Redo|Bold|Italic|Underline|StrikeThrough|JustifyLeft|JustifyCenter|JustifyRight|Outdent|Indent|InsertOrderedList|InsertUnorderedList|InsertHorizontalRule)$/i, BodyContents: /([\s\S]*\<body[^\>]*\>)([\s\S]*)(\<\/body\>[\s\S]*)/i, ToReplace: /___fcktoreplace:([\w]+)/ig, MetaHttpEquiv: /http-equiv\s*=\s*["']?([^"' ]+)/i, HasBaseTag: /<base /i, HtmlOpener: /<html\s?[^>]*>/i, HeadOpener: /<head\s?[^>]*>/i, HeadCloser: /<\/head\s*>/i, FCK_Class: /(\s*FCK__[A-Za-z]*\s*)/, ElementName: /(^[a-z_:][\w.\-:]*\w$)|(^[a-z_]$)/, SpaceNoClose: /\/>/g, EmptyParagraph: /^<(p|div|address|h\d|center)(?=[ >])[^>]*>\s*(<\/\1>)?$/, EmptyOutParagraph: /^<(p|div|address|h\d|center)(?=[ >])[^>]*>(?:\s*|&nbsp;)(<\/\1>)?$/, TagBody: /></, StrongOpener: /<STRONG([ \>])/gi, StrongCloser: /<\/STRONG>/gi, EmOpener: /<EM([ \>])/gi, EmCloser: /<\/EM>/gi, GeckoEntitiesMarker: /#\?-\:/g, ProtectUrlsImg: /<img(?=\s).*?\ssrc=((?:(?:\s*)("|').*?\2)|(?:[^"'][^ >]+))/gi, ProtectUrlsA: /<a(?=\s).*?\shref=((?:(?:\s*)("|').*?\2)|(?:[^"'][^ >]+))/gi, Html4DocType: /HTML 4\.0 Transitional/i, DocTypeTag: /<!DOCTYPE[^>]*>/i, TagsWithEvent: /<[^\>]+ on\w+[\s\r\n]*=[\s\r\n]*?('|")[\s\S]+?\>/g, EventAttributes: /\s(on\w+)[\s\r\n]*=[\s\r\n]*?('|")([\s\S]*?)\2/g, ProtectedEvents: /\s\w+_fckprotectedatt="([^"]+)"/g, StyleProperties: /\S+\s*:/g, InvalidSelfCloseTags: /(<(?!base|meta|link|hr|br|param|img|area|input)([a-zA-Z0-9:]+)[^>]*)\/>/gi};
var FCKListsLib = {BlockElements: { address: 1, blockquote: 1, center: 1, div: 1, dl: 1, fieldset: 1, form: 1, h1: 1, h2: 1, h3: 1, h4: 1, h5: 1, h6: 1, hr: 1, marquee: 1, noscript: 1, ol: 1, p: 1, pre: 1, script: 1, table: 1, ul: 1 }, NonEmptyBlockElements: { p: 1, div: 1, h1: 1, h2: 1, h3: 1, h4: 1, h5: 1, h6: 1, address: 1, pre: 1, ol: 1, ul: 1, li: 1, td: 1, th: 1 }, InlineChildReqElements: { abbr: 1, acronym: 1, b: 1, bdo: 1, big: 1, cite: 1, code: 1, del: 1, dfn: 1, em: 1, font: 1, i: 1, ins: 1, label: 1, kbd: 1, q: 1, samp: 1, small: 1, span: 1, strong: 1, sub: 1, sup: 1, tt: 1, u: 1, 'var': 1 }, EmptyElements: { base: 1, meta: 1, link: 1, hr: 1, br: 1, param: 1, img: 1, area: 1, input: 1 }, PathBlockElements: { address: 1, blockquote: 1, dl: 1, h1: 1, h2: 1, h3: 1, h4: 1, h5: 1, h6: 1, p: 1, pre: 1, ol: 1, ul: 1, li: 1, dt: 1, de: 1 }, PathBlockLimitElements: { body: 1, td: 1, th: 1, caption: 1, form: 1 }, Setup: function () {
    this.PathBlockLimitElements.div = 1;
}};
var FCKLanguageManager = FCK.Language = {AvailableLanguages: {en: 'English', 'zh-cn': 'Chinese Simplified'}, GetActiveLanguage: function () {
    if (FCKConfig.AutoDetectLanguage) {
        var A;
        if (navigator.userLanguage) A = navigator.userLanguage.toLowerCase(); else if (navigator.language) A = navigator.language.toLowerCase(); else {
            return FCKConfig.DefaultLanguage;
        }
        ;
        if (A.length >= 5) {
            A = A.substr(0, 5);
            if (this.AvailableLanguages[A]) return A;
        }
        ;
        if (A.length >= 2) {
            A = A.substr(0, 2);
            if (this.AvailableLanguages[A]) return A;
        }
    }
    ;
    return this.DefaultLanguage;
}, TranslateElements: function (A, B, C, D) {
    var e = A.getElementsByTagName(B);
    var E, s;
    for (var i = 0; i < e.length; i++) {
        if ((E = e[i].getAttribute('fckLang'))) {
            if ((s = FCKLang[E])) {
                if (D) s = FCKTools.HTMLEncode(s);
                eval('e[i].' + C + ' = s');
            }
        }
    }
}, TranslatePage: function (A) {
    this.TranslateElements(A, 'INPUT', 'value');
    this.TranslateElements(A, 'SPAN', 'innerHTML');
    this.TranslateElements(A, 'LABEL', 'innerHTML');
    this.TranslateElements(A, 'OPTION', 'innerHTML', true);
}, Initialize: function () {
    if (this.AvailableLanguages[FCKConfig.DefaultLanguage]) this.DefaultLanguage = FCKConfig.DefaultLanguage; else this.DefaultLanguage = 'en';
    this.ActiveLanguage = {};
    this.ActiveLanguage.Code = this.GetActiveLanguage();
    this.ActiveLanguage.Name = this.AvailableLanguages[this.ActiveLanguage.Code];
}};
var FCKXHtmlEntities = {};
FCKXHtmlEntities.Initialize = function () {
    if (FCKXHtmlEntities.Entities) return;
    var A = '';
    var B, e;
    if (FCKConfig.ProcessHTMLEntities) {
        FCKXHtmlEntities.Entities = {' ': 'nbsp', '¡': 'iexcl', '¢': 'cent', '£': 'pound', '¤': 'curren', '¥': 'yen', '¦': 'brvbar', '§': 'sect', '¨': 'uml', '©': 'copy', 'ª': 'ordf', '«': 'laquo', '¬': 'not', '­': 'shy', '®': 'reg', '¯': 'macr', '°': 'deg', '±': 'plusmn', '²': 'sup2', '³': 'sup3', '´': 'acute', 'µ': 'micro', '¶': 'para', '·': 'middot', '¸': 'cedil', '¹': 'sup1', 'º': 'ordm', '»': 'raquo', '¼': 'frac14', '½': 'frac12', '¾': 'frac34', '¿': 'iquest', '×': 'times', '÷': 'divide', 'ƒ': 'fnof', '•': 'bull', '…': 'hellip', '′': 'prime', '″': 'Prime', '‾': 'oline', '⁄': 'frasl', '℘': 'weierp', 'ℑ': 'image', 'ℜ': 'real', '™': 'trade', 'ℵ': 'alefsym', '←': 'larr', '↑': 'uarr', '→': 'rarr', '↓': 'darr', '↔': 'harr', '↵': 'crarr', '⇐': 'lArr', '⇑': 'uArr', '⇒': 'rArr', '⇓': 'dArr', '⇔': 'hArr', '∀': 'forall', '∂': 'part', '∃': 'exist', '∅': 'empty', '∇': 'nabla', '∈': 'isin', '∉': 'notin', '∋': 'ni', '∏': 'prod', '∑': 'sum', '−': 'minus', '∗': 'lowast', '√': 'radic', '∝': 'prop', '∞': 'infin', '∠': 'ang', '∧': 'and', '∨': 'or', '∩': 'cap', '∪': 'cup', '∫': 'int', '∴': 'there4', '∼': 'sim', '≅': 'cong', '≈': 'asymp', '≠': 'ne', '≡': 'equiv', '≤': 'le', '≥': 'ge', '⊂': 'sub', '⊃': 'sup', '⊄': 'nsub', '⊆': 'sube', '⊇': 'supe', '⊕': 'oplus', '⊗': 'otimes', '⊥': 'perp', '⋅': 'sdot', '◊': 'loz', '♠': 'spades', '♣': 'clubs', '♥': 'hearts', '♦': 'diams', '"': 'quot', 'ˆ': 'circ', '˜': 'tilde', ' ': 'ensp', ' ': 'emsp', ' ': 'thinsp', '‌': 'zwnj', '‍': 'zwj', '‎': 'lrm', '‏': 'rlm', '–': 'ndash', '—': 'mdash', '‘': 'lsquo', '’': 'rsquo', '‚': 'sbquo', '“': 'ldquo', '”': 'rdquo', '„': 'bdquo', '†': 'dagger', '‡': 'Dagger', '‰': 'permil', '‹': 'lsaquo', '›': 'rsaquo', '€': 'euro'};
        for (e in FCKXHtmlEntities.Entities) A += e;
        if (FCKConfig.IncludeLatinEntities) {
            B = {'À': 'Agrave', 'Á': 'Aacute', 'Â': 'Acirc', 'Ã': 'Atilde', 'Ä': 'Auml', 'Å': 'Aring', 'Æ': 'AElig', 'Ç': 'Ccedil', 'È': 'Egrave', 'É': 'Eacute', 'Ê': 'Ecirc', 'Ë': 'Euml', 'Ì': 'Igrave', 'Í': 'Iacute', 'Î': 'Icirc', 'Ï': 'Iuml', 'Ð': 'ETH', 'Ñ': 'Ntilde', 'Ò': 'Ograve', 'Ó': 'Oacute', 'Ô': 'Ocirc', 'Õ': 'Otilde', 'Ö': 'Ouml', 'Ø': 'Oslash', 'Ù': 'Ugrave', 'Ú': 'Uacute', 'Û': 'Ucirc', 'Ü': 'Uuml', 'Ý': 'Yacute', 'Þ': 'THORN', 'ß': 'szlig', 'à': 'agrave', 'á': 'aacute', 'â': 'acirc', 'ã': 'atilde', 'ä': 'auml', 'å': 'aring', 'æ': 'aelig', 'ç': 'ccedil', 'è': 'egrave', 'é': 'eacute', 'ê': 'ecirc', 'ë': 'euml', 'ì': 'igrave', 'í': 'iacute', 'î': 'icirc', 'ï': 'iuml', 'ð': 'eth', 'ñ': 'ntilde', 'ò': 'ograve', 'ó': 'oacute', 'ô': 'ocirc', 'õ': 'otilde', 'ö': 'ouml', 'ø': 'oslash', 'ù': 'ugrave', 'ú': 'uacute', 'û': 'ucirc', 'ü': 'uuml', 'ý': 'yacute', 'þ': 'thorn', 'ÿ': 'yuml', 'Œ': 'OElig', 'œ': 'oelig', 'Š': 'Scaron', 'š': 'scaron', 'Ÿ': 'Yuml'};
            for (e in B) {
                FCKXHtmlEntities.Entities[e] = B[e];
                A += e;
            }
            ;
            B = null;
        }
        ;
        if (FCKConfig.IncludeGreekEntities) {
            B = {'Α': 'Alpha', 'Β': 'Beta', 'Γ': 'Gamma', 'Δ': 'Delta', 'Ε': 'Epsilon', 'Ζ': 'Zeta', 'Η': 'Eta', 'Θ': 'Theta', 'Ι': 'Iota', 'Κ': 'Kappa', 'Λ': 'Lambda', 'Μ': 'Mu', 'Ν': 'Nu', 'Ξ': 'Xi', 'Ο': 'Omicron', 'Π': 'Pi', 'Ρ': 'Rho', 'Σ': 'Sigma', 'Τ': 'Tau', 'Υ': 'Upsilon', 'Φ': 'Phi', 'Χ': 'Chi', 'Ψ': 'Psi', 'Ω': 'Omega', 'α': 'alpha', 'β': 'beta', 'γ': 'gamma', 'δ': 'delta', 'ε': 'epsilon', 'ζ': 'zeta', 'η': 'eta', 'θ': 'theta', 'ι': 'iota', 'κ': 'kappa', 'λ': 'lambda', 'μ': 'mu', 'ν': 'nu', 'ξ': 'xi', 'ο': 'omicron', 'π': 'pi', 'ρ': 'rho', 'ς': 'sigmaf', 'σ': 'sigma', 'τ': 'tau', 'υ': 'upsilon', 'φ': 'phi', 'χ': 'chi', 'ψ': 'psi', 'ω': 'omega'};
            for (e in B) {
                FCKXHtmlEntities.Entities[e] = B[e];
                A += e;
            }
            ;
            B = null;
        }
    } else {
        FCKXHtmlEntities.Entities = {};
        A = ' ';
    }
    ;
    var C = '[' + A + ']';
    if (FCKConfig.ProcessNumericEntities) C = '[^ -~]|' + C;
    var D = FCKConfig.AdditionalNumericEntities;
    if (D && D.length > 0) C += '|' + FCKConfig.AdditionalNumericEntities;
    FCKXHtmlEntities.EntitiesRegex = new RegExp(C, 'g');
}
var FCKXHtml = {};
FCKXHtml.CurrentJobNum = 0;
FCKXHtml.GetXHTML = function (A, B, C) {
    FCKXHtmlEntities.Initialize();
    this._NbspEntity = (FCKConfig.ProcessHTMLEntities ? 'nbsp' : '#160');
    var D = FCK.IsDirty();
    this._CreateNode = FCKConfig.ForceStrongEm ? FCKXHtml_CreateNode_StrongEm : FCKXHtml_CreateNode_Normal;
    FCKXHtml.SpecialBlocks = [];
    this.XML = FCKTools.CreateXmlObject('DOMDocument');
    this.MainNode = this.XML.appendChild(this.XML.createElement('xhtml'));
    FCKXHtml.CurrentJobNum++;
    if (B) this._AppendNode(this.MainNode, A); else this._AppendChildNodes(this.MainNode, A, false);
    var E = this._GetMainXmlString();
    this.XML = null;
    E = E.substr(7, E.length - 15).Trim();
    if (FCKBrowserInfo.IsGecko) E = E.replace(/<br\/>$/, '');
    E = E.replace(FCKRegexLib.SpaceNoClose, ' />');
    if (C) E = FCKCodeFormatter.Format(E);
    for (var i = 0; i < FCKXHtml.SpecialBlocks.length; i++) {
        var F = new RegExp('___FCKsi___' + i);
        E = E.replace(F, FCKXHtml.SpecialBlocks[i]);
    }
    ;
    E = E.replace(FCKRegexLib.GeckoEntitiesMarker, '&');
    if (!D) FCK.ResetIsDirty();
    return E;
};
FCKXHtml._AppendAttribute = function (A, B, C) {
    try {
        if (C == undefined || C == null) C = ''; else if (C.replace) {
            C = C.replace(FCKXHtmlEntities.EntitiesRegex, FCKXHtml_GetEntity);
        }
        ;
        var D = this.XML.createAttribute(B);
        D.value = C;
        A.attributes.setNamedItem(D);
    } catch (e) {
    }
};
FCKXHtml._AppendChildNodes = function (A, B, C) {
    var D = B.firstChild;
    while (D) {
        this._AppendNode(A, D);
        D = D.nextSibling;
    }
    ;
    if (C) FCKDomTools.TrimNode(A, true);
    if (A.childNodes.length == 0) {
        if (C && FCKConfig.FillEmptyBlocks) {
            this._AppendEntity(A, this._NbspEntity);
            return A;
        }
        ;
        var E = A.nodeName;
        if (FCKListsLib.InlineChildReqElements[E]) return null;
        if (!FCKListsLib.EmptyElements[E]) A.appendChild(this.XML.createTextNode(''));
    }
    ;
    return A;
};
FCKXHtml._AppendNode = function (A, B) {
    if (!B) return false;
    switch (B.nodeType) {
        case 1:
            if (B.getAttribute('_fckfakelement')) return FCKXHtml._AppendNode(A, FCK.GetRealElement(B));
            if (FCKBrowserInfo.IsGecko && B.hasAttribute('_moz_editor_bogus_node')) return false;
            if (B.getAttribute('_fcktemp')) return false;
            var C = B.tagName.toLowerCase();
            if (FCKBrowserInfo.IsIE) {
                if (B.scopeName && B.scopeName != 'HTML' && B.scopeName != 'FCK') C = B.scopeName.toLowerCase() + ':' + C;
            } else {
                if (C.StartsWith('fck:')) C = C.Remove(0, 4);
            }
            ;
            if (!FCKRegexLib.ElementName.test(C)) return false;
            if (C == 'br' && B.getAttribute('type', 2) == '_moz') return false;
            if (B._fckxhtmljob && B._fckxhtmljob == FCKXHtml.CurrentJobNum) return false;
            var D = this._CreateNode(C);
            FCKXHtml._AppendAttributes(A, B, D, C);
            B._fckxhtmljob = FCKXHtml.CurrentJobNum;
            var E = FCKXHtml.TagProcessors[C];
            if (E) D = E(D, B, A); else D = this._AppendChildNodes(D, B, Boolean(FCKListsLib.NonEmptyBlockElements[C]));
            if (!D) return false;
            A.appendChild(D);
            break;
        case 3:
            return this._AppendTextNode(A, B.nodeValue.ReplaceNewLineChars(' '));
        case 8:
            if (FCKBrowserInfo.IsIE && !B.innerHTML) break;
            try {
                A.appendChild(this.XML.createComment(B.nodeValue));
            } catch (e) {/*Do nothing... probably this is a wrong format comment.*/
            }
            ;
            break;
        default:
            A.appendChild(this.XML.createComment("Element not supported - Type: " + B.nodeType + " Name: " + B.nodeName));
            break;
    }
    ;
    return true;
};
function FCKXHtml_CreateNode_StrongEm(A) {
    switch (A) {
        case 'b':
            A = 'strong';
            break;
        case 'i':
            A = 'em';
            break;
    }
    ;
    return this.XML.createElement(A);
};
function FCKXHtml_CreateNode_Normal(A) {
    return this.XML.createElement(A);
};
FCKXHtml._AppendSpecialItem = function (A) {
    return '___FCKsi___' + FCKXHtml.SpecialBlocks.AddItem(A);
};
FCKXHtml._AppendEntity = function (A, B) {
    A.appendChild(this.XML.createTextNode('#?-:' + B + ';'));
};
FCKXHtml._AppendTextNode = function (A, B) {
    var C = B.length > 0;
    if (C) A.appendChild(this.XML.createTextNode(B.replace(FCKXHtmlEntities.EntitiesRegex, FCKXHtml_GetEntity)));
    return C;
};
function FCKXHtml_GetEntity(A) {
    var B = FCKXHtmlEntities.Entities[A] || ('#' + A.charCodeAt(0));
    return '#?-:' + B + ';';
};
FCKXHtml._RemoveAttribute = function (A, B, C) {
    var D = A.attributes.getNamedItem(C);
    if (D && B.test(D.nodeValue)) {
        var E = D.nodeValue.replace(B, '');
        if (E.length == 0) A.attributes.removeNamedItem(C); else D.nodeValue = E;
    }
};
FCKXHtml.TagProcessors = {img: function (A, B) {
    if (!A.attributes.getNamedItem('alt')) FCKXHtml._AppendAttribute(A, 'alt', '');
    var C = B.getAttribute('_fcksavedurl');
    if (C != null) FCKXHtml._AppendAttribute(A, 'src', C);
    return A;
}, a: function (A, B) {
    if (B.innerHTML.Trim().length == 0 && !B.name) return false;
    var C = B.getAttribute('_fcksavedurl');
    if (C != null) FCKXHtml._AppendAttribute(A, 'href', C);
    if (FCKBrowserInfo.IsIE) {
        FCKXHtml._RemoveAttribute(A, FCKRegexLib.FCK_Class, 'class');
        if (B.name) FCKXHtml._AppendAttribute(A, 'name', B.name);
    }
    ;
    A = FCKXHtml._AppendChildNodes(A, B, false);
    return A;
}, script: function (A, B) {
    if (!A.attributes.getNamedItem('type')) FCKXHtml._AppendAttribute(A, 'type', 'text/javascript');
    A.appendChild(FCKXHtml.XML.createTextNode(FCKXHtml._AppendSpecialItem(B.text)));
    return A;
}, style: function (A, B) {
    if (!A.attributes.getNamedItem('type')) FCKXHtml._AppendAttribute(A, 'type', 'text/css');
    A.appendChild(FCKXHtml.XML.createTextNode(FCKXHtml._AppendSpecialItem(B.innerHTML)));
    return A;
}, title: function (A, B) {
    A.appendChild(FCKXHtml.XML.createTextNode(FCK.EditorDocument.title));
    return A;
}, table: function (A, B) {
    if (FCKBrowserInfo.IsIE) FCKXHtml._RemoveAttribute(A, FCKRegexLib.FCK_Class, 'class');
    A = FCKXHtml._AppendChildNodes(A, B, false);
    return A;
}, ol: function (A, B, C) {
    if (B.innerHTML.Trim().length == 0) return false;
    var D = C.lastChild;
    if (D && D.nodeType == 3) D = D.previousSibling;
    if (D && D.nodeName.toUpperCase() == 'LI') {
        B._fckxhtmljob = null;
        FCKXHtml._AppendNode(D, B);
        return false;
    }
    ;
    A = FCKXHtml._AppendChildNodes(A, B);
    return A;
}, span: function (A, B) {
    if (B.innerHTML.length == 0) return false;
    A = FCKXHtml._AppendChildNodes(A, B, false);
    return A;
}, iframe: function (A, B) {
    var C = B.innerHTML;
    if (FCKBrowserInfo.IsGecko) C = FCKTools.HTMLDecode(C);
    C = C.replace(/\s_fcksavedurl="[^"]*"/g, '');
    A.appendChild(FCKXHtml.XML.createTextNode(FCKXHtml._AppendSpecialItem(C)));
    return A;
}};
FCKXHtml.TagProcessors.ul = FCKXHtml.TagProcessors.ol;
FCKXHtml._GetMainXmlString = function () {
    return this.MainNode.xml;
};
FCKXHtml._AppendAttributes = function (A, B, C, D) {
    var E = B.attributes;
    for (var n = 0; n < E.length; n++) {
        var F = E[n];
        if (F.specified) {
            var G = F.nodeName.toLowerCase();
            var H;
            if (G.StartsWith('_fck')) continue; else if (G == 'style') H = B.style.cssText.replace(FCKRegexLib.StyleProperties, FCKTools.ToLowerCase); else if (G == 'class' || G.indexOf('on') == 0) H = F.nodeValue; else if (D == 'body' && G == 'contenteditable') continue; else if (F.nodeValue === true) H = G; else {
                try {
                    H = B.getAttribute(G, 2);
                } catch (e) {
                }
            }
            ;
            this._AppendAttribute(C, G, H || F.nodeValue);
        }
    }
};
FCKXHtml.TagProcessors['font'] = function (A, B) {
    if (A.attributes.length == 0) A = FCKXHtml.XML.createDocumentFragment();
    A = FCKXHtml._AppendChildNodes(A, B);
    return A;
};
FCKXHtml.TagProcessors['div'] = function (A, B) {
    if (B.align.length > 0) FCKXHtml._AppendAttribute(A, 'align', B.align);
    A = FCKXHtml._AppendChildNodes(A, B, true);
    return A;
}
var FCKCodeFormatter = {};
FCKCodeFormatter.Init = function () {
    var A = this.Regex = {};
    A.BlocksOpener = /\<(P|DIV|H1|H2|H3|H4|H5|H6|ADDRESS|PRE|OL|UL|LI|TITLE|META|LINK|BASE|SCRIPT|LINK|TD|TH|AREA|OPTION)[^\>]*\>/gi;
    A.BlocksCloser = /\<\/(P|DIV|H1|H2|H3|H4|H5|H6|ADDRESS|PRE|OL|UL|LI|TITLE|META|LINK|BASE|SCRIPT|LINK|TD|TH|AREA|OPTION)[^\>]*\>/gi;
    A.NewLineTags = /\<(BR|HR)[^\>]*\>/gi;
    A.MainTags = /\<\/?(HTML|HEAD|BODY|FORM|TABLE|TBODY|THEAD|TR)[^\>]*\>/gi;
    A.LineSplitter = /\s*\n+\s*/g;
    A.IncreaseIndent = /^\<(HTML|HEAD|BODY|FORM|TABLE|TBODY|THEAD|TR|UL|OL)[ \/\>]/i;
    A.DecreaseIndent = /^\<\/(HTML|HEAD|BODY|FORM|TABLE|TBODY|THEAD|TR|UL|OL)[ \>]/i;
    A.FormatIndentatorRemove = new RegExp('^' + FCKConfig.FormatIndentator);
    A.ProtectedTags = /(<PRE[^>]*>)([\s\S]*?)(<\/PRE>)/gi;
};
FCKCodeFormatter._ProtectData = function (A, B, C, D) {
    return B + '___FCKpd___' + FCKCodeFormatter.ProtectedData.AddItem(C) + D;
};
FCKCodeFormatter.Format = function (A) {
    if (!this.Regex) this.Init();
    FCKCodeFormatter.ProtectedData = [];
    var B = A.replace(this.Regex.ProtectedTags, FCKCodeFormatter._ProtectData);
    B = B.replace(this.Regex.BlocksOpener, '\n$&');
    B = B.replace(this.Regex.BlocksCloser, '$&\n');
    B = B.replace(this.Regex.NewLineTags, '$&\n');
    B = B.replace(this.Regex.MainTags, '\n$&\n');
    var C = '';
    var D = B.split(this.Regex.LineSplitter);
    B = '';
    for (var i = 0; i < D.length; i++) {
        var E = D[i];
        if (E.length == 0) continue;
        if (this.Regex.DecreaseIndent.test(E)) C = C.replace(this.Regex.FormatIndentatorRemove, '');
        B += C + E + '\n';
        if (this.Regex.IncreaseIndent.test(E)) C += FCKConfig.FormatIndentator;
    }
    ;
    for (var j = 0; j < FCKCodeFormatter.ProtectedData.length; j++) {
        var F = new RegExp('___FCKpd___' + j);
        B = B.replace(F, FCKCodeFormatter.ProtectedData[j].replace(/\$/g, '$$$$'));
    }
    ;
    return B.Trim();
}
var FCKUndo = {};
FCKUndo.SavedData = [];
FCKUndo.CurrentIndex = -1;
FCKUndo.TypesCount = FCKUndo.MaxTypes = 25;
FCKUndo.Typing = false;
FCKUndo.SaveUndoStep = function () {
    if (FCK.EditMode != 0) return;
    FCKUndo.SavedData = FCKUndo.SavedData.slice(0, FCKUndo.CurrentIndex + 1);
    var A = FCK.EditorDocument.body.innerHTML;
    if (FCKUndo.CurrentIndex >= 0 && A == FCKUndo.SavedData[FCKUndo.CurrentIndex][0]) return;
    if (FCKUndo.CurrentIndex + 1 >= FCKConfig.MaxUndoLevels) FCKUndo.SavedData.shift(); else FCKUndo.CurrentIndex++;
    var B;
    if (FCK.EditorDocument.selection.type == 'Text') B = FCK.EditorDocument.selection.createRange().getBookmark();
    FCKUndo.SavedData[FCKUndo.CurrentIndex] = [A, B];
    FCK.Events.FireEvent("OnSelectionChange");
};
FCKUndo.CheckUndoState = function () {
    return (FCKUndo.Typing || FCKUndo.CurrentIndex > 0);
};
FCKUndo.CheckRedoState = function () {
    return (!FCKUndo.Typing && FCKUndo.CurrentIndex < (FCKUndo.SavedData.length - 1));
};
FCKUndo.Undo = function () {
    if (FCKUndo.CheckUndoState()) {
        if (FCKUndo.CurrentIndex == (FCKUndo.SavedData.length - 1)) {
            FCKUndo.SaveUndoStep();
        }
        ;
        FCKUndo._ApplyUndoLevel(--FCKUndo.CurrentIndex);
        FCK.Events.FireEvent("OnSelectionChange");
    }
};
FCKUndo.Redo = function () {
    if (FCKUndo.CheckRedoState()) {
        FCKUndo._ApplyUndoLevel(++FCKUndo.CurrentIndex);
        FCK.Events.FireEvent("OnSelectionChange");
    }
};
FCKUndo._ApplyUndoLevel = function (A) {
    var B = FCKUndo.SavedData[A];
    if (!B) return;
    FCK.SetInnerHtml(B[0]);
    if (B[1]) {
        var C = FCK.EditorDocument.selection.createRange();
        C.moveToBookmark(B[1]);
        C.select();
    }
    ;
    FCKUndo.TypesCount = 0;
    FCKUndo.Typing = false;
}
var FCKEditingArea = function (A) {
    this.TargetElement = A;
    this.Mode = 0;
    if (FCK.IECleanup) FCK.IECleanup.AddItem(this, FCKEditingArea_Cleanup);
};
FCKEditingArea.prototype.Start = function (A, B) {
    var C = this.TargetElement;
    var D = FCKTools.GetElementDocument(C);
    while (C.childNodes.length > 0) C.removeChild(C.childNodes[0]);
    if (this.Mode == 0) {
        var E = this.IFrame = D.createElement('iframe');
        E.src = 'javascript:void(0)';
        E.frameBorder = 0;
        E.width = E.height = '100%';
        C.appendChild(E);
        if (FCKBrowserInfo.IsIE) A = A.replace(/(<base[^>]*?)\s*\/?>(?!\s*<\/base>)/gi, '$1></base>'); else if (!B) {
            if (FCKBrowserInfo.IsGecko) A = A.replace(/(<body[^>]*>)\s*(<\/body>)/i, '$1' + GECKO_BOGUS + '$2');
            var F = A.match(FCKRegexLib.BodyContents);
            if (F) {
                A = F[1] + '&nbsp;' + F[3];
                this._BodyHTML = F[2];
            } else this._BodyHTML = A;
        }
        ;
        this.Window = E.contentWindow;
        var G = this.Document = this.Window.document;
        G.open();
        G.write(A);
        G.close();
        if (FCKBrowserInfo.IsGecko10 && !B) {
            this.Start(A, true);
            return;
        }
        ;
        this.Window._FCKEditingArea = this;
        if (FCKBrowserInfo.IsGecko10) this.Window.setTimeout(FCKEditingArea_CompleteStart, 500); else FCKEditingArea_CompleteStart.call(this.Window);
    } else {
        var H = this.Textarea = D.createElement('textarea');
        H.className = 'SourceField';
        H.dir = 'ltr';
        H.style.width = H.style.height = '100%';
        H.style.border = 'none';
        C.appendChild(H);
        H.value = A;
        FCKTools.RunFunction(this.OnLoad);
    }
};
function FCKEditingArea_CompleteStart() {
    if (!this.document.body) {
        this.setTimeout(FCKEditingArea_CompleteStart, 50);
        return;
    }
    ;
    var A = this._FCKEditingArea;
    A.MakeEditable();
    FCKTools.RunFunction(A.OnLoad);
};
FCKEditingArea.prototype.MakeEditable = function () {
    var A = this.Document;
    if (FCKBrowserInfo.IsIE) {
        A.body.contentEditable = true;
    } else {
        try {
            if (this._BodyHTML) {
                A.body.innerHTML = this._BodyHTML;
                this._BodyHTML = null;
            }
            ;
            A.designMode = 'on';
            try {
                A.execCommand('styleWithCSS', false, false);
            } catch (e) {
                A.execCommand('useCSS', false, true);
            }
            ;
            A.execCommand('enableObjectResizing', false, true);
            A.execCommand('enableInlineTableEditing', false, false);
        } catch (e) {
        }
    }
};
FCKEditingArea.prototype.Focus = function () {
    try {
        if (this.Mode == 0) {
            if (FCKBrowserInfo.IsIE && this.Document.hasFocus()) return;
            if (FCKBrowserInfo.IsSafari) this.IFrame.focus(); else {
                this.Window.focus();
            }
        } else {
            var A = FCKTools.GetElementDocument(this.Textarea);
            if ((!A.hasFocus || A.hasFocus()) && A.activeElement == this.Textarea) return;
            this.Textarea.focus();
        }
    } catch (e) {
    }
};
function FCKEditingArea_Cleanup() {
    this.TargetElement = null;
    this.IFrame = null;
    this.Document = null;
    this.Textarea = null;
    if (this.Window) {
        this.Window._FCKEditingArea = null;
        this.Window = null;
    }
};
var FCKElementPath = function (A) {
    var B = null;
    var C = null;
    var D = [];
    var e = A;
    while (e) {
        if (e.nodeType == 1) {
            if (!this.LastElement) this.LastElement = e;
            var E = e.nodeName.toLowerCase();
            if (!C) {
                if (!B && FCKListsLib.PathBlockElements[E] != null) B = e;
                if (FCKListsLib.PathBlockLimitElements[E] != null) C = e;
            }
            ;
            D.push(e);
            if (E == 'body') break;
        }
        ;
        e = e.parentNode;
    }
    ;
    this.Block = B;
    this.BlockLimit = C;
    this.Elements = D;
};
var FCKDomRange = function (A) {
    this.Window = A;
};
FCKDomRange.prototype = {_UpdateElementInfo: function () {
    if (!this._Range) this.Release(true); else {
        var A = this._Range.startContainer;
        var B = this._Range.endContainer;
        var C = new FCKElementPath(A);
        this.StartContainer = C.LastElement;
        this.StartBlock = C.Block;
        this.StartBlockLimit = C.BlockLimit;
        if (A != B) C = new FCKElementPath(B);
        this.EndContainer = C.LastElement;
        this.EndBlock = C.Block;
        this.EndBlockLimit = C.BlockLimit;
    }
}, CreateRange: function () {
    return new FCKW3CRange(this.Window.document);
}, DeleteContents: function () {
    if (this._Range) {
        this._Range.deleteContents();
        this._UpdateElementInfo();
    }
}, ExtractContents: function () {
    if (this._Range) {
        var A = this._Range.extractContents();
        this._UpdateElementInfo();
        return A;
    }
}, CheckIsCollapsed: function () {
    if (this._Range) return this._Range.collapsed;
}, Collapse: function (A) {
    if (this._Range) this._Range.collapse(A);
    this._UpdateElementInfo();
}, Clone: function () {
    var A = FCKTools.CloneObject(this);
    if (this._Range) A._Range = this._Range.cloneRange();
    return A;
}, MoveToNodeContents: function (A) {
    if (!this._Range) this._Range = this.CreateRange();
    this._Range.selectNodeContents(A);
    this._UpdateElementInfo();
}, MoveToElementStart: function (A) {
    this.SetStart(A, 1);
    this.SetEnd(A, 1);
}, MoveToElementEditStart: function (A) {
    var B;
    while ((B = A.firstChild) && B.nodeType == 1 && FCKListsLib.EmptyElements[B.nodeName.toLowerCase()] == null) A = B;
    this.MoveToElementStart(A);
}, InsertNode: function (A) {
    if (this._Range) this._Range.insertNode(A);
}, CheckIsEmpty: function (A) {
    if (this.CheckIsCollapsed()) return true;
    var B = this.Window.document.createElement('div');
    this._Range.cloneContents().AppendTo(B);
    FCKDomTools.TrimNode(B, A);
    return (B.innerHTML.length == 0);
}, CheckStartOfBlock: function () {
    var A = this.Clone();
    A.Collapse(true);
    A.SetStart(A.StartBlock || A.StartBlockLimit, 1);
    var B = A.CheckIsEmpty();
    A.Release();
    return B;
}, CheckEndOfBlock: function (A) {
    var B = this.Clone();
    B.Collapse(false);
    B.SetEnd(B.EndBlock || B.EndBlockLimit, 2);
    var C = B.CheckIsCollapsed();
    if (!C) {
        var D = this.Window.document.createElement('div');
        B._Range.cloneContents().AppendTo(D);
        FCKDomTools.TrimNode(D, true);
        C = true;
        var E = D;
        while ((E = E.lastChild)) {
            if (E.previousSibling || E.nodeType != 1 || FCKListsLib.InlineChildReqElements[E.nodeName.toLowerCase()] == null) {
                C = false;
                break;
            }
        }
    }
    ;
    B.Release();
    if (A) this.Select();
    return C;
}, CreateBookmark: function () {
    var A = {StartId: 'fck_dom_range_start_' + (new Date()).valueOf() + '_' + Math.floor(Math.random() * 1000), EndId: 'fck_dom_range_end_' + (new Date()).valueOf() + '_' + Math.floor(Math.random() * 1000)};
    var B = this.Window.document;
    var C;
    var D;
    if (!this.CheckIsCollapsed()) {
        C = B.createElement('span');
        C.id = A.EndId;
        C.innerHTML = '&nbsp;';
        D = this.Clone();
        D.Collapse(false);
        D.InsertNode(C);
    }
    ;
    C = B.createElement('span');
    C.id = A.StartId;
    C.innerHTML = '&nbsp;';
    D = this.Clone();
    D.Collapse(true);
    D.InsertNode(C);
    return A;
}, MoveToBookmark: function (A, B) {
    var C = this.Window.document;
    var D = C.getElementById(A.StartId);
    var E = C.getElementById(A.EndId);
    this.SetStart(D, 3);
    if (!B) FCKDomTools.RemoveNode(D);
    if (E) {
        this.SetEnd(E, 3);
        if (!B) FCKDomTools.RemoveNode(E);
    } else this.Collapse(true);
}, SetStart: function (A, B) {
    var C = this._Range;
    if (!C) C = this._Range = this.CreateRange();
    switch (B) {
        case 1:
            C.setStart(A, 0);
            break;
        case 2:
            C.setStart(A, A.childNodes.length);
            break;
        case 3:
            C.setStartBefore(A);
            break;
        case 4:
            C.setStartAfter(A);
    }
    ;
    this._UpdateElementInfo();
}, SetEnd: function (A, B) {
    var C = this._Range;
    if (!C) C = this._Range = this.CreateRange();
    switch (B) {
        case 1:
            C.setEnd(A, 0);
            break;
        case 2:
            C.setEnd(A, A.childNodes.length);
            break;
        case 3:
            C.setEndBefore(A);
            break;
        case 4:
            C.setEndAfter(A);
    }
    ;
    this._UpdateElementInfo();
}, Expand: function (A) {
    var B, oSibling;
    switch (A) {
        case 'block_contents':
            if (this.StartBlock) this.SetStart(this.StartBlock, 1); else {
                B = this._Range.startContainer;
                if (B.nodeType == 1) {
                    if (!(B = B.childNodes[this._Range.startOffset])) B = B.firstChild;
                }
                ;
                if (!B) return;
                while (true) {
                    oSibling = B.previousSibling;
                    if (!oSibling) {
                        if (B.parentNode != this.StartBlockLimit) B = B.parentNode; else break;
                    } else if (oSibling.nodeType != 1 || !(/^(?:P|DIV|H1|H2|H3|H4|H5|H6|ADDRESS|PRE|OL|UL|LI|DT|DE)$/).test(oSibling.nodeName.toUpperCase())) {
                        B = oSibling;
                    } else break;
                }
                ;
                this._Range.setStartBefore(B);
            }
            ;
            if (this.EndBlock) this.SetEnd(this.EndBlock, 2); else {
                B = this._Range.endContainer;
                if (B.nodeType == 1) B = B.childNodes[this._Range.endOffset] || B.lastChild;
                if (!B) return;
                while (true) {
                    oSibling = B.nextSibling;
                    if (!oSibling) {
                        if (B.parentNode != this.EndBlockLimit) B = B.parentNode; else break;
                    } else if (oSibling.nodeType != 1 || !(/^(?:P|DIV|H1|H2|H3|H4|H5|H6|ADDRESS|PRE|OL|UL|LI|DT|DE)$/).test(oSibling.nodeName.toUpperCase())) {
                        B = oSibling;
                    } else break;
                }
                ;
                this._Range.setEndAfter(B);
            }
            ;
            this._UpdateElementInfo();
    }
}, Release: function (A) {
    if (!A) this.Window = null;
    this.StartContainer = null;
    this.StartBlock = null;
    this.StartBlockLimit = null;
    this.EndContainer = null;
    this.EndBlock = null;
    this.EndBlockLimit = null;
    this._Range = null;
}};
FCKDomRange.prototype.MoveToSelection = function () {
    this.Release(true);
    this._Range = new FCKW3CRange(this.Window.document);
    var A = this.Window.document.selection;
    if (A.type != 'Control') {
        B = this._GetSelectionMarkerTag(true);
        this._Range.setStart(B.parentNode, FCKDomTools.GetIndexOf(B));
        B.parentNode.removeChild(B);
        var B = this._GetSelectionMarkerTag(false);
        this._Range.setEnd(B.parentNode, FCKDomTools.GetIndexOf(B));
        B.parentNode.removeChild(B);
        this._UpdateElementInfo();
    } else {
        var C = A.createRange().item(0);
        if (C) {
            this._Range.setStartBefore(C);
            this._Range.setEndAfter(C);
            this._UpdateElementInfo();
        }
    }
};
FCKDomRange.prototype.Select = function () {
    if (this._Range) {
        var A = this.CheckIsCollapsed();
        var B = this._GetRangeMarkerTag(true);
        if (!A) var C = this._GetRangeMarkerTag(false);
        var D = this.Window.document.body.createTextRange();
        D.moveToElementText(B);
        D.moveStart('character', 1);
        if (!A) {
            var E = this.Window.document.body.createTextRange();
            E.moveToElementText(C);
            D.setEndPoint('EndToEnd', E);
            D.moveEnd('character', -1);
        }
        ;
        this._Range.setStartBefore(B);
        B.parentNode.removeChild(B);
        if (A) {
            try {
                D.pasteHTML('&nbsp;');
                D.moveStart('character', -1);
            } catch (e) {
            }
            ;
            D.select();
            D.pasteHTML('');
        } else {
            this._Range.setEndBefore(C);
            C.parentNode.removeChild(C);
            D.select();
        }
    }
};
FCKDomRange.prototype._GetSelectionMarkerTag = function (A) {
    var B = this.Window.document.selection.createRange();
    B.collapse(A === true);
    var C = 'fck_dom_range_temp_' + (new Date()).valueOf() + '_' + Math.floor(Math.random() * 1000);
    B.pasteHTML('<span id="' + C + '"></span>');
    return this.Window.document.getElementById(C);
};
FCKDomRange.prototype._GetRangeMarkerTag = function (A) {
    var B = this._Range;
    if (!A) {
        B = B.cloneRange();
        B.collapse(A === true);
    }
    ;
    var C = this.Window.document.createElement('span');
    C.innerHTML = '&nbsp;';
    B.insertNode(C);
    return C;
}
var FCKDocumentFragment = function (A) {
    this._Document = A;
    this.RootNode = A.createElement('div');
};
FCKDocumentFragment.prototype = {AppendTo: function (A) {
    FCKDomTools.MoveChildren(this.RootNode, A);
}, AppendHtml: function (A) {
    var B = this._Document.createElement('div');
    B.innerHTML = A;
    FCKDomTools.MoveChildren(B, this.RootNode);
}, InsertAfterNode: function (A) {
    var B = this.RootNode;
    var C;
    while ((C = B.lastChild)) FCKDomTools.InsertAfterNode(A, B.removeChild(C));
}};
var FCKW3CRange = function (A) {
    this._Document = A;
    this.startContainer = null;
    this.startOffset = null;
    this.endContainer = null;
    this.endOffset = null;
    this.collapsed = true;
};
FCKW3CRange.CreateRange = function (A) {
    return new FCKW3CRange(A);
};
FCKW3CRange.CreateFromRange = function (A, B) {
    var C = FCKW3CRange.CreateRange(A);
    C.setStart(B.startContainer, B.startOffset);
    C.setEnd(B.endContainer, B.endOffset);
    return C;
};
FCKW3CRange.prototype = {_UpdateCollapsed: function () {
    this.collapsed = (this.startContainer == this.endContainer && this.startOffset == this.endOffset);
}, setStart: function (A, B) {
    this.startContainer = A;
    this.startOffset = B;
    if (!this.endContainer) {
        this.endContainer = A;
        this.endOffset = B;
    }
    ;
    this._UpdateCollapsed();
}, setEnd: function (A, B) {
    this.endContainer = A;
    this.endOffset = B;
    if (!this.startContainer) {
        this.startContainer = A;
        this.startOffset = B;
    }
    ;
    this._UpdateCollapsed();
}, setStartAfter: function (A) {
    this.setStart(A.parentNode, FCKDomTools.GetIndexOf(A) + 1);
}, setStartBefore: function (A) {
    this.setStart(A.parentNode, FCKDomTools.GetIndexOf(A));
}, setEndAfter: function (A) {
    this.setEnd(A.parentNode, FCKDomTools.GetIndexOf(A) + 1);
}, setEndBefore: function (A) {
    this.setEnd(A.parentNode, FCKDomTools.GetIndexOf(A));
}, collapse: function (A) {
    if (A) {
        this.endContainer = this.startContainer;
        this.endOffset = this.startOffset;
    } else {
        this.startContainer = this.endContainer;
        this.startOffset = this.endOffset;
    }
    ;
    this.collapsed = true;
}, selectNodeContents: function (A) {
    this.setStart(A, 0);
    this.setEnd(A, A.nodeType == 3 ? A.data.length : A.childNodes.length);
}, insertNode: function (A) {
    var B = this.startContainer;
    var C = this.startOffset;
    if (B.nodeType == 3) {
        B.splitText(C);
        if (B == this.endContainer) this.setEnd(B.nextSibling, this.endOffset - this.startOffset);
        FCKDomTools.InsertAfterNode(B, A);
        return;
    } else {
        B.insertBefore(A, B.childNodes[C] || null);
        if (B == this.endContainer) {
            this.endOffset++;
            this.collapsed = false;
        }
    }
}, deleteContents: function () {
    if (this.collapsed) return;
    this._ExecContentsAction(0);
}, extractContents: function () {
    var A = new FCKDocumentFragment(this._Document);
    if (!this.collapsed) this._ExecContentsAction(1, A);
    return A;
}, cloneContents: function () {
    var A = new FCKDocumentFragment(this._Document);
    if (!this.collapsed) this._ExecContentsAction(2, A);
    return A;
}, _ExecContentsAction: function (A, B) {
    var C = this.startContainer;
    var D = this.endContainer;
    var E = this.startOffset;
    var F = this.endOffset;
    var G = false;
    var H = false;
    if (D.nodeType == 3) D = D.splitText(F); else {
        if (D.childNodes.length > 0) {
            if (F > D.childNodes.length - 1) {
                D = FCKDomTools.InsertAfterNode(D.lastChild, this._Document.createTextNode(''));
                H = true;
            } else D = D.childNodes[F];
        }
    }
    ;
    if (C.nodeType == 3) {
        C.splitText(E);
        if (C == D) D = C.nextSibling;
    } else {
        if (C.childNodes.length > 0 && E <= C.childNodes.length - 1) {
            if (E == 0) {
                C = C.insertBefore(this._Document.createTextNode(''), C.firstChild);
                G = true;
            } else C = C.childNodes[E].previousSibling;
        }
    }
    ;
    var I = FCKDomTools.GetParents(C);
    var J = FCKDomTools.GetParents(D);
    var i, topStart, topEnd;
    for (i = 0; i < I.length; i++) {
        topStart = I[i];
        topEnd = J[i];
        if (topStart != topEnd) break;
    }
    ;
    var K, levelStartNode, levelClone, currentNode, currentSibling;
    if (B) K = B.RootNode;
    for (var j = i; j < I.length; j++) {
        levelStartNode = I[j];
        if (K && levelStartNode != C) levelClone = K.appendChild(levelStartNode.cloneNode(levelStartNode == C));
        currentNode = levelStartNode.nextSibling;
        while (currentNode) {
            if (currentNode == J[j] || currentNode == D) break;
            currentSibling = currentNode.nextSibling;
            if (A == 2) K.appendChild(currentNode.cloneNode(true)); else {
                currentNode.parentNode.removeChild(currentNode);
                if (A == 1) K.appendChild(currentNode);
            }
            ;
            currentNode = currentSibling;
        }
        ;
        if (K) K = levelClone;
    }
    ;
    if (B) K = B.RootNode;
    for (var k = i; k < J.length; k++) {
        levelStartNode = J[k];
        if (A > 0 && levelStartNode != D) levelClone = K.appendChild(levelStartNode.cloneNode(levelStartNode == D));
        if (!I[k] || levelStartNode.parentNode != I[k].parentNode) {
            currentNode = levelStartNode.previousSibling;
            while (currentNode) {
                if (currentNode == I[k] || currentNode == C) break;
                currentSibling = currentNode.previousSibling;
                if (A == 2) K.insertBefore(currentNode.cloneNode(true), K.firstChild); else {
                    currentNode.parentNode.removeChild(currentNode);
                    if (A == 1) K.insertBefore(currentNode, K.firstChild);
                }
                ;
                currentNode = currentSibling;
            }
        }
        ;
        if (K) K = levelClone;
    }
    ;
    if (A == 2) {
        var L = this.startContainer;
        if (L.nodeType == 3) {
            L.data += L.nextSibling.data;
            L.parentNode.removeChild(L.nextSibling);
        }
        ;
        var M = this.endContainer;
        if (M.nodeType == 3 && M.nextSibling) {
            M.data += M.nextSibling.data;
            M.parentNode.removeChild(M.nextSibling);
        }
    } else {
        if (topStart && topEnd && (C.parentNode != topStart.parentNode || D.parentNode != topEnd.parentNode)) this.setStart(topEnd.parentNode, FCKDomTools.GetIndexOf(topEnd));
        this.collapse(true);
    }
    ;
    if (G) C.parentNode.removeChild(C);
    if (H && D.parentNode) D.parentNode.removeChild(D);
}, cloneRange: function () {
    return FCKW3CRange.CreateFromRange(this._Document, this);
}, toString: function () {
    var A = this.cloneContents();
    var B = this._Document.createElement('div');
    A.AppendTo(B);
    return B.textContent || B.innerText;
}};
var FCKDocumentProcessor = {};
FCKDocumentProcessor._Items = [];
FCKDocumentProcessor.AppendNew = function () {
    var A = {};
    this._Items.AddItem(A);
    return A;
};
FCKDocumentProcessor.Process = function (A) {
    var B, i = 0;
    while ((B = this._Items[i++])) B.ProcessDocument(A);
};
var FCKDocumentProcessor_CreateFakeImage = function (A, B) {
    var C = FCK.EditorDocument.createElement('IMG');
    C.className = A;
    C.src = FCKConfig.FullBasePath + 'images/spacer.gif';
    C.setAttribute('_fckfakelement', 'true', 0);
    C.setAttribute('_fckrealelement', FCKTempBin.AddElement(B), 0);
    return C;
};
var FCKFlashProcessor = FCKDocumentProcessor.AppendNew();
FCKFlashProcessor.ProcessDocument = function (A) {
    var B = A.getElementsByTagName('EMBED');
    var C;
    var i = B.length - 1;
    while (i >= 0 && (C = B[i--])) {
        var D = C.attributes['type'];
        if ((C.src && C.src.EndsWith('.swf', true)) || (D && D.nodeValue == 'application/x-shockwave-flash')) {
            var E = C.cloneNode(true);
            if (FCKBrowserInfo.IsIE) {
                var F = ['scale', 'play', 'loop', 'menu', 'wmode', 'quality'];
                for (var G = 0; G < F.length; G++) {
                    var H = C.getAttribute(F[G]);
                    if (H) E.setAttribute(F[G], H);
                }
                ;
                E.setAttribute('type', D.nodeValue);
            }
            ;
            var I = FCKDocumentProcessor_CreateFakeImage('FCK__Flash', E);
            I.setAttribute('_fckflash', 'true', 0);
            FCKFlashProcessor.RefreshView(I, C);
            C.parentNode.insertBefore(I, C);
            C.parentNode.removeChild(C);
        }
    }
};
FCKFlashProcessor.RefreshView = function (A, B) {
    if (B.getAttribute('width') > 0) A.style.width = FCKTools.ConvertHtmlSizeToStyle(B.getAttribute('width'));
    if (B.getAttribute('height') > 0) A.style.height = FCKTools.ConvertHtmlSizeToStyle(B.getAttribute('height'));
};
FCK.GetRealElement = function (A) {
    var e = FCKTempBin.Elements[A.getAttribute('_fckrealelement')];
    if (A.getAttribute('_fckflash')) {
        if (A.style.width.length > 0) e.width = FCKTools.ConvertStyleSizeToHtml(A.style.width);
        if (A.style.height.length > 0) e.height = FCKTools.ConvertStyleSizeToHtml(A.style.height);
    }
    ;
    return e;
};
if (FCKBrowserInfo.IsIE) {
    FCKDocumentProcessor.AppendNew().ProcessDocument = function (A) {
        var B = A.getElementsByTagName('HR');
        var C;
        var i = B.length - 1;
        while (i >= 0 && (C = B[i--])) {
            var D = A.createElement('hr');
            D.mergeAttributes(C, true);
            FCKDomTools.InsertAfterNode(C, D);
            C.parentNode.removeChild(C);
        }
    }
}
;
var FCKSelection = FCK.Selection = {};
FCKSelection.GetType = function () {
    return FCK.EditorDocument.selection.type;
};
FCKSelection.GetSelectedElement = function () {
    if (this.GetType() == 'Control') {
        var A = FCK.EditorDocument.selection.createRange();
        if (A && A.item) return FCK.EditorDocument.selection.createRange().item(0);
    }
    ;
    return null;
};
FCKSelection.GetParentElement = function () {
    switch (this.GetType()) {
        case 'Control':
            return FCKSelection.GetSelectedElement().parentElement;
        case 'None':
            return null;
        default:
            return FCK.EditorDocument.selection.createRange().parentElement();
    }
};
FCKSelection.SelectNode = function (A) {
    FCK.Focus();
    FCK.EditorDocument.selection.empty();
    var B;
    try {
        B = FCK.EditorDocument.body.createControlRange();
        B.addElement(A);
    } catch (e) {
        B = FCK.EditorDocument.body.createTextRange();
        B.moveToElementText(A);
    }
    ;
    B.select();
};
FCKSelection.Collapse = function (A) {
    FCK.Focus();
    if (this.GetType() == 'Text') {
        var B = FCK.EditorDocument.selection.createRange();
        B.collapse(A == null || A === true);
        B.select();
    }
};
FCKSelection.HasAncestorNode = function (A) {
    var B;
    if (FCK.EditorDocument.selection.type == "Control") {
        B = this.GetSelectedElement();
    } else {
        var C = FCK.EditorDocument.selection.createRange();
        B = C.parentElement();
    }
    ;
    while (B) {
        if (B.tagName == A) return true;
        B = B.parentNode;
    }
    ;
    return false;
};
FCKSelection.MoveToAncestorNode = function (A) {
    var B, oRange;
    if (!FCK.EditorDocument) return null;
    if (FCK.EditorDocument.selection.type == "Control") {
        oRange = FCK.EditorDocument.selection.createRange();
        for (i = 0; i < oRange.length; i++) {
            if (oRange(i).parentNode) {
                B = oRange(i).parentNode;
                break;
            }
        }
    } else {
        oRange = FCK.EditorDocument.selection.createRange();
        B = oRange.parentElement();
    }
    ;
    while (B && B.nodeName != A) B = B.parentNode;
    return B;
};
FCKSelection.Delete = function () {
    var A = FCK.EditorDocument.selection;
    if (A.type.toLowerCase() != "none") {
        A.clear();
    }
    ;
    return A;
};
var FCKNamedCommand = function (A) {
    this.Name = A;
};
FCKNamedCommand.prototype.Execute = function () {
    FCK.ExecuteNamedCommand(this.Name);
};
FCKNamedCommand.prototype.GetState = function () {
    return FCK.GetNamedCommandState(this.Name);
};
var FCKDialogCommand = function (A, B, C, D, E, F, G) {
    this.Name = A;
    this.Title = B;
    this.Url = C;
    this.Width = D;
    this.Height = E;
    this.GetStateFunction = F;
    this.GetStateParam = G;
    this.Resizable = false;
};
FCKDialogCommand.prototype.Execute = function () {
    FCKDialog.OpenDialog('FCKDialog_' + this.Name, this.Title, this.Url, this.Width, this.Height, null, null, this.Resizable);
};
FCKDialogCommand.prototype.GetState = function () {
    if (this.GetStateFunction) return this.GetStateFunction(this.GetStateParam); else return 0;
};
var FCKUndefinedCommand = function () {
    this.Name = 'Undefined';
};
FCKUndefinedCommand.prototype.Execute = function () {
    alert(FCKLang.NotImplemented);
};
FCKUndefinedCommand.prototype.GetState = function () {
    return 0;
};
var FCKFontNameCommand = function () {
    this.Name = 'FontName';
};
FCKFontNameCommand.prototype.Execute = function (A) {
    if (A !== null && A !== "") FCK.ExecuteNamedCommand('FontName', A);
};
FCKFontNameCommand.prototype.GetState = function () {
    return FCK.GetNamedCommandValue('FontName');
};
var FCKFontSizeCommand = function () {
    this.Name = 'FontSize';
};
FCKFontSizeCommand.prototype.Execute = function (A) {
    if (typeof(A) == 'string') A = parseInt(A, 10);
    if (A == null || A == '') FCK.ExecuteNamedCommand('FontSize', 3); else FCK.ExecuteNamedCommand('FontSize', A);
};
FCKFontSizeCommand.prototype.GetState = function () {
    return FCK.GetNamedCommandValue('FontSize');
};
var FCKFormatBlockCommand = function () {
    this.Name = 'FormatBlock';
};
FCKFormatBlockCommand.prototype.Execute = function (A) {
    if (A == null || A == '') FCK.ExecuteNamedCommand('FormatBlock', '<P>'); else if (A == 'div' && FCKBrowserInfo.IsGecko) FCK.ExecuteNamedCommand('FormatBlock', 'div'); else FCK.ExecuteNamedCommand('FormatBlock', '<' + A + '>');
};
FCKFormatBlockCommand.prototype.GetState = function () {
    return FCK.GetNamedCommandValue('FormatBlock');
};
var FCKPreviewCommand = function () {
    this.Name = 'Preview';
};
FCKPreviewCommand.prototype.Execute = function () {
    FCK.Preview();
};
FCKPreviewCommand.prototype.GetState = function () {
    return 0;
};
var FCKSourceCommand = function () {
    this.Name = 'Source';
};
FCKSourceCommand.prototype.Execute = function () {
    FCK.SwitchEditMode();
};
FCKSourceCommand.prototype.GetState = function () {
    return (FCK.EditMode == 0 ? 0 : 1);
};
var FCKUndoCommand = function () {
    this.Name = 'Undo';
};
FCKUndoCommand.prototype.Execute = function () {
    if (FCKBrowserInfo.IsIE) FCKUndo.Undo(); else FCK.ExecuteNamedCommand('Undo');
};
FCKUndoCommand.prototype.GetState = function () {
    if (FCKBrowserInfo.IsIE) return (FCKUndo.CheckUndoState() ? 0 : -1); else return FCK.GetNamedCommandState('Undo');
};
var FCKRedoCommand = function () {
    this.Name = 'Redo';
};
FCKRedoCommand.prototype.Execute = function () {
    if (FCKBrowserInfo.IsIE) FCKUndo.Redo(); else FCK.ExecuteNamedCommand('Redo');
};
FCKRedoCommand.prototype.GetState = function () {
    if (FCKBrowserInfo.IsIE) return (FCKUndo.CheckRedoState() ? 0 : -1); else return FCK.GetNamedCommandState('Redo');
};
var FCKUnlinkCommand = function () {
    this.Name = 'Unlink';
};
FCKUnlinkCommand.prototype.Execute = function () {
    if (FCKBrowserInfo.IsGecko) {
        var A = FCK.Selection.MoveToAncestorNode('A');
        if (A) FCKTools.RemoveOuterTags(A);
        return;
    }
    ;
    FCK.ExecuteNamedCommand(this.Name);
};
FCKUnlinkCommand.prototype.GetState = function () {
    var A = FCK.GetNamedCommandState(this.Name);
    if (A == 0 && FCK.EditMode == 0) {
        var B = FCKSelection.MoveToAncestorNode('A');
        var C = (B && B.name.length > 0 && B.href.length == 0);
        if (C) A = -1;
    }
    ;
    return A;
};
var FCKPasteCommand = function () {
    this.Name = 'Paste';
};
FCKPasteCommand.prototype = {Execute: function () {
    if (FCKBrowserInfo.IsIE) FCK.Paste(); else FCK.ExecuteNamedCommand('Paste');
}, GetState: function () {
    return FCK.GetNamedCommandState('Paste');
}};
var FCKTextColorCommand = function (A) {
    this.Name = A == 'ForeColor' ? 'TextColor' : 'BGColor';
    this.Type = A;
    var B;
    if (FCKBrowserInfo.IsIE) B = window; else if (FCK.ToolbarSet._IFrame) B = FCKTools.GetElementWindow(FCK.ToolbarSet._IFrame); else B = window.parent;
    this._Panel = new FCKPanel(B);
    this._Panel.AppendStyleSheet(FCKConfig.SkinPath + 'fck_editor.css');
    this._Panel.MainNode.className = 'FCK_Panel';
    this._CreatePanelBody(this._Panel.Document, this._Panel.MainNode);
    FCKTools.DisableSelection(this._Panel.Document.body);
};
FCKTextColorCommand.prototype.Execute = function (A, B, C) {
    FCK._ActiveColorPanelType = this.Type;
    this._Panel.Show(A, B, C);
};
FCKTextColorCommand.prototype.SetColor = function (A) {
    if (FCK._ActiveColorPanelType == 'ForeColor') FCK.ExecuteNamedCommand('ForeColor', A); else if (FCKBrowserInfo.IsGeckoLike) {
        if (FCKBrowserInfo.IsGecko) FCK.EditorDocument.execCommand('useCSS', false, false);
        FCK.ExecuteNamedCommand('hilitecolor', A);
        if (FCKBrowserInfo.IsGecko) FCK.EditorDocument.execCommand('useCSS', false, true);
    } else FCK.ExecuteNamedCommand('BackColor', A);
    delete FCK._ActiveColorPanelType;
};
FCKTextColorCommand.prototype.GetState = function () {
    return 0;
};
function FCKTextColorCommand_OnMouseOver() {
    this.className = 'ColorSelected';
};
function FCKTextColorCommand_OnMouseOut() {
    this.className = 'ColorDeselected';
};
function FCKTextColorCommand_OnClick() {
    this.className = 'ColorDeselected';
    this.Command.SetColor('#' + this.Color);
    this.Command._Panel.Hide();
};
function FCKTextColorCommand_AutoOnClick() {
    this.className = 'ColorDeselected';
    this.Command.SetColor('');
    this.Command._Panel.Hide();
};
FCKTextColorCommand.prototype._CreatePanelBody = function (A, B) {
    function CreateSelectionDiv() {
        var C = A.createElement("DIV");
        C.className = 'ColorDeselected';
        C.onmouseover = FCKTextColorCommand_OnMouseOver;
        C.onmouseout = FCKTextColorCommand_OnMouseOut;
        return C;
    };
    var D = B.appendChild(A.createElement("TABLE"));
    D.className = 'ForceBaseFont';
    D.style.tableLayout = 'fixed';
    D.cellPadding = 0;
    D.cellSpacing = 0;
    D.border = 0;
    D.width = 150;
    var E = D.insertRow(-1).insertCell(-1);
    E.colSpan = 8;
    var C = E.appendChild(CreateSelectionDiv());
    C.innerHTML = '<table cellspacing="0" cellpadding="0" width="100%" border="0">\n			<tr>\n				<td><div class="ColorBoxBorder"><div class="ColorBox" style="background-color: #000000"></div></div></td>\n				<td nowrap width="100%" align="center">' + FCKLang.ColorAutomatic + '</td>\n			</tr>\n		</table>';
    C.Command = this;
    C.onclick = FCKTextColorCommand_AutoOnClick;
    var G = FCKConfig.FontColors.toString().split(',');
    var H = 0;
    while (H < G.length) {
        var I = D.insertRow(-1);
        for (var i = 0; i < 8 && H < G.length; i++, H++) {
            C = I.insertCell(-1).appendChild(CreateSelectionDiv());
            C.Color = G[H];
            C.innerHTML = '<div class="ColorBoxBorder"><div class="ColorBox" style="background-color: #' + G[H] + '"></div></div>';
            C.Command = this;
            C.onclick = FCKTextColorCommand_OnClick;
        }
    }
}
var FCKPastePlainTextCommand = function () {
    this.Name = 'PasteText';
};
FCKPastePlainTextCommand.prototype.Execute = function () {
    FCK.PasteAsPlainText();
};
FCKPastePlainTextCommand.prototype.GetState = function () {
    return FCK.GetNamedCommandState('Paste');
};
var FCKCommands = FCK.Commands = {};
FCKCommands.LoadedCommands = {};
FCKCommands.RegisterCommand = function (A, B) {
    this.LoadedCommands[A] = B;
};
FCKCommands.GetCommand = function (A) {
    var B = FCKCommands.LoadedCommands[A];
    if (B) return B;
    switch (A) {
        case 'Link':
            B = new FCKDialogCommand('Link', FCKLang.DlgLnkWindowTitle, 'dialog/fck_link.html', 400, 240);
            break;
        case 'Unlink':
            B = new FCKUnlinkCommand();
            break;
        case 'About':
            B = new FCKDialogCommand('About', FCKLang.About, 'dialog/fck_about.html', 400, 280);
            break;
        case 'Image':
            B = new FCKDialogCommand('Image', FCKLang.DlgImgTitle, 'dialog/fck_image.html', 450, 260);
            break;
        case 'Flash':
            B = new FCKDialogCommand('Flash', FCKLang.DlgFlashTitle, 'dialog/fck_flash.html', 450, 240);
            break;
        case 'Table':
            B = new FCKDialogCommand('Table', FCKLang.DlgTableTitle, 'dialog/fck_table.html', 450, 240);
            break;
        case 'FontName':
            B = new FCKFontNameCommand();
            break;
        case 'FontSize':
            B = new FCKFontSizeCommand();
            break;
        case 'Source':
            B = new FCKSourceCommand();
            break;
        case 'Preview':
            B = new FCKPreviewCommand();
            break;
        case 'TextColor':
            B = new FCKTextColorCommand('ForeColor');
            break;
        case 'BGColor':
            B = new FCKTextColorCommand('BackColor');
            break;
        case 'Paste':
            B = new FCKPasteCommand();
            break;
        case 'PasteText':
            B = new FCKPastePlainTextCommand();
            break;
        case 'Undo':
            B = new FCKUndoCommand();
            break;
        case 'Redo':
            B = new FCKRedoCommand();
            break;
        case 'Undefined':
            B = new FCKUndefinedCommand();
            break;
        default:
            if (FCKRegexLib.NamedCommands.test(A)) B = new FCKNamedCommand(A); else {
                alert(FCKLang.UnknownCommand.replace(/%1/g, A));
                return null;
            }
    }
    ;
    FCKCommands.LoadedCommands[A] = B;
    return B;
}
var FCKPanel = function (A) {
    this.IsRTL = (FCKLang.Dir == 'rtl');
    this._LockCounter = 0;
    this._Window = A || window;
    var B;
    if (FCKBrowserInfo.IsIE) {
        this._Popup = this._Window.createPopup();
        B = this.Document = this._Popup.document;
        FCK.IECleanup.AddItem(this, FCKPanel_Cleanup);
    } else {
        var C = this._IFrame = this._Window.document.createElement('iframe');
        C.src = 'javascript:void(0)';
        C.allowTransparency = true;
        C.frameBorder = '0';
        C.scrolling = 'no';
        C.style.position = 'absolute';
        C.style.zIndex = FCKConfig.FloatingPanelsZIndex;
        C.width = C.height = 0;
        if (this._Window == window.parent && window.frameElement) window.frameElement.parentNode.insertBefore(C, window.frameElement); else this._Window.document.body.appendChild(C);
        var D = C.contentWindow;
        B = this.Document = D.document;
        var E = '';
        if (FCKBrowserInfo.IsSafari) E = '<base href="' + window.document.location + '">';
        B.open();
        B.write('<html><head>' + E + '<\/head><body style="margin:0px;padding:0px;"><\/body><\/html>');
        B.close();
        FCKTools.AddEventListenerEx(D, 'focus', FCKPanel_Window_OnFocus, this);
        FCKTools.AddEventListenerEx(D, 'blur', FCKPanel_Window_OnBlur, this);
    }
    ;
    B.dir = FCKLang.Dir;
    this.MainNode = B.body.appendChild(B.createElement('DIV'));
    this.MainNode.style.cssFloat = this.IsRTL ? 'right' : 'left';
};
FCKPanel.prototype.AppendStyleSheet = function (A) {
    FCKTools.AppendStyleSheet(this.Document, A);
};
FCKPanel.prototype.Preload = function (x, y, A) {
    if (this._Popup) this._Popup.show(x, y, 0, 0, A);
};
FCKPanel.prototype.Show = function (x, y, A, B, C) {
    var D;
    if (this._Popup) {
        this._Popup.show(x, y, 0, 0, A);
        this.MainNode.style.width = B ? B + 'px' : '';
        this.MainNode.style.height = C ? C + 'px' : '';
        D = this.MainNode.offsetWidth;
        if (this.IsRTL) {
            if (A) x = (x * -1) + A.offsetWidth - D;
        }
        ;
        this._Popup.show(x, y, D, this.MainNode.offsetHeight, A);
        if (this.OnHide) {
            if (this._Timer) CheckPopupOnHide.call(this, true);
            this._Timer = FCKTools.SetInterval(CheckPopupOnHide, 100, this);
        }
    } else {
        if (typeof(FCKFocusManager) != 'undefined') FCKFocusManager.Lock();
        if (this.ParentPanel) this.ParentPanel.Lock();
        this.MainNode.style.width = B ? B + 'px' : '';
        this.MainNode.style.height = C ? C + 'px' : '';
        D = this.MainNode.offsetWidth;
        if (!B)    this._IFrame.width = 1;
        if (!C)    this._IFrame.height = 1;
        D = this.MainNode.offsetWidth;
        var E = FCKTools.GetElementPosition(A.nodeType == 9 ? (FCKTools.IsStrictMode(A) ? A.documentElement : A.body) : A, this._Window);
        if (this.IsRTL) x = (x * -1);
        x += E.X;
        y += E.Y;
        if (this.IsRTL) {
            if (A) x = x + A.offsetWidth - D;
        } else {
            var F = FCKTools.GetViewPaneSize(this._Window);
            var G = FCKTools.GetScrollPosition(this._Window);
            var H = F.Height + G.Y;
            var I = F.Width + G.X;
            if ((x + D) > I) x -= x + D - I;
            if ((y + this.MainNode.offsetHeight) > H) y -= y + this.MainNode.offsetHeight - H;
        }
        ;
        if (x < 0) x = 0;
        this._IFrame.style.left = x + 'px';
        this._IFrame.style.top = y + 'px';
        var J = D;
        var K = this.MainNode.offsetHeight;
        this._IFrame.width = J;
        this._IFrame.height = K;
        this._IFrame.contentWindow.focus();
    }
    ;
    this._IsOpened = true;
    FCKTools.RunFunction(this.OnShow, this);
};
FCKPanel.prototype.Hide = function (A) {
    if (this._Popup) this._Popup.hide(); else {
        if (!this._IsOpened) return;
        if (typeof(FCKFocusManager) != 'undefined') FCKFocusManager.Unlock();
        this._IFrame.width = this._IFrame.height = 0;
        this._IsOpened = false;
        if (this.ParentPanel) this.ParentPanel.Unlock();
        if (!A) FCKTools.RunFunction(this.OnHide, this);
    }
};
FCKPanel.prototype.CheckIsOpened = function () {
    if (this._Popup) return this._Popup.isOpen; else return this._IsOpened;
};
FCKPanel.prototype.CreateChildPanel = function () {
    var A = this._Popup ? FCKTools.GetDocumentWindow(this.Document) : this._Window;
    var B = new FCKPanel(A);
    B.ParentPanel = this;
    return B;
};
FCKPanel.prototype.Lock = function () {
    this._LockCounter++;
};
FCKPanel.prototype.Unlock = function () {
    if (--this._LockCounter == 0 && !this.HasFocus) this.Hide();
};
function FCKPanel_Window_OnFocus(e, A) {
    A.HasFocus = true;
};
function FCKPanel_Window_OnBlur(e, A) {
    A.HasFocus = false;
    if (A._LockCounter == 0) FCKTools.RunFunction(A.Hide, A);
};
function CheckPopupOnHide(A) {
    if (A || !this._Popup.isOpen) {
        window.clearInterval(this._Timer);
        this._Timer = null;
        FCKTools.RunFunction(this.OnHide, this);
    }
};
function FCKPanel_Cleanup() {
    this._Popup = null;
    this._Window = null;
    this.Document = null;
    this.MainNode = null;
}
var FCKIcon = function (A) {
    var B = A ? typeof(A) : 'undefined';
    switch (B) {
        case 'number':
            this.Path = FCKConfig.SkinPath + 'fck_strip.gif';
            this.Size = 16;
            this.Position = A;
            break;
        case 'undefined':
            this.Path = FCK_SPACER_PATH;
            break;
        case 'string':
            this.Path = A;
            break;
        default:
            this.Path = A[0];
            this.Size = A[1];
            this.Position = A[2];
    }
};
FCKIcon.prototype.CreateIconElement = function (A) {
    var B, eIconImage;
    if (this.Position) {
        var C = '-' + ((this.Position - 1) * this.Size) + 'px';
        if (FCKBrowserInfo.IsIE) {
            B = A.createElement('DIV');
            eIconImage = B.appendChild(A.createElement('IMG'));
            eIconImage.src = this.Path;
            eIconImage.style.top = C;
        } else {
            B = A.createElement('IMG');
            B.src = FCK_SPACER_PATH;
            B.style.backgroundPosition = '0px ' + C;
            B.style.backgroundImage = 'url(' + this.Path + ')';
        }
    } else {
        if (FCKBrowserInfo.IsIE) {
            B = A.createElement('DIV');
            eIconImage = B.appendChild(A.createElement('IMG'));
            eIconImage.src = this.Path ? this.Path : FCK_SPACER_PATH;
        } else {
            B = A.createElement('IMG');
            B.src = this.Path ? this.Path : FCK_SPACER_PATH;
        }
    }
    ;
    B.className = 'TB_Button_Image';
    return B;
}
var FCKToolbarButtonUI = function (A, B, C, D, E, F) {
    this.Name = A;
    this.Label = B || A;
    this.Tooltip = C || this.Label;
    this.Style = E || 0;
    this.State = F || 0;
    this.Icon = new FCKIcon(D);
    if (FCK.IECleanup) FCK.IECleanup.AddItem(this, FCKToolbarButtonUI_Cleanup);
};
FCKToolbarButtonUI.prototype._CreatePaddingElement = function (A) {
    var B = A.createElement('IMG');
    B.className = 'TB_Button_Padding';
    B.src = FCK_SPACER_PATH;
    return B;
};
FCKToolbarButtonUI.prototype.Create = function (A) {
    var B = this.MainElement;
    if (B) {
        FCKToolbarButtonUI_Cleanup.call(this);
        if (B.parentNode) B.parentNode.removeChild(B);
        B = this.MainElement = null;
    }
    ;
    var C = FCKTools.GetElementDocument(A);
    B = this.MainElement = C.createElement('DIV');
    B._FCKButton = this;
    B.title = this.Tooltip;
    if (FCKBrowserInfo.IsGecko) B.onmousedown = FCKTools.CancelEvent;
    this.ChangeState(this.State, true);
    if (this.Style == 0 && !this.ShowArrow) {
        B.appendChild(this.Icon.CreateIconElement(C));
    } else {
        var D = B.appendChild(C.createElement('TABLE'));
        D.cellPadding = 0;
        D.cellSpacing = 0;
        var E = D.insertRow(-1);
        var F = E.insertCell(-1);
        if (this.Style == 0) F.appendChild(this.Icon.CreateIconElement(C)); else F.appendChild(this._CreatePaddingElement(C));
        if (this.Style == 1) {
            F = E.insertCell(-1);
            F.className = 'TB_Button_Text';
            F.noWrap = true;
            F.appendChild(C.createTextNode(this.Label));
        }
        ;
        if (this.ShowArrow) {
            if (this.Style != 0) {
                E.insertCell(-1).appendChild(this._CreatePaddingElement(C));
            }
            ;
            F = E.insertCell(-1);
            var G = F.appendChild(C.createElement('IMG'));
            G.src = FCKConfig.SkinPath + 'images/toolbar.buttonarrow.gif';
            G.width = 5;
            G.height = 3;
        }
        ;
        F = E.insertCell(-1);
        F.appendChild(this._CreatePaddingElement(C));
    }
    ;
    A.appendChild(B);
};
FCKToolbarButtonUI.prototype.ChangeState = function (A, B) {
    if (!B && this.State == A) return;
    var e = this.MainElement;
    switch (parseInt(A, 10)) {
        case 0:
            e.className = 'TB_Button_Off';
            e.onmouseover = FCKToolbarButton_OnMouseOverOff;
            e.onmouseout = FCKToolbarButton_OnMouseOutOff;
            e.onclick = FCKToolbarButton_OnClick;
            break;
        case 1:
            e.className = 'TB_Button_On';
            e.onmouseover = FCKToolbarButton_OnMouseOverOn;
            e.onmouseout = FCKToolbarButton_OnMouseOutOn;
            e.onclick = FCKToolbarButton_OnClick;
            break;
        case -1:
            e.className = 'TB_Button_Disabled';
            e.onmouseover = null;
            e.onmouseout = null;
            e.onclick = null;
            break;
    }
    ;
    this.State = A;
};
function FCKToolbarButtonUI_Cleanup() {
    if (this.MainElement) {
        this.MainElement._FCKButton = null;
        this.MainElement = null;
    }
};
function FCKToolbarButton_OnMouseOverOn() {
    this.className = 'TB_Button_On_Over';
};
function FCKToolbarButton_OnMouseOutOn() {
    this.className = 'TB_Button_On';
};
function FCKToolbarButton_OnMouseOverOff() {
    this.className = 'TB_Button_Off_Over';
};
function FCKToolbarButton_OnMouseOutOff() {
    this.className = 'TB_Button_Off';
};
function FCKToolbarButton_OnClick(e) {
    if (this._FCKButton.OnClick) this._FCKButton.OnClick(this._FCKButton);
};
var FCKToolbarButton = function (A, B, C, D, E, F, G) {
    this.CommandName = A;
    this.Label = B;
    this.Tooltip = C;
    this.Style = D;
    this.SourceView = E ? true : false;
    this.ContextSensitive = F ? true : false;
    if (G == null) this.IconPath = FCKConfig.SkinPath + 'toolbar/' + A.toLowerCase() + '.gif'; else if (typeof(G) == 'number') this.IconPath = [FCKConfig.SkinPath + 'fck_strip.gif', 16, G];
};
FCKToolbarButton.prototype.Create = function (A) {
    this._UIButton = new FCKToolbarButtonUI(this.CommandName, this.Label, this.Tooltip, this.IconPath, this.Style);
    this._UIButton.OnClick = this.Click;
    this._UIButton._ToolbarButton = this;
    this._UIButton.Create(A);
};
FCKToolbarButton.prototype.RefreshState = function () {
    var A = FCK.ToolbarSet.CurrentInstance.Commands.GetCommand(this.CommandName).GetState();
    if (A == this._UIButton.State) return;
    this._UIButton.ChangeState(A);
};
FCKToolbarButton.prototype.Click = function () {
    var A = this._ToolbarButton || this;
    FCK.ToolbarSet.CurrentInstance.Commands.GetCommand(A.CommandName).Execute();
};
FCKToolbarButton.prototype.Enable = function () {
    this.RefreshState();
};
FCKToolbarButton.prototype.Disable = function () {
    this._UIButton.ChangeState(-1);
}
var FCKSpecialCombo = function (A, B, C, D, E) {
    this.FieldWidth = B || 100;
    this.PanelWidth = C || 150;
    this.PanelMaxHeight = D || 150;
    this.Label = '&nbsp;';
    this.Caption = A;
    this.Tooltip = A;
    this.Style = 0;
    this.Enabled = true;
    this.Items = {};
    this._Panel = new FCKPanel(E || window);
    this._Panel.AppendStyleSheet(FCKConfig.SkinPath + 'fck_editor.css');
    this._PanelBox = this._Panel.MainNode.appendChild(this._Panel.Document.createElement('DIV'));
    this._PanelBox.className = 'SC_Panel';
    this._PanelBox.style.width = this.PanelWidth + 'px';
    this._PanelBox.innerHTML = '<table cellpadding="0" cellspacing="0" width="100%" style="TABLE-LAYOUT: fixed"><tr><td nowrap></td></tr></table>';
    this._ItemsHolderEl = this._PanelBox.getElementsByTagName('TD')[0];
    if (FCK.IECleanup) FCK.IECleanup.AddItem(this, FCKSpecialCombo_Cleanup);
};
function FCKSpecialCombo_ItemOnMouseOver() {
    this.className += ' SC_ItemOver';
};
function FCKSpecialCombo_ItemOnMouseOut() {
    this.className = this.originalClass;
};
function FCKSpecialCombo_ItemOnClick() {
    this.className = this.originalClass;
    this.FCKSpecialCombo._Panel.Hide();
    this.FCKSpecialCombo.SetLabel(this.FCKItemLabel);
    if (typeof(this.FCKSpecialCombo.OnSelect) == 'function') this.FCKSpecialCombo.OnSelect(this.FCKItemID, this);
};
FCKSpecialCombo.prototype.AddItem = function (A, B, C, D) {
    var E = this._ItemsHolderEl.appendChild(this._Panel.Document.createElement('DIV'));
    E.className = E.originalClass = 'SC_Item';
    E.innerHTML = B;
    E.FCKItemID = A;
    E.FCKItemLabel = C || A;
    E.FCKSpecialCombo = this;
    E.Selected = false;
    if (FCKBrowserInfo.IsIE) E.style.width = '100%';
    if (D) E.style.backgroundColor = D;
    E.onmouseover = FCKSpecialCombo_ItemOnMouseOver;
    E.onmouseout = FCKSpecialCombo_ItemOnMouseOut;
    E.onclick = FCKSpecialCombo_ItemOnClick;
    this.Items[A.toString().toLowerCase()] = E;
    return E;
};
FCKSpecialCombo.prototype.SelectItem = function (A) {
    A = A ? A.toString().toLowerCase() : '';
    var B = this.Items[A];
    if (B) {
        B.className = B.originalClass = 'SC_ItemSelected';
        B.Selected = true;
    }
};
FCKSpecialCombo.prototype.SelectItemByLabel = function (A, B) {
    for (var C in this.Items) {
        var D = this.Items[C];
        if (D.FCKItemLabel == A) {
            D.className = D.originalClass = 'SC_ItemSelected';
            D.Selected = true;
            if (B) this.SetLabel(A);
        }
    }
};
FCKSpecialCombo.prototype.DeselectAll = function (A) {
    for (var i in this.Items) {
        this.Items[i].className = this.Items[i].originalClass = 'SC_Item';
        this.Items[i].Selected = false;
    }
    ;
    if (A) this.SetLabel('');
};
FCKSpecialCombo.prototype.SetLabelById = function (A) {
    A = A ? A.toString().toLowerCase() : '';
    var B = this.Items[A];
    this.SetLabel(B ? B.FCKItemLabel : '');
};
FCKSpecialCombo.prototype.SetLabel = function (A) {
    this.Label = A.length == 0 ? '&nbsp;' : A;
    if (this._LabelEl) {
        this._LabelEl.innerHTML = this.Label;
        FCKTools.DisableSelection(this._LabelEl);
    }
};
FCKSpecialCombo.prototype.SetEnabled = function (A) {
    this.Enabled = A;
    this._OuterTable.className = A ? '' : 'SC_FieldDisabled';
};
FCKSpecialCombo.prototype.Create = function (A) {
    var B = FCKTools.GetElementDocument(A);
    var C = this._OuterTable = A.appendChild(B.createElement('TABLE'));
    C.cellPadding = 0;
    C.cellSpacing = 0;
    C.insertRow(-1);
    var D;
    var E;
    switch (this.Style) {
        case 0:
            D = 'TB_ButtonType_Icon';
            E = false;
            break;
        case 1:
            D = 'TB_ButtonType_Text';
            E = false;
            break;
    }
    ;
    if (this.Caption && this.Caption.length > 0 && E) {
        var F = C.rows[0].insertCell(-1);
        F.innerHTML = this.Caption;
        F.className = 'SC_FieldCaption';
    }
    ;
    var G = FCKTools.AppendElement(C.rows[0].insertCell(-1), 'div');
    if (E) {
        G.className = 'SC_Field';
        G.style.width = this.FieldWidth + 'px';
        G.innerHTML = '<table width="100%" cellpadding="0" cellspacing="0" style="TABLE-LAYOUT: fixed;"><tbody><tr><td class="SC_FieldLabel"><label>&nbsp;</label></td><td class="SC_FieldButton">&nbsp;</td></tr></tbody></table>';
        this._LabelEl = G.getElementsByTagName('label')[0];
        this._LabelEl.innerHTML = this.Label;
    } else {
        G.className = 'TB_Button_Off';
        G.innerHTML = '<table title="' + this.Tooltip + '" class="' + D + '" cellspacing="0" cellpadding="0" border="0"><tr><td><img class="TB_Button_Padding" src="' + FCK_SPACER_PATH + '" /></td><td class="TB_Text">' + this.Caption + '</td><td><img class="TB_Button_Padding" src="' + FCK_SPACER_PATH + '" /></td><td class="TB_ButtonArrow"><img src="' + FCKConfig.SkinPath + 'images/toolbar.buttonarrow.gif" width="5" height="3"></td><td><img class="TB_Button_Padding" src="' + FCK_SPACER_PATH + '" /></td></tr></table>';
    }
    ;
    G.SpecialCombo = this;
    G.onmouseover = FCKSpecialCombo_OnMouseOver;
    G.onmouseout = FCKSpecialCombo_OnMouseOut;
    G.onclick = FCKSpecialCombo_OnClick;
    FCKTools.DisableSelection(this._Panel.Document.body);
};
function FCKSpecialCombo_Cleanup() {
    this._LabelEl = null;
    this._OuterTable = null;
    this._ItemsHolderEl = null;
    this._PanelBox = null;
    if (this.Items) {
        for (var A in this.Items) this.Items[A] = null;
    }
};
function FCKSpecialCombo_OnMouseOver() {
    if (this.SpecialCombo.Enabled) {
        switch (this.SpecialCombo.Style) {
            case 0:
                this.className = 'TB_Button_On_Over';
                break;
            case 1:
                this.className = 'TB_Button_On_Over';
                break;
        }
    }
};
function FCKSpecialCombo_OnMouseOut() {
    switch (this.SpecialCombo.Style) {
        case 0:
            this.className = 'TB_Button_Off';
            break;
        case 1:
            this.className = 'TB_Button_Off';
            break;
    }
};
function FCKSpecialCombo_OnClick(e) {
    var A = this.SpecialCombo;
    if (A.Enabled) {
        var B = A._Panel;
        var C = A._PanelBox;
        var D = A._ItemsHolderEl;
        var E = A.PanelMaxHeight;
        if (A.OnBeforeClick) A.OnBeforeClick(A);
        if (FCKBrowserInfo.IsIE) B.Preload(0, this.offsetHeight, this);
        if (D.offsetHeight > E) C.style.height = E + 'px'; else C.style.height = '';
        B.Show(0, this.offsetHeight, this);
    }
};
var FCKToolbarSpecialCombo = function () {
    this.SourceView = false;
    this.ContextSensitive = true;
    this._LastValue = null;
};
function FCKToolbarSpecialCombo_OnSelect(A, B) {
    FCK.ToolbarSet.CurrentInstance.Commands.GetCommand(this.CommandName).Execute(A, B);
};
FCKToolbarSpecialCombo.prototype.Create = function (A) {
    this._Combo = new FCKSpecialCombo(this.GetLabel(), this.FieldWidth, this.PanelWidth, this.PanelMaxHeight, FCKBrowserInfo.IsIE ? window : FCKTools.GetElementWindow(A).parent);
    this._Combo.Tooltip = this.Tooltip;
    this._Combo.Style = this.Style;
    this.CreateItems(this._Combo);
    this._Combo.Create(A);
    this._Combo.CommandName = this.CommandName;
    this._Combo.OnSelect = FCKToolbarSpecialCombo_OnSelect;
};
function FCKToolbarSpecialCombo_RefreshActiveItems(A, B) {
    A.DeselectAll();
    A.SelectItem(B);
    A.SetLabelById(B);
};
FCKToolbarSpecialCombo.prototype.RefreshState = function () {
    var A;
    var B = FCK.ToolbarSet.CurrentInstance.Commands.GetCommand(this.CommandName).GetState();
    if (B != -1) {
        A = 1;
        if (this.RefreshActiveItems) this.RefreshActiveItems(this._Combo, B); else {
            if (this._LastValue != B) {
                this._LastValue = B;
                FCKToolbarSpecialCombo_RefreshActiveItems(this._Combo, B);
            }
        }
    } else A = -1;
    if (A == this.State) return;
    if (A == -1) {
        this._Combo.DeselectAll();
        this._Combo.SetLabel('');
    }
    ;
    this.State = A;
    this._Combo.SetEnabled(A != -1);
};
FCKToolbarSpecialCombo.prototype.Enable = function () {
    this.RefreshState();
};
FCKToolbarSpecialCombo.prototype.Disable = function () {
    this.State = -1;
    this._Combo.DeselectAll();
    this._Combo.SetLabel('');
    this._Combo.SetEnabled(false);
};
var FCKToolbarFontsCombo = function (A, B) {
    this.CommandName = 'FontName';
    this.Label = this.GetLabel();
    this.Tooltip = A ? A : this.Label;
    this.Style = B ? B : 0;
};
FCKToolbarFontsCombo.prototype = new FCKToolbarSpecialCombo;
FCKToolbarFontsCombo.prototype.GetLabel = function () {
    return FCKLang.Font;
};
FCKToolbarFontsCombo.prototype.CreateItems = function (A) {
    var B = FCKConfig.FontNames.split(';');
    for (var i = 0; i < B.length; i++) this._Combo.AddItem(B[i], '<font face="' + B[i] + '" style="font-size: 12px">' + B[i] + '</font>');
}
var FCKToolbarFontSizeCombo = function (A, B) {
    this.CommandName = 'FontSize';
    this.Label = this.GetLabel();
    this.Tooltip = A ? A : this.Label;
    this.Style = B ? B : 0;
};
FCKToolbarFontSizeCombo.prototype = new FCKToolbarSpecialCombo;
FCKToolbarFontSizeCombo.prototype.GetLabel = function () {
    return FCKLang.FontSize;
};
FCKToolbarFontSizeCombo.prototype.CreateItems = function (A) {
    A.FieldWidth = 70;
    var B = FCKConfig.FontSizes.split(';');
    for (var i = 0; i < B.length; i++) {
        var C = B[i].split('/');
        this._Combo.AddItem(C[0], '<font size="' + C[0] + '">' + C[1] + '</font>', C[1]);
    }
}
var FCKToolbarPanelButton = function (A, B, C, D, E) {
    this.CommandName = A;
    var F;
    if (E == null) F = FCKConfig.SkinPath + 'toolbar/' + A.toLowerCase() + '.gif'; else if (typeof(E) == 'number') F = [FCKConfig.SkinPath + 'fck_strip.gif', 16, E];
    var G = this._UIButton = new FCKToolbarButtonUI(A, B, C, F, D);
    G._FCKToolbarPanelButton = this;
    G.ShowArrow = true;
    G.OnClick = FCKToolbarPanelButton_OnButtonClick;
};
FCKToolbarPanelButton.prototype.TypeName = 'FCKToolbarPanelButton';
FCKToolbarPanelButton.prototype.Create = function (A) {
    A.className += 'Menu';
    this._UIButton.Create(A);
    var B = FCK.ToolbarSet.CurrentInstance.Commands.GetCommand(this.CommandName)._Panel;
    B._FCKToolbarPanelButton = this;
    var C = B.Document.body.appendChild(B.Document.createElement('div'));
    C.style.position = 'absolute';
    C.style.top = '0px';
    var D = this.LineImg = C.appendChild(B.Document.createElement('IMG'));
    D.className = 'TB_ConnectionLine';
    D.src = FCK_SPACER_PATH;
    B.OnHide = FCKToolbarPanelButton_OnPanelHide;
};
function FCKToolbarPanelButton_OnButtonClick(A) {
    var B = this._FCKToolbarPanelButton;
    var e = B._UIButton.MainElement;
    B._UIButton.ChangeState(1);
    B.LineImg.style.width = (e.offsetWidth - 2) + 'px';
    FCK.ToolbarSet.CurrentInstance.Commands.GetCommand(B.CommandName).Execute(0, e.offsetHeight - 1, e);
};
function FCKToolbarPanelButton_OnPanelHide() {
    var A = this._FCKToolbarPanelButton;
    A._UIButton.ChangeState(0);
};
FCKToolbarPanelButton.prototype.RefreshState = FCKToolbarButton.prototype.RefreshState;
FCKToolbarPanelButton.prototype.Enable = FCKToolbarButton.prototype.Enable;
FCKToolbarPanelButton.prototype.Disable = FCKToolbarButton.prototype.Disable;
var FCKToolbarItems = {};
FCKToolbarItems.LoadedItems = {};
FCKToolbarItems.RegisterItem = function (A, B) {
    this.LoadedItems[A] = B;
};
FCKToolbarItems.GetItem = function (A) {
    var B = FCKToolbarItems.LoadedItems[A];
    if (B) return B;
    switch (A) {
        case 'Source':
            B = new FCKToolbarButton('Source', FCKLang.Source, null, null, true, true, 1);
            break;
        case 'Preview':
            B = new FCKToolbarButton('Preview', FCKLang.Preview, null, null, true, null, 5);
            break;
        case 'About':
            B = new FCKToolbarButton('About', FCKLang.About, null, null, true, null, 47);
            break;
        case 'Cut':
            B = new FCKToolbarButton('Cut', FCKLang.Cut, null, null, false, true, 7);
            break;
        case 'Copy':
            B = new FCKToolbarButton('Copy', FCKLang.Copy, null, null, false, true, 8);
            break;
        case 'Paste':
            B = new FCKToolbarButton('Paste', FCKLang.Paste, null, null, false, true, 9);
            break;
        case 'PasteText':
            B = new FCKToolbarButton('PasteText', FCKLang.PasteText, null, null, false, true, 10);
            break;
        case 'Undo':
            B = new FCKToolbarButton('Undo', FCKLang.Undo, null, null, false, true, 14);
            break;
        case 'Redo':
            B = new FCKToolbarButton('Redo', FCKLang.Redo, null, null, false, true, 15);
            break;
        case 'Bold':
            B = new FCKToolbarButton('Bold', FCKLang.Bold, null, null, false, true, 20);
            break;
        case 'Italic':
            B = new FCKToolbarButton('Italic', FCKLang.Italic, null, null, false, true, 21);
            break;
        case 'Underline':
            B = new FCKToolbarButton('Underline', FCKLang.Underline, null, null, false, true, 22);
            break;
        case 'StrikeThrough':
            B = new FCKToolbarButton('StrikeThrough', FCKLang.StrikeThrough, null, null, false, true, 23);
            break;
        case 'OrderedList':
            B = new FCKToolbarButton('InsertOrderedList', FCKLang.NumberedListLbl, FCKLang.NumberedList, null, false, true, 26);
            break;
        case 'UnorderedList':
            B = new FCKToolbarButton('InsertUnorderedList', FCKLang.BulletedListLbl, FCKLang.BulletedList, null, false, true, 27);
            break;
        case 'Outdent':
            B = new FCKToolbarButton('Outdent', FCKLang.DecreaseIndent, null, null, false, true, 28);
            break;
        case 'Indent':
            B = new FCKToolbarButton('Indent', FCKLang.IncreaseIndent, null, null, false, true, 29);
            break;
        case 'Link':
            B = new FCKToolbarButton('Link', FCKLang.InsertLinkLbl, FCKLang.InsertLink, null, false, true, 34);
            break;
        case 'Unlink':
            B = new FCKToolbarButton('Unlink', FCKLang.RemoveLink, null, null, false, true, 35);
            break;
        case 'Image':
            B = new FCKToolbarButton('Image', FCKLang.InsertImageLbl, FCKLang.InsertImage, null, false, true, 37);
            break;
        case 'Flash':
            B = new FCKToolbarButton('Flash', FCKLang.InsertFlashLbl, FCKLang.InsertFlash, null, false, true, 38);
            break;
        case 'Table':
            B = new FCKToolbarButton('Table', FCKLang.InsertTableLbl, FCKLang.InsertTable, null, false, true, 39);
            break;
        case 'Rule':
            B = new FCKToolbarButton('InsertHorizontalRule', FCKLang.InsertLineLbl, FCKLang.InsertLine, null, false, true, 40);
            break;
        case 'JustifyLeft':
            B = new FCKToolbarButton('JustifyLeft', FCKLang.LeftJustify, null, null, false, true, 30);
            break;
        case 'JustifyCenter':
            B = new FCKToolbarButton('JustifyCenter', FCKLang.CenterJustify, null, null, false, true, 31);
            break;
        case 'JustifyRight':
            B = new FCKToolbarButton('JustifyRight', FCKLang.RightJustify, null, null, false, true, 32);
            break;
        case 'FontName':
            B = new FCKToolbarFontsCombo();
            break;
        case 'FontSize':
            B = new FCKToolbarFontSizeCombo();
            break;
        case 'TextColor':
            B = new FCKToolbarPanelButton('TextColor', FCKLang.TextColor, null, null, 45);
            break;
        case 'BGColor':
            B = new FCKToolbarPanelButton('BGColor', FCKLang.BGColor, null, null, 46);
            break;
        default:
            alert(FCKLang.UnknownToolbarItem.replace(/%1/g, A));
            return null;
    }
    ;
    FCKToolbarItems.LoadedItems[A] = B;
    return B;
}
var FCKToolbar = function () {
    this.Items = [];
    if (FCK.IECleanup) FCK.IECleanup.AddItem(this, FCKToolbar_Cleanup);
};
FCKToolbar.prototype.AddItem = function (A) {
    return this.Items[this.Items.length] = A;
};
FCKToolbar.prototype.AddButton = function (A, B, C, D, E, F) {
    if (typeof(D) == 'number') D = [this.DefaultIconsStrip, this.DefaultIconSize, D];
    var G = new FCKToolbarButtonUI(A, B, C, D, E, F);
    G._FCKToolbar = this;
    G.OnClick = FCKToolbar_OnItemClick;
    return this.AddItem(G);
};
function FCKToolbar_OnItemClick(A) {
    var B = A._FCKToolbar;
    if (B.OnItemClick) B.OnItemClick(B, A);
};
FCKToolbar.prototype.AddSeparator = function () {
    this.AddItem(new FCKToolbarSeparator());
};
FCKToolbar.prototype.Create = function (A) {
    if (this.MainElement) {
        if (this.MainElement.parentNode) this.MainElement.parentNode.removeChild(this.MainElement);
        this.MainElement = null;
    }
    ;
    var B = FCKTools.GetElementDocument(A);
    var e = this.MainElement = B.createElement('table');
    e.className = 'TB_Toolbar';
    e.style.styleFloat = e.style.cssFloat = (FCKLang.Dir == 'ltr' ? 'left' : 'right');
    e.dir = FCKLang.Dir;
    e.cellPadding = 0;
    e.cellSpacing = 0;
    this.RowElement = e.insertRow(-1);
    var C;
    if (!this.HideStart) {
        C = this.RowElement.insertCell(-1);
        C.appendChild(B.createElement('div')).className = 'TB_Start';
    }
    ;
    for (var i = 0; i < this.Items.length; i++) {
        this.Items[i].Create(this.RowElement.insertCell(-1));
    }
    ;
    if (!this.HideEnd) {
        C = this.RowElement.insertCell(-1);
        C.appendChild(B.createElement('div')).className = 'TB_End';
    }
    ;
    A.appendChild(e);
};
function FCKToolbar_Cleanup() {
    this.MainElement = null;
    this.RowElement = null;
};
var FCKToolbarSeparator = function () {
};
FCKToolbarSeparator.prototype.Create = function (A) {
    FCKTools.AppendElement(A, 'div').className = 'TB_Separator';
}
var FCKToolbarBreak = function () {
};
FCKToolbarBreak.prototype.Create = function (A) {
    var B = FCKTools.GetElementDocument(A).createElement('div');
    B.className = 'TB_Break';
    B.style.clear = FCKLang.Dir == 'rtl' ? 'left' : 'right';
    A.appendChild(B);
}
function FCKToolbarSet_Create(A) {
    var B;
    B = new FCKToolbarSet(document);
    B.CurrentInstance = FCK;
    FCK.AttachToOnSelectionChange(B.RefreshItemsState);
    return B;
};
function FCK_OnBlur(A) {
    var B = A.ToolbarSet;
    if (B.CurrentInstance == A) B.Disable();
};
function FCK_OnFocus(A) {
    var B = A.ToolbarSet;
    var C = A || FCK;
    B.CurrentInstance.FocusManager.RemoveWindow(B._IFrame.contentWindow);
    B.CurrentInstance = C;
    C.FocusManager.AddWindow(B._IFrame.contentWindow, true);
    B.Enable();
};
function FCKToolbarSet_Cleanup() {
    this._TargetElement = null;
    this._IFrame = null;
};
function FCKToolbarSet_Target_Cleanup() {
    this.__FCKToolbarSet = null;
};
var FCKToolbarSet = function (A) {
    this._Document = A;
    this._TargetElement = A.getElementById('xToolbar');
    this.Toolbars = [];
    this.IsLoaded = false;
    if (FCK.IECleanup) FCK.IECleanup.AddItem(this, FCKToolbarSet_Cleanup);
};
FCKToolbarSet.prototype.Load = function (A) {
    this.Name = A;
    this.Items = [];
    this.ItemsWysiwygOnly = [];
    this.ItemsContextSensitive = [];
    this._TargetElement.innerHTML = '';
    var B = FCKConfig.ToolbarSets[A];
    if (!B) {
        alert(FCKLang.UnknownToolbarSet.replace(/%1/g, A));
        return;
    }
    ;
    this.Toolbars = [];
    for (var x = 0; x < B.length; x++) {
        var C = B[x];
        if (!C) continue;
        var D;
        if (typeof(C) == 'string') {
            if (C == '/') D = new FCKToolbarBreak();
        } else {
            D = new FCKToolbar();
            for (var j = 0; j < C.length; j++) {
                var E = C[j];
                if (E == '-') D.AddSeparator(); else {
                    var F = FCKToolbarItems.GetItem(E);
                    if (F) {
                        D.AddItem(F);
                        this.Items.push(F);
                        if (!F.SourceView) this.ItemsWysiwygOnly.push(F);
                        if (F.ContextSensitive) this.ItemsContextSensitive.push(F);
                    }
                }
            }
        }
        ;
        D.Create(this._TargetElement);
        this.Toolbars[this.Toolbars.length] = D;
    }
    ;
    FCKTools.DisableSelection(this._Document.getElementById('xExpanded'));
    if (FCK.Status != 2) FCK.Events.AttachEvent('OnStatusChange', this.RefreshModeState); else this.RefreshModeState();
    this.IsLoaded = true;
    this.IsEnabled = true;
    FCKTools.RunFunction(this.OnLoad);
};
FCKToolbarSet.prototype.Enable = function () {
    if (this.IsEnabled) return;
    this.IsEnabled = true;
    var A = this.Items;
    for (var i = 0; i < A.length; i++) A[i].RefreshState();
};
FCKToolbarSet.prototype.Disable = function () {
    if (!this.IsEnabled) return;
    this.IsEnabled = false;
    var A = this.Items;
    for (var i = 0; i < A.length; i++) A[i].Disable();
};
FCKToolbarSet.prototype.RefreshModeState = function (A) {
    if (FCK.Status != 2) return;
    var B = A ? A.ToolbarSet : this;
    var C = B.ItemsWysiwygOnly;
    if (FCK.EditMode == 0) {
        for (var i = 0; i < C.length; i++) C[i].Enable();
        B.RefreshItemsState(A);
    } else {
        B.RefreshItemsState(A);
        for (var j = 0; j < C.length; j++) C[j].Disable();
    }
};
FCKToolbarSet.prototype.RefreshItemsState = function (A) {
    var B = (A ? A.ToolbarSet : this).ItemsContextSensitive;
    for (var i = 0; i < B.length; i++) B[i].RefreshState();
};
var FCKDialog = {};
FCKDialog.OpenDialog = function (A, B, C, D, E, F, G, H) {
    var I = {};
    I.Title = B;
    I.Page = C;
    I.Editor = window;
    I.CustomValue = F;
    var J = FCKConfig.BasePath + 'fckdialog.html';
    this.Show(I, A, J, D, E, G, H);
};
FCKDialog.Show = function (A, B, C, D, E, F, G) {
    if (!F) F = window;
    var H = 'help:no;scroll:no;status:no;resizable:' + (G ? 'yes' : 'no') + ';dialogWidth:' + D + 'px;dialogHeight:' + E + 'px';
    FCKFocusManager.Lock();
    var I = 'B';
    try {
        I = F.showModalDialog(C, A, H);
    } catch (e) {
    }
    ;
    if ('B' === I) alert(FCKLang.DialogBlocked);
    FCKFocusManager.Unlock();
};
var FCKPlugin = function (A, B, C) {
    this.Name = A;
    this.BasePath = C ? C : FCKConfig.PluginsPath;
    this.Path = this.BasePath + A + '/';
    if (!B || B.length == 0) this.AvailableLangs = []; else this.AvailableLangs = B.split(',');
};
FCKPlugin.prototype.Load = function () {
    if (this.AvailableLangs.length > 0) {
        var A;
        if (this.AvailableLangs.IndexOf(FCKLanguageManager.ActiveLanguage.Code) >= 0) A = FCKLanguageManager.ActiveLanguage.Code; else A = this.AvailableLangs[0];
        LoadScript(this.Path + 'lang/' + A + '.js');
    }
    ;
    LoadScript(this.Path + 'fckplugin.js');
}
var FCKPlugins = FCK.Plugins = {};
FCKPlugins.ItemsCount = 0;
FCKPlugins.Items = {};
FCKPlugins.Load = function () {
    var A = FCKPlugins.Items;
    for (var i = 0; i < FCKConfig.Plugins.Items.length; i++) {
        var B = FCKConfig.Plugins.Items[i];
        var C = A[B[0]] = new FCKPlugin(B[0], B[1], B[2]);
        FCKPlugins.ItemsCount++;
    }
    ;
    for (var s in A) A[s].Load();
    FCKPlugins.Load = null;
}
