function reloadPage(win) {
	if(!win) win=window;
    var location = win.location;
    location.href = location.pathname + location.search;
}