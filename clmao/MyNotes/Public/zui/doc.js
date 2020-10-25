var config = {'debugdisabled': true};

$(function()
{
    $(window).resize(function(){$('#main').css('min-height', $(window).height());}).resize();

    $('.navbar-collapse > .nav > li > a').each(function()
    {
        var $this = $(this);
        var href = $this.attr('href');
        var target = href.substring(href.indexOf('#'), href.length);
        $this.attr('data-target', target);
    });
    $('body').scrollspy({target: '#navbar-collapse'});
   $('.navchild').click(function(){
       
       $(".jumbotron").html();
       $(".jumbotron").load("http://a.com/code/Home/Admin/addContent.html");
   });
    // hljs.initHighlightingOnLoad();
    prettyPrint();
    
    // tooltip demo
    $('.tooltip-demo').tooltip({
      selector: "[data-toggle=tooltip]",
      container: "body"
    });

    // popover demo
    $("[data-toggle=popover]").popover();

    // navbar collapse
    $('.navbar-collapsed .nav > .nav-heading').click(function(event)
    {
        var $nav = $(this).closest('.nav');
        if($nav.hasClass('collapsed'))
        {
            if($(window).width() < 767)
            {
                $('.navbar-collapsed .nav').not($nav).children('li:not(.nav-heading)').slideUp('fast', function(){
                    $(this).closest('.nav').addClass('collapsed');
                });
            }
            $nav.removeClass('collapsed').children('li:not(.nav-heading)').slideDown('fast');
        }
        else
        {
            $nav.children('li:not(.nav-heading)').slideUp('fast', function(){$nav.addClass('collapsed')});
        }
    });

    $('section .page-header h2 > small.label').tooltip({placement: 'right'});



    

    if($.fn.boards) $('.boards').boards();

    // Chosen
    if($.fn.chosen) $('.chosen-select').chosen();
    if($.fn.chosenIcons) $('#chosenIcons').chosenIcons();

    // datetime picker
    if($.fn.datetimepicker)
    {
        $('.form-datetime').datetimepicker(
        {
            weekStart: 1,
            todayBtn:  1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            forceParse: 0,
            showMeridian: 1,
            format: 'yyyy-mm-dd hh:ii'
        });
        $('.form-date').datetimepicker(
        {
            language:  'zh-CN',
            weekStart: 1,
            todayBtn:  1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            minView: 2,
            forceParse: 0,
            format: 'yyyy-mm-dd'
        });
        $('.form-time').datetimepicker({
            language:  'zh-CN',
            weekStart: 1,
            todayBtn:  1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 1,
            minView: 0,
            maxView: 1,
            forceParse: 0,
            format: 'hh:ii'
        });
    }

    
});
