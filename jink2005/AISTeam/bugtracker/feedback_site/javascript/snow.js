SnowShow = function() {
	var no = 25; // snow number
	var speed = 15; // smaller number moves the snow faster
	var snowflake = "images/snow.gif";
	var dx, xp, yp;    // coordinate and position variables
	var am, stx, sty;  // amplitude and step variables

	var Generated = 0;
	

	var ShowGenerate = function() {
		var i;

		if (Generated) {
			return;
		}

		dx = new Array();
		xp = new Array();
		yp = new Array();
		am = new Array();
		stx = new Array();
		sty = new Array();
		for (i = 0; i < no; ++ i) {  
			var div;

			dx[i] = 0;                        // set coordinate variables
			xp[i] = Math.random()*(document.body.clientWidth-50);  // set position variables
			yp[i] = Math.random()*document.body.clientHeight;
			am[i] = Math.random()*20;         // set amplitude variables
			stx[i] = 0.02 + Math.random()/10; // set step variables
			sty[i] = 0.7 + Math.random();     // set step variables
	
			div = document.createElement('DIV');
			div.id = 'dot'+i;
			document.body.appendChild(div);
			div.style.position = 'absolute';
			div.style.zIndex = 10000;
			div.style.visibility = 'visible';
			div.style.top = 15;
			div.style.left = 15;
			div.innerHTML = '<img src="'+snowflake+'" border="0">';
		}
		Generated = 1;
	};
	return {
		Show: function() {
			var div, i;
			ShowGenerate();
			for (i = 0; i < no; ++ i) {  // iterate for every dot
				div = document.getElementById("dot"+i);
				yp[i] += sty[i];
				if (yp[i] > document.body.clientHeight+50) {
					xp[i] = Math.random()*(document.body.clientWidth-am[i]-30);
					yp[i] = 0;
					stx[i] = 0.02 + Math.random()/10;
					sty[i] = 0.7 + Math.random();
				}
				dx[i] += stx[i];
				div.style.top = yp[i];
				div.style.left = xp[i] + am[i]*Math.sin(dx[i]);
			}
			setTimeout("SnowShow.Show()", speed);
		}
	};
}();
