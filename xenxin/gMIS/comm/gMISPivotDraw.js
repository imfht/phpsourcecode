/**
 * gMIS Pivot Draw
 * work with act/pivot.php
 * Xexnin@Ufqi
 * ver 0.1
 * Fri, 4 Aug 2017 21:08:12 +0800
 */

function gMISPivotDraw(dataTbl, calList, grpList, sumList, statList, targetTbl){
	var dtbl = dataTbl;
	//console.log("dtbl:"+dtbl+", calList:["+calList+"] grpList:["+grpList+"] sumList:["
	//		+sumList+"] statList:["+statList+"]");
	console.log("dtbl:"+dtbl+", calList:["+calList+"]");
	var widthWeight = 1.8; // more space for calculate fields
	var barPercent = 0.92; // bar width percent, leave space for chars right
	var objtbl = document.getElementById(dtbl);
	calList = JSON.parse(calList);
	grpList = JSON.parse(grpList);
	sumList = JSON.parse(sumList);
	statList = JSON.parse(statList);
	var diagramStr = '<table id="gmis_pivot_draw_tbl" style="border:1px solid black; '
			+'width:96%; margin-left:auto; margin-right:auto;" class="gmis_pivot_draw_tbl">';
	if(objtbl){
		var rowLen = objtbl.rows.length;
		var statColArr = new Array();
		for(var i=0; i<rowLen; i++){
			var cells = objtbl.rows[i].cells;
			var cellLen = cells.length;
			diagramStr += '<tr>';
			for(var j=0; j<cellLen; j++){
				var tdv = cells[j].innerText;
				//console.log("tr:"+i+", td:"+j+", val:"+tdv);
				if(i==1){ // very first row
					if(statList.hasOwnProperty(tdv)){
						statColArr[j] = tdv;
					}
					else{
						statColArr[j] = '';
					}
					//console.log('j:'+j+' tdv:'+tdv+', colName:'+statColArr[j]);
				}
				else if(tdv == 'GrandTotal' || tdv == 'Average.of'
					|| tdv == 'Max.of' || tdv == 'Min.of' || tdv == 'ALL'){
					//- sensitive fields, skip
					break;
				}
				if(i> 1 && statColArr[j] != ''){ // from 2nd row
					var colName = statColArr[j];
					var maxN = statList[colName]['max'];
					var minN = statList[colName]['min'];
					var prtofsum = shortenDecimal(tdv*100/(sumList[colName])); //- sumList?
					var prtofmax = shortenDecimal(tdv*100/(maxN)); //- statList?
					var tdWidth = 100/(cellLen/widthWeight);
					var perctChar = prtofsum + '%';
					if(tdv == maxN){
						perctChar = '<span style="color:red;"><b>'+prtofsum+'%</b></span>';
					}
					else if(tdv == minN){
						perctChar = '<span style="color:silver;">'+prtofsum+'%</span>';
					}
					diagramStr += '<td style="overflow:hidden;width:'+tdWidth+'%;"><span style="width:'
						+(prtofmax*barPercent)+'%;" title="'+tdv+'" class="spanbar">'
						+shortenDecimal(tdv)+'</span> '+perctChar+'</td>';
				}
				else{
					diagramStr += '<td>'+tdv+'</td>';
				}
			}
			diagramStr += '</tr>';
		}
	}
	else{
		console.log('objtbl failed. 201708042105.');
	}
	diagramStr += '</table>';
	var tgttbl = document.getElementById(targetTbl);
	if(tgttbl){
		tgttbl.innerHTML = diagramStr;
	}
	else{
		console.log('targettbl failed. 201708042208.');
	}
	return true;
}

//- simple sprintf
//- Mon, 7 Aug 2017 22:42:32 +0800
function shortenDecimal(f){
	var fa = f + '';
	var dotPos = fa.indexOf('.');
	if(dotPos > -1){
		var fi = fa.substring(0,dotPos);
		fa = fi + fa.substr(dotPos, 2);
	}
	return fa;
}
