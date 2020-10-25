
/**
* jQuery.colorize
* Copyright (c) 2008-2009 Eric Karimov - ekarim57(at)gmail(dot)com | http://franca.exofire.net/jq/
* Dual licensed under MIT and GPL.
* Date: 7/02/2009
*
* @projectDescription Table colorize using jQuery.
* http://franca.exofire.net/jq/colorize
*
* @author Eric Karimov, contributor Aymeric Augustin
* @version 1.5.1
*
* @param {altColor, bgColor, hoverColor, hoverClass, hiliteColor, hiliteClass, oneClick, columns,  banColumns}
* altColor : alternate row background color, 'none' can be used for no alternate background color
* bgColor : background color (The default background color is white).
* hoverColor : background color when you hover a mouse over a row
* hoverClass: style class for mouseover; takes precedence over hoverColor property; may slow down performance in IE
* hiliteColor : row highlight background color, 'none' can be used for no highlight
* hiliteClass: style class for highlighting a row or a column; takes precedence over the hiliteColor setting
* oneClick : true/false(default) -	 if true, clicking a new row reverts the current highlighted row to the original background color
* columns : true/false/'hover'  - The default is false. if true, highlights columns instead of rows. If the value is 'hover',
* 	 a column is highlighted on mouseover, but does not respond to clicking. Instead, a row is highlighted when clicked.
* banColumns : []	- columns not to be highlighted or hovered over; supply an array of column indices, starting from 0; 'last' can be used
* to ban the last column
* @return {jQuery} Returns the same jQuery object, for chaining.
*
* @example $('#tbl1').colorize();
*
* @$('#tbl1').colorize({bgColor:'#EAF6CC', hoverColor:'green', hiliteColor:'red', columns:true, banColumns:[4,5,'last']});
*
* @$('#tbl1').colorize({ columns : 'hover', oneClick:true});
* All the parameters are optional.
*/
jQuery.fn.colorize = function(params) {
	options = {
		altColor: '#ECF6FC',
		bgColor: '#fff',
		hoverColor: '#BCD4EC',
		hoverClass:'',
		hiliteColor: 'yellow',
		hiliteClass:'',
		oneClick: false,
		columns: false,
		banColumns: []
	};
	jQuery.extend(options, params);
	var colorHandler = {
		addHoverClass: function(){
			this.origColor = this.style.backgroundColor;
			this.style.backgroundColor='';
			jQuery(this).addClass(options.hoverClass);
		},
		addBgHover:function (){
			this.origColor = this.style.backgroundColor;
			this.style.backgroundColor= options.hoverColor;
		},
		removeHoverClass: function(){
			jQuery(this).removeClass(options.hoverClass);
			this.style.backgroundColor=this.origColor;
		},
		removeBgHover: function(){
			  this.style.backgroundColor=this.origColor;
		},
		checkHover: function() {
			if (!this.onfire) this.hover();
		},
		checkHoverOut: function() {
			if (!this.onfire) this.removeHover();
		},
		highlight: function() {
			if(options.hiliteClass.length>0 || options.hiliteColor != 'none')
			{
				if(!this.onfire & options.columns=='hover')
						this.origColor = this.style.backgroundColor;
				this.onfire = true;
				if(options.hiliteClass.length>0){
					this.style.backgroundColor='';
					jQuery(this).addClass(options.hiliteClass).removeClass(options.hoverClass);
				}
				else if (options.hiliteColor != 'none') {
					this.style.backgroundColor= options.hiliteColor;
				}
			}
		},
		stopHighlight: function() {
			this.onfire = false;
			this.style.backgroundColor = (this.origColor)?this.origColor:'';
			jQuery(this).removeClass(options.hiliteClass).removeClass(options.hoverClass);
		}
	}
	 function  processCells (cells, idx, func) {
		var colCells = getColCells(cells, idx);
		jQuery.each(colCells, function(index, cell2) {
			func.call(cell2);
		});
	    function getColCells (cells, idx) {
			var arr = [];
			for (var i = 0; i < cells.length; i++) {
				if (cells[i].cellIndex == idx)
					arr.push(cells[i]);
			}
			return arr;
		}
	}
	function processAdapter(cells, cell, func) {
		processCells(cells, cell.cellIndex, func);
	}
  var clickHandler = {
	toggleColumnClick : function (cells) {
		var func = (!this.onfire) ? colorHandler.highlight : colorHandler.stopHighlight;
		processAdapter(cells, this, func);
	},
	toggleRowClick: function(cells) {
		row = jQuery(this).parent().get(0);
		if (!row.onfire)
			colorHandler.highlight.call(row);
		else
			colorHandler.stopHighlight.call(row);
	},
	oneClick : function (cell, cells, indx){
	        if (cells.clicked != null){
	            if (cells.clicked == indx) // repeat the same set click
	            {
	                this.stopHilite();
	                cells.clicked = null; //  set was not selected
	            }
	            else{
	                this.stopHilite();
	                this.hilite.call(cell);
	            }
	        }
	        else if (cells.clicked == null) {
	            this.hilite.call(cell);
	        }
	   },
	 oneColumnClick : function (cells) {
	        var indx = this.cellIndex;
	        clickHandler.hilite= hilite;
	        clickHandler.stopHilite = stopHilite;
	        clickHandler.oneClick (this, cells, indx);
	        function stopHilite(){
	            processCells(cells, cells.clicked, colorHandler.stopHighlight);
	        }
	        function hilite(){
	            processAdapter(cells, this, colorHandler.highlight);
	            cells.clicked  = indx;
	        }
	   },
	  oneRowClick  : function (cells) {
	    var row = jQuery(this).parent().get(0);
	    var indx = row.rowIndex;
	    clickHandler.hilite= hilite;
	    clickHandler.stopHilite = stopHilite;
	    clickHandler.oneClick (this, cells, indx);
	     function stopHilite(){
	         colorHandler.stopHighlight.call(clickHandler.tbl.rows[cells.clicked]); // delete the selected row
	     }
	     function hilite (){
	         colorHandler.highlight.call(row); // the current row is set to select
	         cells.clicked = indx; //the current row is recorded
	     }
	   }
	 }
	function checkBan() {
		return (jQuery.inArray(this.cellIndex, options.banColumns) != -1) ;
	}
	function attachHoverHandler(){
		this.hover = optionsHandler.hover;
		this.removeHover = optionsHandler.removeHover;
	}
	function handleColumnHoverEvents(cell, cells){
		attachHoverHandler.call (cell);
		cell.onmouseover = function() {
			if (checkBan.call(this)) return;
			processAdapter(cells, this, colorHandler.checkHover);
		}
		cell.onmouseout = function() {
			if (checkBan.call(this)) return;
			processAdapter(cells, this, colorHandler.checkHoverOut);
		}
	}
	function handleRowHoverEvents(cell, cells){
		row = jQuery(cell).parent().get(0);
		attachHoverHandler.call (row);
		row.onmouseover = colorHandler.checkHover ;
		row.onmouseout = colorHandler.checkHoverOut ;
	}
	var optionsHandler ={
		getHover: function(){
			if(options.hoverClass.length>0){
				this.hover = colorHandler.addHoverClass;
				this.removeHover = colorHandler.removeHoverClass;
			}
			else{
				this.hover = colorHandler.addBgHover;
				this.removeHover = colorHandler.removeBgHover;
			}
		},
		getRowClick : 	function (){
			if(options.oneClick)
				return clickHandler.oneRowClick;
			else
				return clickHandler.toggleRowClick;
		},
		getColumnClick : 	function (){
			if(options.oneClick)
				return clickHandler.oneColumnClick;
			else
				return clickHandler.toggleColumnClick;
		}
	}
	var rowHandler = {
		handleHoverEvents : handleRowHoverEvents,
		clickFunc : optionsHandler.getRowClick()
	}
	var colHandler = {
		handleHoverEvents : handleColumnHoverEvents,
		clickFunc : optionsHandler.getColumnClick()
	}
	return this.each(function() {
		if (options.altColor!='none') {
			jQuery(this).find('tr:odd').css('background', options.bgColor);
			jQuery(this).find('tr:even').css('background', options.altColor);
		}
    	if (jQuery(this).find('thead tr:last th').length > 0)
			 var cells = jQuery(this).find('td, thead tr:last th');
		else
			var cells = jQuery(this).find('td,th');
		cells.clicked = null;
		if (jQuery.inArray('last', options.banColumns) != -1){
			if(this.rows.length>0){
				options.banColumns.push(this.rows[0].cells.length-1);
			}
		}
		optionsHandler.getHover();
		clickHandler.tbl = this;
		if(options.columns){
			var handler = colHandler;
			if(options.columns=='hover') handler.clickFunc = optionsHandler.getRowClick();
		}
		else{
			var handler = rowHandler;
		}
		jQuery.each(cells, function(i, cell) {
			 handler.handleHoverEvents (this, cells);
			 this.onclick = function() {
			    if(checkBan.call(this)) return;
			 	handler.clickFunc.call(this, cells);
		     }
		});
	});
 }
