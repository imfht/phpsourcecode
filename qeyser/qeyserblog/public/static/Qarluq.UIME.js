/*
 * Qarluq JavaScript Uighur Input Method v1.0
 *
 * Author: Adiljan.Yasin
 * http://lab.qarluq.com/
 *
 * Copyright 2012, Qarluq Media Tech Co.,ltd.
 * http://lab.qarluq.com/license/uime
 *
 * Date: Wed June 27 18:07:08 2012 +0800
 */
var notug = "[username][email][password][sort][click][url]";
var yesug = "";// "[yesugid][yesugid][yesugid]"
var bodyFonts = 'UKIJ Ekran';
var bodyDir = "rtl";
var openQuete = 0x00AB;
var closeQuete = 0x00BB;
var lastQuete = closeQuete;
var u_imu = 1;
var ctrl = false;
var UIMEInitCount = 0;
var UIMEInitMaxCount = 30000;
var UIMEInitTime = 350;

var Input = {
    34 : 34,
    47 : 1574,
    63 : 1567,
    44 : 1548,
    109 : 1605,
    77 : 1605,
    110 : 1606,
    78 : 1606,
    98 : 1576,
    66 : 1576,
    118 : 1736,
    86 : 1736,
    99 : 1594,
    67 : 1594,
    120 : 1588,
    88 : 1588,
    122 : 1586,
    90 : 1586,
    97 : 1726,
    65 : 1726,
    115 : 1587,
    83 : 1587,
    100 : 1583,
    68 : 1688,
    102 : 1575,
    70 : 1601,
    103 : 1749,
    71 : 1711,
    104 : 1609,
    72 : 1582,
    106 : 1602,
    74 : 1580,
    107 : 1603,
    75 : 1734,
    108 : 1604,
    76 : 1604,
    59 : 1563,
    113 : 1670,
    81 : 1670,
    119 : 1739,
    87 : 1739,
    101 : 1744,
    69 : 1744,
    114 : 1585,
    82 : 1585,
    116 : 1578,
    84 : 1600,
    121 : 1610,
    89 : 1610,
    117 : 1735,
    85 : 1735,
    105 : 1709,
    73 : 1709,
    111 : 1608,
    79 : 1608,
    112 : 1662,
    80 : 1662
};
var Direction = {
    221 : {
        "dir" : "rtl",
        "align" : "right"
    },
    219 : {
        "dir" : "ltr",
        "align" : "left"
    }
};

function ug(e) {
    if (window.UIMEA)
        delete window.UIMEA;
    var src = e.srcElement || e.target;// 事件对象
    var tagName = src.tagName.toUpperCase();// tagName
    var kc = e.keyCode || e.which;

    if (e.type == "keydown") {
        ctrl = e.ctrlKey && e.keyCode == 17;
        if (e.keyCode == 75 && e.ctrlKey) {
            u_imu = u_imu == 0 ? 1 : 0;
        }

        if ((tagName == "INPUT" || tagName == "TEXTAREA" || tagName == "BODY")
            && Direction[kc]) {
            src.style.direction = Direction[kc].dir;
            src.style.textAlign = Direction[kc].align;
        }

    }

    if (e.type == "keypress") {
        if (e.keyCode == 32 || e.altKey || e.metaKey || e.ctrlKey) {
            return;
        }
        ctrl = false;

        if (!src.readOnly && !src.disabled) {
            // 验证是否可以输入Uyghurche
            var attUime=src.getAttribute('uime') || src.uime;//对象属性设置的输入法
            if (attUime == undefined) {
                var method = checkUIME(src);
                if (method == 0) {
                    if (!u_imu) {
                        return;
                    }
                } else {
                    return;
                }
            } else {
                if ('0' == attUime) {
                    return;
                }
            }
            kc = Input[kc];
            kc = checkQuete(kc);
            if (kc != null) {
                if (isMSIE()) {
                    e.keyCode = kc;
                } else {
                    var input = String.fromCharCode(kc);
                    if (tagName == "TEXTAREA"
                        || (tagName == "INPUT" && (src.getAttribute("type") == "text" || src.type == 'text'))) {
                        var text = src.value;
                        var begin = src.selectionStart;
                        var text1 = text.substring(0, begin);
                        var text2 = text.substring(src.selectionEnd);
                        src.value = text1.concat(input, text2);
                        begin++;
                        src.selectionStart = begin;
                        src.selectionEnd = begin;
                        if (e.preventDefault)
                            e.preventDefault();

                        // 强制触发被修改事件
                        var che = document.createEvent("HTMLEvents");
                        che.initEvent("change", true, true);
                        src.dispatchEvent(che);
                        // 强制触发被修改事件
                        return;
                    } else if (tagName == "BODY") {
                        var doc = src.ownerDocument;
                        var sel = doc.getSelection();
                        if (isWEBKIT()) {
                            var r1 = sel.getRangeAt(0);
                            r1.deleteContents();
                            doc.execCommand('insertHTML', true, input);
                            if (e.preventDefault)
                                e.preventDefault();
                        } else if (isFIREFOX()) {
                            if (sel.getRangeAt && sel.rangeCount) {
                                range = sel.getRangeAt(0);
                                range.deleteContents();
                                textNode = document.createTextNode(input);
                                range.insertNode(textNode);
                                /*
                                 * Move caret to the end of the newly inserted
                                 * text node
                                 */
                                range.setStart(textNode, textNode.length);
                                range.setEnd(textNode, textNode.length);
                                sel.removeAllRanges();
                                sel.addRange(range);
                            }
                            if (e.preventDefault)
                                e.preventDefault();
                        } else {
                            e.keyCode = kc;
                        }
                    }

                }
            }
        }
    }

    if (e.type == "keyup") {
        if (ctrl && e.keyCode == 17) {
            u_imu = u_imu == 0 ? 1 : 0;
        }
    }
}
function checkQuete(kc) {
    if (kc == 34) {
        if (lastQuete == openQuete) {
            kc = closeQuete;
        } else {
            kc = openQuete;
        }
        lastQuete = kc;
    }
    return kc;
}
function checkUIME(elm) {
    var id = elm.getAttribute("id") || elm.id;
    var name = elm.getAttribute("name") || elm.name;
    var method = 0;// 0表示自动，1表示绝对输入，2表示绝对不输入
    var flag;
    if (yesug != null && yesug != "") {
        flag = yesug.indexOf("[" + id + "]") != -1
        || yesug.indexOf("[" + name + "]") != -1;
        if (flag) {
            method = 1;
        }
    } else {
        flag = notug.indexOf("[" + id + "]") == -1
        && notug.indexOf("[" + name + "]") == -1;
        if (!flag) {
            method = -1;
        }
    }
    return method;
}

function addListener(obj, type, collback, capture) {
    if (document.attachEvent) {// IE
        obj.attachEvent("on" + type, collback, capture);
    } else {
        obj.addEventListener(type, collback, capture);
    }
}

function initUIME() {
    var frames = document.getElementsByTagName("iframe");
    for ( var i = 0; i < frames.length; i++) {
        var ifr = frames[i];
        if (!ifr.contentWindow.UIME || ifr.contentWindow.UIMEA) {
            var doc = ifr.contentWindow.document;
            if (doc) {
                addUEvent(doc);
                if (bodyFonts != "") {
                    try {
                        doc.body.style.fontFamily = bodyFonts;
                    } catch (e) {
                    }
                }
                if (bodyDir != "") {
                    try {
                        doc.body.style.direction = bodyDir;
                    } catch (e) {
                    }
                }
                ifr.contentWindow.UIME = 1;
                ifr.contentWindow.UIMEA = 1;
            }
        }
    }
    if (!window.UIME) {
        window.UIME = 1;
        addUEvent(document);
    }
    if (UIMEInitCount < UIMEInitMaxCount) {
        setTimeout(initUIME, UIMEInitTime);
        UIMEInitCount++;
    }
    // document.title = UIMEInitCount;
}
function addUEvent(obj) {
    addListener(obj, "keydown", ug, true);
    addListener(obj, "keypress", ug, true);
    addListener(obj, "keyup", ug, true);
}
function isMSIE() {
    return navigator.userAgent.toUpperCase().indexOf("MSIE") != -1;
}
function isWEBKIT() {
    return navigator.userAgent.toUpperCase().indexOf("WEBKIT") != -1;
}
function isFIREFOX() {
    return navigator.userAgent.toUpperCase().indexOf("FIREFOX") != -1;
}
addListener(window, "load", function() {
    // initUIME();
}, true);
initUIME();

/** ************* */

function insertHtml(where, el, html) {
    where = where.toLowerCase();
    if (el.insertAdjacentHTML) {
        switch (where) {
            case "beforebegin":
                el.insertAdjacentHTML('BeforeBegin', html);
                return el.previousSibling;
            case "afterbegin":
                el.insertAdjacentHTML('AfterBegin', html);
                return el.firstChild;
            case "beforeend":
                el.insertAdjacentHTML('BeforeEnd', html);
                return el.lastChild;
            case "afterend":
                el.insertAdjacentHTML('AfterEnd', html);
                return el.nextSibling;
        }
        throw 'Illegal insertion point -> "' + where + '"';
    }
    var range = el.ownerDocument.createRange();
    var frag;
    switch (where) {
        case "beforebegin":
            range.setStartBefore(el);
            frag = range.createContextualFragment(html);
            el.parentNode.insertBefore(frag, el);
            return el.previousSibling;
        case "afterbegin":
            if (el.firstChild) {
                range.setStartBefore(el.firstChild);
                frag = range.createContextualFragment(html);
                el.insertBefore(frag, el.firstChild);
                return el.firstChild;
            } else {
                el.innerHTML = html;
                return el.firstChild;
            }
        case "beforeend":
            if (el.lastChild) {
                range.setStartAfter(el.lastChild);
                frag = range.createContextualFragment(html);
                el.appendChild(frag);
                return el.lastChild;
            } else {
                el.innerHTML = html;
                return el.lastChild;
            }
        case "afterend":
            range.setStartAfter(el);
            frag = range.createContextualFragment(html);
            el.parentNode.insertBefore(frag, el.nextSibling);
            return el.nextSibling;
    }
    throw 'Illegal insertion point -> "' + where + '"';
}