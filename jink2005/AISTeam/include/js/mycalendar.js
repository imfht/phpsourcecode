
function makeDatepicker(m,y,div)
{
	theCal = new calendar(m,y);
	theCal.getDatepicker(div);
}
function makeCal(m,y,div)
{
	theCal = new calendar(m,y);
	theCal.getCal(div);
}
function calendar(theMonth,theYear,options)
{
	this.dayNames = ["Mo","Di","Mi","Do","Fr","Sa","So"];
	this.monthNames = ["Januar","Februar","März","April","Mai","Juni","Juli","August","September","Oktober","November","Dezember"];
	this.keepEmpty = false;
	this.dateSeparator = ".";
	this.relateTo = "";
	this.dateFormat = "d.m.Y";


	this.calendar = [];
	datobj = new Date();
	this.currmonth = datobj.getMonth();
	this.curryear = datobj.getFullYear();
	this.currday = datobj.getDate();


	if(theMonth > 12)
	{
		theMonth = 1;
		theYear = theYear+1;
	}
	if(theMonth < 1)
	{
		theMonth = 12;
		theYear = theYear-1;
	}

	this.month = theMonth-1;
	this.year = theYear;

	this.daysInMonth = this.getDaysInMonth(this.month,this.year);
	this.daysLastMonth = this.getDaysInMonth(this.month-1,this.year);
	var firstDay = new Date(this.year, this.month, 1);
	this.startDay = firstDay.getDay()-1;
	var tempDays = this.startDay + this.daysInMonth;
	this.weeksInMonth = Math.ceil(tempDays/7);

}

calendar.prototype.getCal = function(theDiv)
{
	var theHtml = "";
	var pmonth = this.month;
	var nmonth = this.month+2;

	theHtml += "<table class=\"cal\" cellpadding=\"0\" cellspacing=\"1\" border = \"0\">";
	theHtml += "<tr class=\"head\" >" +
			"<td class = \"back\"><a href = \"javascript:void(0);\"></a></td>" +
			"<td colspan=\"5\" >" + this.monthNames[this.month] + " " + this.year + "</td>" +
			"<td class=\"next\"><a href = \"javascript:void(0);\"></a></td></tr>";


	theHtml += "<tr class = \"weekday\">";
	for(i=0;i<this.dayNames.length;i++)
	{
		theHtml += "<td >" + this.dayNames[i] + "</td>"
	}
	theHtml += "</tr>";


	var intMinithecal = this.buildCal();

	for(j=0;j<this.weeksInMonth;j++) {
		theHtml += "<tr >";
		for(i=0;i<7;i++) {
				var theDay = intMinithecal[j][i];
				//theDay = theDay - 1;
				if(theDay > 0 && theDay <= this.daysInMonth)
				{
					if(this.currmonth == this.month && this.curryear == this.year && this.currday == theDay)
					{
						theHtml += "<td class = \"today\">"+ theDay + "</td>";
					}
					else
					{
						theHtml += "<td >"+ theDay + "</td>";
					}


				}
				else if(theDay < 1)
				{
					theHtml += "<td class = \"wrong\">"+(this.daysLastMonth+theDay)+"</td>";
				}
				else if(theDay > this.daysInMonth)
				{
					theHtml += "<td class = \"wrong\">"+(theDay-this.daysInMonth)+"</td>";
				}
			}
		theHtml += "</tr>";
	}
	theHtml += "</table>";

	$(theDiv).innerHTML = theHtml;


	theMonths = this.monthNames
	theDays = this.dayNames;
	keepEmpty = this.keepEmpty;
	dateSeparator = this.dateSeparator;
	theYear = this.year;
	$$("#"+theDiv+" .cal  .back a").each
	(
		function (item)
		{
			item.onclick = function()
			{
					theCal = new calendar(pmonth,theYear);
					theCal.monthNames = theMonths;
					theCal.dayNames = theDays;
					theCal.keepEmpty = keepEmpty;
					theCal.dateSeparator = dateSeparator;

					theCal.getCal(theDiv);
			}
		}
	);
		$$("#"+theDiv+" .cal .next a").each
	(
		function (item)
		{
			item.onclick = function()
			{
					internalCals = new calendar(nmonth,theYear);
					internalCals.monthNames = theMonths;
					internalCals.dayNames = theDays;
					internalCals.keepEmpty = keepEmpty;
					internalCals.dateSeparator = dateSeparator;

					internalCals.getCal(theDiv);
			}
		}
	);
}

calendar.prototype.getDatepicker = function(theDiv)
{
    if(this.dateFormat == "m/d/Y" || this.dateFormat == "m/d/y")
    {
        this.dateSeparator = "/";
    }
	var theHtml = "";
	var pmonth = this.month;
	var nmonth = this.month+2;
	this.theDiv = theDiv;


	if(this.relateTo)
	{

		$(this.relateTo).onfocus = function()
		{

			new Effect.Appear(theDiv,{duration:1.0});
		}


	}

	theHtml += "<table class=\"cal\" cellpadding=\"0\" cellspacing=\"1\" border = \"0\">";
	theHtml += "<tr class=\"head\" >" +
			"<td class = \"back\"><a href = \"javascript:void(0);\"></a></td>" +
			"<td colspan=\"5\" >" + this.monthNames[this.month] + " " + this.year + "</td>" +
			"<td class=\"next\"><a href = \"javascript:void(0);\"></a></td></tr>";

	theHtml += "<tr class = \"weekday\">";
	for(i=0;i<this.dayNames.length;i++)
	{
		theHtml += "<td >" + this.dayNames[i] + "</td>"
	}
	theHtml += "</tr>";

	var thecal = this.buildCal();

	if(!this.keepEmpty && !$(this.relateTo).value)
	{
		if((this.month+1) < 10)
		{
			strMon = "0" + (this.month+1);
		}
		else
		{
			strMon = this.month+1;
		}
		if(this.currday < 10 && this.currday > 0)
		{
			strDay = "0" + this.currday;
		}
		else
		{
			strDay = this.currday;
		}

        if(this.dateFormat == "d.m.Y" || this.dateFormat == "d.m.y")
        {
		  initStr = strDay + this.dateSeparator + strMon + this.dateSeparator + this.curryear;
		}
		else if(this.dateFormat == "m/d/Y" || this.dateFormat == "m/d/y")
		{
		  initStr = strMon + this.dateSeparator + strDay + this.dateSeparator + this.curryear;
		}
        //initStr = strMon + "/" + strDay + "/" + this.year;
		$(this.relateTo).value = initStr;
	}
		selectedVals = $(this.relateTo).value.split(this.dateSeparator);


	for(j=0;j<this.weeksInMonth;j++) {
		theHtml += "<tr>";
		for(i=0;i<7;i++) {
				var theDay = thecal[j][i];
				strDay = theDay;
				if(theDay < 10 && theDay > 0)
				{
					strDay = "0" + theDay;
				}

				if((this.month+1) < 10)
				{
					strMon = "0" + (this.month+1);
				}
				else
				{
					strMon = this.month+1;
				}

                if(this.dateFormat == "d.m.Y" || this.dateFormat == "d.m.y")
                {
        		  dstring = strDay + this.dateSeparator + strMon + this.dateSeparator + this.year;
        		}
        		else if(this.dateFormat == "m/d/Y" || this.dateFormat == "m/d/y")
        		{
        		  dstring = strMon + this.dateSeparator + strDay + this.dateSeparator + this.year;
        		}

				//dstring = strMon + "/" + strDay + "/" + this.year;
				if(theDay > 0 && theDay <= this.daysInMonth)
				{
					if(this.currmonth == this.month && this.curryear == this.year && this.currday == theDay)
					{
						theHtml += "<td class = \"today\" onclick = \"$('"+this.relateTo+"').value='"+dstring+"';new Effect.Fade('"+theDiv+"');\">"+ theDay + "</td>";
					}
					else if(this.month == (selectedVals[1]-1) && this.year == selectedVals[2] && selectedVals[0] == theDay)
					{
						theHtml += "<td class = \"red\" onclick = \"$('"+this.relateTo+"').value='"+dstring+"';new Effect.Fade('"+theDiv+"');\">"+ theDay + "</td>";
					}
					else
					{
						theHtml += "<td class = \"normalday\" onclick = \"$('"+this.relateTo+"').value='"+dstring+"';new Effect.Fade('"+theDiv+"');\">"+ theDay + "</td>";
					}
				}
				else if(theDay < 1)
				{
					theHtml += "<td class = \"wrong\">"+(this.daysLastMonth+theDay)+"</td>";
				}
				else if(theDay > this.daysInMonth)
				{
					theHtml += "<td class = \"wrong\">"+(theDay-this.daysInMonth)+"</td>";
				}
			}
		theHtml += "</tr>";
	}

	theHtml += "<tr><td colspan = \"7\" class = \"dpfoot\"><a href = \"javascript:void(0);\" onclick = \"javascript:new Effect.Fade('"+theDiv+"','{duration:1.0}');\">Close</a></td></tr></table>";

	$(theDiv).innerHTML = theHtml;

	var theMonths = this.monthNames
	var theDays = this.dayNames;
	var keepEmpty = this.keepEmpty;
	var dateSeparator = this.dateSeparator;
	var theYear = this.year;
	var theRelate = this.relateTo;
	$$("#"+theDiv+" .cal .back a").each
	(
		function (item)
		{
			item.onclick = function()
			{
					var internalCal = new calendar(pmonth,theYear);
					internalCal.monthNames = theMonths;
					internalCal.dayNames = theDays;
					internalCal.keepEmpty = keepEmpty;
					internalCal.relateTo = theRelate;
					internalCal.dateSeparator = dateSeparator;
					internalCal.getDatepicker(theDiv);

			}
		}
	);
	$$("#"+theDiv+" .cal .next a").each
	(
		function (item)
		{
			item.onclick = function()
			{
					var internalCal = new calendar(nmonth,theYear);
					internalCal.monthNames = theMonths;
					internalCal.dayNames = theDays;
					internalCal.keepEmpty = keepEmpty;
					internalCal.relateTo = theRelate;
					internalCal.dateSeparator = dateSeparator;
					internalCal.getDatepicker(theDiv);
			}
		}
	);
}

calendar.prototype.showDatepicker = function()
{

}

calendar.prototype.buildCal = function()
{
	var counter = 0;
	for(j=0;j<this.weeksInMonth;j++) {
			this.calendar[j] = [];
			for(i=0;i<7;i++) {
				counter++;
				var theday = counter-this.startDay;
				this.calendar[j][i] = theday;
		}
	}

	return this.calendar;
}

calendar.prototype.getDaysInMonth = function(intMonth, intYear)
{
	dteMonth = new Date(intYear,intMonth);
	intDaysInMonth = 28;
	blnDateFound = false;

	while (!blnDateFound)
	{
		dteMonth.setDate(intDaysInMonth+1);
		intNewMonth = dteMonth.getMonth();

		if (intNewMonth != intMonth)
		{
		  blnDateFound = true;
		}
		else
		{
		  intDaysInMonth++;
		}
	}

	return intDaysInMonth;
}

