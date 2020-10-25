/* Copyright(c) 2008 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: dialog.js,v 1.1 2008/11/30 03:33:17 alex Exp $
 *
 */
/* options:
 *		option.title
 *		option.msg
 *		option.buttons[], ok, yes, no, save, cancel, submit
 *		option.fn, function(button), button is string:ok|yes|no|cancel
 *		option.width
 *
 * Example:
 *		ALEXWANG.Dialog.Show({
 *			title: 'this is title',
 *			msg: 'this is message',
 *			width: 400,
 *			buttons: ['yes', 'no'],
 *			fn: alextest
 *		});
 *
 * This function need STRING['button_ok'], STRING['button_cancel']....to localize button text
 * When there is no STRING[], 
 */
ALEXWANG.Dialog = function()
{
	var opt, div, dialog;
	
	return {
		Show: function(options) {
			var div_close, div_header;
			var buttons = '';

			ALEXWANG.Misc.DocumentMask();

			dialog = this;
			opt = options;
			
			for (var i = 0; i < options.buttons.length; i++) {
				if (buttons !== '') {
					buttons = buttons + '&nbsp;&nbsp;';
				}
				buttons = buttons + '<button id="aw-dlg-button-"' + options.buttons[i] + 
									'" class="aw-dlg-button" onclick="ALEXWANG.Dialog.ButtonHandler(\''+options.buttons[i]+'\');">' + 
									STRING['button_'+options.buttons[i]]+'</button>';
			}

			if (!div) {
				div = document.createElement('DIV');
				div.id = 'message_box';
				document.body.appendChild(div);
			}
			div.className = 'aw-dlg';
			div.innerHTML = '<div class="aw-dlg-hd-left"><div class="aw-dlg-hd-right"><div id="message_box_hd" class="aw-dlg-hd">'+options.title+'</div></div></div>' + 
				'<div class="aw-dlg-dlg-body">'+options.msg+'</div>'+
				'<div class="aw-dlg-bg-left"><div class="aw-dlg-bg-right"><div class="aw-dlg-bg-center"><p align="center">'+buttons+'</p></div></div></div>'+
				'<div id="aw-dlg-close" class="aw-dlg-close"></div>';

			
			if (!options.width || options.width < 200) { options.width = 200; }
			div.style.width = options.width + 'px';

			/* Put the dialog in the top 1/4 of screen */
			div.style.top = ALEXWANG.Misc.GetTrueBody().scrollTop + (ALEXWANG.Misc.GetViewportHeight()/4) + 'px';
			div.style.left = (ALEXWANG.Misc.GetViewportWidth()/2) - (parseInt(div.style.width, 10)/2) + ALEXWANG.Misc.GetTrueBody().scrollLeft + 'px';
			div.style.position = 'absolute';
			div.style.display = 'block';
			div.style.zIndex = 2000001;
			
			div_header = document.getElementById('message_box_hd');
			ALEXWANG.Misc.SetMoveAble(div_header, div);

			div_close = document.getElementById('aw-dlg-close');
			div_close.onmouseover = function() {
				this.className = 'aw-dlg-close aw-dlg-close-over';
			};
			div_close.onmouseout = function() {
				this.className = 'aw-dlg-close';
			};
			div_close.onclick = function() {
				dialog.Cancel();
			};

		},
		Cancel:function() {
			 ALEXWANG.Misc.DocumentUnMask();
			 div.style.display = 'none';
		},
		ButtonHandler: function(button) {
			 this.Cancel();
			 if (opt.fn) {
				 opt.fn(button);
			 }
		}
	};
}();

