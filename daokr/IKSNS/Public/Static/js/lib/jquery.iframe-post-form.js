/**
* jQuery plugin for posting form including file inputs.
*
* Copyright (c) 2010 - 2011 Ewen Elder
*
* Licensed under the MIT and GPL licenses:
* http://www.opensource.org/licenses/mit-license.php
* http://www.gnu.org/licenses/gpl.html
*
* @author: Ewen Elder <ewen at jainaewen dot com> <glomainn at yahoo dot co dot uk>
* @version: 1.1.1 (2011-07-29)
**/
(function ($)
{
  $.fn.iframePostForm = function (options)
  {
    var response,
    returnResponse,
    element,
    status = true,
    iframe;

    options = $.extend({}, $.fn.iframePostForm.defaults, options);


    // Add the iframe.
    if (!$('#' + options.iframeID).length)
    {
      $('body').append('<iframe id="' + options.iframeID + '" name="' + options.iframeID + '" style="display:none" />');
    }


    return $(this).each(function ()
    {
      element = $(this);


      // Target the iframe.
      element.attr('target', options.iframeID);


      // Submit listener.
      element.submit(function ()
      {
        // If status is false then abort.
        status = options.post.apply(this);

        if (status === false)
        {
          return status;
        }


        iframe = $('#' + options.iframeID).load(function ()
        {
          response = iframe.contents().find('body');


          if (options.json)
          {
            var real_text = response.text();
            //if ($.browser.opera) {
              //real_text = real_text.substring(5);
            //} else if (($.browser.msie && $.browser.version < 7) || $.browser.mozilla || $.browser.webkit) {
              //real_text = response.text();
            //}
            try {
              returnResponse = $.parseJSON(real_text);
            } catch (e) {}
            returnResponse = returnResponse || '';
          }

          else
          {
            returnResponse = response.html();
          }


          options.complete.apply(this, [returnResponse]);

          iframe.unbind('load');


          setTimeout(function ()
          {
            response.html('');
          }, 1);
        });
      });
    });
  };


  $.fn.iframePostForm.defaults =
  {
    iframeID : 'iframe-post-form',       // Iframe ID.
    json : false,                        // Parse server response as a json object.
    post : function () {},               // Form onsubmit.
    complete : function (response) {}    // After response from the server has been received.
  };
})(jQuery);
