/* Copyright(c) 2008 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: tooltip.js,v 1.1 2008/11/30 03:31:44 alex Exp $
 *
 */
/* options:
 *		option.title
 *		option.msg
 *		option.width
 *
 * Example:
 *		ALEXWANG.Tooltip.Show({
 *			title: 'this is title',
 *			msg: 'this is message',
 *			width: 400,
 *		});
 *
 * <img src="/images/tooltip.gif" border="0" 
 *	onmouseover="return ALEXWANG.Tooltip.Show({title: 'this is title', msg:'this is message', width:250});" 
 *	onmouseout="ALEXWANG.Tooltip.Hide();">
 *  
 * This function requires ALEXWANG.Misc (include alexwang/misc.js)
 */
ALEXWANG.Tooltip = function()
{
	var opt, div, tooltip, iframe;

	var MSIE = (-1 != navigator.userAgent.indexOf('MSIE'));

	return {
		Show: function(options) {
			var div_close, div_header;

			tooltip = this;
			opt = options;
			
			if (!div) {
				div = document.createElement('DIV');
				div.id = 'aw-tooltip';
				document.body.appendChild(div);

				/* This iframe is to fix the IE6 problem. In IE6, the <select> will
				 * always on top if we don't use iframe to overwrite it.
				 */
				if (MSIE) {
					iframe = document.createElement('IFRAME');
					document.body.appendChild(iframe);
				}
			}
			div.style.display = 'none';

			div.className = 'aw-dlg';
			div.innerHTML = '<div class="aw-dlg-hd-left"><div class="aw-dlg-hd-right"><div class="aw-dlg-hd">'+options.title+'</div></div></div>' + 
				'<div class="aw-dlg-dlg-body"><div style="width:100%;overflow:hidden;">'+options.msg+'</div></div>'+
				'<div class="aw-dlg-bg-left"><div class="aw-dlg-bg-right"><div class="aw-dlg-bg-center"><p align="center"></p></div></div></div>';
			
			if (!options.width || options.width < 200) { options.width = 200; }
			div.style.width = options.width + 'px';
			div.style.position = 'absolute';
			div.style.zIndex = 2000002;

			if (MSIE) {
				iframe.style.zIndex = div.style.zIndex - 1;
				iframe.style.position = 'absolute';
				iframe.style.border = 0;

				// IE/Windows
				iframe.style.filter = "alpha(opacity: 40)";
				// Safari < 1.2, Konqueror
				iframe.style.KHTMLOpacity = 0.4;
				// Older Mozilla and Firefox
				iframe.style.MozOpacity = 0.4;
				// Safari 1.2, newer Firefox and Mozilla, CSS3
				iframe.style.opacity = 0.4;
			}

			document.onmousemove = function(e) {
				if (!e) {
					e = window.event;
				}

				var curX = MSIE?(e.clientX+ALEXWANG.Misc.GetTrueBody().scrollLeft) : e.pageX;
				var curY = MSIE?(e.clientY+ALEXWANG.Misc.GetTrueBody().scrollTop) : e.pageY;

				//Find out how close the mouse is to the corner of the window
				var winwidth = ALEXWANG.Misc.GetViewportWidth();
				var winheight = ALEXWANG.Misc.GetViewportHeight();
				var rightedge = MSIE? winwidth-e.clientX-12 : winwidth-e.clientX-12;
				var bottomedge = MSIE? winheight-e.clientY-10 : winheight-e.clientY-10;
				var leftedge = -1000;

				//if the horizontal distance isn't enough to accomodate the width of the context menu
				if (rightedge < div.offsetWidth) {
					//move the horizontal position of the menu to the left by it's width
					div.style.left = curX-div.offsetWidth+"px";
				} else if (curX < leftedge) {
					div.style.left = "5px";
				} else {
					//position the horizontal position of the menu where the mouse is positioned
					div.style.left = curX+12+"px";
				}
				//same concept with the vertical position
				if (bottomedge < div.offsetHeight) {
					div.style.top = curY-div.offsetHeight-10+"px";
				} else {
					div.style.top = curY+10+"px";
				}

				div.style.display = 'block';
				if (MSIE) {
					iframe.style.top = div.style.top;
					iframe.style.left = div.style.left;
					iframe.style.width = div.offsetWidth;
					iframe.style.height = div.offsetHeight;
					iframe.style.display = 'block';
				}
			};

		},
		Hide: function() {
			document.onmousemove = null;
			div.style.display = 'none';
			if (MSIE) {
				iframe.style.display = 'none';
			}
		}
	};
}();
