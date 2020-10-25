/*
 *  TextAreaResizer script by Jason Johnston (jj@lojjic.net)
 *  Created August 2003.  Use freely, but give me credit.
 *
 *  This script adds a handle below textareas that the user
 *  can drag with the mouse to resize the textarea.
 ************************************************************
 *  Modified by John Ha 2005, 2006 (ink@bur.st)
 *
 *  Modified by Peter Klein/Stefan Galinski for special needs in LFEditor
 */

function TextAreaResizer(elt) {
	this.element = elt;
	this.create();
}

TextAreaResizer.prototype = {
	// create hr element (+class, +title tooltip)
	create: function() {
		var elt = this.element;
		if (elt.title != 'true') {
			var thisRef = this; // for usage in events definition
			var h = this.handle = document.createElement("hr");
			h.className = 'handle-normal';

			// tooltip dont work in every browser correct (eg. firefox no page break)
			if (typeof tooltip != 'undefined') {
				h.title = '<ul><li>Click & drag to resize</li><li>Double-left-click to' +
					'minimize/maximize</li><li>Right-click best-fit to window</li></ul>';
			} else if (checkIt('opera')) {
				h.title = '- Click & drag to resize \n- Double-left-click to minimize/maximize \n';
			} else {
				h.title = '- Click & drag to resize \n- Double-left-click to minimize/maximize \n' +
					'- Right-click best-fit to window';
			}

			// double click resizing
			addEvent(h, 'dblclick', function() {
				thisRef.max(1);
			}, false);

			// onclick optimal resizing
			addEvent(h, 'mousedown', function(evt) {
				if (!checkIt('opera')) {
					if (evt.button == 2) {
						thisRef.max(2);
					} else {
						thisRef.dragStart(evt);
					}
				}
			}, false);

			// class changing mechanism
			addEvent(h, 'mouseover', function() {
				h.className = 'handle-highlight';
			}, false);
			addEvent(h, 'mouseout', this.handleHigh = function() {
				h.className = 'handle-normal';
			}, false);

			// deactivate context menu
			addEvent(h, 'contextmenu', function() {
				return false;
			}, false);

			// insert now into document
			elt.parentNode.insertBefore(h, elt.nextSibling);
		}
	},

	dragStart: function(evt) {
		var thisRef = this;

		// lock cursor shape
		document.getElementsByTagName('body')[0].style.cursor = 's-resize';

		if (typeof (this.handle.mouseoverHandler) == 'function' &&
			typeof (this.handle.mouseoutHandler) == 'function') {
			// save mouseover handler from dom-tooltips
			this.mouseoverHandler = this.handle.mouseoverHandler;

			// disable mouseover for tooltips - tooltips should be "off" while dragging
			removeEvent(this.handle, 'mouseover', this.handle.mouseoverHandler, false);

			// turn off tooltip
			this.handle.mouseoutHandler();
		}

		// highlight should remain on
		removeEvent(this.handle, 'mouseout', this.handleHigh, false);

		this.dragStartY = evt.clientY + 8;
		this.dragStartH = this.element.offsetHeight;

		addEvent(document, 'mousemove', this.dragMoveHdlr = function(evt) {
			thisRef.dragMove(evt);
		}, false);
		addEvent(document, 'mouseup', this.dragStopHdlr = function() {
			thisRef.dragStop();

			// restore default cursor shape
			document.getElementsByTagName('body')[0].style.cursor = 'default';

			// restore mouseover for tooltips after drag stop
			if (typeof (thisRef.mouseoverHandler) == 'function') {
				addEvent(thisRef.handle, 'mouseover', thisRef.mouseoverHandler, false);
			}

			// restore highlight handler
			thisRef.handle.className = 'handle-normal';
			addEvent(thisRef.handle, 'mouseout', thisRef.handleHigh =
				function() {
					thisRef.handle.className = 'handle-normal';
				}, false);
		}, false);
	},

	dragMove: function(evt) {
		var height = this.dragStartH + evt.clientY - this.dragStartY;
		this.element.style.height = (height > 0 ? height : 0) + 'px';
	},

	dragStop: function() {
		//this.element.style.borderStyle = 'solid';
		removeEvent(document, 'mousemove', this.dragMoveHdlr, false);
		removeEvent(document, 'mouseup', this.dragStopHdlr, false);
	},

	destroy: function() {
		var elt = this.element;
		elt.parentNode.removeChild(this.handle);
		elt.style.height = '';
	},

	max: function(mode) {
		if (!this.defHeight) {
			this.defHeight = this.element.offsetHeight;
		}
		if (!this.defWidth) {
			this.defWidth = this.handle.offsetWidth;
		}

		if (this.element.style.height == '1px') {
			this.element.style.height = this.defHeight + 'px';
		} else {
			this.element.style.height = '1px';
		}

		if (mode == 2) {
			var str = '';
			for (var i = 0; i < parseInt(this.element.scrollWidth / 10); i++) {
				str += '		';
			}
			this.element.value += str;

			// IE Bug? Need to retrieve scrollWidth first to init it!
			var dummy = this.element.scrollWidth,
				wrap = 0;
			if (this.element.scrollWidth == this.element.clientWidth) {
				wrap = 1;
			}
			this.element.value = this.element.value.replace(str, '');

			// IE Bug? Need to retrieve scrollHeight first to init it!
			dummy = this.element.scrollHeight;
			var maxHeight = this.element.scrollHeight +
				(checkIt('msie') ? wrap ? -6 : 9 : wrap ? 0 : this.element.scrollWidth > this.element.clientWidth ? 20 : 0);

			if (maxHeight > winSize()[1]) {
				maxHeight = winSize()[1] - (checkIt('msie') ? 60 : 90);
			}
			// For some reason Netscape won't accept style.height greater than 10000px
			this.element.style.height = maxHeight - (checkIt('msie') ? 8 : 0) + 'px';
		}

		return false;
	}
};

function winSize() {
	var myWidth = 0, myHeight = 0;
	if (typeof (window.innerWidth) == 'number') {
		//Non-IE
		myWidth = window.innerWidth - 16;
		myHeight = window.innerHeight - 16;
	} else if (document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight)) {
		//IE 6+ in 'standards compliant mode'
		myWidth = document.documentElement.clientWidth - 20;
		myHeight = document.documentElement.clientHeight - 20;
	} else if (document.body && (document.body.clientWidth || document.body.clientHeight)) {
		//IE 4 compatible
		myWidth = document.body.clientWidth - 20;
		myHeight = document.body.clientHeight - 20;
	}
	return new Array(myWidth, myHeight);
}

// safari, omniweb, opera, webtv, icab, msie
function checkIt(string) {
	return navigator.userAgent.toLowerCase().indexOf(string) + 1;
}

function LFEtextarea_init() {
	var textareas = document.getElementsByTagName('textarea');
	// Somehow var i was being corrupted.
	// Only when max(3) or max(2) called.
	// Using z instead. Weird.
	for (var z = 0; z < textareas.length; z++) {
		new TextAreaResizer(textareas[z]);
	}
	// Re-init dom-tooltips (if available), so tooltips are shown for handles
	typeof tooltip != 'undefined' ? tooltip.init(new Array('hr', 'a')) : 0;
}

if (typeof schedule != 'function') {
	function addEvent(obj, evType, fn, useCapture) {
		if (obj.addEventListener) {
			obj.addEventListener(evType, fn, useCapture);
			return true;
		} else if (obj.attachEvent) {
			return obj.attachEvent("on" + evType, fn);
		}

		return false;
	}

	function removeEvent(obj, evType, fn, useCapture) {
		if (obj.removeEventListener) {
			obj.removeEventListener(evType, fn, useCapture);
			return true;
		} else if (obj.detachEvent) {
			return obj.detachEvent('on' + evType, fn);
		}

		return false;
	}

	addEvent(window, 'load', function() {
		LFEtextarea_init();
	}, false);
} else {
	schedule('textarea_init()');
}
