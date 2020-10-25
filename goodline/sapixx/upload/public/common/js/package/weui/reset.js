(function (doc, win) {
    var resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
        recalc = function () {
            var cW = document.documentElement.clientWidth,
                iW = window.innerWidth;
            var w = Math.max(cW, iW);
                w = w > 750 ? 750 : w;
            var fz = ~~(w / 7.5);
            document.getElementsByTagName("html")[0].style.cssText = 'font-size: ' + fz + "px";

            function setHtmlSize() {
                var realfz = ~~(+window.getComputedStyle(document.getElementsByTagName("html")[0]).fontSize.replace('px', '') * 10000) / 10000;
                if (fz !== realfz) {
                    document.getElementsByTagName("html")[0].style.cssText = 'font-size: ' + fz * (fz / realfz) + "px";
                }
            }
            setHtmlSize();
        };
    if (!doc.addEventListener) return;
    win.addEventListener(resizeEvt, recalc, false);
    doc.addEventListener('DOMContentLoaded', recalc, false);
})(document, window);