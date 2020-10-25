(function ($) {

    var eCalendar = function (options, object) {
        // Initializing global variables
        var adDay = new Date().getDate();
        var adMonth = new Date().getMonth();
        var adYear = new Date().getFullYear();
        var dDay = adDay;
        var dMonth = adMonth;
        var dYear = adYear;
        var instance = object;

        var settings = $.extend({}, $.fn.eCalendar.defaults, options);

        function lpad(value, length, pad) {
            if (typeof pad == 'undefined') {
                pad = '0';
            }
            var p;
            for (var i = 0; i < length; i++) {
                p += pad;
            }
            return (p + value).slice(-length);
        }

        var mouseOver = function () {
            $(this).addClass('c-nav-btn-over');
        };
        var mouseLeave = function () {
            $(this).removeClass('c-nav-btn-over');
        };
		//鼠标移上左侧日期
        var mouseOverEvent = function () {
           $(this).addClass('c-event-over');
            var d = $(this).attr('data-event-day');
           $('div.c-event-item[data-event-day="' + d + '"]').addClass('c-item-over');
        };
		//鼠标移出左侧日期
        var mouseLeaveEvent = function () {
           $(this).removeClass('c-event-over')
           var d = $(this).attr('data-event-day');
           $('div.c-event-item[data-event-day="' + d + '"]').removeClass('c-item-over');
        };
		//鼠标点击左侧日期
		var mouseClickEvent = function(){
			var d = $(this).attr('data-event-day');
			$('div.c-event-item').removeClass('show').addClass('hide');//隐藏所有数据
            $('div.c-event-item[data-event-day="' + d + '"]').removeClass('hide').addClass('show');//显示点击日期的数据
            $('.data-head > span').html(dMonth+1+'-'+d);
		}
		//鼠标移上右侧详情
        var mouseOverItem = function () {
            $(this).addClass('c-item-over');
            var d = $(this).attr('data-event-day');
            $('div.c-event[data-event-day="' + d + '"]').addClass('c-event-over');
        };
		//鼠标移出右侧详情
        var mouseLeaveItem = function () {
            $(this).removeClass('c-item-over')
            var d = $(this).attr('data-event-day');
            $('div.c-event[data-event-day="' + d + '"]').removeClass('c-event-over');
        };
        var nextMonth = function () {
            if (dMonth < 11) {
                dMonth++;
            } else {
                dMonth = 0;
                dYear++;
            }
            print();
        };
        var previousMonth = function () {
            if (dMonth > 0) {
                dMonth--;
            } else {
                dMonth = 11;
                dYear--;
            }
            print();
        };

        function loadEvents() {
            if (typeof settings.url != 'undefined' && settings.url != '') {
                $.ajax({url: settings.url,
                    async: false,
                    success: function (result) {
                        settings.events = result.data;
                    }
                });
            }
        }

        function print() {
            loadEvents();
            var dWeekDayOfMonthStart = new Date(dYear, dMonth, 1).getDay();
            var dLastDayOfMonth = new Date(dYear, dMonth + 1, 0).getDate();
            var dLastDayOfPreviousMonth = new Date(dYear, dMonth , 0).getDate() - dWeekDayOfMonthStart + 1;

            var cBody = $('<div/>').addClass('c-grid');
            var cEvents = $('<div/>').addClass('c-event-grid');
            var cEventsBody = $('<div/>').addClass('c-event-body');
            var cTasksBody = $('<div/>').addClass('c-task-body');
           /*  cEvents.append($('<div/>').addClass('c-event-title c-pad-top').html(settings.eventTitle)); */
            cEvents.append(cTasksBody);
            cEvents.append(cEventsBody);
			cTasksBody.append('<div class="data-head">任务<span>'+(dMonth+1)+'-'+dDay+'</span><a href="./index.php?m=task&a=add" class="quick-add"><i class="icon-plus"></i></a></div>');
			cEventsBody.append('<div class="data-head">日程<span>'+(dMonth+1)+'-'+dDay+'</span><a href="./index.php?m=event&a=add" class="quick-add"><i class="icon-plus"></i></a></div>');
            var cNext = $('<div/>').addClass('c-next c-grid-title c-pad-top');
            var cMonth = $('<div/>').addClass('c-month c-grid-title c-pad-top');
            var cPrevious = $('<div/>').addClass('c-previous c-grid-title c-pad-top');
            cPrevious.html(settings.textArrows.previous);
            cMonth.html(settings.months[dMonth] + ' ' + dYear);
            cNext.html(settings.textArrows.next);

            cPrevious.on('mouseover', mouseOver).on('mouseleave', mouseLeave).on('click', previousMonth);
            cNext.on('mouseover', mouseOver).on('mouseleave', mouseLeave).on('click', nextMonth);

            cBody.append(cPrevious);
            cBody.append(cMonth);
            cBody.append(cNext);
            for (var i = 0; i < settings.weekDays.length; i++) {
                var cWeekDay = $('<div/>').addClass('c-week-day c-pad-top');
                cWeekDay.html(settings.weekDays[i]);
                cBody.append(cWeekDay);
            }
            var day = 1;
            var dayOfNextMonth = 1;
            for (var i = 0; i < 42; i++) {
                var cDay = $('<div/>');
                if (i < dWeekDayOfMonthStart) {
                    cDay.addClass('c-day-previous-month c-pad-top');
                    cDay.html(dLastDayOfPreviousMonth++);
                } else if (day <= dLastDayOfMonth) {
                    cDay.addClass('c-day c-pad-top');
                    if (day == dDay && adMonth == dMonth && adYear == dYear) {
                        cDay.addClass('c-today');
                    }
                    for (var j = 0; j < settings.events.length; j++) {
                        var d = settings.events[j].datetime;
						d = new Date(d * 1000);
                        if (d.getDate() == day && (d.getMonth()) == dMonth && d.getFullYear() == dYear) {
                            cDay.addClass('c-event').attr('data-event-day', d.getDate());
                            cDay.on('mouseover', mouseOverEvent).on('mouseleave', mouseLeaveEvent);
                            cDay.on('click', mouseClickEvent);//点击日期，显示当天详情，其它时间的隐藏
                        }
                    }
                    cDay.html(day++);
                } else {
                    cDay.addClass('c-day-next-month c-pad-top');
                    cDay.html(dayOfNextMonth++);
                }
                cBody.append(cDay);
            }

			var eventList = $('<div/>').addClass('c-event-list');
			var taskList = $('<div/>').addClass('c-task-list');
			
            for (var i = 0; i < settings.events.length; i++) {
                var d = settings.events[i].datetime;
				d = new Date(d * 1000);
				var t = settings.events[i].type;

                if ((d.getMonth()) == dMonth && d.getFullYear() == dYear) {
                    var date = lpad(d.getMonth(), 2) + '-' + lpad(d.getDate(), 2);
					//如果日期是今天，则显示右侧详细内容，否则隐藏
					if(d.getDate() == dDay){
						var item = $('<div/>').addClass('c-event-item show');
					}else{
						var item = $('<div/>').addClass('c-event-item hide');
					}
                    var title = $('<div/>').addClass('title').html(settings.events[i].title + '<br/>');
					
                    item.attr('data-event-day', d.getDate());
                    item.on('mouseover', mouseOverItem).on('mouseleave', mouseLeaveItem);
                    item.append(title);
					//根据type将任务和日程分至各自栏目
					if(t == 'task'){
						taskList.append(item);
					}else if(t == 'event'){
						eventList.append(item);
					}
                }
            }
            $(instance).addClass('calendar');
            cTasksBody.append(taskList);
			cEventsBody.append(eventList);
			
            $(instance).html(cBody).append(cEvents);
        }

        return print();
    }

    $.fn.eCalendar = function (oInit) {
        return this.each(function () {
            return eCalendar(oInit, $(this));
        });
    };

    // plugin defaults
    $.fn.eCalendar.defaults = {
        weekDays: [ '日','一', '二', '三', '四', '五', '六'],
        months: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
        textArrows: {previous: '<', next: '>'},
        eventTitle: '详情',
        url: 'index.php?m=index&a=calendar'
    };
}(jQuery));