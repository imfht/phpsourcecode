/* Copyright(c) 2008 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: misc.js,v 1.2 2009/02/11 10:41:34 alex Exp $
 *
 */
String.prototype.trim=function(){
	return this.replace(/^\s+|\s+$/g,"");
};

ALEXWANG.Misc = function()
{
	var MSIE = (-1 != navigator.userAgent.indexOf('MSIE'));

	return {
		MouseSelectionDisable: function(element) {
			if (element.onselectstart !== undefined){
				element.onselectstart = function() { return false; };
			}
			else if (element.style.MozUserSelect !== undefined){
				element.style.MozUserSelect = "none";
			}
			else if (element.style.KhtmlUserSelect !== undefined){
				element.style.KhtmlUserSelect = "none";
			}
			if (element.ondrag !== undefined){
				element.ondrag = function() { return false; };
			}
		},
		MouseSelectionEnable: function(element) {
			if (element.onselectstart !== undefined){
				element.onselectstart = function() { return true; };
			}
			else if (element.style.MozUserSelect !== undefined){
				element.style.MozUserSelect = "normal";
			}
			else if (element.style.KhtmlUserSelect !== undefined){
				element.style.KhtmlUserSelect = "normal";
			}
			if (element.ondrag !== undefined){
				element.ondrag = function() { return true; };
			}
		},
		SetMoveAble: function(trigger_div, move_div) {
			var DiffX, DiffY;
		
			this.MouseSelectionDisable(trigger_div);
		
			trigger_div.onmousedown = function(e) {

				if (!e) {
					e = window.event;
				}
		
				
				DiffX =  e.clientX - parseInt(move_div.style.left, 10);
				DiffY =  e.clientY - parseInt(move_div.style.top, 10);
		
				document.onmousemove = function(e) {
					if (!e) {
						e = window.event;
					}
		
					var CurX = e.clientX;
					var CurY = e.clientY;
		
					var left = ((CurX - DiffX) >= 0) ? (CurX - DiffX): 0;
					var top = ((CurY - DiffY) >= 0) ? (CurY - DiffY): 0;
		
		
					move_div.style.left = left + 'px';
					move_div.style.top = top + 'px';
					return;
				};
				document.onmouseup = function(e) {
					document.onmousemove = null;
				};
			};
		},
		GetTrueBody: function() {
			 return(document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body;
		},
		GetViewportWidth: function() {
			var width = self.innerWidth;
			var mode = document.compatMode;
		
			if (mode || MSIE) {
				width = this.GetTrueBody().clientWidth;
			}
			return parseInt(width, 10);
		},
        GetViewportHeight: function() {
			var height = self.innerHeight;
			var mode = document.compatMode;
		
			if (mode || MSIE) {
				height = this.GetTrueBody().clientHeight;
			}
		
			return parseInt(height, 10);
		},
		GetDocumentWidth: function() {
			 var scrollWidth = parseInt(this.GetTrueBody().scrollWidth, 10);
			 return Math.max(scrollWidth, this.GetViewportWidth());
		},
        GetDocumentHeight: function() {
			 var scrollHeight = parseInt(this.GetTrueBody().scrollHeight, 10);
			 return Math.max(scrollHeight, this.GetViewportHeight());
		},
		/**
		 * Mask the screen.
		 * 
		 * loading (true/false): whether to show a loading animation.
		 * message (string): message to display when mask the screen.
		 */
        DocumentMask: function(loading, message) {
			 var div;
			 var hintArea = false;

			 if (loading || (typeof message == 'string')) {
				 hintArea = true;
			 }

			 div = document.getElementById('document_mask');
			 if (!div) {
				 div = document.createElement('DIV');
				 div.id = 'document_mask';
				 document.body.appendChild(div);

				 div.maskMsg = document.createElement('DIV');
				 document.body.appendChild(div.maskMsg);

				 if (typeof message == 'string') {
					 if (loading) {
						 div.maskMsg.innerHTML = '<img border="0" src="images/loading.gif" width="16" height="16" align="texttop"> '+message;
					 } else {
						 div.maskMsg.innerHTML = message;
					 }
				 } else if (loading) {
					 div.maskMsg.innerHTML = '<img border="0" src="images/loading.gif" width="16" height="16" align="middle">';
				 }
		

				/* This iframe is to fix the IE6 problem. In IE6, the <select> will
				 * always on top if we don't use iframe to overwrite it.
				 */
				if (MSIE) {
					iframe = document.createElement('IFRAME');
					iframe.id = 'document_mask_iframe';
					document.body.appendChild(iframe);
				}
			}
			div.style.top = '0px';
			div.style.left = '0px';
			div.style.zIndex = 10000;
			div.style.width = this.GetDocumentWidth().toString() + 'px';
			div.style.height = this.GetDocumentHeight().toString() + 'px';
			div.style.display = 'block';
			div.style.position = 'absolute';
			div.style.filter = "alpha(opacity:50)";
			div.style.KHTMLOpacity = 0.5;
			div.style.MozOpacity = 0.5;
			div.style.opacity = 0.5;
			div.style.background = '#ccc';
		
			if (hintArea) {
				div.maskMsg.style.border = 'double #c3daf9';
				div.maskMsg.style.backgroundColor = '#ffffff';
				div.maskMsg.style.position = 'absolute';
				div.maskMsg.style.display = 'block';
				div.maskMsg.style.fontSize = '12px';
				div.maskMsg.style.fontFamily = 'Arial, Helvetica, sans-serif';
				div.maskMsg.style.color = '#333333';
				div.maskMsg.style.padding = '5px 10px';
				div.maskMsg.style.top = this.GetTrueBody().scrollTop + (this.GetViewportHeight()/2) - (parseInt(div.maskMsg.clientHeight, 10) / 2) + 'px';
				div.maskMsg.style.left = (this.GetViewportWidth()/2) - (parseInt(div.maskMsg.clientWidth, 10)/2) +  this.GetTrueBody().scrollLeft + 'px'; 
				div.maskMsg.style.zIndex = div.style.zIndex + 1;
			} else {
				div.maskMsg.style.display = 'none';
			}
		
			if (MSIE) {
				iframe.style.top = '0px';
				iframe.style.left = '0px';
				iframe.style.zIndex = div.style.zIndex - 1;
				iframe.style.display = 'block';
				iframe.style.position = 'absolute';
				iframe.style.border = 0;
				iframe.style.width = div.style.width;
				iframe.style.height = div.style.height;
				iframe.style.filter = "alpha(opacity:0)";
				iframe.style.KHTMLOpacity = 0;
				iframe.style.MozOpacity = 0;
				iframe.style.opacity = 0;
			}
		
			window.onresize = function() {
				var div = document.getElementById('document_mask');
					
				if (div) {
					div.style.width = ALEXWANG.Misc.GetDocumentWidth().toString() + 'px';
					div.style.height = ALEXWANG.Misc.GetDocumentHeight().toString() + 'px';
					if (MSIE) {
						var iframe = document.getElementById('document_mask_iframe');
						iframe.style.width = div.style.width;
						iframe.style.height = div.style.height;
					}
				}
			};
		},
		DocumentUnMask: function() {
			var div = document.getElementById('document_mask');
			
			if (!div) {
				return;
			}
		
			if (MSIE) {
				var iframe = document.getElementById('document_mask_iframe');
				iframe.style.display = 'none';
			}
		
			div.maskMsg.style.display = 'none';
			div.style.display = 'none';
		
			window.onresize = null;
		}
	};
}();

