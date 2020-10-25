

$(function() {

		// sparkline charts
		 var myvalues = [10,8,5,7,4,6,7,1,3,5,9,4,4,1];
  	$('.mini-graph.success').sparkline(myvalues, {type: 'bar', barColor: 'white',lineColor:'black',  height: '40'} );
    $('.inlinesparkline').sparkline(); 

		// sparkline charts
		 var myvalues = [10,8,5,3,5,7,4,6,7,1,9,4,4,1];
  	$('.mini-graph.pie').sparkline(myvalues, {type: 'pie', barColor: 'white', height: '40'} );

		// sparkline charts
		 var myvalues = [10,8,5,7,4,3,5,9,4,4,1];
  	$('.mini-graph.info').sparkline(myvalues, {type: 'bar', barColor: 'white',  height: '40'} );

		// sparkline charts
		 var myvalues = [10,8,5,7,4,6,7,1,3,5,9,4,4,1];
  	$('.mini-graph.danger').sparkline(myvalues, {type: 'bar', barColor: 'white',  height: '40'} );



		//Real time chart on main page
		var data = [],
			totalPoints = 300;

		function getRandomData() {

			if (data.length > 0)
				data = data.slice(1);

			// Do a random walk

			while (data.length < totalPoints) {

				var prev = data.length > 0 ? data[data.length - 1] : 50,
					y = prev + Math.random() * 10 - 5;

				if (y < 0) {
					y = 0;
				} else if (y > 100) {
					y = 100;
				}

				data.push(y);
			}

			// Zip the generated y values with the x values

			var res = [];
			for (var i = 0; i < data.length; ++i) {
				res.push([i, data[i]]);
			}

			return res;
		}

		// Set up the control widget

		var updateInterval = 30;
		$("#updateInterval").val(updateInterval).change(function () {
			var v = $(this).val();
			if (v && !isNaN(+v)) {
				updateInterval = +v;
				if (updateInterval < 1) {
					updateInterval = 1;
				} else if (updateInterval > 2000) {
					updateInterval = 2000;
				}
				$(this).val("" + updateInterval);
			}
		});

		var plot = $.plot("#placeholder", [ getRandomData() ], {
			series: {
				shadowSize: 0	// Drawing is faster without shadows
			},
			yaxis: {
				min: 0,
				max: 100
			},
			xaxis: {
				show: false
			}
		});

		function update() {

			plot.setData([getRandomData()]);

			// Since the axes don't change, we don't need to call plot.setupGrid()

			plot.draw();
			setTimeout(update, updateInterval);
		}

		update();
		// Randomly Generated Data

				
	});
