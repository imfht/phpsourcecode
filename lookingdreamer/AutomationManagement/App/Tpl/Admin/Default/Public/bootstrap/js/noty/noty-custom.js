function generate(layout) {
  	var n = noty({
  		text: layout,
  		type: 'alert',
      dismissQueue: true,
  		layout: layout,
  		theme: 'defaultTheme'
  	});
  	console.log('html: '+n.options.id);
  }

  function generateAll() {
    generate('top');
    generate('topCenter');
    generate('topLeft');
    generate('topRight');
    generate('center');
    generate('centerLeft');
    generate('centerRight');
    generate('bottom');
    generate('bottomCenter');
    generate('bottomLeft');
    generate('bottomRight');
  }
  
  $(document).ready(function() {
  	
		var notes = [];

		notes['alert'] = 'Best check yo self, you\'re not looking too good.';
		notes['error'] = 'Change a few things up and try submitting again.';
		notes['success'] = 'You successfully read this important alert message.';
		notes['information'] = 'This alert needs your attention, but it\'s not super important.';
		notes['warning'] = '<strong>Warning!</strong> <br /> Best check yo self, you\'re not looking too good.';
		notes['confirm'] = 'Do you want to continue?';
		
		$('.buttons-noty a.btn-noty').click(function (event) {
			

			event.preventDefault();
	    
				var self = $(this);

				if (self.data('layout') == 'inline') {
					$(self.data('custom')).noty({
						text        : notes[self.data('type')],
						type        : self.data('type'),
						dismissQueue: true,
						buttons     : (self.data('type') != 'confirm') ? false : [
							{addClass: 'btn btn-primary', text: 'Ok', onClick: function ($noty) {

								// this = button element
								// $noty = $noty element
								$noty.close();
								$(self.data('custom')).noty({force: true, text: 'You clicked "Ok" button', type: 'success'});
							}
							},
							{addClass: 'btn btn-danger', text: 'Cancel', onClick: function ($noty) {
								$noty.close();
								$(self.data('custom')).noty({force: true, text: 'You clicked "Cancel" button', type: 'error'});
							}
							}
						]
					});
					return false;
				}

				noty({
					text        : notes[self.data('type')],
					type        : self.data('type'),
					dismissQueue: true,
					layout      : self.data('layout'),
					buttons     : (self.data('type') != 'confirm') ? false : [
						{addClass: 'btn btn-primary', text: 'Ok', onClick: function ($noty) {
							alert('vj');

							// this = button element
							// $noty = $noty element

							$noty.close();
							noty({force: true, text: 'You clicked "Ok" button', type: 'success', layout: self.data('layout')});
						}
						},
						{addClass: 'btn btn-danger', text: 'Cancel', onClick: function ($noty) {
							$noty.close();
							noty({force: true, text: 'You clicked "Cancel" button', type: 'error', layout: self.data('layout')});
						}
						}
					]
				});
				return false;
			});  	
  });

