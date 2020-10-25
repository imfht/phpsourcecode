// jQuery $('document').ready(); function 
$('document').ready(function(){

	$.gritter.add({
		// (string | mandatory) the heading of the notification
		title: 'Howdy!!',
		// (string | mandatory) the text inside the notification
		text: 'I am notification, You can kick me out by closing or I will leave very soon',
		image: 'images/theme/avatarOne.png',
		sticky: false

	});
});
