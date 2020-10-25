/* Copyright(c) 2003-2007 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: misc.js,v 1.2 2008/11/30 03:46:28 alex Exp $
 *
 */
var Firefox = (document.getElementById && !document.all);
var MSIE = (-1 != navigator.userAgent.indexOf('MSIE'));

String.prototype.trim=function(){
	return this.replace(/^\s+|\s+$/g,"");
};

function Redirect(url)
{
	parent.location=url;
}

var Submitted = 0;
function OnSubmit(form)
{
	if (Submitted) {
		return false;
	}
	Submitted = 1;
	return true;
}

