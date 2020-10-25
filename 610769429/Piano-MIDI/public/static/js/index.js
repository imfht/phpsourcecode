var colorSetup = ['0,0,0','255,255,255','192,192,192','128,128,128','255,0,0','128,0,0','0,255,0','0,128,0','0,0,255','0,0,128','255,0,255','128,0,128','0,255,255','0,128,128','255,255,0','128,128,0','140,198,231','25,202,173','140,199,181','160,238,225','190,231,233','190,237,199','214,213,183','209,186,116','230,206,172','236,173,158','244,96,108'];
var allKeyPosition = {"A":"45","A1":"33","A1B1":"34","A2":"21","A2B2":"22","AB":"46","B":"47","B1":"35","B2":"23","C":"36","C1":"24","C1D1":"25","CD":"37","D":"38","D1":"26","D1E1":"27","DE":"39","E":"40","E1":"28","F":"41","F1":"29","F1G1":"30","FG":"42","G":"43","G1":"31","G1A1":"32","GA":"44","a":"57","a1":"69","a1b1":"70","a2":"81","a2b2":"82","a3":"93","a3b3":"94","a4":"105","a4b4":"106","ab":"58","b":"59","b1":"71","b2":"83","b3":"95","b4":"107","c":"48","c1":"60","c1d1":"61","c2":"72","c2d2":"73","c3":"84","c3d3":"85","c4":"96","c4d4":"97","c5":"108","cd":"49","d":"50","d1":"62","d1e1":"63","d2":"74","d2e2":"75","d3":"86","d3e3":"87","d4":"98","d4e4":"99","de":"51","e":"52","e1":"64","e2":"76","e3":"88","e4":"100","f":"53","f1":"65","f1g1":"66","f2":"77","f2g2":"78","f3":"89","f3g3":"90","f4":"101","f4g4":"102","fg":"54","g":"55","g1":"67","g1a1":"68","g2":"79","g2a2":"80","g3":"91","g3a3":"92","g4":"103","g4a4":"104","ga":"56"};
var leftBlack = new Array(1,3,6,8,11,13,16,18,21,23,26,28,31,33);
var rightBlack = new Array(2,5,7,10,12,15,17,20,22,25,27,30,32,35);
var winWidth = $(window).width(),winHeight = $(window).height();
var runTime = localStorage.getItem('runTime')?localStorage.getItem('runTime'):8;
var colorS = localStorage.getItem('colorS')?localStorage.getItem('colorS'):'rgba(140,198,231,1)';
var rHtCss = "animation:runHeight "+runTime+"s;-moz-animation:runHeight "+runTime+"s;-webkit-animation:runHeight "+runTime+"s;-o-animation:runHeight "+runTime+"s;animation-timing-function: linear;";
var rBmCss = "animation:runBottom "+runTime+"s;-moz-animation:runBottom "+runTime+"s;-webkit-animation:runBottom "+runTime+"s;-o-animation:runBottom "+runTime+"s;animation-timing-function: linear;";
$(function(){
	for(i in colorSetup){
		var tmpClass = '';
		if(i == 16) tmpClass = 'check';
		$('#colorS span').append('<font class="'+tmpClass+'" style="background:rgba('+colorSetup[i]+',1)"></font>');
	}
	setSetting();
	/**/
    var _keyboardH = $('#_keyboard').height();
    $('#exhibition,#exhibition2').height(winHeight - _keyboardH);
    $("html,body").bind('touchmove',function(e){
        e.preventDefault();
    });
	for(var i=0;i<52;i++){
        $('#_keyboard').append("<div class='_white effective'></div>");
        $('#exhibition').append("<div class='_white'></div>");
        $('#_keyboard2,#exhibition2').append("<div class='_black'></div>");
    }
	var whiteWidth = $('#_keyboard ._white').width();
	$('#_keyboard2 ._black,#exhibition2 ._black').css('margin-right',whiteWidth/2.62);//调整黑键宽度
	var blackWidth = $('#_keyboard2 ._black').width();
    $('#_keyboard2 ._black,#exhibition2 ._black').css('left',-(blackWidth/2.5));//调整黑键左移方位
	blackAdjust();
	/**/
	$('#_keyboard ._white').mouseenter(function(){
		startBlock(allKeyPosition[$(this).attr('id')]);
		$(this).css('background','#9FC4E7');
	});
	$('#_keyboard ._white').mouseleave(function(){
		endBlock(allKeyPosition[$(this).attr('id')]);
		$(this).css('background','#FBFBFB');
	});
	$('#_keyboard2 ._black').mouseenter(function(){
		startBlock(allKeyPosition[$(this).attr('id')]);
		$(this).css('background','#53799B');
	});
	$('#_keyboard2 ._black').mouseleave(function(){
		endBlock(allKeyPosition[$(this).attr('id')]);
		$(this).css('background','#1D1D1D');
	});
})
function blackAdjust(){
	$('#_keyboard2 ._black,#exhibition2 ._black').addClass('keyYes');
	var dataArr = [0,2,5,9,12,16,19,23,26,30,33,37,40,44,47,51];
	for(i in dataArr) $('#_keyboard2 ._black:eq('+dataArr[i]+'),#exhibition2 ._black:eq('+dataArr[i]+')').addClass('keyNo').removeClass('keyYes').css('visibility','hidden');
    $('#_keyboard2 .keyYes').addClass('effective');
    $('#exhibition div,#exhibition2 .keyYes').addClass('enBlock');
	for(i in leftBlack) $('#_keyboard2 .keyYes:eq('+leftBlack[i]+'),#exhibition2 .keyYes:eq('+leftBlack[i]+')').css('left','-='+'3.9px');
	for(i in rightBlack) $('#_keyboard2 .keyYes:eq('+rightBlack[i]+'),#exhibition2 .keyYes:eq('+rightBlack[i]+')').css('left','+='+'3px');
	bindId();
}
var pianoArr = {21:"A2",22:"A2B2",23:"B2",24:"C1",25:"C1D1",26:"D1",27:"D1E1",28:"E1",29:"F1",30:"F1G1",31:"G1",32:"G1A1",33:"A1",34:"A1B1",35:"B1",36:"C",37:"CD",38:"D",39:"DE",40:"E",41:"F",42:"FG",43:"G",44:"GA",45:"A",46:"AB",47:"B",48:"c",49:"cd",50:"d",51:"de",52:"e",53:"f",54:"fg",55:"g",56:"ga",57:"a",58:"ab",59:"b",60:"c1",61:"c1d1",62:"d1",63:"d1e1",64:"e1",65:"f1",66:"f1g1",67:"g1",68:"g1a1",69:"a1",70:"a1b1",71:"b1",72:"c2",73:"c2d2",74:"d2",75:"d2e2",76:"e2",77:"f2",78:"f2g2",79:"g2",80:"g2a2",81:"a2",82:"a2b2",83:"b2",84:"c3",85:"c3d3",86:"d3",87:"d3e3",88:"e3",89:"f3",90:"f3g3",91:"g3",92:"g3a3",93:"a3",94:"a3b3",95:"b3",96:"c4",97:"c4d4",98:"d4",99:"d4e4",100:"e4",101:"f4",102:"f4g4",103:"g4",104:"g4a4",105:"a4",106:"a4b4",107:"b4",108:"c5"};
var pianoArr_ = ["A2","B2","C1","D1","E1","F1","G1","A1","B1","C","D","E","F","G","A","B","c","d","e","f","g","a","b","c1","d1","e1","f1","g1","a1","b1","c2","d2","e2","f2","g2","a2","b2","c3","d3","e3","f3","g3","a3","b3","c4","d4","e4","f4","g4","a4","b4","c5","A2B2","C1D1","D1E1","F1G1","G1A1","A1B1","CD","DE","FG","GA","AB","cd","de","fg","ga","ab","c1d1","d1e1","f1g1","g1a1","a1b1","c2d2","d2e2","f2g2","g2a2","a2b2","c3d3","d3e3","f3g3","g3a3","a3b3","c4d4","d4e4","f4g4","g4a4","a4b4"];
function bindId(){
	$('.effective').each(function(i){
		$(this).attr('id',pianoArr_[+i]);
    })
    $('.enBlock').each(function(i){
		$(this).attr('id','_'+allKeyPosition[pianoArr_[+i]]);
    })
}
/**/
if(navigator.requestMIDIAccess){navigator.requestMIDIAccess().then(succ,fail);} 
function succ(midi){
    var inputs = midi.inputs.values();
    for(var input = inputs.next();input && !input.done;input = inputs.next()) input.value.onmidimessage = onMIDIMessage;
    console.log('Success access to your midi devices.');
}
function fail(){
    console.error('No access to your midi devices.');
}
function onMIDIMessage(msg){
  	var tmpId = msg.data[1],tmpId_ = msg.data[2];
    if(tmpId_ == 127) return;
	var bom = $('#'+pianoArr[tmpId]),bg = 'background';
  	if(tmpId && tmpId_ > 0){
        startBlock(tmpId);
		if($(bom).hasClass('_white')) $(bom).css(bg,'#9FC4E7'); else $(bom).css(bg,'#53799B');
	}
	if(tmpId && tmpId_ === 0){
        endBlock(tmpId);
		if($(bom).hasClass('_white')) $(bom).css(bg,'#FBFBFB'); else $(bom).css(bg,'#1D1D1D');
	}
}
function startBlock(id){
    $('#_'+id).append("<div class='slider run_"+id+"' style='"+rHtCss+"background:"+colorS+"'></div>");
}
function endBlock(id){
    var newClass = Math.round(new Date().getTime()),newHeight = $('.run_'+id).height();
    $('#_'+id).append("<div class='slider_ endTo_"+newClass+"' style='bottom:0;height:"+newHeight+"px;"+rBmCss+"background:"+colorS+"'></div>");
    $('#_'+id+' .slider').remove();
    $('.endTo_'+newClass).bind('animationend',function(e){
        $(this).remove();
    })
}
function setSetting(){
	$('#colorS font').click(function(){
		$('#colorS font').removeClass('check');
		$(this).addClass('check');
	});
	$('#setting').click(function(){
		$('#settingBox,#bgColor').show();
	});
	$('#bgColor,#close').click(function(){
		$('#settingBox,#bgColor').hide();
	});
	$('#reset').click(function(){
		localStorage.clear();
		window.location.reload();
	});
	$('#saveS').click(function(){
		localStorage.setItem('runTime',$('#runTime').val());
		localStorage.setItem('colorS',$('#colorS .check').css('background'));
		localStorage.setItem('colorSindex',$('#colorS .check').index());
		window.location.reload();
	});
	if(localStorage.getItem('colorSindex')){
		$('#colorS font').removeClass('check');
		$('#colorS font').eq(localStorage.getItem('colorSindex')).addClass('check');
	}
	$('#runTime').val(runTime);
}
window.onresize = function(){
	window.location.reload();
}