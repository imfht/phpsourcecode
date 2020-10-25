(function ($) {
  
  // JavaScript Document

  /* ---------------------------------------------------------------------- */
  /* Unique ID Generator
/* ---------------------------------------------------------------------- */
  function uuid() {
    var uuid = (function () {
      var i, c = "89ab", u = [];
      for (i = 0; i < 36; i += 1) {
        u[i] = (Math.random() * 16 | 0).toString(16);
      }
      u[8] = u[13] = u[18] = u[23] = "-";
      u[14] = "4";
      u[19] = c.charAt(Math.random() * 4 | 0);
      return u.join("");
    })();
    return {
      toString: function () {
        return uuid;
      },
      valueOf: function () {
        return uuid;
      }
    };
  }

  function isIOS6(){
    if (/(iPhone|iPod|iPad)/i.test(navigator.userAgent)) {
      if (/OS [6](.*) like Mac OS X/i.test(navigator.userAgent)) {
        return true;
      }
    }
    return false;
  }
  // This library re-implements setTimeout, setInterval, clearTimeout, clearInterval for iOS6.
  // iOS6 suffers from a bug that kills timers that are created while a page is scrolling.
  // This library fixes that problem by recreating timers after scrolling finishes (with interval correction).
  // This code is free to use by anyone (MIT, blabla).
  // Original Author: rkorving@wizcorp.jp
  if(isIOS6()){
    (function (window) {
      var timeouts = {};
      var intervals = {};
      var orgSetTimeout = window.setTimeout;
      var orgSetInterval = window.setInterval;
      var orgClearTimeout = window.clearTimeout;
      var orgClearInterval = window.clearInterval;
      // To prevent errors if loaded on older IE.
      if (!window.addEventListener) return false;
      function createTimer(set, map, args) {
        var id, cb = args[0],
        repeat = (set === orgSetInterval);

        function callback() {
          if (cb) {
            cb.apply(window, arguments);
            if (!repeat) {
              delete map[id];
              cb = null;
            }
          }
        }
        args[0] = callback;
        id = set.apply(window, args);
        map[id] = {
          args: args,
          created: Date.now(),
          cb: cb,
          id: id
        };
        return id;
      }

      function resetTimer(set, clear, map, virtualId, correctInterval) {
        var timer = map[virtualId];
        if (!timer) {
          return;
        }
        var repeat = (set === orgSetInterval);
        // cleanup
        clear(timer.id);
        // reduce the interval (arg 1 in the args array)
        if (!repeat) {
          var interval = timer.args[1];
          var reduction = Date.now() - timer.created;
          if (reduction < 0) {
            reduction = 0;
          }
          interval -= reduction;
          if (interval < 0) {
            interval = 0;
          }
          timer.args[1] = interval;
        }
        // recreate
        function callback() {
          if (timer.cb) {
            timer.cb.apply(window, arguments);
            if (!repeat) {
              delete map[virtualId];
              timer.cb = null;
            }
          }
        }
        timer.args[0] = callback;
        timer.created = Date.now();
        timer.id = set.apply(window, timer.args);
      }
      window.setTimeout = function () {
        return createTimer(orgSetTimeout, timeouts, arguments);
      };
      window.setInterval = function () {
        return createTimer(orgSetInterval, intervals, arguments);
      };
      window.clearTimeout = function (id) {
        var timer = timeouts[id];
        if (timer) {
          delete timeouts[id];
          orgClearTimeout(timer.id);
        }
      };
      window.clearInterval = function (id) {
        var timer = intervals[id];
        if (timer) {
          delete intervals[id];
          orgClearInterval(timer.id);
        }
      };
      //check and add listener on the top window if loaded on frameset/iframe
      var win = window;
      while (win.location != win.parent.location) {
        win = win.parent;
      }
      win.addEventListener('scroll', function () {
        // recreate the timers using adjusted intervals
        // we cannot know how long the scroll-freeze lasted, so we cannot take that into account
        var virtualId;
        for (virtualId in timeouts) {
          resetTimer(orgSetTimeout, orgClearTimeout, timeouts, virtualId);
        }
        for (virtualId in intervals) {
          resetTimer(orgSetInterval, orgClearInterval, intervals, virtualId);
        }
      });
    }(window));
  }

  // jQuery Initialization
  jQuery(document).ready(function($){

    /* ---------------------------------------------------------------------- */
    /*	Detect Touch Device
    /* ---------------------------------------------------------------------- */
    if(Modernizr.touch){
      function removeHoverState(){
        $("body").addClass("no-touch");
      }
    }

    /* ---------------------------------------------------------------------- */
    /* Fixes for Browsers
    /* ---------------------------------------------------------------------- */
	
    function isChrome(){
      return (typeof window.chrome === "object") ||
      /chrome/.test( navigator.userAgent.toLowerCase() ) ||
      /chrom(e|ium)/.test(navigator.userAgent.toLowerCase())
    }

    function isIE10(){
      return ($.browser.msie  && parseInt($.browser.version, 10) === 10);
    }

    function isOpera(){
      return window.opera || $.browser.opera;
    }

    if (isChrome()){
      var firstSubmenuId = $('#nav :first :first :first').attr('rel');
      $('.ddsubmenustyle').each(function(){
        var firstChild = $(this).children()[0];
        if(firstChild.tagName.toLowerCase() === 'li'){
          firstChild = $(firstChild).parent();
        }
        var firstChildId = firstChild.attr('id');
        if(firstChildId !== firstSubmenuId){
          $(this).css('margin-left', '-1px');
        }
      });
    }
	
    if(isOpera()){
      $('.flexslider .slides > li').each(function(){
        $(this).css('overflow', 'hidden');
      });
    }

    /* ---------------------------------------------------------------------- */
    /* Entry Slider
    /* ---------------------------------------------------------------------- */

    if($().cycle){

      var entrySliders = $('.entry-slider > ul');

      $.fn.cycle.transitions.scrollHorizontal = function($cont, $slides, opts) {
        $cont.css('overflow', 'hidden');
        opts.before.push($.fn.cycle.commonReset);
        var w = $cont.width();
        opts.cssFirst.left = 0;
        opts.cssBefore.left = w;
        opts.cssBefore.top = 0;
        opts.animIn.left = 0;
        opts.animOut.left = 0-w;

        if( $cont.data('dir') === 'prev' ) {
          opts.cssBefore.left = -w;
          opts.animOut.left = w;
        }
      };

      function initEntrySlider(entrySliders, isFirstTime){
        entrySliders.each(function(i) {
          var slider = $(this);
          var initPerformed = isFirstTime && slider.data('initInvoked');
          if(!initPerformed){
            slider.data('initInvoked', 'true');

            var sliderId = 'entry-slider-'+i;
            slider.attr('id', sliderId);
            var prevButtonId = sliderId+'-prev';
            var nextButtonId = sliderId+'-next';

            if(slider.data('enable') === 'false' ){
              return;
            }

            slider.css('height', slider.children('li:first').height());
            var firstSlide = slider.children('li')[0];
            var lastSlide = slider.children('li')[slider.children('li').length-1];

            if(slider.children('li').length > 1){
              if(slider.parent().find('#'+prevButtonId).length==0){
                slider.parent().append('<div class="entry-slider-nav"><a id="'+prevButtonId+'" class="prev">Prev</a><a id="'+nextButtonId+'" class="next">Next</a></div>');
              }
            }

            slider.cycle({
              onPrevNextEvent: function(isNext, zeroBasedSlideIndex, slideElement){
                $(slideElement).parent().data('dir', isNext? 'next' : 'prev');
              },
              before: function( curr, next, opts, forwardFlag ) {
                var $this = $(this);
                var sliderId = $this.closest('ul').attr('id');
                // set the container's height to that of the current slide
                $this.parent().stop().animate({
                  height:$this.height()
                }, opts.speed);
                if(opts['nowrap']){
                  var prevButton = $('#'+sliderId+'-prev');
                  var nextButton = $('#'+sliderId+'-next');
                  if((firstSlide == next) && (!prevButton.hasClass('disabled'))){
                    prevButton.addClass('disabled');
                  }else{
                    prevButton.removeClass('disabled');
                  }

                  if((lastSlide == next) && (!nextButton.hasClass('disabled'))){
                    nextButton.addClass('disabled');
                  }else{
                    nextButton.removeClass('disabled');
                  }
                }
              },
              containerResize : false,
              pauseOnPagerHover : true,
              nowrap : false, // if true, the carousel will not be circular
              easing : 'easeInOutExpo',
              fx : 'scrollHorizontal',
              speed : 600,
              timeout : 0,
              fit : true,
              width : '100%',
              pause : true,
              slideResize : true,
              slideExpr : 'li',
              prev : '#' + prevButtonId,
              next : '#' + nextButtonId
            });
          }
        });
        if(Modernizr.touch && $().swipe) {
          function doEntrySliderSwipe( e, dir ) {
            var sliderId = $(e.currentTarget).attr('id');
            if(dir == 'left' ) {
              $('#'+sliderId+'-next').trigger('click');
            }
            if(dir == 'right' ) {
              $('#'+sliderId+'-prev').trigger('click');
            }
          }

          entrySliders.each(function() {
            var slider = $(this);
            var initPerformed = isFirstTime && slider.data('swipeInvoked');
            if(!initPerformed){
              slider.data('swipeInvoked', 'true');
              slider.swipe({
                click       : function(e, target){
                  $(target).trigger('click');
                },
                swipeLeft       : doEntrySliderSwipe,
                swipeRight      : doEntrySliderSwipe,
                allowPageScroll : 'auto'
              });
            }
          });

        }

      }

      function initAllEntrySliders(isFirstTime){
        initEntrySlider($('.entry-slider > ul'), isFirstTime);
      }

      function resizeEntrySlider(entrySliders){
        entrySliders.each(function() {
          var slider = $(this);
          slider.css('height', slider.children('li:first').height());
        });
      }

      function loadEntrySlider(){
        var entrySliderImages = $('.entry-slider > ul > li> a > img');
        var unloadedImagesCount = 0;
        var unloadedImages = [];
        entrySliderImages.each(function(){
          if(!this.complete && this.complete != undefined){
            unloadedImages.push(this);
            unloadedImagesCount++;
          }
        });
        if(unloadedImagesCount == 0){
          initAllEntrySliders(true);
        }else{
          var initAllEntrySlidersInvoked = false;
          var loadedImagesCount = 0;
          $(unloadedImages).bind('load', function (){
            loadedImagesCount++;
            if(loadedImagesCount === unloadedImagesCount){
              if(!initAllEntrySlidersInvoked){
                initAllEntrySlidersInvoked = true;
                initAllEntrySliders(true);
              }
            }
          });
          var timer = window.setTimeout( function() {
            window.clearTimeout(timer);
            $(unloadedImages).each(function() {
              if(this.complete || this.complete === undefined) {
                $(this).trigger('load');
              }
            });
          }, 50);

        }
      }

      loadEntrySlider();

      $(document).ready(function(){
        $(window).resize(function() {
          var timer = window.setTimeout( function() {
            window.clearTimeout(timer);
            resizeEntrySlider(entrySliders);
          }, 30 );
        });
      });
    }

    /* ---------------------------------------------------------------------- */
    /* jCarousel
    /* ---------------------------------------------------------------------- */

    if($().jcarousel){

      var carousels = $('.iconbox-carousel, .project-carousel, .post-carousel');
      var testimonialCarousels = $('.testimonial-carousel');

      function swipeCarousel(e, dir) {
        var carouselParent = $(e.currentTarget).parents().eq(2);
        if(dir.toLowerCase() == 'left' ){
          carouselParent.find('.jcarousel-next').trigger('click');
        }
        if(dir.toLowerCase() == 'right' ){
          carouselParent.find('.jcarousel-prev').trigger('click');
        }
      }

      function getCarouselScrollCount(carousel) {

        var scroll = 100000;
        if(carousel.data('scroll')){
          scroll = parseInt(carousel.data('scroll'));
        }
        var windowWidth = $(window).width();

        if(windowWidth < 480 ) {
          return 1;
        } else if(windowWidth < 768 ) {
          return Math.min(2, scroll);
        } else if(windowWidth < 960 ) {
          return Math.min(3, scroll);
        } else {
          return Math.min(4, scroll);
        }

      }

      function resetCarouselPosition(carousel) {
        if(carousel.data('resize')) {
          carousel.css('left', '0');
        }
      }

      function initBasicCarousel(carousels, bindGestures) {
        carousels.each(function(i) {
          var carousel = $(this);
          var carouselScrollCount = getCarouselScrollCount(carousel);
          carousel.jcarousel({
            scroll: carouselScrollCount,
            animation: 'normal',
            easing: 'easeOutCubic',
            auto: ( carousel.data('auto') ? parseInt( carousel.data('auto') ) : 0 ),
            wrap: 'last',
            itemFallbackDimension: 220,
            itemVisibleInCallback : function() {
              onBeforeAnimation : resetCarouselPosition(carousel);
              onAfterAnimation : resetCarouselPosition(carousel);
            }
          });
        });

        if(bindGestures && Modernizr.touch && $().swipe) {
          carousels.swipe({
            click       : function(e, target){
              $(target).trigger('click');
            },
            swipeLeft       : swipeCarousel,
            swipeRight      : swipeCarousel,
            allowPageScroll : 'auto'
          });
        }
      }

      function resizeBasicCarousel(carousels) {
        carousels.each(function() {
          var carousel = $(this);
          var carouselChildren = carousel.children('li');
          var carouselItemWidth = carouselChildren.first().outerWidth(true);
          var newWidth = carouselChildren.length * carouselItemWidth + 100;
          if(carousel.width() !== newWidth ) {
            carousel.css('width', newWidth).data('resize','true');
            initBasicCarousel(carousel, false);
            carousel.jcarousel('scroll', 1);
            var timer = window.setTimeout( function() {
              window.clearTimeout( timer );
              carousel.data('resize', null);
            }, 600 );
          }
        });
      }

      function initTestimonialCarousel(carousels) {
        carousels.each(function() {
          var carouselId = uuid().toString();
          var carouselParentId = uuid().toString();
          var carousel = $(this);
          var carouselSectionParent  = carousel.parent();
          carouselSectionParent.attr('id', carouselParentId);
          carousel.attr('id', carouselId);
          carousel.jcarousel({
            scroll: 1,
            visible: 1,
            wrap: 'last'
          });
          $('#'+carouselParentId+' .jcarousel-next').attr('id', carouselId+"-next");
          $('#'+carouselParentId+' .jcarousel-prev').attr('id', carouselId+"-prev");
        });

        if(Modernizr.touch && $().swipe) {
          carousels.swipe({
            click       : function(e, target){
              $(target).trigger('click');
            },
            swipeLeft       : swipeCarousel,
            swipeRight      : swipeCarousel,
            allowPageScroll : 'auto'
          });
        }
      }

      initBasicCarousel(carousels, true);
      initTestimonialCarousel(testimonialCarousels);

      $(document).ready(function () {
  
        $(window).resize(function() {
          var timer = window.setTimeout( function() {
            window.clearTimeout(timer);
            resizeBasicCarousel(carousels);
          }, 30 );
        });
        
      });

    }

    /* ---------------------------------------------------------------------- */
    /* Search
    /* ---------------------------------------------------------------------- */

    var searchSubmit = $('#search-submit');
    var searchInput = $('#s');
    if(searchSubmit.length>0){
      searchSubmit.bind("click", function(evt){
        return $.trim(searchInput.val()) != '';
      });
    }

    /* ---------------------------------------------------------------------- */
    /* Tiny Nav
    /* ---------------------------------------------------------------------- */

    if($().tinyNav){
      $('html').addClass('js');
      $("#navlist").tinyNav();
    }

    /* ---------------------------------------------------------------------- */
    /* Responsive Search (must be placed after Tiny Nav)
    /* ---------------------------------------------------------------------- */

    function setSearchBoxVisible(display){
      if(display){
        searchInput.fadeIn(500);
      }else{
        searchInput.fadeOut(500);
      }
    }

    var searchHandler = function() {
      var isSearchHidden = (searchInput.css('display')=='none');
      if(isSearchHidden){
        setSearchBoxVisible(true);
        return false;
      }else if($.trim(searchInput.val()) == ''){
        setSearchBoxVisible(false);
        return false;
      }else{
        return true;
      }
    };

    function doSearchResponsive(isInit){
      var formWidth= $('#navbar').width()-$('#nav').width();
      var searchWidth = searchInput.width();
      var isTiny = (searchInput.data('tiny')=='true');
      if(formWidth<=searchWidth){
        if(!isTiny){
          if(!searchInput.hasClass('small-search')){
            searchInput.addClass('small-search');
          }
          searchInput.data('tiny', 'true');
          setSearchBoxVisible(false);
          searchSubmit.bind('click', searchHandler);
        }
      }else{
        if(isInit){
          setSearchBoxVisible(true);
        }
        if(isTiny){
          searchInput.removeClass('small-search');
          searchInput.data('tiny', 'false');
          setSearchBoxVisible(true);
          searchSubmit.unbind('click', searchHandler);
        }
      }
    }

    function hideSearchResponsive(target){
      if(target.id!='s' && target.id!='search-submit'){
        var isSearchVisible = (searchInput.css('display')!='none');
        var isTiny = (searchInput.data('tiny')=='true');
        if(isSearchVisible && isTiny){
          setSearchBoxVisible(false);
        }
      }
    }
    $(document).ready(function(){
      $(window).resize(function() {
        doSearchResponsive(false);
      });

    });
    doSearchResponsive(true);

    if(Modernizr.touch) {
      $(document).on('touchstart', function (e) {
        var target = e.target;
        hideSearchResponsive(target);
        closePolyglotLanguageSwitcher(target);
        closeDdlevelsmenu(e, target);
      });
    }else{
      $(document).click(function (e) {
        closeDdlevelsmenu(e, '');
        hideSearchResponsive(e.target);
      });
    }

    function closeDdlevelsmenu(e, target){
      var close = true;
      var subuls = ddlevelsmenu.topitems['nav'];
      if(subuls){
		    for(var i=0; i<subuls.length; i++){
		      if($(subuls[i].parentNode).has(target).length>0){
		        close = false;
		      }
		    }
		  }
      if(close){
        subuls = ddlevelsmenu.subuls['nav'];
        for(i=0; i<subuls.length; i++){
          if($(subuls[i]).has(target).length>0){
            close = false;
          }
        }
      }
      if(close){
        subuls = ddlevelsmenu.subuls['nav'];
        for(i=0; i<subuls.length; i++){
          ddlevelsmenu.hidemenu(subuls[i].parentNode);
        }
      }
    }

    /* ---------------------------------------------------------------------- */
    /* Language Switcher
    /* ---------------------------------------------------------------------- */

    var polyglotLanguageSwitcher;
    var polyglotElement = $('#polyglotLanguageSwitcher');

    function initPolyglotLanguageSwitcher(){
      if($().polyglotLanguageSwitcher){
        polyglotLanguageSwitcher = polyglotElement.polyglotLanguageSwitcher({
          effect: 'fade',
          testMode: true
        });
      }
    }

    function closePolyglotLanguageSwitcher(target) {
      var l = polyglotElement.find(target).length;
      if(polyglotLanguageSwitcher && l==0){
        polyglotLanguageSwitcher.close();
      }
    }

    initPolyglotLanguageSwitcher();

    /* ---------------------------------------------------------------------- */
    /* Homepage Slider
    /* ---------------------------------------------------------------------- */

    if($().flexslider){

      function initSlider(slider){
        slider.flexslider({
          pauseOnHover: true, //Boolean: Pause the slideshow when hovering over slider, then resume when no longer hovering
          controlsContainer: ".flex-container" //Selector: Declare which container the navigation elements should be appended too. Default container is the flexSlider element. Example use would be ".flexslider-container", "#container", etc. If the given element is not found, the default action will be taken.
        });
        var next = slider.parent().find('.flex-direction-nav .next');
        var prev = slider.parent().find('.flex-direction-nav .prev');

        // Swipe gestures support
        if(Modernizr.touch && $().swipe) {
          function doSliderSwipe(e, dir ) {
            if(dir.toLowerCase() == 'left' ){
              next.trigger('click');
            }
            if(dir.toLowerCase() == 'right' ){
              prev.trigger('click');
            }
          }

          slider.swipe({
            click       : function(e, target){
              $(target).trigger('click');
            },
            swipeLeft       : doSliderSwipe,
            swipeRight      : doSliderSwipe,
            allowPageScroll : 'auto'
          });

        }
      }

      var sliders = $('.flexslider');
      if(sliders.length > 0){
        sliders.each(function() {
          initSlider($(this));
        });
      }

    }

    /* ---------------------------------------------------------------------- */
    /* Scroll to Top
    /* ---------------------------------------------------------------------- */

    if($().UItoTop){
      $().UItoTop({
        scrollSpeed: 600
      });
    }

    /* ---------------------------------------------------------------------- */
    /* Lightbox
    /* ---------------------------------------------------------------------- */

    function lightbox(){
      if($().fancybox){

        function swipeFancyBox(e, dir) {
          var buttonBox = $('#fancybox-buttons');
          var nextButton = buttonBox.find('.btnNext');
          var prevButton = buttonBox.find('.btnPrev');
          if(dir.toLowerCase() == 'left' && nextButton){
            nextButton.trigger('click');
          }
          if(dir.toLowerCase() == 'right' && prevButton){
            prevButton.trigger('click');
          }
        }

        $(".fancybox").fancybox({
          openEffect  : 'fade',
          closeEffect	: 'fade',
          nextEffect  : 'fade',
          prevEffect  : 'fade',
          arrows  : !Modernizr.touch,
          helpers     : {
            title   : {
              type : 'inside'
            },
            buttons  : {},
            media    : {}
          },
          beforeLoad : function() {
            this.title = 'Image ' + (this.index + 1) + ' of ' + this.group.length + (this.title ? ' - ' + this.title : '');

          /*
                     "this.element" refers to current element, so you can, for example, use the "alt" attribute of the image to store the title:
                     this.title = $(this.element).find('img').attr('alt');
                     */
          },
          afterShow : function(){
            if(Modernizr.touch && $().swipe){
              var fancyBoxOuter = $('.fancybox-wrap');
              var isSwipeAdded = fancyBoxOuter.data('swipe')==='true';
              if(!isSwipeAdded){
                fancyBoxOuter.data('swipe', 'true');
                fancyBoxOuter.swipe({
                  click       : function(e, target){
                    $(target).trigger('click');
                  },
                  swipeLeft       : swipeFancyBox,
                  swipeRight      : swipeFancyBox,
                  allowPageScroll : 'auto'
                });
              }
            }
          }
        });
      }
    }

    lightbox();

    /* ---------------------------------------------------------------------- */
    /* Portfolio Filter
    /* ---------------------------------------------------------------------- */

    if($().quicksand){

      function initQuickSand(){
        // get the action filter option item on page load
        var $filterType = $('#filter li.active a').attr('class');

        // get and assign the ourHolder element to the
        // $holder varible for use later
        var $holder = $('ul#gallery');

        // clone all items within the pre-assigned $holder element
        var $data = $holder.clone();

        // assign transition parameters
        var $preferences = {
          duration: 500,
          easing: 'easeInQuad'
        };

        // attempt to call Quicksand when a filter option
        // item is clicked
        $('#filter li a').click(function(e){
          if(isIE10()){
            $('video').each(function(){
              this.player.pause()
            });
            $('audio').each(function(){
              this.player.pause()
            });
          }
          // reset the active class on all the buttons
          $('#filter li').removeClass('active');
          // assign the class of the clicked filter option
          // element to our $filterType variable
          var $filterType = $(this).attr('class');
          $(this).parent().addClass('active');
          if($filterType == 'all'){
            // assign all li items to the $filteredData var when
            // the 'All' filter option is clicked
            var $filteredData = $data.find('li.entry');
          }
          else{
            // find all li elements that have our required $filterType
            // values for the data-type element
            var $filteredData = $data.find('li[data-type~=' + $filterType + ']');
          }
          // callback function
          $holder.quicksand($filteredData, $preferences, function(){
            lightbox();
            initAllEntrySliders(false);
            resizeVideoEmbed();
            initAllPlayers();
          });

        });
      }

      initQuickSand();

    }

    /* ---------------------------------------------------------------------- */
    /* Fix for YouTube Iframe Z-Index
    /* ---------------------------------------------------------------------- */

    $("iframe").each(function(){
      var ifr_source = $(this).attr('src');
      var wmode = "wmode=transparent";
      if(ifr_source.indexOf('?') != -1) {
        var getQString = ifr_source.split('?');
        var oldString = getQString[1];
        var newString = getQString[0];
        $(this).attr('src',newString+'?'+wmode+'&'+oldString);
      }
      else $(this).attr('src',ifr_source+'?'+wmode);
    });

    /* ---------------------------------------------------------------------- */
    /* Notification Boxes
    /* ---------------------------------------------------------------------- */

    $(".notification-close-info").click(function(){
      $(this).parent().fadeOut("fast");
      return false;
    });

    $(".notification-close-success").click(function(){
      $(this).parent().fadeOut("fast");
      return false;
    });

    $(".notification-close-warning").click(function(){
      $(this).parent().fadeOut("fast");
      return false;
    });

    $(".notification-close-error").click(function(){
      $(this).parent().fadeOut("fast");
      return false;
    });

    /* ---------------------------------------------------------------------- */
    /* Tabs
    /* ---------------------------------------------------------------------- */

    if($().tabs){
      $(".tabs").tabs({
        fx:{
          opacity: 'show'
        }
      });
    }

    /* ---------------------------------------------------------------------- */
    /* Toggle
    /* ---------------------------------------------------------------------- */

    if($().toggle){

      $(".toggle").each(function(){
        if($(this).attr('data-id') == 'open'){
          $(this).accordion({
            header: 'span', 
            collapsible: true, 
            autoHeight: false
          });
        }else{
          $(this).accordion({
            header: 'span', 
            collapsible: true, 
            autoHeight: false, 
            active: false
          });
        }
      });

    }

    /* ---------------------------------------------------------------------- */
    /* Accordion
    /* ---------------------------------------------------------------------- */

    if($().accordion){
      $(".accordion").accordion({
        header: 'span',
        collapsible: true,
        autoHeight: false
      });
    }

    /* ---------------------------------------------------------------------- */
    /* Form Validation
    /* ---------------------------------------------------------------------- */

    if($().validate){
      $("#contact-form, #comment-form").validate();
    }

    if($("#contact-form #submit")){
      $("#contact-form #submit").bind("click", function(evt){

        if($("#contact-form").valid()){

          $("#submit").attr('disabled', 'disabled');
          jQuery.ajax({
            type:"POST",
            url:"contact.php",
            data:getFormData(),
            statusCode: {
              200: function() {
                $(".notification-box-success").css('display', '');
                $("#submit").removeAttr('disabled', '');
                resetFormData();
              },
              500: function() {
                $(".notification-box-error").css('display', '');
                $("#submit").removeAttr('disabled');
              }
            }
          });
        }

        function getFormData(){
          var data = 'a='+evt.timeStamp;
          if($("#contact-form #name")){
            data += '&name='+$("#contact-form #name").attr('value');
          }
          if($("#contact-form #email")){
            data += '&email='+$("#contact-form #email").attr('value');
          }
          if($("#contact-form #website")){
            data += '&website='+$("#contact-form #website").attr('value');
          }
          if($("#contact-form #subject")){
            data += '&subject='+$("#contact-form #subject").attr('value');
          }
          if($("#contact-form #message")){
            data += '&message='+$("#contact-form #message").val();
          }
          return data;
        }

        function resetFormData(){
          var data = 'a='+evt.timeStamp;
          if($("#contact-form #name")){
            $("#contact-form #name").attr('value', '');
          }
          if($("#contact-form #email")){
            $("#contact-form #email").attr('value', '');
          }
          if($("#contact-form #website")){
            $("#contact-form #website").attr('value', '');
          }
          if($("#contact-form #subject")){
            $("#contact-form #subject").attr('value', '');
          }
          if($("#contact-form #message")){
            $("#contact-form #message").val('');
          }
          return data;
        }

        return false;
      });
    }

    /* ---------------------------------------------------------------------- */
    /* Newsletter Subscription
    /* ---------------------------------------------------------------------- */

    if($().validate){
      $("#newsletter-form, #send-newsletter-form").validate();
    }

    if($("#subscribe")){
      $("#subscribe").bind("click", function(evt){

        if($("#newsletter-form").valid()){
          $("#subscribe").attr('disabled', 'disabled');
          jQuery.ajax({
            type:"POST",
            url:"newsletter.php",
            data:getSubscribeFormData(),
            statusCode: {
              200: function() {
                $(".notification-box-success").css('display', '');
                $("#newsletter-form #subscribe").removeAttr('disabled', '');
                resetSubscribeFormData();
              },
              500: function() {
                $(".notification-box-error").css('display', '');
                $("#newsletter-form #subscribe").removeAttr('disabled');
              }
            }
          });
        }

        function getSubscribeFormData(){
          var data = 'action=subscribe';
          if($("#newsletter-form #newsletter")){
            data += '&email='+$("#newsletter-form #newsletter").attr('value');
          }
          return data;
        }

        function resetSubscribeFormData(){
          if($("#newsletter-form #newsletter")){
            $("#newsletter-form #newsletter").attr('value', '');
          }
        }

        return false;
      });
    }

    /* ---------------------------------------------------------------------- */
    /* Responsive Table
    /* ---------------------------------------------------------------------- */

    var switched = false;
    var updateTables = function() {
      if (($(window).width() < 767) && !switched ){
        switched = true;
        $("table.responsive").each(function(i, element) {
          splitTable($(element));
        });
        return true;
      }
      else if (switched && ($(window).width() > 767)) {
        switched = false;
        $("table.responsive").each(function(i, element) {
          unsplitTable($(element));
        });
      }
    };

    $(window).load(updateTables);
    $(window).bind("resize", updateTables);


    function splitTable(original)
    {
      original.wrap("<div class='table-wrapper' />");

      var copy = original.clone();
      copy.find("td:not(:first-child), th:not(:first-child)").css("display", "none");
      copy.removeClass("responsive");

      original.closest(".table-wrapper").append(copy);
      copy.wrap("<div class='pinned' />");
      original.wrap("<div class='scrollable' />");
    }

    function unsplitTable(original) {
      original.closest(".table-wrapper").find(".pinned").remove();
      original.unwrap();
      original.unwrap();
    }

    /* ---------------------------------------------------------------------- */
    /* Responsive Video Embeds
    /* ---------------------------------------------------------------------- */

    if($().fitVids){

      function resizeVideoEmbed(){
        $(".entry-video").fitVids();
      }

      resizeVideoEmbed();
    }

    /* ---------------------------------------------------------------------- */
    /* Self-Hosted Video and Audio Players
    /* ---------------------------------------------------------------------- */

    if($().mediaelementplayer){

      var playersCount = 0;
      var playersInitCount = 0;

      function initAllPlayers(){
        playersInitCount = 0;
        var videoPlayers = $('video');
        var audioPlayers = $('audio');
        playersCount = videoPlayers.length + audioPlayers.length;
        initPlayer(videoPlayers);
        initPlayer(audioPlayers);
      }

      function initPlayer(mediaPlayers){
        if(mediaPlayers && mediaPlayers.length>0){
          mediaPlayers.mediaelementplayer({
            // the order of controls you want on the control bar (and other plugins below)
            features: ['playpause','progress','current','duration',/*'tracks',*/'volume','fullscreen'],
            hideVolumeOnTouchDevices: true,
            success: function (media, node, player) {
              playersInitCount++;
              if(playersInitCount == playersCount){
                resizeVideoPlayer();
                resizeAudioPlayer();
              }
            }
          });
        }
      }

      function getPlayerWidthBasedOnWrapperWidth(player){
        if(player.closest('.large-video').length == 0){
          var	entryWidth = $('.project-carousel > li, .post-carousel > li, ul.portfolio-grid > li.one-fourth').width();
          if(entryWidth){
            if(entryWidth == 220) {
              return 220;
            }else if(entryWidth == 200){
              return 200;
            }else if(entryWidth == 172){
              return 172;
            }
          }
        }
        return -1;
      }

      function resizeVideoPlayer(){
        $('.mejs-video').each(function(i){
          var videoPlayer = $(this);
          var	winWidth = $(window).width();
          var	origWidth = videoPlayer.width();
          var	origHeight = videoPlayer.height();
          var	newWidth = origWidth;

          if(winWidth <= 458) {
            newWidth = 300;
          }else if(winWidth <= 746) {
            newWidth = 420;
          } else if(winWidth <= 938) {
            newWidth = 557;
          }

          var	newWidthTmp = getPlayerWidthBasedOnWrapperWidth(videoPlayer);
          if(newWidthTmp != -1){
            newWidth = newWidthTmp;
          }

          var newHeight = Math.round((newWidth / origWidth) * origHeight);

          if(origWidth != newWidth){
            videoPlayer.find('.mejs-layer').css({
              'width': newWidth, 
              'height': newHeight
            });
            videoPlayer.css({
              'width': newWidth, 
              'height': newHeight
            });

          }

          // hide video controls
          var mejsTimeVar = 'videoMejsTime'+i;
          var mejsVolumeSliderVar = 'videoMejsVolumeSlider'+i;
          if(newWidth <= 200){
            var mejsVolumeSlider = videoPlayer.find('.mejs-volume-slider');
            var mejsTime = videoPlayer.find('.mejs-time');
            if (mejsTime.length>0 && !videoPlayer.data(mejsTimeVar)){
              videoPlayer.data(mejsTimeVar, mejsTime.detach());
            }
            if (mejsVolumeSlider.length>0 && !videoPlayer.data(mejsVolumeSliderVar)){
              videoPlayer.data(mejsVolumeSliderVar, mejsVolumeSlider.detach());
            }
          }else{
            if(videoPlayer.data(mejsTimeVar)){
              var mejsTimeRail = videoPlayer.find('.mejs-time-rail');
              mejsTimeRail.after(videoPlayer.data(mejsTimeVar));
              videoPlayer.removeData(mejsTimeVar);
            }
            if(videoPlayer.data(mejsVolumeSliderVar)){
              var mejsVolumeButton = videoPlayer.find('.mejs-volume-button');
              mejsVolumeButton.append(videoPlayer.data(mejsVolumeSliderVar));
              videoPlayer.removeData(mejsVolumeSliderVar);
            }
          }

        });
      }

      function resizeAudioPlayer(){
        $('.mejs-audio').each(function(i){
          var audioPlayer = $(this);
          var	winWidth = $(window).width();
          var origWidth = audioPlayer.width();
          var	newWidth = origWidth;

          if(winWidth <= 458) {
            newWidth = 300;
          }else if(winWidth <= 746) {
            newWidth = 420;
          } else if(winWidth <= 938) {
            newWidth = 557;
          }

          var	newWidthTmp = getPlayerWidthBasedOnWrapperWidth(audioPlayer);
          if(newWidthTmp != -1){
            newWidth = newWidthTmp;
          }

          if(origWidth != newWidth){
            audioPlayer.find('.mejs-layer').css({
              'width': newWidth
            });
            audioPlayer.css({
              'width': newWidth
            });
          }

          // hide audio controls
          var mejsTimeVar = 'audioMejsTime'+i;
          var mejsHorizontalVolumeSliderVar = 'audioMejsHorizontalVolumeSlider'+i;
          if(newWidth <= 220){
            var mejsHorizontalVolumeSlider = audioPlayer.find('.mejs-horizontal-volume-slider');
            if (mejsHorizontalVolumeSlider.length>0 && !audioPlayer.data(mejsHorizontalVolumeSliderVar)){
              var x =  mejsHorizontalVolumeSlider.detach();
              audioPlayer.data(mejsHorizontalVolumeSliderVar, x);
            }
          }else{
            if(audioPlayer.data(mejsHorizontalVolumeSliderVar)){
              var mejsControls = audioPlayer.find('.mejs-controls');
              mejsControls.append(audioPlayer.data(mejsHorizontalVolumeSliderVar));
              audioPlayer.removeData(mejsHorizontalVolumeSliderVar);
            }
          }

          if(newWidth <= 200){
            var mejsTime = audioPlayer.find('.mejs-time');
            if (mejsTime.length>0 && !audioPlayer.data(mejsTimeVar)){
              audioPlayer.data(mejsTimeVar, mejsTime.detach());
            }
          }else{
            if(audioPlayer.data(mejsTimeVar)){
              var mejsTimeRail = audioPlayer.find('.mejs-time-rail');
              mejsTimeRail.after(audioPlayer.data(mejsTimeVar));
              audioPlayer.removeData(mejsTimeVar);
            }
          }
        });
      }

      $(document).ready(function(){
        $(window).resize(function() {
          resizeVideoPlayer();
          resizeAudioPlayer();
        });
      });

      initAllPlayers();

    }

    /* ---------------------------------------------------------------------- */
    /* Twitter Widget
    /* ---------------------------------------------------------------------- */

    if($().tweet){

      $(".tweet").tweet({
        username: "envato", // Change username here
        join_text: false,
        avatar_size: false, // you can activate the avatar
        count: 2, // number of tweets
        view_text: "view tweet on twitter",
        seconds_ago_text: "about %d seconds ago",
        a_minutes_ago_text: "about a minute ago",
        minutes_ago_text: "about %d minutes ago",
        a_hours_ago_text: "about an hour ago",
        hours_ago_text: "about %d hours ago",
        a_day_ago_text: "about a day ago",
        days_ago_text: "about %d days ago",
        template: "{avatar}{text}{join}{time}" // [string or function] template used to construct each tweet <li> - see code for available vars
      });

    }

    /* ---------------------------------------------------------------------- */
    /* Flickr Widget
    /* ---------------------------------------------------------------------- */

    if($().jflickrfeed){

      $('.flickr-feed').jflickrfeed({
        limit: 6,
        qstrings: {
          id: '52617155@N08' // Flickr ID (Flickr IDs can be found using this tool: http://idgettr.com/)
        },
        itemTemplate: '<li><a href="{{link}}" title="{{title}}" target="_blank"><img src="{{image_s}}" alt="{{title}}" /></a></li>'
      });

    }

    /* ---------------------------------------------------------------------- */
    /* Google Maps
    /* ---------------------------------------------------------------------- */

    var mapObject = jQuery('#map');
    if($().gMap && mapObject.length>0){
      var lat = mapObject.data('lat'); //uses data-lat attribute
      var lng = mapObject.data('lng'); //uses data-lng attribute
      var addr = mapObject.data('address'); //uses data-address attribute
      var zoom = mapObject.data('zoom'); //uses data-zoom attribute
      var markers = {};
      if(addr){
        markers['address'] = addr;
      }else{
        markers['latitude'] = lat;
        markers['longitude'] = lng;
      }

      mapObject.gMap({
        markers:[markers], 
        zoom: zoom
      });
    }

    /* ---------------------------------------------------------------------- */
    /* Sticky Footer
    /* ---------------------------------------------------------------------- */

    // Set minimum height so that the footer will stay at the bottom of the window even if there isn't enough content
    function setMinHeight() {
      var wrap = $('#wrap');
      var body = $('body');
      var content = $('#content');
      content.css('min-height',
        $(window).outerHeight(true)
        - ( body.outerHeight(true) - body.height() )
        - ( wrap.outerHeight(true) - wrap.height() )
        - $('#header').outerHeight(true)
        - ( content.outerHeight(true) - content.height() )
        - $('#footer').outerHeight(true)
        );
    }

    // Init
    setMinHeight();

    // Window resize
    $(document).ready(function(){
      $(window).resize(function() {
        var timer = window.setTimeout( function() {
          window.clearTimeout(timer);
          setMinHeight();
        }, 30);
      });
    });
    /* ---------------------------------------------------------------------- */
    /* Style Switcher
    /* ---------------------------------------------------------------------- */
    var sw = (window.location.href.indexOf('#nosw')<0);
    if($().styleSwitcher && sw){
      var styleSwitcher = $().styleSwitcher();
      styleSwitcher.loadStyleSwitcher();
      styleSwitcher.applySettings();
    }

  });
  
})(jQuery);