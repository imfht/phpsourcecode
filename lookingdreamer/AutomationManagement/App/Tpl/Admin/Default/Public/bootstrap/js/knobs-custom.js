// Knobs

    $(".knob").knob({
	        'min':0,
	        'max':100,
	        'readOnly': true,
	        'width': 120,
	        'height': 120,
	        'thickness': 0.3,
	        'tickColorizeValues': true,
					'bgColor' : '#b2d9ed',
					'fgColor' : '#FFFFFF',
					'inputColor': '#FFFFFF'
	    });


//Knob refresh or update using ajax
$('.panel-refresh').click(function(e){
	e.preventDefault();
	var number = 1 + Math.floor(Math.random() * 100);
	$(this).parent().parent().parent().parent().find("input").val(number);
	parentClass=	$(this).parent().parent().parent().parent();
	var currentTime = new Date()

	
 $({value: 0}).animate({value: number}, {
				        duration: 500,
				        easing:'swing',
				        step: function() 
				        {
				            parentClass.find(".knob").val(Math.ceil(this.value)).trigger('change');
				      			parentClass.find('.panel-footer').html('<span class="text-info">Last updated : ' + moment().format('h:mm:ss a') + ' </span> ');
							  }
				    })
});

//real time knob
setInterval(function() {
	var number = 1 + Math.floor(Math.random() * 100);
	var pre_num= $('.realtime').val();
	$('.realtime-footer').html('<span class="text-info">Last updated : ' + moment().format('h:mm:ss a') + ' </span> ');
	
 $({value: pre_num}).animate({value: number}, {
				        duration: 2000,
				        easing:'swing',
				        step: function() 
				        {
				            $('.realtime').val(Math.ceil(this.value)).trigger('change');
				        }
				    })
	
      	
},
2000);

/* different knobs for demo */

    $(".knob-demo").knob({
        change : function (value) {
            //console.log("change : " + value);
        },
        release : function (value) {
            //console.log(this.$.attr('value'));
            console.log("release : " + value);
        },
        cancel : function () {
            console.log("cancel : ", this);
        },
        draw : function () {

            // "tron" case
            if(this.$.data('skin') == 'tron') {

                var a = this.angle(this.cv)  // Angle
                    , sa = this.startAngle          // Previous start angle
                    , sat = this.startAngle         // Start angle
                    , ea                            // Previous end angle
                    , eat = sat + a                 // End angle
                    , r = 1;

                this.g.lineWidth = this.lineWidth;

                this.o.cursor
                    && (sat = eat - 0.3)
                    && (eat = eat + 0.3);

                if (this.o.displayPrevious) {
                    ea = this.startAngle + this.angle(this.v);
                    this.o.cursor
                        && (sa = ea - 0.3)
                        && (ea = ea + 0.3);
                    this.g.beginPath();
                    this.g.strokeStyle = this.pColor;
                    this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sa, ea, false);
                    this.g.stroke();
                }

                this.g.beginPath();
                this.g.strokeStyle = r ? this.o.fgColor : this.fgColor ;
                this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sat, eat, false);
                this.g.stroke();

                this.g.lineWidth = 2;
                this.g.beginPath();
                this.g.strokeStyle = this.o.fgColor;
                this.g.arc( this.xy, this.xy, this.radius - this.lineWidth + 1 + this.lineWidth * 2 / 3, 0, 2 * Math.PI, false);
                this.g.stroke();

                return false;
            }
        }
    });

    // Example of infinite knob, iPod click wheel
    var v, up=0,down=0,i=0
        ,$idir = $("span.idir")
        ,$ival = $("span.ival")
        ,incr = function() { i++; $idir.show().html("+").fadeOut(); $ival.html(i); }
        ,decr = function() { i--; $idir.show().html("-").fadeOut(); $ival.html(i); };
    $("input.infinite").knob(
                        {
                        min : 0
                        , max : 20
                        , stopper : false
                        , change : function () {
                                        if(v > this.cv){
                                            if(up){
                                                decr();
                                                up=0;
                                            }else{up=1;down=0;}
                                        } else {
                                            if(v < this.cv){
                                                if(down){
                                                    incr();
                                                    down=0;
                                                }else{down=1;up=0;}
                                            }
                                        }
                                        v = this.cv;
                                    }
                        });
