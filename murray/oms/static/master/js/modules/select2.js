// Select2
// -----------------------------------

(function(window, document, $, undefined){

  $(function(){

    if ( !$.fn.select2 ) return;

    // Select 2

    $('#select2-1').select2({
        theme: 'bootstrap'
    });
    $('#select2-2').select2({
        theme: 'bootstrap'
    });
    $('#select2-3').select2({
        theme: 'bootstrap'
    });
    $('#select2-4').select2({
        placeholder: 'Select a state',
        allowClear: true,
        theme: 'bootstrap'
    });

  });

})(window, document, window.jQuery);

