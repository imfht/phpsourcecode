(function() {
    var linechartResize;
    linechartResize = function() {
        $("#linechart-1").sparkline([160, 240, 120, 200, 180, 350, 230, 200, 280, 380, 400, 360, 300, 220, 200, 150, 40, 70, 180, 110, 200, 160, 200, 220], {
            type: "line",
            width: "100%",
            height: "226",
            lineColor: "#a5e1ff",
            fillColor: "rgba(241, 251, 255, 0.9)",
            lineWidth: 2,
            spotColor: "#a5e1ff",
            minSpotColor: "#bee3f6",
            maxSpotColor: "#a5e1ff",
            highlightSpotColor: "#80cff4",
            highlightLineColor: "#cccccc",
            spotRadius: 6,
            chartRangeMin: 0
        });
        $("#linechart-1").sparkline([100, 280, 150, 180, 220, 180, 130, 180, 180, 280, 260, 260, 200, 120, 200, 150, 100, 100, 180, 180, 200, 160, 180, 120], {
            type: "line",
            width: "100%",
            height: "226",
            lineColor: "#cfee74",
            fillColor: "rgba(244, 252, 225, 0.5)",
            lineWidth: 2,
            spotColor: "#b9e72a",
            minSpotColor: "#bfe646",
            maxSpotColor: "#b9e72a",
            highlightSpotColor: "#b9e72a",
            highlightLineColor: "#cccccc",
            spotRadius: 6,
            chartRangeMin: 0,
            composite: true
        });
        $("#linechart-2").sparkline([160, 240, 250, 280, 300, 250, 230, 200, 280, 380, 400, 360, 300, 220, 200, 150, 100, 100, 180, 180, 200, 160, 220, 140], {
            type: "line",
            width: "100%",
            height: "226",
            lineColor: "#a5e1ff",
            fillColor: "rgba(241, 251, 255, 0.9)",
            lineWidth: 2,
            spotColor: "#a5e1ff",
            minSpotColor: "#bee3f6",
            maxSpotColor: "#a5e1ff",
            highlightSpotColor: "#80cff4",
            highlightLineColor: "#cccccc",
            spotRadius: 6,
            chartRangeMin: 0
        });
        $("#linechart-3").sparkline([100, 280, 150, 180, 220, 180, 130, 180, 180, 280, 260, 260, 200, 120, 200, 150, 100, 100, 180, 180, 200, 160, 220, 140], {
            type: "line",
            width: "100%",
            height: "226",
            lineColor: "#cfee74",
            fillColor: "rgba(244, 252, 225, 0.5)",
            lineWidth: 2,
            spotColor: "#b9e72a",
            minSpotColor: "#bfe646",
            maxSpotColor: "#b9e72a",
            highlightSpotColor: "#b9e72a",
            highlightLineColor: "#cccccc",
            spotRadius: 6,
            chartRangeMin: 0
        });
        $("#composite-chart-1").sparkline([160, 220, 260, 120, 320, 260, 300, 160, 240, 100, 240, 120], {
            type: "bar",
            height: "226",
            barSpacing: 10,
            barWidth: 26,
            barColor: "#8fdbda"
        });
        return $("#composite-chart-1").sparkline([100, 280, 150, 180, 220, 180, 130, 180, 180, 280, 260, 260], {
            type: "line",
            width: "100%",
            height: "226",
            lineColor: "#cfee74",
            fillColor: "rgba(244, 252, 225, 0.5)",
            lineWidth: 2,
            spotColor: "#b9e72a",
            minSpotColor: "#bfe646",
            maxSpotColor: "#b9e72a",
            highlightSpotColor: "#b9e72a",
            highlightLineColor: "#cccccc",
            spotRadius: 6,
            chartRangeMin: 0,
            composite: true
        });
    };
    $(document).ready(function() {
        var $container, calendar, d, date, m, y;
        $("#barcharts").sparkline([160, 220, 260, 120, 320, 260, 300, 160, 240, 100, 240, 120], {
            type: "bar",
            height: "80",
            barSpacing: 6,
            barWidth: 12,
            barColor: "#8fdbda"
        });
        $("#barcharts2").sparkline([160, 220, 260, 120, 320, 260, 300, 160, 240, 100, 240, 120], {
            type: "bar",
            height: "80",
            barSpacing: 6,
            barWidth: 12,
            barColor: "#acaeaf"
        });
        $("#barcharts3").sparkline([160, 220, 260, 120, 320, 260, 300, 160, 240, 100, 240, 120], {
            type: "bar",
            height: "80",
            barSpacing: 6,
            barWidth: 12,
            barColor: "#f0ad4e"
        });
        $("#pie-chart").sparkline([2, 8, 6, 10], {
            type: "pie",
            height: "220",
            width: "220",
            offset: "+90",
            sliceColors: ["#a0eeed", "#81e970", "#f5af50", "#f46f50"]
        });
        $(".pie-chart1").easyPieChart({
            size: 220,
            lineWidth: 12,
            lineCap: "square",
            barColor: "#81e970",
            animate: 800,
            scaleColor: false
        });
        $(".pie-chart2").easyPieChart({
            size: 220,
            lineWidth: 12,
            lineCap: "square",
            barColor: "#f46f50",
            animate: 800,
            scaleColor: false
        });
        $(".pie-chart3").easyPieChart({
            size: 220,
            lineWidth: 12,
            lineCap: "square",
            barColor: "#fab43b",
            animate: 800,
            scaleColor: false
        });
        $(".navbar").mouseover(function() {
            $(".navbar").removeClass("closed");
            return setTimeout((function() {
                return $(".navbar").css({
                    overflow: "visible"
                });
            }), 350);
        });
        $(function() {
            var delta, lastScrollTop;
            lastScrollTop = 0;
            delta = 50;
            $(window).scroll(function(event) {
                var st;
                st = $(this).scrollTop();
                if (Math.abs(lastScrollTop - st) <= delta) {
                    return;
                }
                if (st > lastScrollTop) {
                    $('.navbar').addClass("closed");
                    $(".navbar").css({
                        overflow: "hidden"
                    });
                } else {
                    $('.navbar').removeClass("closed");
                    setTimeout((function() {
                        return $(".navbar").css({
                            overflow: "visible"
                        });
                    }), 350);
                }
                return lastScrollTop = st;
            });
            $('.navbar-toggle').click(function() {
                $('body, .navbar').toggleClass("nav-open");
                return $('.container-fluid.main-nav').toggleClass("open");
            });
            linechartResize();
            return $(window).resize(function() {
                return linechartResize();
            });
        });
        $("#dataTable1").dataTable({
            "sPaginationType": "full_numbers",
            aoColumnDefs: [{
                bSortable: false,
                aTargets: [0, -1]
            }]
        });
        $("#myTab a:last").tab("show");
        $("#popover").popover();
        $("#popover-left").popover({
            placement: "left"
        });
        $("#popover-top").popover({
            placement: "top"
        });
        $("#popover-right").popover({
            placement: "right"
        });
        $("#popover-bottom").popover({
            placement: "bottom"
        });
        $("#tooltip").tooltip();
        $("#tooltip-left").tooltip({
            placement: "left"
        });
        $("#tooltip-top").tooltip({
            placement: "top"
        });
        $("#tooltip-right").tooltip({
            placement: "right"
        });
        $("#tooltip-bottom").tooltip({
            placement: "bottom"
        });
        $("#vmap").vectorMap({
            map: "world_en",
            backgroundColor: null,
            color: "#fff",
            hoverOpacity: 0.2,
            selectedColor: "#fff",
            enableZoom: true,
            showTooltip: true,
            values: sample_data,
            scaleColors: ["#59cdfe", "#0079fe"],
            normalizeFunction: "polynomial"
        });
        date = new Date();
        d = date.getDate();
        m = date.getMonth();
        y = date.getFullYear();
        calendar = $("#calendar").fullCalendar({
            header: {
                left: "prev,next today",
                center: "title",
                right: "month,agendaWeek,agendaDay"
            },
            selectable: true,
            selectHelper: true,
            select: function(start, end, allDay) {
                var title;
                title = prompt("Event Title:");
                if (title) {
                    calendar.fullCalendar("renderEvent", {
                        title: title,
                        start: start,
                        end: end,
                        allDay: allDay
                    },
                    true);
                }
                return calendar.fullCalendar("unselect");
            },
            editable: true,
            events: [{
                title: "All Day Event",
                start: new Date(y, m, 1)
            },
            {
                title: "Long Event",
                start: new Date(y, m, d - 5),
                end: new Date(y, m, d - 2)
            },
            {
                id: 999,
                title: "Repeating Event",
                start: new Date(y, m, d - 3, 16, 0),
                allDay: false
            },
            {
                id: 999,
                title: "Repeating Event",
                start: new Date(y, m, d + 4, 16, 0),
                allDay: false
            },
            {
                title: "Meeting",
                start: new Date(y, m, d, 10, 30),
                allDay: false
            },
            {
                title: "Lunch",
                start: new Date(y, m, d, 14, 0),
                end: new Date(y, m, d, 16, 0),
                allDay: false
            },
            {
                title: "Birthday Party",
                start: new Date(y, m, d + 1, 19, 0),
                end: new Date(y, m, d + 1, 22, 30),
                allDay: false
            },
            {
                title: "Click for Google",
                start: new Date(y, m, 28),
                end: new Date(y, m, 29),
                url: "http://google.com/"
            }]
        });
        $container = $(".gallery-container");
        $container.isotope({});
        $(".gallery-filters a").click(function() {
            var selector;
            selector = $(this).attr("data-filter");
            $(".gallery-filters a.selected").removeClass("selected");
            $(this).addClass("selected");
            $container.isotope({
                filter: selector
            });
            return false;
        });
        $('.scrollbar').ClassyScroll({
            sliderOpacity: 1,
            wheelSpeed: 2,
            onscroll: function() {
                return $(this).prev().addClass("shadow");
            }
        });
        $('#popover').popover();
        $(".fancybox").fancybox({
            maxWidth: 700,
            height: 'auto',
            fitToView: false,
            autoSize: true,
            padding: 15,
            nextEffect: 'fade',
            prevEffect: 'fade',
            helpers: {
                title: {
                    type: "outside"
                }
            }
        });
        $('.select2able').select2();
        return $('.login-submit').click(function() {
            // return $('.login').addClass("submitted");
        });
    });
}).call(this);