var GECKO_BOGUS = '<br type="_moz">';

// Gets a element by its Id. Used for shorter coding.
function GetE(elementId) {
    return document.getElementById(elementId);
}

function ShowE(element, isVisible) {
    if (typeof( element ) == 'string')
        element = GetE(element);
    element.style.display = isVisible ? '' : 'none';
}

function SetAttribute(element, attName, attValue) {
    if (attValue == null || attValue.length == 0)
        element.removeAttribute(attName, 0);			// 0 : Case Insensitive
    else
        element.setAttribute(attName, attValue, 0);	// 0 : Case Insensitive
}

function GetAttribute(element, attName, valueIfNull) {
    var oAtt = element.attributes[attName];

    if (oAtt == null || !oAtt.specified)
        return valueIfNull ? valueIfNull : '';

    var oValue = element.getAttribute(attName, 2);

    if (oValue == null)
        oValue = oAtt.nodeValue;

    return ( oValue == null ? valueIfNull : oValue );
}

// Functions used by text fiels to accept numbers only.
function IsDigit(e) {
    if (!e)
        e = event;

    var iCode = ( e.keyCode || e.charCode );

    return (
        ( iCode >= 48 && iCode <= 57 )		// Numbers
            || (iCode >= 37 && iCode <= 40)		// Arrows
            || iCode == 8						// Backspace
            || iCode == 46						// Delete
        );
}

String.prototype.Trim = function () {
    return this.replace(/(^\s*)|(\s*$)/g, '');
}

String.prototype.StartsWith = function (value) {
    return ( this.substr(0, value.length) == value );
}

String.prototype.Remove = function (start, length) {
    var s = '';

    if (start > 0)
        s = this.substring(0, start);

    if (start + length < this.length)
        s += this.substring(start + length, this.length);

    return s;
}

String.prototype.ReplaceAll = function (searchArray, replaceArray) {
    var replaced = this;

    for (var i = 0; i < searchArray.length; i++) {
        replaced = replaced.replace(searchArray[i], replaceArray[i]);
    }

    return replaced;
}