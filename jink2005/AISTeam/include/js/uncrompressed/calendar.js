/**********************************************************************
*          Calendar JavaScript [DOM] v3.01 by Michael Loesler          *
************************************************************************
* Copyright (C) 2005-07 by Michael Loesler, http//derletztekick.com    *
*                                                                      *
*                                                                      *
* This program is free software; you can redistribute it and/or modify *
* it under the terms of the GNU General Public License as published by *
* the Free Software Foundation; either version 3 of the License, or    *
* (at your option) any later version.                                  *
*                                                                      *
* This program is distributed in the hope that it will be useful,      *
* but WITHOUT ANY WARRANTY; without even the implied warranty of       *
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        *
* GNU General Public License for more details.                         *
*                                                                      *
* You should have received a copy of the GNU General Public License    *
* along with this program; if not, see <http://www.gnu.org/licenses/>  *
* or write to the                                                      *
* Free Software Foundation, Inc.,                                      *
* 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.            *
*                                                                      *
 **********************************************************************/

	function CalendarJS() {
		this.now = new Date();
		this.dayname = ["Mo","Di","Mi","Do","Fr","Sa","So"];
		this.monthname = ["Januar","Februar","März","April","Mai","Juni","Juli","August","September","Oktober","November","Dezember"];
		this.dayspermonth = [31,28,31,30,31,30,31,31,30,31,30,31];
		this.tooltip = ["vorheriger Monat","nächster Monat"];
		this.monthCell = document.createElement("th");
		this.tableHead = null;
		this.parEl = null;

		this.init = function( id ) {
			this.date = this.now.getDate();
			this.month = this.mm = this.now.getMonth();
			this.year = this.yy = this.now.getFullYear();
			this.monthCell.colSpan = 5;
			this.monthCell.appendChild(document.createTextNode( this.monthname[this.mm]+" "+this.yy ));
			this.tableHead = this.createTableHead();
			this.parEl = document.getElementById( id );
			this.show();
		}

		this.removeElements = function( Obj ) {
			for (var i=0; i<Obj.childNodes.length; i++)
				Obj.removeChild(Obj.childNodes[i]);
			return Obj;
		}

		this.show = function() {
			this.parEl = this.removeElements( this.parEl );
			this.monthCell.firstChild.replaceData(0, this.monthCell.firstChild.nodeValue.length, this.monthname[this.mm]+" "+this.yy);
			var padding = document.createAttribute("cellpadding");
			padding.nodeValue = "0";
			var spacing = document.createAttribute("cellspacing");
			spacing.nodeValue = "0";

			var table = document.createElement("table");
			table.appendChild( this.createTableBody() );
			table.appendChild( this.tableHead );
			table.setAttributeNode(padding);
			table.setAttributeNode(spacing);
			this.parEl.appendChild( table );
		}

		this.createTableHead = function() {
			var thead = document.createElement("thead");
			var tr = document.createElement("tr");
			var th = document.createElement("th");
			th.appendChild(document.createTextNode( "\u00AB" ));
			th.Instanz = this;
			th.onclick = function() { this.Instanz.switchMonth("prev"); };
			th.title = this.tooltip[0];
			try { th.style.cursor = "pointer"; } catch(e){ th.style.cursor = "hand"; }
			tr.appendChild( th );
			tr.appendChild( this.monthCell );
			th = document.createElement("th");
			th.appendChild(document.createTextNode( "\u00BB" ));
			th.Instanz = this;
			th.onclick = function() { this.Instanz.switchMonth("next"); };
			th.title = this.tooltip[1];
			try { th.style.cursor = "pointer"; } catch(e){ th.style.cursor = "hand"; }
			tr.appendChild( th );
			thead.appendChild( tr );
			tr = document.createElement('tr');
			for (var i=0; i<this.dayname.length; i++)
				tr.appendChild( this.getCell("th", this.dayname[i], "weekday" ) );
			thead.appendChild( tr );
			return thead;
		}

		this.createTableBody = function() {
			var sevendaysaweek = 0;
			var begin = new Date(this.yy, this.mm, 1);
			var firstday = begin.getDay()-1;
			if (firstday < 0)
				firstday = 6;
			if ((this.yy%4==0) && ((this.yy%100!=0) || (this.yy%400==0)))
				this.dayspermonth[1] = 29;

			var tbody = document.createElement("tbody");
			var tr = document.createElement('tr');

			for (var i=0; i<firstday; i++, sevendaysaweek++)
				tr.appendChild( this.getCell( "td", " ", null ) );

			for (var i=1; i<=this.dayspermonth[this.mm]; i++, sevendaysaweek++){
				if (this.dayname.length == sevendaysaweek){
					tbody.appendChild( tr );
					tr = document.createElement('tr');
					sevendaysaweek = 0;
				}
				if (i==this.date && this.mm==this.month && this.yy==this.year && (sevendaysaweek == 5 || sevendaysaweek == 6))
					tr.appendChild( this.getCell( "td", i, "today weekend" ) );
				else if (i==this.date && this.mm==this.month && this.yy==this.year)
					tr.appendChild( this.getCell( "td", i, "today" ) );
				else if (sevendaysaweek == 5 || sevendaysaweek == 6)
					tr.appendChild( this.getCell( "td", i, "weekend" ) );
				else
					tr.appendChild( this.getCell( "td", i, null ) );
			}

			for (var i=sevendaysaweek; i<this.dayname.length; i++)
				tr.appendChild( this.getCell( "td", " ", null  ) );

			tbody.appendChild( tr );
			return tbody;

		}

		this.getCell = function(tag, str, cssClass) {
			var El = document.createElement( tag );
			El.appendChild(document.createTextNode( str ));
			if (cssClass != null)
				El.className = cssClass;
			return El;
		}

		this.switchMonth = function( s ){
			switch (s) {
				case "prev":
					this.yy = (this.mm == 0)?this.yy-1:this.yy;
					this.mm = (this.mm == 0)?11:this.mm-1;
				break;

				case "next":
					this.yy = (this.mm == 11)?this.yy+1:this.yy;
					this.mm = (this.mm == 11)?0:this.mm+1;
				break;
			}
			this.show();
		}
	}

	var DOMContentLoaded = false;
	function addContentLoadListener (func) {
		if (document.addEventListener) {
			var DOMContentLoadFunction = function () {
				window.DOMContentLoaded = true;
				func();
			};
			document.addEventListener("DOMContentLoaded", DOMContentLoadFunction, false);
		}
		var oldfunc = (window.onload || new Function());
		window.onload = function () {
			if (!window.DOMContentLoaded) {
				oldfunc();
				func();
			}
		};
	}

