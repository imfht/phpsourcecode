// JavaScript Document$.tween = function(startProps, endProps, timeSeconds, animType, delay)
{
	var tw = new Tween();
	tw.start(startProps, endProps, timeSeconds, animType, delay);
	//
	//alert(tw)
	//
	return tw;
}


function Tween()
{
	this._frame=20;
	//
	this._startProps = [];
	this._currentProps = [];
	this._endProps = [];
	//
	this._startTimer = 0;
	this._timeSeconds = 0;
	this._animType = "linear";
	this._delay = 0;
	//
	this._runID = -1;
	//
	this.run = function(){}
	this.complete = function(){}
}
//
Tween.prototype.start = function(startProps, endProps, timeSeconds, animType, delay)
{
		if(animType != undefined)this._animType = animType;
		if(delay != undefined)this._delay = delay;
		//
		this._timeSeconds = timeSeconds;
		this._startTimer = new Date().getTime();
		this._startProps = startProps;
		this._endProps = endProps;
		this._currentProps = [];
		//
		var $this = this;
		//
		this._runID = setInterval(
			function(){$this._run();}
		,this._frame);
		//
}

Tween.prototype.stop = function()
{
	clearInterval(this._runID);
}


Tween.prototype._run = function()
{
	if ( new Date().getTime()- this._startTimer - (this._delay * 1000) < 0) return;
	var isEnd = false;
	for(var i in this._startProps)
	{
		this._currentProps[i] = this.getValue(this._startProps[i], this._endProps[i], this._startTimer, new Date().getTime() - (this._delay * 1000) , this._startTimer + (this._timeSeconds * 1000),this._animType);
		if(this._startTimer + (this._timeSeconds * 1000) + (this._delay * 1000) <= new Date().getTime())
		{
			this._currentProps[i] = this._endProps[i];
			isEnd = true;
		}
	}
	this.run(this._currentProps);
	if(isEnd)
	{
		this.stop();
		this.complete(this._currentProps);
	}
}

Tween.prototype.getValue = function(_propStart, _propDest, _timeStart, _timeNow, _timeDest,_animType,_extra)
{
			var t = _timeNow - _timeStart;  // current time (frames, seconds)
			var b = _propStart;             // beginning value
			var c = _propDest - _propStart; // change in value
			var d = _timeDest - _timeStart; // duration (frames, seconds)
			var s,a,p;

			if(_extra == undefined)
			{
				_extra = new Object();
				_extra["e1"] = 1;
				_extra["e2"] = 0;
				a = _extra["e1"];                // amplitude (optional - used only on *elastic easing)
				p = _extra["e2"];                // period (optional - used only on *elastic easing)
				s = _extra["e1"];                // overshoot ammount (optional - used only on *back easing)
			}
			switch (_animType.toLowerCase()) 
			{
				case Tween.linear:
					// simple linear tweening - no easing
					return c*t/d + b;

				case Tween.easeinquad:
					// quadratic (t^2) easing in - accelerating from zero velocity
					return c*(t/=d)*t + b;
				case Tween.easeoutquad:
					// quadratic (t^2) easing out - decelerating to zero velocity
					return -c *(t/=d)*(t-2) + b;
				case Tween.easeinoutquad:
					// quadratic (t^2) easing in/out - acceleration until halfway, then deceleration
					if ((t/=d/2) < 1) return c/2*t*t + b;
					return -c/2 * ((--t)*(t-2) - 1) + b;

				case Tween.easeincubic:
					// cubic (t^3) easing in - accelerating from zero velocity
					return c*(t/=d)*t*t + b;
				case Tween.easeoutcubic:
					// cubic (t^3) easing out - decelerating to zero velocity
					return c*((t=t/d-1)*t*t + 1) + b;
				case Tween.easeinoutcubic:
					// cubic (t^3) easing in/out - acceleration until halfway, then deceleration
					if ((t/=d/2) < 1) return c/2*t*t*t + b;
				return c/2*((t-=2)*t*t + 2) + b;

				case Tween.easeinquart:
					// quartic (t^4) easing in - accelerating from zero velocity
					return c*(t/=d)*t*t*t + b;
				case Tween.easeoutquart:
					// quartic (t^4) easing out - decelerating to zero velocity
					return -c * ((t=t/d-1)*t*t*t - 1) + b;
				case Tween.easeinoutquart:
					// quartic (t^4) easing in/out - acceleration until halfway, then deceleration
					if ((t/=d/2) < 1) return c/2*t*t*t*t + b;
					return -c/2 * ((t-=2)*t*t*t - 2) + b;

				case Tween.easeinquint:
					// quintic (t^5) easing in - accelerating from zero velocity
					return c*(t/=d)*t*t*t*t + b;
				case Tween.easeoutquint:
					// quintic (t^5) easing out - decelerating to zero velocity
					return c*((t=t/d-1)*t*t*t*t + 1) + b;
				case Tween.easeinoutquint:
					// quintic (t^5) easing in/out - acceleration until halfway, then deceleration
					if ((t/=d/2) < 1) return c/2*t*t*t*t*t + b;
					return c/2*((t-=2)*t*t*t*t + 2) + b;

				case Tween.easeinsine:
					// sinusoidal (sin(t)) easing in - accelerating from zero velocity
					return -c * Math.cos(t/d * (Math.PI/2)) + c + b;
				case Tween.easeoutsine:
					// sinusoidal (sin(t)) easing out - decelerating to zero velocity
					return c * Math.sin(t/d * (Math.PI/2)) + b;
				case Tween.easeinoutsine:
					// sinusoidal (sin(t)) easing in/out - acceleration until halfway, then deceleration
					return -c/2 * (Math.cos(Math.PI*t/d) - 1) + b;

				case Tween.easeinexpo:
					// exponential (2^t) easing in - accelerating from zero velocity
					return (t==0) ? b : c * Math.pow(2, 10 * (t/d - 1)) + b;
				case Tween.easeoutexpo:
					// exponential (2^t) easing out - decelerating to zero velocity
					return (t==d) ? b+c : c * (-Math.pow(2, -10 * t/d) + 1) + b;
				case Tween.easeinoutexpo:
					// exponential (2^t) easing in/out - acceleration until halfway, then deceleration
					if (t==0) return b;
					if (t==d) return b+c;
					if ((t/=d/2) < 1) return c/2 * Math.pow(2, 10 * (t - 1)) + b;
					return c/2 * (-Math.pow(2, -10 * --t) + 2) + b;

				case Tween.easeincirc:
					// circular (sqrt(1-t^2)) easing in - accelerating from zero velocity
					return -c * (Math.sqrt(1 - (t/=d)*t) - 1) + b;
				case Tween.easeoutcirc:
					// circular (sqrt(1-t^2)) easing out - decelerating to zero velocity
					return c * Math.sqrt(1 - (t=t/d-1)*t) + b;
				case Tween.easeinoutcirc:
					// circular (sqrt(1-t^2)) easing in/out - acceleration until halfway, then deceleration
					if ((t/=d/2) < 1) return -c/2 * (Math.sqrt(1 - t*t) - 1) + b;
					return c/2 * (Math.sqrt(1 - (t-=2)*t) + 1) + b;

				case Tween.easeinelastic:
					// elastic (exponentially decaying sine wave)
					if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
					if (a < Math.abs(c)) { a=c; s=p/4; }
					else s = p/(2*Math.PI) * Math.asin (c/a);
					return -(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;
				case Tween.easeoutelastic:
					// elastic (exponentially decaying sine wave)
					if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
					if (a < Math.abs(c)) { a=c; s=p/4; }
					else s = p/(2*Math.PI) * Math.asin (c/a);
					return a*Math.pow(2,-10*t) * Math.sin( (t*d-s)*(2*Math.PI)/p ) + c + b;
				case Tween.easeinoutelastic:
					// elastic (exponentially decaying sine wave)
					if (t==0) return b;  if ((t/=d/2)==2) return b+c;  if (!p) p=d*(.3*1.5);
					if (a < Math.abs(c)) { a=c; s=p/4; }
					else s = p/(2*Math.PI) * Math.asin (c/a);
					if (t < 1) return -.5*(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;
					return a*Math.pow(2,-10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )*.5 + c + b;

				// Robert Penner's explanation for the s parameter (overshoot ammount):
				//  s controls the amount of overshoot: higher s means greater overshoot
				//  s has a default value of 1.70158, which produces an overshoot of 10 percent
				//  s==0 produces cubic easing with no overshoot
				case Tween.easeinback:
					// back (overshooting cubic easing: (s+1)*t^3 - s*t^2) easing in - backtracking slightly, then reversing direction and moving to target
					if (s == undefined) s = 1.70158;
					return c*(t/=d)*t*((s+1)*t - s) + b;
				case Tween.easeoutback:
					// back (overshooting cubic easing: (s+1)*t^3 - s*t^2) easing out - moving towards target, overshooting it slightly, then reversing and coming back to target
					if (s == undefined) s = 1.70158;
					return c*((t=t/d-1)*t*((s+1)*t + s) + 1) + b;
				case Tween.easeinoutback:
					// back (overshooting cubic easing: (s+1)*t^3 - s*t^2) easing in/out - backtracking slightly, then reversing direction and moving to target, then overshooting target, reversing, and finally coming back to target
					if (s == undefined) s = 1.70158; 
					if ((t/=d/2) < 1) return c/2*(t*t*(((s*=(1.525))+1)*t - s)) + b;
					return c/2*((t-=2)*t*(((s*=(1.525))+1)*t + s) + 2) + b;

				// This were changed a bit by me (since I'm not using Penner's own Math.* functions)
				// So I changed it to call getValue() instead (with some different arguments)
				case Tween.easeinbounce:
					// bounce (exponentially decaying parabolic bounce) easing in
					return c - getValue (0, c, 0, d-t, d, Tween.easeoutbounce) + b;
				case Tween.easeoutbounce:
					// bounce (exponentially decaying parabolic bounce) easing out
					if ((t/=d) < (1/2.75)) {
					  return c*(7.5625*t*t) + b;
					} else if (t < (2/2.75)) {
					  return c*(7.5625*(t-=(1.5/2.75))*t + .75) + b;
					} else if (t < (2.5/2.75)) {
					  return c*(7.5625*(t-=(2.25/2.75))*t + .9375) + b;
					} else {
					  return c*(7.5625*(t-=(2.625/2.75))*t + .984375) + b;
					}
				case Tween.easeinoutbounce:
					// bounce (exponentially decaying parabolic bounce) easing in/out
					if (t < d/2) return getValue (0, c, 0, t*2, d, Tween.easeinbounce) * .5 + b;
					return getValue(0, c, 0, t * 2 - d, d, Tween.easeoutbounce) * .5 + c * .5 + b;
			}
			return c*t/d + b;
}	
//{animiType
Tween.linear  = "linear";
Tween.easeinquad  = "easeinquad";
Tween.easeoutquad  = "easeoutquad";
Tween.easeinoutquad  = "easeinoutquad";
Tween.easeincubic  = "easeincubic";
Tween.easeoutcubic  = "easeoutcubic";
Tween.easeinoutcubic  = "easeinoutcubic";
Tween.easeinquart  = "easeinquart";
Tween.easeoutquart  = "easeoutquart";
Tween.easeinoutquart  = "easeinoutquart";
Tween.easeinquint  = "easeinquint";
Tween.easeoutquint  = "easeoutquint";
Tween.easeinoutquint  = "easeinoutquint";
Tween.easeinsine  = "easeinsine";
Tween.easeoutsine  = "easeoutsine";
Tween.easeinoutsine  = "easeinoutsine";
Tween.easeinexpo  = "easeinexpo";
Tween.easeoutexpo  = "easeoutexpo";
Tween.easeinoutexpo  = "easeinoutexpo";
Tween.easeincirc  = "easeincirc";
Tween.easeoutcirc  = "easeoutcirc";
Tween.easeinoutcirc  = "easeinoutcirc";
Tween.easeinelastic  = "easeinelastic";
Tween.easeoutelastic  = "easeoutelastic";
Tween.easeinoutelastic  = "easeinoutelastic";
Tween.easeinback  = "easeinback";
Tween.easeoutback  = "easeoutback";
Tween.easeinoutback  = "easeinoutback";
Tween.easeinbounce  = "easeinbounce";
Tween.easeoutbounce  = "easeoutbounce";
Tween.easeinoutbounce  = "easeinoutbounce";
//}	