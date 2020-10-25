$(function() {

//Basic Flot Graph
		var d1 = [];
		for (var i = 0; i < 14; i += 0.5) {
			d1.push([i, Math.sin(i)]);
		}

		var d2 = [[0, 3], [4, 8], [8, 5], [9, 13]];

		// A null signifies separate line segments

		var d3 = [[0, 12], [7, 12], null, [7, 2.5], [12, 2.5]];

		$.plot("#flotOne", [ d1, d2, d3 ]);


//Series types of graph


var d1 = [];
for (var i = 0; i < 14; i += 0.5) {
	d1.push([i, Math.sin(i)]);
}

var d2 = [[0, 3], [4, 8], [8, 5], [9, 13]];

var d3 = [];
for (var i = 0; i < 14; i += 0.5) {
	d3.push([i, Math.cos(i)]);
}

var d4 = [];
for (var i = 0; i < 14; i += 0.1) {
	d4.push([i, Math.sqrt(i * 10)]);
}

var d5 = [];
for (var i = 0; i < 14; i += 0.5) {
	d5.push([i, Math.sqrt(i)]);
}

var d6 = [];
for (var i = 0; i < 14; i += 0.5 + Math.random()) {
	d6.push([i, Math.sqrt(2*i + Math.sin(i) + 5)]);
}

$.plot("#flotTwo", [{
	data: d1,
	lines: { show: true, fill: true }
}, {
	data: d2,
	bars: { show: true }
}, {
	data: d3,
	points: { show: true }
}, {
	data: d4,
	lines: { show: true }
}, {
	data: d5,
	lines: { show: true },
	points: { show: true }
}, {
	data: d6,
	lines: { show: true, steps: true }
}]);


//Toggling series

var datasets = {
			"usa": {
				label: "USA",
				data: [[1988, 483994], [1989, 479060], [1990, 457648], [1991, 401949], [1992, 424705], [1993, 402375], [1994, 377867], [1995, 357382], [1996, 337946], [1997, 336185], [1998, 328611], [1999, 329421], [2000, 342172], [2001, 344932], [2002, 387303], [2003, 440813], [2004, 480451], [2005, 504638], [2006, 528692]]
			},        
			"russia": {
				label: "Russia",
				data: [[1988, 218000], [1989, 203000], [1990, 171000], [1992, 42500], [1993, 37600], [1994, 36600], [1995, 21700], [1996, 19200], [1997, 21300], [1998, 13600], [1999, 14000], [2000, 19100], [2001, 21300], [2002, 23600], [2003, 25100], [2004, 26100], [2005, 31100], [2006, 34700]]
			},
			"uk": {
				label: "UK",
				data: [[1988, 62982], [1989, 62027], [1990, 60696], [1991, 62348], [1992, 58560], [1993, 56393], [1994, 54579], [1995, 50818], [1996, 50554], [1997, 48276], [1998, 47691], [1999, 47529], [2000, 47778], [2001, 48760], [2002, 50949], [2003, 57452], [2004, 60234], [2005, 60076], [2006, 59213]]
			},
			"germany": {
				label: "Germany",
				data: [[1988, 55627], [1989, 55475], [1990, 58464], [1991, 55134], [1992, 52436], [1993, 47139], [1994, 43962], [1995, 43238], [1996, 42395], [1997, 40854], [1998, 40993], [1999, 41822], [2000, 41147], [2001, 40474], [2002, 40604], [2003, 40044], [2004, 38816], [2005, 38060], [2006, 36984]]
			},
			"denmark": {
				label: "Denmark",
				data: [[1988, 3813], [1989, 3719], [1990, 3722], [1991, 3789], [1992, 3720], [1993, 3730], [1994, 3636], [1995, 3598], [1996, 3610], [1997, 3655], [1998, 3695], [1999, 3673], [2000, 3553], [2001, 3774], [2002, 3728], [2003, 3618], [2004, 3638], [2005, 3467], [2006, 3770]]
			},
			"sweden": {
				label: "Sweden",
				data: [[1988, 6402], [1989, 6474], [1990, 6605], [1991, 6209], [1992, 6035], [1993, 6020], [1994, 6000], [1995, 6018], [1996, 3958], [1997, 5780], [1998, 5954], [1999, 6178], [2000, 6411], [2001, 5993], [2002, 5833], [2003, 5791], [2004, 5450], [2005, 5521], [2006, 5271]]
			},
			"norway": {
				label: "Norway",
				data: [[1988, 4382], [1989, 4498], [1990, 4535], [1991, 4398], [1992, 4766], [1993, 4441], [1994, 4670], [1995, 4217], [1996, 4275], [1997, 4203], [1998, 4482], [1999, 4506], [2000, 4358], [2001, 4385], [2002, 5269], [2003, 5066], [2004, 5194], [2005, 4887], [2006, 4891]]
			}
		};

		// hard-code color indices to prevent them from shifting as
		// countries are turned on/off

		var i = 0;
		$.each(datasets, function(key, val) {
			val.color = i;
			++i;
		});

		// insert checkboxes 
		var choiceContainer = $("#choices");
		$.each(datasets, function(key, val) {
			choiceContainer.append("<input type='checkbox' class='choiceinput' name='" + key +
				"' checked='checked' id='id" + key + "'></input>" +
				"<label for='id" + key + "'>"
				+ val.label + "</label>");
		});


		choiceContainer.find("input").click(plotAccordingToChoices);

		function plotAccordingToChoices() {

			var data = [];

			choiceContainer.find("input:checked").each(function () {
				var key = $(this).attr("name");
				if (key && datasets[key]) {
					data.push(datasets[key]);
				}
			});

			if (data.length > 0) {
				$.plot("#flotThree", data, {
					yaxis: {
						min: 0
					},
					xaxis: {
						tickDecimals: 0
					}
				});
			}
		}

		plotAccordingToChoices();

// All types of pie charts

// Example Data

		//var data = [
		//	{ label: "Series1",  data: 10},
		//	{ label: "Series2",  data: 30},
		//	{ label: "Series3",  data: 90},
		//	{ label: "Series4",  data: 70},
		//	{ label: "Series5",  data: 80},
		//	{ label: "Series6",  data: 110}
		//];

		//var data = [
		//	{ label: "Series1",  data: [[1,10]]},
		//	{ label: "Series2",  data: [[1,30]]},
		//	{ label: "Series3",  data: [[1,90]]},
		//	{ label: "Series4",  data: [[1,70]]},
		//	{ label: "Series5",  data: [[1,80]]},
		//	{ label: "Series6",  data: [[1,0]]}
		//];

		//var data = [
		//	{ label: "Series A",  data: 0.2063},
		//	{ label: "Series B",  data: 38888}
		//];

		// Randomly Generated Data

		var data = [],
			series = Math.floor(Math.random() * 6) + 3;

		for (var i = 0; i < series; i++) {
			data[i] = {
				label: "Series" + (i + 1),
				data: Math.floor(Math.random() * 100) + 1
			}
		}

		var placeholder = $("#placeholder");

		$("#example-1").click(function() {

			placeholder.unbind();

			$("#title").text("Default pie chart");
			$("#description").text("The default pie chart with no options set.");

			$.plot(placeholder, data, {
				series: {
					pie: { 
						show: true
					}
				}
			});

			setCode([
				"$.plot('#placeholder', data, {",
				"    series: {",
				"        pie: {",
				"            show: true",
				"        }",
				"    }",
				"});"
			]);
		});

		$("#example-2").click(function() {

			placeholder.unbind();

			$("#title").text("Default without legend");
			$("#description").text("The default pie chart when the legend is disabled. Since the labels would normally be outside the container, the chart is resized to fit.");

			$.plot(placeholder, data, {
				series: {
					pie: { 
						show: true
					}
				},
				legend: {
					show: false
				}
			});

			setCode([
				"$.plot('#placeholder', data, {",
				"    series: {",
				"        pie: {",
				"            show: true",
				"        }",
				"    },",
				"    legend: {",
				"        show: false",
				"    }",
				"});"
			]);
		});

		$("#example-3").click(function() {

			placeholder.unbind();

			$("#title").text("Custom Label Formatter");
			$("#description").text("Added a semi-transparent background to the labels and a custom labelFormatter function.");

			$.plot(placeholder, data, {
				series: {
					pie: { 
						show: true,
						radius: 1,
						label: {
							show: true,
							radius: 1,
							formatter: labelFormatter,
							background: {
								opacity: 0.8
							}
						}
					}
				},
				legend: {
					show: false
				}
			});

			setCode([
				"$.plot('#placeholder', data, {",
				"    series: {",
				"        pie: {",
				"            show: true,",
				"            radius: 1,",
				"            label: {",
				"                show: true,",
				"                radius: 1,",
				"                formatter: labelFormatter,",
				"                background: {",
				"                    opacity: 0.8",
				"                }",
				"            }",
				"        }",
				"    },",
				"    legend: {",
				"        show: false",
				"    }",
				"});"
			]);
		});

		$("#example-4").click(function() {

			placeholder.unbind();

			$("#title").text("Label Radius");
			$("#description").text("Slightly more transparent label backgrounds and adjusted the radius values to place them within the pie.");

			$.plot(placeholder, data, {
				series: {
					pie: { 
						show: true,
						radius: 1,
						label: {
							show: true,
							radius: 3/4,
							formatter: labelFormatter,
							background: {
								opacity: 0.5
							}
						}
					}
				},
				legend: {
					show: false
				}
			});

			setCode([
				"$.plot('#placeholder', data, {",
				"    series: {",
				"        pie: {",
				"            show: true,",
				"            radius: 1,",
				"            label: {",
				"                show: true,",
				"                radius: 3/4,",
				"                formatter: labelFormatter,",
				"                background: {",
				"                    opacity: 0.5",
				"                }",
				"            }",
				"        }",
				"    },",
				"    legend: {",
				"        show: false",
				"    }",
				"});"
			]);
		});

		$("#example-5").click(function() {

			placeholder.unbind();

			$("#title").text("Label Styles #1");
			$("#description").text("Semi-transparent, black-colored label background.");

			$.plot(placeholder, data, {
				series: {
					pie: { 
						show: true,
						radius: 1,
						label: {
							show: true,
							radius: 3/4,
							formatter: labelFormatter,
							background: { 
								opacity: 0.5,
								color: "#000"
							}
						}
					}
				},
				legend: {
					show: false
				}
			});

			setCode([
				"$.plot('#placeholder', data, {",
				"    series: {",
				"        pie: { ",
				"            show: true,",
				"            radius: 1,",
				"            label: {",
				"                show: true,",
				"                radius: 3/4,",
				"                formatter: labelFormatter,",
				"                background: { ",
				"                    opacity: 0.5,",
				"                    color: '#000'",
				"                }",
				"            }",
				"        }",
				"    },",
				"    legend: {",
				"        show: false",
				"    }",
				"});"
			]);
		});

		$("#example-6").click(function() {

			placeholder.unbind();

			$("#title").text("Label Styles #2");
			$("#description").text("Semi-transparent, black-colored label background placed at pie edge.");

			$.plot(placeholder, data, {
				series: {
					pie: { 
						show: true,
						radius: 3/4,
						label: {
							show: true,
							radius: 3/4,
							formatter: labelFormatter,
							background: { 
								opacity: 0.5,
								color: "#000"
							}
						}
					}
				},
				legend: {
					show: false
				}
			});

			setCode([
				"$.plot('#placeholder', data, {",
				"    series: {",
				"        pie: {",
				"            show: true,",
				"            radius: 3/4,",
				"            label: {",
				"                show: true,",
				"                radius: 3/4,",
				"                formatter: labelFormatter,",
				"                background: {",
				"                    opacity: 0.5,",
				"                    color: '#000'",
				"                }",
				"            }",
				"        }",
				"    },",
				"    legend: {",
				"        show: false",
				"    }",
				"});"
			]);
		});

		$("#example-7").click(function() {

			placeholder.unbind();

			$("#title").text("Hidden Labels");
			$("#description").text("Labels can be hidden if the slice is less than a given percentage of the pie (10% in this case).");

			$.plot(placeholder, data, {
				series: {
					pie: { 
						show: true,
						radius: 1,
						label: {
							show: true,
							radius: 2/3,
							formatter: labelFormatter,
							threshold: 0.1
						}
					}
				},
				legend: {
					show: false
				}
			});

			setCode([
				"$.plot('#placeholder', data, {",
				"    series: {",
				"        pie: {",
				"            show: true,",
				"            radius: 1,",
				"            label: {",
				"                show: true,",
				"                radius: 2/3,",
				"                formatter: labelFormatter,",
				"                threshold: 0.1",
				"            }",
				"        }",
				"    },",
				"    legend: {",
				"        show: false",
				"    }",
				"});"
			]);
		});

		$("#example-8").click(function() {

			placeholder.unbind();

			$("#title").text("Combined Slice");
			$("#description").text("Multiple slices less than a given percentage (5% in this case) of the pie can be combined into a single, larger slice.");

			$.plot(placeholder, data, {
				series: {
					pie: { 
						show: true,
						combine: {
							color: "#999",
							threshold: 0.05
						}
					}
				},
				legend: {
					show: false
				}
			});

			setCode([
				"$.plot('#placeholder', data, {",
				"    series: {",
				"        pie: {",
				"            show: true,",
				"            combine: {",
				"                color: '#999',",
				"                threshold: 0.1",
				"            }",
				"        }",
				"    },",
				"    legend: {",
				"        show: false",
				"    }",
				"});"
			]);
		});

		$("#example-9").click(function() {

			placeholder.unbind();

			$("#title").text("Rectangular Pie");
			$("#description").text("The radius can also be set to a specific size (even larger than the container itself).");

			$.plot(placeholder, data, {
				series: {
					pie: { 
						show: true,
						radius: 500,
						label: {
							show: true,
							formatter: labelFormatter,
							threshold: 0.1
						}
					}
				},
				legend: {
					show: false
				}
			});

			setCode([
				"$.plot('#placeholder', data, {",
				"    series: {",
				"        pie: {",
				"            show: true,",
				"            radius: 500,",
				"            label: {",
				"                show: true,",
				"                formatter: labelFormatter,",
				"                threshold: 0.1",
				"            }",
				"        }",
				"    },",
				"    legend: {",
				"        show: false",
				"    }",
				"});"
			]);
		});

		$("#example-10").click(function() {

			placeholder.unbind();

			$("#title").text("Tilted Pie");
			$("#description").text("The pie can be tilted at an angle.");

			$.plot(placeholder, data, {
				series: {
					pie: { 
						show: true,
						radius: 1,
						tilt: 0.5,
						label: {
							show: true,
							radius: 1,
							formatter: labelFormatter,
							background: {
								opacity: 0.8
							}
						},
						combine: {
							color: "#999",
							threshold: 0.1
						}
					}
				},
				legend: {
					show: false
				}
			});

			setCode([
				"$.plot('#placeholder', data, {",
				"    series: {",
				"        pie: {",
				"            show: true,",
				"            radius: 1,",
				"            tilt: 0.5,",
				"            label: {",
				"                show: true,",
				"                radius: 1,",
				"                formatter: labelFormatter,",
				"                background: {",
				"                    opacity: 0.8",
				"                }",
				"            },",
				"            combine: {",
				"                color: '#999',",
				"                threshold: 0.1",
				"            }",
				"        }",
				"    },",
				"    legend: {",
				"        show: false",
				"    }",
				"});",
			]);
		});

		$("#example-11").click(function() {

			placeholder.unbind();

			$("#title").text("Donut Hole");
			$("#description").text("A donut hole can be added.");

			$.plot(placeholder, data, {
				series: {
					pie: { 
						innerRadius: 0.5,
						show: true
					}
				}
			});

			setCode([
				"$.plot('#placeholder', data, {",
				"    series: {",
				"        pie: {",
				"            innerRadius: 0.5,",
				"            show: true",
				"        }",
				"    }",
				"});"
			]);
		});

		$("#example-12").click(function() {

			placeholder.unbind();

			$("#title").text("Interactivity");
			$("#description").text("The pie can be made interactive with hover and click events.");

			$.plot(placeholder, data, {
				series: {
					pie: { 
						show: true
					}
				},
				grid: {
					hoverable: true,
					clickable: true
				}
			});

			setCode([
				"$.plot('#placeholder', data, {",
				"    series: {",
				"        pie: {",
				"            show: true",
				"        }",
				"    },",
				"    grid: {",
				"        hoverable: true,",
				"        clickable: true",
				"    }",
				"});"
			]);

			placeholder.bind("plothover", function(event, pos, obj) {

				if (!obj) {
					return;
				}

				var percent = parseFloat(obj.series.percent).toFixed(2);
				$("#hover").html("<span style='font-weight:bold; color:" + obj.series.color + "'>" + obj.series.label + " (" + percent + "%)</span>");
			});

			placeholder.bind("plotclick", function(event, pos, obj) {

				if (!obj) {
					return;
				}

				percent = parseFloat(obj.series.percent).toFixed(2);
				alert(""  + obj.series.label + ": " + percent + "%");
			});
		});

		// Show the initial default chart

		$("#example-1").click();


});


function labelFormatter(label, series) {
	return "<div style='font-size:8pt; text-align:center; padding:2px; color:white;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
}

//

function setCode(lines) {
	$("#code").text(lines.join("\n"));
}
